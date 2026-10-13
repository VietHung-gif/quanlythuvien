<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BorrowDetail;
use App\Models\Category;
use App\Services\AuditLogger;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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
                    $query->where('available_copies', '>', 10);
                } elseif ($stock === 'low_stock') {
                    $query->where('available_copies', '>', 0)->where('available_copies', '<=', 10);
                } elseif ($stock === 'out_of_stock') {
                    $query->where('available_copies', '<=', 0);
                }
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        // Lấy danh sách thể loại cho dropdown bộ lọc
        $categories = Category::withCount('books')->orderBy('name')->get();

        // Thống kê nhanh số lượng (theo 3 phân cấp tồn kho)
        $totalAll = Book::count();
        $totalInStock = Book::where('available_copies', '>', 10)->count();
        $totalLowStock = Book::where('available_copies', '>', 0)->where('available_copies', '<=', 10)->count();
        $totalOutOfStock = Book::where('available_copies', '<=', 0)->count();

        // Trả về view books/index.blade.php
        return view('books.index', compact('books', 'categories', 'search', 'categoryId', 'stock', 'totalAll', 'totalInStock', 'totalLowStock', 'totalOutOfStock'));
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
            'publisher' => 'nullable|string|max:255',
            'publish_year' => 'nullable|integer|min:1900|max:'.((int) date('Y') + 1),
            'description' => 'nullable|string|max:2000',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'category_id' => 'required|exists:categories,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Gán mặc định cho nhà xuất bản và năm nếu chưa nhập
        if (empty($validated['publisher'])) {
            $cat = Category::find($validated['category_id']);
            $validated['publisher'] = 'NXB '.($cat ? $cat->name : 'Tổng hợp');
        }
        if (empty($validated['publish_year'])) {
            $validated['publish_year'] = (int) date('Y');
        }

        // 2. Xử lý tải ảnh lên (Thử Cloudinary -> Fallback Local Storage -> Tự động tìm kiếm nếu không có file)
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $this->handleCoverUpload($request->file('cover_image'), $validated['title']);
        } else {
            // Nếu người dùng không tải ảnh lên, tự động tra cứu ảnh bìa từ Open Library
            $autoCover = $this->autoFetchCover($validated['title']);
            if ($autoCover) {
                $validated['cover_image'] = $autoCover;
            } else {
                unset($validated['cover_image']);
            }
        }

        // 3. Tự động khởi tạo số bản in khả dụng = tổng số lượng sách mới
        $validated['available_copies'] = (int) $validated['quantity'];

        // 4. Lưu vào database
        $book = Book::create($validated);

        // 5. Ghi nhận nhật ký kiểm toán (Audit Log)
        $userName = auth()->user() ? auth()->user()->name : 'Thủ thư';
        AuditLogger::log(
            action: 'CREATE_BOOK',
            description: "Người dùng {$userName} đã thêm cuốn sách mới \"{$book->title}\" (Số lượng: {$book->quantity}).",
            targetType: 'Book',
            targetId: $book->id,
            oldValues: null,
            newValues: $book->toArray()
        );

        // 6. Chuyển hướng về lại trang danh sách sách
        return redirect()->route('books.index')
            ->with('success', "Thêm cuốn sách \"{$book->title}\" thành công với số lượng {$book->quantity} cuốn!");
    }

    /**
     * Hiển thị trang chi tiết ấn phẩm sách (Độc giả & Quản trị viên).
     */
    public function show(string|int $id)
    {
        $book = Book::with('category')->findOrFail($id);

        // Ghi nhận tiến trình nhiệm vụ "Tìm Kiếm Tri Thức" (Xem chi tiết sách hàng ngày)
        if (Auth::check()) {
            $viewedKey = 'user_'.Auth::id().'_viewed_books_'.now()->toDateString();
            $viewed = session($viewedKey, []);
            if (! in_array((int) $book->id, $viewed, true)) {
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
            'publisher' => 'nullable|string|max:255',
            'publish_year' => 'nullable|integer|min:1900|max:'.((int) date('Y') + 1),
            'description' => 'nullable|string|max:2000',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'category_id' => 'required|exists:categories,id',
            'quantity' => 'required|integer|min:0',
        ]);

        // Xử lý upload ảnh mới (Cloudinary hoặc Local Storage) nếu có chọn file
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $this->handleCoverUpload($request->file('cover_image'), $validated['title']);
        } else {
            // Không upload ảnh mới thì giữ nguyên ảnh bìa hiện tại
            unset($validated['cover_image']);
        }

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
     * Cập nhật nhanh số lượng tồn kho (Dành riêng cho Admin & Thủ thư khi hết sách hoặc cần bổ sung)
     */
    public function updateStock(Request $request, string|int $id)
    {
        $book = Book::findOrFail($id);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $oldQuantity = (int) $book->quantity;
        $oldAvailable = (int) ($book->available_copies ?? $book->quantity);
        $newQuantity = (int) $validated['quantity'];

        // Tính toán lại số lượng khả dụng
        $diff = $newQuantity - $oldQuantity;
        $newAvailable = max(0, $oldAvailable + $diff);

        $book->update([
            'quantity' => $newQuantity,
            'available_copies' => $newAvailable,
        ]);

        $userName = auth()->user() ? auth()->user()->name : 'Thủ thư';
        AuditLogger::log(
            action: 'RESTOCK_BOOK',
            description: "{$userName} đã bổ sung kho cho sách \"{$book->title}\" (Tồn kho: {$oldQuantity} -> {$newQuantity}).",
            targetType: 'Book',
            targetId: $book->id,
            oldValues: ['quantity' => $oldQuantity, 'available_copies' => $oldAvailable],
            newValues: ['quantity' => $newQuantity, 'available_copies' => $newAvailable]
        );

        return redirect()->back()
            ->with('success', "Đã bổ sung số lượng cho cuốn sách \"{$book->title}\" thành {$newQuantity} cuốn để độc giả có thể mượn!");
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
        $hasActiveDetail = BorrowDetail::where('book_id', $book->id)->whereIn('status', ['pending', 'borrowing', 'approved'])->exists();
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

    /**
     * Xử lý lưu ảnh bìa (Cloudinary hoặc Local Storage)
     */
    protected function handleCoverUpload($file, string $title): ?string
    {
        // 1. Thử upload lên Cloudinary nếu có cấu hình
        if (env('CLOUDINARY_URL')) {
            try {
                Configuration::instance(env('CLOUDINARY_URL'));
                $uploadResult = (new UploadApi)->upload($file->getRealPath());
                if (! empty($uploadResult['secure_url'])) {
                    return $uploadResult['secure_url'];
                }
            } catch (\Throwable $e) {
                // Cloudinary lỗi -> fallback xuống local storage bên dưới
            }
        }

        // 2. Lưu vào local storage (storage/app/public/books)
        try {
            $filename = Str::slug($title).'-'.time().'.'.$file->getClientOriginalExtension();

            return $file->storeAs('books', $filename, 'public');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Tự động tìm kiếm ảnh bìa cho sách mới qua Open Library
     */
    protected function autoFetchCover(string $title): ?string
    {
        try {
            $clean = trim((string) preg_replace('/\s*[\(\[\{].*?[\)\]\}]\s*/', ' ', $title));
            $clean = trim(preg_replace('/^manga\s+/i', '', $clean));
            $short = trim(explode(':', $clean)[0]);
            $short = trim(explode(' - ', $short)[0]);

            $res = Http::withoutVerifying()->timeout(5)->get('https://openlibrary.org/search.json', [
                'title' => $short,
                'limit' => 1,
            ]);

            $coverId = $res->json('docs.0.cover_i');
            if ($coverId) {
                return "https://covers.openlibrary.org/b/id/{$coverId}-L.jpg";
            }
        } catch (\Throwable $e) {
        }

        return null;
    }
}
