<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\BorrowDetail;
use App\Models\BorrowRecord;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Lấy danh sách các cuốn sách trong giỏ từ Session và đồng bộ tồn kho thực tế.
     */
    public function index(): JsonResponse
    {
        $cart = session()->get('cart', []);

        if (!empty($cart)) {
            $bookIds = array_keys($cart);
            $books = Book::whereIn('id', $bookIds)->get()->keyBy('id');

            foreach ($cart as $id => &$item) {
                if (isset($books[$id])) {
                    $item['max_quantity'] = (int) $books[$id]->available_copies;
                    $item['title'] = $books[$id]->title;
                    $item['author'] = $books[$id]->author;
                    $item['publisher'] = $books[$id]->publisher ?? 'NXB Trẻ';
                    $item['publish_year'] = $books[$id]->publish_year ?? 2024;
                    $item['cover_url'] = $books[$id]->cover_url;

                    if ($books[$id]->available_copies <= 0) {
                        unset($cart[$id]);
                    } elseif ($item['quantity'] > $books[$id]->available_copies) {
                        $item['quantity'] = max(1, (int) $books[$id]->available_copies);
                    }
                } else {
                    unset($cart[$id]);
                }
            }
            unset($item);
            session()->put('cart', $cart);
        }

        $totalSelected = 0;
        $totalSelectedBooks = 0;
        foreach ($cart as $item) {
            if (!empty($item['selected'])) {
                $totalSelected++;
                $totalSelectedBooks += $item['quantity'];
            }
        }

        $user = Auth::user();
        $isVip = $user ? $user->isVip() : false;
        $maxLimit = $user ? $user->maxBorrowLimit() : 3;
        $borrowDays = $user ? $user->borrowDurationDays() : 14;
        $depositFee = $isVip ? 0 : ($totalSelectedBooks * 50000);
        $unpaidFine = $user ? $user->unpaid_fine : 0;

        return response()->json([
            'success' => true,
            'cart' => array_values($cart),
            'total_items' => count($cart),
            'total_selected' => $totalSelected,
            'total_selected_books' => $totalSelectedBooks,
            'unpaid_fine' => $unpaidFine,
            'has_unpaid_fine' => $unpaidFine > 0,
            'is_vip' => $isVip,
            'membership_type' => $isVip ? 'vip' : 'standard',
            'max_limit' => $maxLimit,
            'borrow_days' => $borrowDays,
            'deposit_fee' => $depositFee,
            'formatted_deposit_fee' => $isVip ? '0đ (Đặc quyền VIP miễn cọc)' : number_format($depositFee, 0, ',', '.') . ' đ',
            'delivery_address' => $user ? ($user->delivery_address ?? '') : '',
            'phone_number' => $user ? ($user->phone_number ?? $user->phone ?? '') : '',
        ]);
    }

    /**
     * Thêm sách vào giỏ & kiểm tra tồn kho tức thì (BƯỚC 1).
     */
    public function add(Request $request): JsonResponse
    {
        // VẤN ĐỀ 2: Chặn thêm vào giỏ nếu đang nợ tiền phạt chưa thanh toán
        if (Auth::check()) {
            $unpaidFine = Auth::user()->unpaid_fine;
            if ($unpaidFine > 0) {
                return response()->json([
                    'success' => false,
                    'has_unpaid_fine' => true,
                    'unpaid_fine' => $unpaidFine,
                    'message' => "Tài khoản của bạn đang có tiền phạt chưa thanh toán (" . number_format($unpaidFine, 0, ',', '.') . " VNĐ). Vui lòng gặp thủ thư thanh toán đủ tiền phạt để tiếp tục mượn sách!",
                ], 422);
            }
        }

        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $book = Book::findOrFail($validated['book_id']);

        // 1. Trường hợp sách ĐÃ HẾT TỒN KHO KHẢ DỤNG (available_copies <= 0)
        if ($book->available_copies <= 0) {
            return response()->json([
                'success' => false,
                'out_of_stock' => true,
                'title' => 'Thông báo',
                'message' => 'Hiện tại cuốn sách này đã được mượn hết. Bạn vui lòng chọn một cuốn sách khác nhé!',
            ]);
        }

        // 2. Trường hợp sách CÒN HÀNG (available_copies > 0)
        $cart = session()->get('cart', []);

        if (isset($cart[$book->id])) {
            if ($cart[$book->id]['quantity'] < $book->available_copies) {
                $cart[$book->id]['quantity']++;
                $cart[$book->id]['selected'] = true;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Bạn đã thêm số lượng tối đa hiện có trong kho ({$book->available_copies} cuốn)! ",
                    'cart' => array_values($cart),
                    'total_items' => count($cart),
                ]);
            }
        } else {
            $cart[$book->id] = [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'publisher' => $book->publisher ?? 'NXB Trẻ',
                'publish_year' => $book->publish_year ?? 2024,
                'cover_url' => $book->cover_url,
                'quantity' => 1,
                'max_quantity' => (int) $book->available_copies,
                'selected' => true,
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Thành công! Sách đã được thêm vào giỏ',
            'cart' => array_values($cart),
            'total_items' => count($cart),
            'total_quantity' => array_sum(array_column($cart, 'quantity')),
        ]);
    }

    /**
     * Cập nhật số lượng của cuốn sách trong giỏ (- hoặc +).
     */
    public function updateQuantity(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $bookId = $validated['book_id'];
        $newQty = $validated['quantity'];

        $book = Book::findOrFail($bookId);
        $cart = session()->get('cart', []);

        if (isset($cart[$bookId])) {
            if ($newQty > $book->available_copies) {
                return response()->json([
                    'success' => false,
                    'message' => "Kho chỉ còn {$book->available_copies} cuốn khả dụng!",
                ]);
            }

            $cart[$bookId]['quantity'] = $newQty;
            $cart[$bookId]['max_quantity'] = (int) $book->available_copies;
            session()->put('cart', $cart);
        }

        return $this->index();
    }

    /**
     * Tích chọn / Bỏ chọn từng cuốn hoặc chọn tất cả trong giỏ.
     */
    public function toggleSelect(Request $request): JsonResponse
    {
        $cart = session()->get('cart', []);
        $bookId = $request->input('book_id');
        $selected = (bool) $request->input('selected', true);

        if ($bookId === 'all') {
            foreach ($cart as &$item) {
                $item['selected'] = $selected;
            }
            unset($item);
        } elseif (isset($cart[$bookId])) {
            $cart[$bookId]['selected'] = $selected;
        }

        session()->put('cart', $cart);

        return $this->index();
    }

    /**
     * Xóa một cuốn sách khỏi giỏ hàng.
     */
    public function remove(int $bookId): JsonResponse
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$bookId])) {
            unset($cart[$bookId]);
            session()->put('cart', $cart);
        }

        return $this->index();
    }

    /**
     * Xóa sạch toàn bộ giỏ hàng.
     */
    public function clear(): JsonResponse
    {
        session()->forget('cart');
        return $this->index();
    }

    /**
     * BƯỚC 2: Đăng ký mượn sách & Tạo phiếu mượn mới.
     */
    public function checkout(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'login_required' => true,
                'message' => 'Vui lòng đăng nhập để tiến hành đăng ký mượn sách!',
                'login_url' => route('login'),
            ], 401);
        }

        $cart = session()->get('cart', []);
        $selectedItems = array_filter($cart, fn($item) => !empty($item['selected']));

        if (empty($selectedItems)) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng tích chọn ít nhất 1 cuốn sách trong giỏ để đăng ký mượn!',
            ]);
        }

        $user = Auth::user();

        // 1. Kiểm tra nợ tiền phạt
        $unpaidFine = $user->unpaid_fine;
        if ($unpaidFine > 0) {
            return response()->json([
                'success' => false,
                'has_unpaid_fine' => true,
                'unpaid_fine' => $unpaidFine,
                'message' => "Tài khoản của bạn đang có tiền phạt chưa thanh toán (" . number_format($unpaidFine, 0, ',', '.') . " VNĐ). Vui lòng gặp thủ thư thanh toán đủ tiền phạt để tiếp tục mượn sách!",
            ], 422);
        }

        // 2. Tính tổng số sách đang chọn mượn
        $totalBooks = 0;
        foreach ($selectedItems as $item) {
            $totalBooks += (int) $item['quantity'];
        }

        // 3. KIỂM TRA HẠN MỨC GÓI HỘI VIÊN: Standard (3 cuốn) vs Scholar VIP (15 cuốn)
        $isVip = $user->isVip();
        $maxLimit = $user->maxBorrowLimit();
        if ($totalBooks > $maxLimit) {
            return response()->json([
                'success' => false,
                'exceed_limit' => true,
                'upgrade_suggested' => true,
                'current_limit' => $maxLimit,
                'total_requested' => $totalBooks,
                'upgrade_url' => route('membership.index'),
                'message' => "Bạn đang chọn {$totalBooks} cuốn. Gói Standard Member chỉ cho phép mượn tối đa {$maxLimit} cuốn/lần. Nâng cấp Scholar VIP để mượn tối đa 15 cuốn cùng lúc!",
            ], 422);
        }

        // 4. XÁC ĐỊNH HÌNH THỨC NHẬN SÁCH & PHÍ CỌC
        $deliveryMethod = 'counter_normal';
        $shippingAddress = null;

        if ($isVip) {
            $deliveryMethod = $request->input('delivery_method', 'counter_priority');
            if (!in_array($deliveryMethod, ['counter_priority', 'home_delivery', 'counter_normal'])) {
                $deliveryMethod = 'counter_priority';
            }
            if ($deliveryMethod === 'home_delivery') {
                $shippingAddress = trim($request->input('shipping_address') ?: ($user->delivery_address ?? ''));
                if (empty($shippingAddress)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Vui lòng nhập địa chỉ nhận sách tận nhà / KTX hoặc chọn làn ưu tiên tại Quầy!',
                    ], 422);
                }
                // Tự động lưu địa chỉ vào hồ sơ người dùng
                $user->update(['delivery_address' => $shippingAddress]);
            }
            $depositFee = 0.00; // 0đ ĐẶC QUYỀN VIP MIỄN CỌC SÁCH
        } else {
            $deliveryMethod = 'counter_normal';
            $depositFee = (float) ($totalBooks * 50000); // 50.000đ/cuốn đối với độc giả thường
        }

        // Cập nhật SĐT nếu người dùng cung cấp
        $phone = $request->input('phone', $user->phone_number ?: ($user->phone ?? null));
        if ($phone && empty($user->phone_number)) {
            $user->update(['phone_number' => $phone]);
        }

        $borrowDays = $user->borrowDurationDays(); // 30 ngày cho VIP, 14 ngày cho Standard
        $dueDate = Carbon::now()->addDays($borrowDays)->toDateString();

        return DB::transaction(function () use ($selectedItems, $request, $user, $totalBooks, $isVip, $deliveryMethod, $shippingAddress, $depositFee, $phone, $borrowDays, $dueDate) {
            // 5. Kiểm tra tồn kho từng cuốn với khóa dòng (Lock for update)
            foreach ($selectedItems as $item) {
                $book = Book::lockForUpdate()->find($item['id']);
                if (!$book || $book->available_copies < $item['quantity']) {
                    $title = $book ? $book->title : 'Một cuốn sách';
                    return response()->json([
                        'success' => false,
                        'message' => "Rất tiếc! Cuốn sách \"{$title}\" không còn đủ số lượng khả dụng trong kho (cần {$item['quantity']}, chỉ còn " . ($book ? $book->available_copies : 0) . ").",
                    ], 422);
                }
            }

            // Ghi chú hình thức nhận sách
            $deliveryTag = match ($deliveryMethod) {
                'home_delivery' => "[🚚 SHIP TẬN NƠI: {$shippingAddress}]",
                'counter_priority' => "[⚡ LÀN ƯU TIÊN LẤY NGAY TẠI QUẦY]",
                default => "[🏢 QUẦY THÔNG THƯỜNG]",
            };
            $customNote = $request->input('notes') ?: 'Đăng ký mượn trực tuyến từ Giỏ Sách';
            $fullNote = $deliveryTag . ' ' . $customNote;

            // 6. Tạo 1 Phiếu mượn mới trong bảng borrows
            $code = Borrow::generateCode();
            $borrow = Borrow::create([
                'code' => $code,
                'user_id' => $user->id,
                'borrower_name' => $user->name,
                'borrower_phone' => $phone,
                'borrower_email' => $user->email,
                'borrow_date' => Carbon::now()->toDateString(),
                'due_date' => $dueDate,
                'status' => 'pending',
                'borrow_status' => 'Chưa mượn',
                'delivery_method' => $deliveryMethod,
                'shipping_address' => $shippingAddress,
                'deposit_fee' => $depositFee,
                'deposit_amount' => $depositFee,
                'total_quantity' => $totalBooks,
                'notes' => $fullNote,
            ]);

            // 7. Lưu chi tiết các cuốn sách vào bảng borrow_details & đồng bộ sang borrow_records
            foreach ($selectedItems as $item) {
                $book = Book::lockForUpdate()->find($item['id']);

                BorrowDetail::create([
                    'borrow_id' => $borrow->id,
                    'book_id' => $book->id,
                    'quantity' => $item['quantity'],
                    'status' => 'pending',
                ]);

                // Đồng bộ sang bảng borrow_records cũ để tương thích với các view quản lý
                BorrowRecord::create([
                    'book_id' => $book->id,
                    'user_id' => $user->id,
                    'borrower_name' => $user->name,
                    'borrower_email' => $user->email,
                    'borrower_phone' => $phone,
                    'quantity' => $item['quantity'],
                    'borrow_date' => Carbon::now()->toDateString(),
                    'due_date' => $dueDate,
                    'status' => 'pending',
                    'delivery_method' => $deliveryMethod,
                    'shipping_address' => $shippingAddress,
                    'deposit_fee' => $depositFee,
                    'deposit_amount' => $depositFee,
                    'notes' => "[Phiếu {$code}] {$deliveryTag} (SL: {$item['quantity']} cuốn)",
                    'fine_amount' => 0,
                    'fine_status' => 'none',
                ]);
            }

            // 8. Tự động cộng +50 EXP công lực (Nếu VIP thì tự động x2 thành +100 EXP!)
            $user->awardExp(50);

            // 9. Làm rỗng các sách đã mượn khỏi giỏ hàng
            $cart = session()->get('cart', []);
            foreach ($selectedItems as $item) {
                unset($cart[$item['id']]);
            }
            session()->put('cart', $cart);

            $successMsg = $isVip 
                ? "Đăng ký mượn thành công {$totalBooks} cuốn! Đặc quyền Scholar VIP: Miễn cọc 0đ, thời hạn {$borrowDays} ngày và {$deliveryTag}."
                : "Đăng ký mượn thành công {$totalBooks} cuốn! Tiền cọc tạm tính: " . number_format($depositFee, 0, ',', '.') . " đ.";

            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'code' => $borrow->code,
                'delivery_method' => $deliveryMethod,
                'deposit_fee' => $depositFee,
                'due_date' => Carbon::parse($dueDate)->format('d/m/Y'),
                'tracking_url' => route('borrows.tracking'),
                'cart' => array_values($cart),
                'total_items' => count($cart),
            ]);
        });
    }
}
