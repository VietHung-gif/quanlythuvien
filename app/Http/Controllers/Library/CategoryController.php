<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Hiển thị danh sách các thể loại sách (có tìm kiếm và phân trang).
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $categories = Category::withCount('books')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('categories.index', compact('categories', 'search'));
    }

    /**
     * Hiển thị form tạo mới thể loại.
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * Lưu thể loại mới vào cơ sở dữ liệu.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Vui lòng nhập tên thể loại.',
            'name.unique' => 'Tên thể loại này đã tồn tại trong hệ thống.',
            'name.max' => 'Tên thể loại không được vượt quá 255 ký tự.',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Thêm thể loại sách mới thành công!');
    }

    /**
     * Chuyển hướng hoặc xem chi tiết thể loại (Nếu là Admin/Thủ thư -> trang sửa, nếu là độc giả -> kho sách theo thể loại).
     */
    public function show(Category $category): RedirectResponse
    {
        if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isLibrarian())) {
            return redirect()->route('categories.edit', $category);
        }

        return redirect()->route('books.index', ['category_id' => $category->id]);
    }

    /**
     * Hiển thị form chỉnh sửa thông tin thể loại.
     */
    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Cập nhật thông tin thể loại trong cơ sở dữ liệu.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$category->id,
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Vui lòng nhập tên thể loại.',
            'name.unique' => 'Tên thể loại này đã tồn tại trong hệ thống.',
            'name.max' => 'Tên thể loại không được vượt quá 255 ký tự.',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Cập nhật thể loại sách thành công!');
    }

    /**
     * Xóa thể loại khỏi cơ sở dữ liệu.
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Kiểm tra an toàn: nếu thể loại đang có sách thì không cho xóa trực tiếp
        if ($category->books()->exists()) {
            $bookCount = $category->books()->count();

            return redirect()->route('categories.index')
                ->with('error', "Không thể xóa thể loại \"{$category->name}\" vì hiện có {$bookCount} cuốn sách đang thuộc thể loại này!");
        }

        $categoryName = $category->name;
        $categoryId = $category->id;
        $category->delete();

        // Ghi AuditLog xóa thể loại
        $userName = auth()->user() ? auth()->user()->name : 'Admin';
        AuditLogger::log(
            action: 'DELETE_CATEGORY',
            description: "Admin {$userName} đã xóa thể loại \"{$categoryName}\" khỏi hệ thống.",
            targetType: 'Category',
            targetId: $categoryId,
            oldValues: ['name' => $categoryName],
            newValues: null
        );

        return redirect()->route('categories.index')
            ->with('success', "Xóa thể loại \"{$categoryName}\" thành công!");
    }
}
