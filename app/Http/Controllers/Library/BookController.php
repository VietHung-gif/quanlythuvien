<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Book;
use App\Models\Category;
use App\Services\AuditLogger;
class BookController extends Controller
{
    /**
     * Hiển thị danh sách sách (tìm kiếm theo tên/tác giả, lọc theo thể loại và phân trang 12 cuốn/trang).
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $stock = $request->input('stock', 'all');

        // Lấy danh sách sách kèm thể loại, hỗ trợ tìm kiếm, lọc thể loại và tình trạng tồn kho
        $books = Book::with('category')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('author', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($stock !== 'all', function ($query) use ($stock) {
                if ($stock === 'in_stock') {
                    $query->where('available_copies', '>', 0);
                } elseif ($stock === 'out_of_stock') {
                    $query->where('available_copies', '<=', 0);
                }
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        // Lấy danh sách thể loại cho dropdown bộ lọc
        $categories = Category::withCount('books')->orderBy('name')->get();

        // Thống kê nhanh số lượng
        $totalAll = Book::count();
        $totalInStock = Book::where('available_copies', '>', 0)->count();
        $totalOutOfStock = Book::where('available_copies', '<=', 0)->count();

        // Trả về view books/index.blade.php
        return view('books.index', compact('books', 'categories', 'search', 'categoryId', 'stock', 'totalAll', 'totalInStock', 'totalOutOfStock'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Lấy danh sách thể loại để đưa vào ô chọn (dropdown select)
        $categories = Category::all();
        return view('books.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Kiểm tra dữ liệu người dùng nhập vào (Validation)
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'cover_image' => 'nullable|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // 2. Tự động khởi tạo số bản in khả dụng = tổng số lượng sách mới
        $validated['available_copies'] = (int) $validated['quantity'];

        // 3. Lưu vào database
        Book::create($validated);

        // 4. Chuyển hướng về lại trang danh sách sách
        return redirect()->route('books.index')
            ->with('success', 'Thêm cuốn sách mới thành công!');
    }

    /**
     * Hiển thị trang chi tiết ấn phẩm sách (Độc giả & Quản trị viên).
     */
    public function show(string|int $id)
    {
        $book = Book::with('category')->findOrFail($id);

        // Ghi nhận tiến trình nhiệm vụ "Tìm Kiếm Tri Thức" (Xem chi tiết sách hàng ngày)
        if (Auth::check()) {
            $viewedKey = 'user_' . Auth::id() . '_viewed_books_' . now()->toDateString();
            $viewed = session($viewedKey, []);
            if (!in_array((int) $book->id, $viewed, true)) {
                $viewed[] = (int) $book->id;
                session([$viewedKey => $viewed]);
            }
        }

        // Lấy tối đa 4 cuốn sách cùng chuyên ngành / thể loại (loại trừ sách hiện tại)
        $relatedBooks = Book::with('category')
            ->where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->latest()
            ->take(4)
            ->get();

        // Nếu thể loại này chưa đủ 4 cuốn thì bổ sung các đầu sách mới nhất khác trong kho
        if ($relatedBooks->count() < 4) {
            $extraBooks = Book::with('category')
                ->where('id', '!=', $book->id)
                ->whereNotIn('id', $relatedBooks->pluck('id'))
                ->latest()
                ->take(4 - $relatedBooks->count())
                ->get();
            $relatedBooks = $relatedBooks->concat($extraBooks);
        }

        return view('books.show', compact('book', 'relatedBooks'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $book = Book::findOrFail($id);
        $categories = Category::orderBy('name')->get();

        return view('books.edit', compact('book', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $book = Book::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'cover_image' => 'nullable|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $oldValues = [
            'quantity' => (int) $book->quantity,
            'available_copies' => (int) ($book->available_copies ?? $book->quantity),
            'title' => $book->title,
        ];

        // Đồng bộ số lượng khả dụng nếu tồn kho thay đổi
        $diff = (int) $validated['quantity'] - (int) $book->quantity;
        if (isset($book->available_copies)) {
            $validated['available_copies'] = max(0, (int) $book->available_copies + $diff);
        }

        $book->update($validated);

        $newValues = [
            'quantity' => (int) $book->quantity,
            'available_copies' => (int) ($book->available_copies ?? $book->quantity),
            'title' => $book->title,
        ];

        // Ghi AuditLog can thiệp kho sách
        $userName = auth()->user() ? auth()->user()->name : 'Thủ thư';
        AuditLogger::log(
            action: 'UPDATE_STOCK',
            description: "Người dùng {$userName} đã cập nhật thông tin sách \"{$book->title}\" (Tồn kho: {$oldValues['quantity']} -> {$newValues['quantity']}).",
            targetType: 'Book',
            targetId: $book->id,
            oldValues: $oldValues,
            newValues: $newValues
        );

        return redirect()->route('books.index')
            ->with('success', "Cập nhật cuốn sách \"{$book->title}\" thành công!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $book = Book::findOrFail($id);
        $title = $book->title;
        $oldValues = [
            'title' => $book->title,
            'quantity' => $book->quantity,
            'author' => $book->author,
        ];

        // Kiểm tra an toàn: Không cho phép xóa sách nếu đang có người mượn hoặc chờ duyệt
        $hasActiveRecord = $book->borrowRecords()->whereIn('status', ['borrowing', 'pending'])->exists();
        $hasActiveDetail = \App\Models\BorrowDetail::where('book_id', $book->id)->whereIn('status', ['pending', 'borrowing', 'approved'])->exists();
        if ($hasActiveRecord || $hasActiveDetail) {
            return redirect()->route('books.index')
                ->with('error', "Không thể xóa cuốn sách \"{$title}\" vì hiện đang có độc giả mượn hoặc đang có yêu cầu chờ duyệt!");
        }

        $book->delete();

        // Ghi AuditLog xóa sách
        $userName = auth()->user() ? auth()->user()->name : 'Admin';
        AuditLogger::log(
            action: 'DELETE_BOOK',
            description: "Admin {$userName} đã xóa vĩnh viễn cuốn sách \"{$title}\" khỏi hệ thống.",
            targetType: 'Book',
            targetId: (int) $id,
            oldValues: $oldValues,
            newValues: null
        );

        // Chuyển hướng lại trang danh sách sách
        return redirect()->route('books.index')
            ->with('success', "Đã xóa cuốn sách \"{$title}\" thành công!");
    }
}
