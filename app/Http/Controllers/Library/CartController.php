<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Hiển thị trang danh sách giỏ sách mượn.
     */
    public function index(Request $request): View
    {
        $cart = session()->get('cart', []);

        // Đồng bộ dữ liệu mới nhất từ CSDL
        if (! empty($cart)) {
            $bookIds = array_keys($cart);
            $books = Book::with('category')->whereIn('id', $bookIds)->get()->keyBy('id');

            foreach ($cart as $id => &$item) {
                if (isset($books[$id])) {
                    $item['title'] = $books[$id]->title;
                    $item['author'] = $books[$id]->author;
                    $item['category'] = $books[$id]->category ? $books[$id]->category->name : 'Tổng hợp';
                    $item['cover_url'] = $books[$id]->cover_url;
                    $item['available_copies'] = (int) $books[$id]->available_copies;
                    $item['quantity'] = (int) ($item['quantity'] ?? 1);
                    if ($item['quantity'] < 1) {
                        $item['quantity'] = 1;
                    }
                } else {
                    unset($cart[$id]);
                }
            }
            unset($item);
            session()->put('cart', $cart);
        }

        $user = Auth::user();
        $isVip = $user ? $user->isVip() : false;
        $maxLimit = $user ? $user->maxBorrowLimit() : 3;
        $borrowDays = $user ? $user->borrowDurationDays() : 14;
        $totalBooks = array_sum(array_column($cart, 'quantity')) ?: count($cart);
        $depositFee = $isVip ? 0 : ($totalBooks * 50000);

        return view('cart.index', compact('cart', 'user', 'isVip', 'maxLimit', 'borrowDays', 'totalBooks', 'depositFee'));
    }

    /**
     * Thêm 1 cuốn sách vào Session Giỏ hàng (Hỗ trợ cả AJAX Fetch & Web Redirect).
     * Nếu sách chưa có -> Thêm số lượng = 1.
     * Nếu sách đã có -> Tự động cộng dồn số lượng +1.
     * Cả hai trường hợp đều trả về DUY NHẤT 1 thông báo thành công: "Thêm sách thành công! Đã thêm vào giỏ mượn".
     */
    public function add(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
        ], [
            'book_id.required' => 'Vui lòng chọn cuốn sách cần thêm.',
            'book_id.exists' => 'Cuốn sách này không tồn tại trong hệ thống.',
        ]);

        $book = Book::findOrFail($validated['book_id']);
        $user = Auth::user();
        $isJson = $request->expectsJson() || $request->ajax() || $request->wantsJson();

        // 1. Kiểm tra nợ tiền phạt
        if ($user && $user->unpaid_fine > 0) {
            $msg = 'Tài khoản của bạn đang có tiền phạt chưa thanh toán ('.number_format($user->unpaid_fine, 0, ',', '.').' VNĐ). Vui lòng thanh toán tiền phạt để tiếp tục mượn sách!';
            if ($isJson) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ], 422);
            }

            return redirect()->back()->with('error', $msg);
        }

        // 2. Kiểm tra tồn kho khả dụng
        if ($book->available_copies <= 0) {
            $msg = "Rất tiếc! Cuốn sách \"{$book->title}\" hiện đã được mượn hết.";
            if ($isJson) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ], 422);
            }

            return redirect()->back()->with('error', $msg);
        }

        $cart = session()->get('cart', []);

        // 3. Xử lý thêm vào giỏ hoặc cộng dồn số lượng
        if (isset($cart[$book->id])) {
            // Sách đã có sẵn trong giỏ -> Tự động cộng dồn số lượng lên +1
            $currentQty = (int) ($cart[$book->id]['quantity'] ?? 1);
            $availableCopies = (int) $book->available_copies;
            if ($currentQty < $availableCopies) {
                $cart[$book->id]['quantity'] = $currentQty + 1;
            }
        } else {
            // Sách chưa có trong giỏ -> Thêm vào với số lượng = 1
            $cart[$book->id] = [
                'id' => $book->id,
                'book_id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'category' => $book->category ? $book->category->name : 'Tổng hợp',
                'cover_url' => $book->cover_url,
                'available_copies' => (int) $book->available_copies,
                'quantity' => 1,
            ];
        }

        session()->put('cart', $cart);

        $cartCount = array_sum(array_column($cart, 'quantity')) ?: count($cart);
        $successMsg = 'Thêm sách thành công! Đã thêm vào giỏ mượn';

        if ($isJson) {
            return response()->json([
                'success' => true,
                'cart_count' => $cartCount,
                'message' => $successMsg,
                'book' => [
                    'id' => $book->id,
                    'title' => $book->title,
                    'quantity' => $cart[$book->id]['quantity'],
                ],
            ]);
        }

        return redirect()->back()->with('success', $successMsg);
    }

    /**
     * Cập nhật số lượng (+ / -) sách trong Session Giỏ hàng.
     */
    public function updateQuantity(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'action' => 'required|in:increment,decrement',
        ]);

        $bookId = $validated['book_id'];
        $action = $validated['action'];
        $cart = session()->get('cart', []);

        if (! isset($cart[$bookId])) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy cuốn sách này trong giỏ mượn.',
                ], 404);
            }

            return redirect()->route('cart.index')->with('error', 'Không tìm thấy cuốn sách này trong giỏ mượn.');
        }

        $book = Book::find($bookId);
        $availableCopies = $book ? (int) $book->available_copies : 99;
        $currentQty = (int) ($cart[$bookId]['quantity'] ?? 1);

        if ($action === 'increment') {
            if ($currentQty < $availableCopies) {
                $cart[$bookId]['quantity'] = $currentQty + 1;
            } else {
                $msg = "Số lượng cuốn \"{$cart[$bookId]['title']}\" đã đạt mức tối đa hiện có trong kho ({$availableCopies} cuốn).";
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $msg,
                    ], 422);
                }

                return redirect()->back()->with('error', $msg);
            }
        } elseif ($action === 'decrement') {
            if ($currentQty > 1) {
                $cart[$bookId]['quantity'] = $currentQty - 1;
            }
        }

        session()->put('cart', $cart);
        $totalBooks = array_sum(array_column($cart, 'quantity')) ?: count($cart);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'book_id' => $bookId,
                'quantity' => $cart[$bookId]['quantity'],
                'cart_count' => $totalBooks,
                'message' => 'Cập nhật số lượng sách thành công.',
            ]);
        }

        return redirect()->back()->with('success', 'Cập nhật số lượng sách thành công.');
    }

    /**
     * Xóa 1 cuốn sách khỏi Session Giỏ hàng.
     */
    public function remove(Request $request): RedirectResponse
    {
        $bookId = $request->input('book_id');
        $cart = session()->get('cart', []);

        if ($bookId && isset($cart[$bookId])) {
            $title = $cart[$bookId]['title'] ?? 'Cuốn sách';
            unset($cart[$bookId]);
            session()->put('cart', $cart);

            return redirect()->back()->with('success', "Đã xóa \"{$title}\" khỏi giỏ mượn.");
        }

        return redirect()->back()->with('error', 'Không tìm thấy cuốn sách này trong giỏ mượn.');
    }

    /**
     * Dọn dẹp sạch toàn bộ sách trong giỏ.
     */
    public function clear(): RedirectResponse
    {
        session()->forget('cart');

        return redirect()->route('cart.index')->with('success', 'Đã làm trống giỏ mượn sách thành công.');
    }
}
