<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\BorrowDetail;
use App\Models\BorrowRecord;
use App\Models\LibraryFund;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BorrowController extends Controller
{
    /**
     * Mức phí phạt quá hạn mặc định (VNĐ / ngày).
     */
    const FINE_PER_DAY = 5000;

    /**
     * Hiển thị danh sách phiếu mượn sách kèm bộ lọc và thống kê.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status', 'all');

        $borrows = BorrowRecord::with(['book', 'user'])
            ->when($search, function ($query, $search) {
                $cleanSearch = trim($search);
                $query->where(function ($q) use ($cleanSearch) {
                    // Hỗ trợ tra cứu theo mã phiếu mượn (VD: #PKM0012, PKM12 hoặc số 12)
                    if (preg_match('/^(?:#?PKM)?\s*(\d+)$/i', $cleanSearch, $matches)) {
                        $q->where('id', (int) $matches[1]);
                    }

                    $q->orWhere('borrower_name', 'like', "%{$cleanSearch}%")
                      ->orWhere('borrower_phone', 'like', "%{$cleanSearch}%")
                      ->orWhere('borrower_email', 'like', "%{$cleanSearch}%")
                      ->orWhereHas('book', function ($b) use ($cleanSearch) {
                          $b->where('title', 'like', "%{$cleanSearch}%")
                            ->orWhere('author', 'like', "%{$cleanSearch}%")
                            ->orWhere('isbn', 'like', "%{$cleanSearch}%");
                      });
                });
            })
            ->when($status !== 'all', function ($query) use ($status) {
                if ($status === 'overdue') {
                    $query->where('status', 'borrowing')
                          ->where('due_date', '<', Carbon::now()->toDateString());
                } elseif ($status === 'renewal_pending') {
                    $query->where('status', 'borrowing')
                          ->where('renewal_status', 'pending');
                } else {
                    $query->where('status', $status);
                }
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Thống kê nhanh hiển thị ở đầu trang
        $totalPending = BorrowRecord::where('status', 'pending')->count();
        $totalRenewalPending = BorrowRecord::where('status', 'borrowing')->where('renewal_status', 'pending')->count();
        $totalActive = BorrowRecord::where('status', 'borrowing')->count();
        $totalOverdue = BorrowRecord::where('status', 'borrowing')
            ->where('due_date', '<', Carbon::now()->toDateString())
            ->count();
        $totalReturned = BorrowRecord::where('status', 'returned')->count();
        $totalFines = BorrowRecord::sum('fine_amount');

        return view('borrows.index', compact(
            'borrows',
            'search',
            'status',
            'totalPending',
            'totalRenewalPending',
            'totalActive',
            'totalOverdue',
            'totalReturned',
            'totalFines'
        ));
    }

    /**
     * Dành cho độc giả: Xem danh sách sách tài khoản hiện tại đang mượn và lịch sử trả.
     */
    public function myBorrows(Request $request): View
    {
        $userId = $request->user()->id;

        $borrows = BorrowRecord::with('book')
            ->where('user_id', $userId)
            ->latest('borrow_date')
            ->paginate(10);

        return view('borrows.my-borrows', compact('borrows'));
    }

    /**
     * Hiển thị form lập phiếu mượn sách mới.
     */
    public function create(): View
    {
        // Chỉ lấy những sách còn số lượng khả dụng > 0
        $books = Book::where('available_copies', '>', 0)
            ->with('category')
            ->orderBy('title')
            ->get();

        $users = User::orderBy('name')->get();

        $defaultBorrowDate = Carbon::now()->format('Y-m-d');
        $defaultDueDate = Carbon::now()->addDays(14)->format('Y-m-d');

        return view('borrows.create', compact('books', 'users', 'defaultBorrowDate', 'defaultDueDate'));
    }

    /**
     * Xử lý lưu phiếu mượn sách mới & tự động trừ số lượng tồn kho.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'user_id' => 'nullable|exists:users,id',
            'borrower_name' => 'required|string|max:255',
            'borrower_phone' => 'nullable|string|max:20',
            'borrower_email' => 'nullable|email|max:255',
            'quantity' => 'nullable|integer|min:1',
            'borrow_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:borrow_date',
            'notes' => 'nullable|string|max:1000',
        ], [
            'book_id.required' => 'Vui lòng chọn cuốn sách cần mượn.',
            'borrower_name.required' => 'Vui lòng nhập tên người mượn.',
            'borrow_date.required' => 'Vui lòng chọn ngày mượn.',
            'due_date.required' => 'Vui lòng chọn hạn trả sách.',
            'due_date.after_or_equal' => 'Hạn trả sách phải sau hoặc bằng ngày mượn.',
        ]);

        // VẤN ĐỀ 2: Chặn tạo phiếu nếu độc giả còn nợ tiền phạt chưa thanh toán
        $targetUserId = $validated['user_id'] ?? ($request->user() ? $request->user()->id : Auth::id());
        if ($targetUserId) {
            $targetUser = User::find($targetUserId);
            if ($targetUser && $targetUser->unpaid_fine > 0) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Tài khoản của bạn đang có tiền phạt chưa thanh toán (" . number_format($targetUser->unpaid_fine, 0, ',', '.') . " VNĐ). Vui lòng gặp thủ thư thanh toán đủ tiền phạt để tiếp tục mượn sách!");
            }
        }

        return DB::transaction(function () use ($validated, $request, $targetUserId) {
            // Khóa dòng để tránh tranh chấp số lượng tồn kho (Race Condition)
            $book = Book::lockForUpdate()->findOrFail($validated['book_id']);
            $borrowQuantity = (int) ($validated['quantity'] ?? 1);

            // VẤN ĐỀ 1: Kiểm tra tồn kho của cuốn sách
            if ($book->available_copies < $borrowQuantity) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Không đủ sách trong kho để duyệt yêu cầu này (cần {$borrowQuantity}, chỉ còn {$book->available_copies})!");
            }

            // Trừ số lượng khả dụng trong kho (giữ nguyên tổng số lượng sở hữu quantity)
            $book->decrement('available_copies', $borrowQuantity);

            // Trích tiền cọc sách từ ví độc giả (VIP được miễn cọc, Độc giả thường cọc 50.000đ/cuốn)
            $depositAmount = 0;
            if ($targetUserId) {
                $borrowUser = User::find($targetUserId);
                if ($borrowUser) {
                    $isVip = $borrowUser->isVip();
                    $depositAmount = $isVip ? 0 : (50000 * $borrowQuantity);
                    if ($depositAmount > 0) {
                        $wallet = $borrowUser->getOrCreateWallet();
                        if ($wallet->balance >= $depositAmount) {
                            $wallet->withdraw(
                                amount: $depositAmount,
                                type: 'book_deposit_hold',
                                description: "Tạm giữ tiền cọc mượn {$borrowQuantity} cuốn \"{$book->title}\"",
                                referenceId: 'HOLD_' . time()
                            );
                        }
                    }
                }
            }

            // Tạo bản ghi mượn sách
            $borrow = BorrowRecord::create([
                'book_id' => $validated['book_id'],
                'user_id' => $validated['user_id'] ?? null,
                'borrower_name' => $validated['borrower_name'],
                'borrower_phone' => $validated['borrower_phone'] ?? null,
                'borrower_email' => $validated['borrower_email'] ?? null,
                'quantity' => $borrowQuantity,
                'borrow_date' => $validated['borrow_date'],
                'due_date' => $validated['due_date'],
                'status' => 'borrowing',
                'deposit_amount' => $depositAmount,
                'fine_amount' => 0,
                'fine_status' => 'none',
                'is_fine_paid' => false,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Tự động cộng ngay +50 EXP vào tài khoản của độc giả khi mượn sách thành công
            if ($targetUserId) {
                $borrowUser = User::find($targetUserId);
                if ($borrowUser) {
                    $borrowUser->awardExp(50);
                }
            }

            $depositMsg = $depositAmount > 0 ? " Đã tạm giữ " . number_format($depositAmount, 0, ',', '.') . "đ tiền cọc sách." : " (Gói VIP miễn cọc sách).";
            return redirect()->route('borrows.index')
                ->with('success', "Mượn sách thành công ({$borrowQuantity} cuốn)!{$depositMsg} Công lực của bạn tăng thêm +50 EXP!");
        });
    }

    /**
     * Xử lý nghiệp vụ trả sách:
     * - Hoàn trả lại đúng số lượng cuốn sách đó vào kho (+quantity).
     * - Tự động tính tiền phạt nếu quá hạn (5.000 VNĐ / ngày quá hạn).
     */
    public function returnBook(Request $request, BorrowRecord $borrow): RedirectResponse
    {
        if ($borrow->status === 'returned') {
            return redirect()->route('borrows.index')
                ->with('error', 'Phiếu mượn này đã được hoàn tất trả sách trước đó!');
        }

        $request->validate([
            'return_date' => 'nullable|date',
            'custom_fine' => 'nullable|integer|min:0',
            'return_notes' => 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use ($request, $borrow) {
            $returnDate = Carbon::parse($request->input('return_date', Carbon::now()))->startOfDay();
            $dueDate = Carbon::parse($borrow->due_date)->startOfDay();

            // Tính số ngày quá hạn và tiền phạt
            $overdueDays = 0;
            $fineAmount = 0;

            if ($returnDate->greaterThan($dueDate)) {
                $overdueDays = $dueDate->diffInDays($returnDate);
                $fineAmount = $overdueDays * self::FINE_PER_DAY;
            }

            // Nếu thủ thư có nhập tiền phạt tùy chỉnh thì ưu tiên lấy giá trị đó
            if ($request->filled('custom_fine')) {
                $fineAmount = (int) $request->input('custom_fine');
            }

            // Hoàn trả lại đúng số lượng cuốn sách đó vào kho khả dụng
            $borrowQuantity = (int) ($borrow->quantity ?? 1);
            $book = Book::lockForUpdate()->find($borrow->book_id);
            if ($book) {
                $book->increment('available_copies', $borrowQuantity);
            }

            // Cập nhật ghi chú nếu có
            $notes = $borrow->notes;
            if ($request->filled('return_notes')) {
                $notes = $notes ? $notes . "\n[Lúc trả]: " . $request->input('return_notes') : $request->input('return_notes');
            }

            // TIẾN HÀNH ĐỐI SOÁT TÀI CHÍNH (TIỀN CỌC & PHÍ PHẠT TRỄ HẠN)
            $borrowUser = $borrow->user_id ? User::find($borrow->user_id) : null;
            $isVipBorrow = ($borrowUser && $borrowUser->isVip()) || ($borrow->deposit_fee == 0 && in_array($borrow->delivery_method, ['counter_priority', 'home_delivery']));
            $depositAmount = $isVipBorrow ? 0.00 : (float) ($borrow->deposit_fee > 0 ? $borrow->deposit_fee : ($borrow->deposit_amount > 0 ? $borrow->deposit_amount : 50000));
            $wallet = $borrowUser ? $borrowUser->getOrCreateWallet() : null;

            $fineStatus = 'none';
            $isFinePaid = false;
            $finePaidAt = null;
            $financialLog = '';

            if ($borrowUser && $wallet) {
                if ($overdueDays <= 0 && $fineAmount <= 0) {
                    // 1. Đúng hạn: Hoàn trả tiền cọc về ví (nếu có cọc)
                    if ($depositAmount > 0) {
                        $wallet->deposit(
                            amount: $depositAmount,
                            description: "Hoàn trả 100% tiền cọc mượn cuốn \"{$borrow->book->title}\" (Phiếu #PKM{$borrow->id})",
                            referenceId: "REFUND_PKM{$borrow->id}"
                        );
                        Transaction::create([
                            'user_id' => $borrowUser->id,
                            'type' => 'book_deposit_refund',
                            'amount' => $depositAmount,
                            'status' => 'completed',
                            'description' => "Hoàn trả 100% tiền cọc mượn cuốn \"{$borrow->book->title}\" (Phiếu #PKM{$borrow->id})",
                            'reference_id' => "REFUND_PKM{$borrow->id}",
                        ]);
                        $financialLog = "Đã hoàn trả 100% tiền cọc (" . number_format($depositAmount, 0, ',', '.') . " đ) về ví.";
                    } else {
                        $financialLog = "Đặc quyền Scholar VIP: Miễn cọc 0đ.";
                    }
                } else {
                    // 2. Quá hạn: Khấu trừ tiền phạt vào tiền cọc (nếu có cọc)
                    if ($depositAmount >= $fineAmount && $fineAmount > 0) {
                        $refundRemainder = $depositAmount - $fineAmount;

                        // Ghi nhận nộp phạt từ tiền cọc
                        Transaction::create([
                            'user_id' => $borrowUser->id,
                            'type' => 'fine_payment',
                            'amount' => $fineAmount,
                            'status' => 'completed',
                            'description' => "Khấu trừ tiền phạt trễ hạn ({$overdueDays} ngày) từ tiền cọc phiếu #PKM{$borrow->id}",
                            'reference_id' => "FINE_PKM{$borrow->id}",
                        ]);

                        // Hoàn phần dư còn lại về ví
                        if ($refundRemainder > 0) {
                            $wallet->deposit(
                                amount: $refundRemainder,
                                description: "Hoàn trả phần tiền cọc còn lại sau khi khấu trừ phạt phiếu #PKM{$borrow->id}",
                                referenceId: "REFUND_PKM{$borrow->id}"
                            );
                            Transaction::create([
                                'user_id' => $borrowUser->id,
                                'type' => 'book_deposit_refund',
                                'amount' => $refundRemainder,
                                'status' => 'completed',
                                'description' => "Hoàn trả phần cọc còn lại sau khi khấu trừ phạt phiếu #PKM{$borrow->id}",
                                'reference_id' => "REFUND_PKM{$borrow->id}",
                            ]);
                        }

                        // Doanh thu phạt tự động chuyển vào Quỹ bảo trì & tu bổ
                        LibraryFund::create([
                            'source_name' => "Doanh thu phạt quá hạn lưu thông (#PKM{$borrow->id})",
                            'amount' => $fineAmount,
                            'allocated_for' => 'facility_maintenance',
                            'notes' => "Khấu trừ trực tiếp từ tiền cọc của độc giả {$borrow->borrower_name}",
                        ]);

                        $fineStatus = 'paid';
                        $isFinePaid = true;
                        $finePaidAt = Carbon::now();
                        $financialLog = "Đã khấu trừ phạt " . number_format($fineAmount, 0, ',', '.') . " đ từ cọc, hoàn phần dư " . number_format($refundRemainder, 0, ',', '.') . " đ về ví.";
                    } elseif ($depositAmount > 0) {
                        // Tiền cọc không đủ bù phạt
                        $remainingFine = $fineAmount - $depositAmount;
                        Transaction::create([
                            'user_id' => $borrowUser->id,
                            'type' => 'fine_payment',
                            'amount' => $depositAmount,
                            'status' => 'completed',
                            'description' => "Khấu trừ toàn bộ cọc ({$depositAmount}đ) vào tiền phạt trễ hạn phiếu #PKM{$borrow->id}",
                            'reference_id' => "FINE_PKM{$borrow->id}",
                        ]);

                        LibraryFund::create([
                            'source_name' => "Doanh thu phạt quá hạn lưu thông (#PKM{$borrow->id})",
                            'amount' => $depositAmount,
                            'allocated_for' => 'facility_maintenance',
                            'notes' => "Khấu trừ toàn bộ cọc của độc giả {$borrow->borrower_name}, còn nợ {$remainingFine}đ",
                        ]);

                        $fineStatus = 'unpaid';
                        $isFinePaid = false;
                        $fineAmount = $remainingFine;
                        $financialLog = "Đã trừ hết cọc " . number_format($depositAmount, 0, ',', '.') . " đ vào phạt. Còn thiếu " . number_format($remainingFine, 0, ',', '.') . " đ chưa thu.";
                    } else {
                        // VIP được miễn cọc 0đ, toàn bộ tiền phạt ghi nợ vào tài khoản
                        $fineStatus = 'unpaid';
                        $isFinePaid = false;
                        $financialLog = "Độc giả Scholar VIP (miễn cọc) quá hạn {$overdueDays} ngày. Phí phạt " . number_format($fineAmount, 0, ',', '.') . " đ ghi nhận chưa thanh toán.";
                    }
                }
            } else {
                $fineStatus = $fineAmount > 0 ? 'unpaid' : 'none';
            }

            $finalNotes = $notes;
            if ($financialLog) {
                $finalNotes = $finalNotes ? $finalNotes . "\n[Tài chính]: " . $financialLog : "[Tài chính]: " . $financialLog;
            }

            $borrow->update([
                'return_date' => $returnDate->toDateString(),
                'status' => 'returned',
                'fine_amount' => $fineAmount,
                'fine_status' => $fineStatus,
                'is_fine_paid' => $isFinePaid,
                'fine_paid_at' => $finePaidAt,
                'notes' => $finalNotes,
            ]);

            // Đồng bộ trạng thái trả sách sang bảng borrow_details và borrows mẹ nếu có
            if ($borrow->user_id) {
                BorrowDetail::where('book_id', $borrow->book_id)
                    ->whereIn('status', ['approved', 'borrowing', 'pending'])
                    ->whereHas('borrow', function ($q) use ($borrow) {
                        $q->where('user_id', $borrow->user_id);
                    })
                    ->limit(1)
                    ->update(['status' => 'returned', 'returned_quantity' => $borrowQuantity]);

                $parentBorrows = Borrow::where('user_id', $borrow->user_id)
                    ->whereIn('status', ['approved', 'borrowing'])
                    ->get();

                foreach ($parentBorrows as $pBorrow) {
                    $unreturnedCount = $pBorrow->details()->where('status', '!=', 'returned')->count();
                    if ($unreturnedCount === 0) {
                        $pBorrow->update([
                            'status' => 'returned',
                            'borrow_status' => 'Đã trả',
                            'return_date' => $returnDate->toDateString(),
                            'fine_amount' => $fineAmount,
                            'fine_status' => $fineStatus,
                            'is_fine_paid' => $isFinePaid,
                            'fine_paid_at' => $finePaidAt,
                        ]);
                    }
                }
            }

            $formattedFine = number_format($fineAmount, 0, ',', '.') . ' đ';

            // Ghi AuditLog hoạt động trả sách
            $fineNote = $overdueDays > 0 ? " (Quá hạn {$overdueDays} ngày, phạt {$formattedFine})" : " (Đúng hạn)";
            AuditLogger::log(
                action: 'RETURN_BOOK',
                description: "Thủ thư {$request->user()->name} đã xác nhận trả cuốn \"{$borrow->book->title}\" (SL: {$borrowQuantity} cuốn) từ độc giả \"{$borrow->borrower_name}\"{$fineNote}. {$financialLog}",
                targetType: 'BorrowRecord',
                targetId: $borrow->id,
                oldValues: ['status' => 'borrowing'],
                newValues: ['status' => 'returned', 'overdue_days' => $overdueDays, 'fine_amount' => $fineAmount, 'financial_log' => $financialLog]
            );

            // Thưởng +50 EXP cho độc giả nếu trả sách đúng hạn
            if ($overdueDays === 0 && $borrow->user_id) {
                $borrowUser = User::find($borrow->user_id);
                if ($borrowUser) {
                    $borrowUser->awardExp(50);
                }
            }

            if ($overdueDays > 0) {
                return redirect()->route('borrows.index')
                    ->with('success', "Xác nhận trả sách \"{$borrow->book->title}\" thành công! {$financialLog}");
            }

            $bonusExpText = $borrow->user_id ? " Độc giả được thưởng +50 EXP vì hoàn trả đúng hạn!" : "";
            return redirect()->route('borrows.index')
                ->with('success', "Trả cuốn sách \"{$borrow->book->title}\" đúng hạn thành công! {$financialLog}{$bonusExpText}");
        });
    }

    /**
     * Dành cho độc giả: Gửi yêu cầu mượn sách trực tuyến (trạng thái pending).
     */
    public function requestBorrow(Request $request, Book $book): RedirectResponse
    {
        $user = $request->user();

        // VẤN ĐỀ 2: Chặn yêu cầu mượn nếu còn nợ tiền phạt chưa thanh toán
        $unpaidFine = $user->unpaid_fine;
        if ($unpaidFine > 0) {
            return redirect()->back()
                ->with('error', "Tài khoản của bạn đang có tiền phạt chưa thanh toán (" . number_format($unpaidFine, 0, ',', '.') . " VNĐ). Vui lòng gặp thủ thư thanh toán đủ tiền phạt để tiếp tục mượn sách!");
        }

        $borrowQuantity = max(1, (int) $request->input('quantity', 1));

        // Kiểm tra hạn mức mượn tối đa theo gói hội viên
        $currentBorrowingCount = (int) BorrowRecord::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'borrowing'])
            ->sum('quantity');

        $maxLimit = method_exists($user, 'maxBorrowLimit') ? $user->maxBorrowLimit() : 3;
        if (($currentBorrowingCount + $borrowQuantity) > $maxLimit) {
            return redirect()->back()
                ->with('error', "Bạn đang mượn/chờ duyệt {$currentBorrowingCount} cuốn. Hạn mức tối đa theo gói hội viên của bạn là {$maxLimit} cuốn. Không thể gửi thêm yêu cầu mượn {$borrowQuantity} cuốn!");
        }

        return DB::transaction(function () use ($request, $book, $user, $borrowQuantity) {
            $lockedBook = Book::lockForUpdate()->findOrFail($book->id);

            // 1. Kiểm tra tồn kho
            if ($lockedBook->available_copies < $borrowQuantity) {
                return redirect()->back()
                    ->with('error', "Cuốn sách \"{$lockedBook->title}\" hiện không đủ số lượng khả dụng trong kho (cần {$borrowQuantity}, chỉ còn {$lockedBook->available_copies})!");
            }

            // 2. Kiểm tra xem độc giả đã có yêu cầu chờ duyệt hoặc đang mượn cuốn này chưa
            $existing = BorrowRecord::where('user_id', $user->id)
                ->where('book_id', $lockedBook->id)
                ->whereIn('status', ['pending', 'borrowing'])
                ->first();

            if ($existing) {
                if ($existing->status === 'pending') {
                    return redirect()->back()
                        ->with('error', "Bạn đã gửi yêu cầu mượn cuốn sách \"{$lockedBook->title}\" trước đó và đang chờ Admin duyệt!");
                }
                return redirect()->back()
                    ->with('error', "Bạn hiện đang mượn cuốn sách \"{$lockedBook->title}\" và chưa hoàn trả!");
            }

            $dueDays = method_exists($user, 'borrowDurationDays') ? $user->borrowDurationDays() : 14;

            BorrowRecord::create([
                'book_id' => $lockedBook->id,
                'user_id' => $user->id,
                'borrower_name' => $user->name,
                'borrower_email' => $user->email,
                'borrower_phone' => $request->input('borrower_phone') ?? ($user->phone_number ?? null),
                'quantity' => $borrowQuantity,
                'borrow_date' => Carbon::now()->toDateString(),
                'due_date' => Carbon::now()->addDays($dueDays)->toDateString(),
                'status' => 'pending',
                'notes' => $request->filled('notes') ? $request->input('notes') : 'Độc giả gửi yêu cầu mượn trực tuyến',
                'fine_amount' => 0,
                'fine_status' => 'none',
            ]);

            // Tự động cộng ngay +50 EXP vào tài khoản của độc giả khi mượn sách
            $user->awardExp(50);

            return redirect()->route('borrows.my')
                ->with('success', "Gửi yêu cầu mượn sách thành công! Công lực của bạn tăng thêm +50 EXP!");
        });
    }

    /**
     * Dành cho độc giả: Hủy yêu cầu mượn sách khi còn ở trạng thái chờ duyệt (pending).
     */
    public function cancelRequest(Request $request, BorrowRecord $borrow): RedirectResponse
    {
        // Chỉ người tạo yêu cầu hoặc Admin mới có quyền hủy
        if ($borrow->user_id !== $request->user()->id && !in_array($request->user()->role, ['admin', 'librarian'])) {
            abort(403, 'Bạn không có quyền hủy yêu cầu này.');
        }

        if ($borrow->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Chỉ có thể hủy những yêu cầu đang ở trạng thái chờ duyệt!');
        }

        $bookTitle = $borrow->book->title ?? 'Sách';
        $borrow->delete();

        return redirect()->back()
            ->with('success', "Đã hủy yêu cầu mượn sách \"{$bookTitle}\" thành công.");
    }

    /**
     * Dành cho độc giả: Gửi yêu cầu xin gia hạn mượn sách thêm 7 ngày (chờ Admin/Thủ thư duyệt).
     * Yêu cầu: status == 'borrowing', chưa quá hạn, mỗi phiếu chỉ gửi yêu cầu 1 lần (renewal_status == 'none').
     */
    public function requestRenewal(Request $request, $borrow): JsonResponse|RedirectResponse
    {
        $user = $request->user() ?: Auth::user();

        // 1. Tìm đúng record (hỗ trợ cả phiếu Borrow lẫn chi tiết BorrowRecord)
        $record = null;
        if ($borrow instanceof Borrow || $borrow instanceof BorrowRecord) {
            $record = $borrow;
        } else {
            $record = Borrow::where('id', $borrow)->where('user_id', $user->id)->first()
                   ?? BorrowRecord::where('id', $borrow)->where('user_id', $user->id)->first()
                   ?? Borrow::find($borrow)
                   ?? BorrowRecord::find($borrow);
        }

        if (!$record) {
            abort(404, 'Không tìm thấy phiếu mượn hợp lệ.');
        }

        // Phân quyền: Chỉ chủ sở hữu phiếu mượn hoặc Admin/Thủ thư mới được gửi yêu cầu
        if ($record->user_id && $record->user_id !== $user->id && !in_array($user->role, ['admin', 'librarian'])) {
            abort(403, 'Bạn không có quyền thao tác trên phiếu mượn này.');
        }

        // 2. Chỉ được gia hạn khi đang ở trạng thái mượn
        $isBorrowing = in_array($record->status, ['borrowing', 'approved'])
                    || (isset($record->borrow_status) && $record->borrow_status === 'Đã mượn');

        if (!$isBorrowing || $record->status === 'returned' || $record->status === 'rejected') {
            $msg = 'Chỉ có thể xin gia hạn cho sách đang trong thời hạn mượn!';
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : redirect()->back()->with('error', $msg);
        }

        if (isset($record->borrow_status) && $record->borrow_status === 'Chưa mượn') {
            $msg = 'Phiếu mượn chưa được nhận sách tại thư viện, chưa thể xin gia hạn!';
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : redirect()->back()->with('error', $msg);
        }

        // 3. Nếu sách đã bị quá hạn thì không cho gia hạn
        $isOverdue = false;
        if ($record instanceof BorrowRecord && method_exists($record, 'isOverdue')) {
            $isOverdue = $record->isOverdue();
        } elseif ($record->due_date) {
            $isOverdue = Carbon::parse($record->due_date)->startOfDay()->isPast();
        }

        if ($isOverdue) {
            $msg = 'Sách đã quá hạn trả! Vui lòng mang sách đến quầy thư viện để hoàn trả và thanh toán phí phạt.';
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : redirect()->back()->with('error', $msg);
        }

        // 4. Kiểm tra xem đã được gia hạn trước đó chưa (tối đa 1 lần)
        if (($record->renewed_times ?? 0) >= 1 || $record->renewal_status === 'approved' || str_contains($record->notes ?? '', '[Đồng ý gia hạn]') || str_contains($record->notes ?? '', '[Gia hạn]')) {
            $msg = 'Mỗi lượt mượn chỉ được phép gia hạn tối đa 1 lần (+7 ngày)!';
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : redirect()->back()->with('error', $msg);
        }

        // 5. Kiểm tra nếu đang có yêu cầu chờ duyệt
        if ($record->renewal_status === 'pending') {
            $msg = 'Yêu cầu gia hạn của bạn đang chờ Admin / Thủ thư xét duyệt!';
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : redirect()->back()->with('error', $msg);
        }

        // 6. Cập nhật trạng thái yêu cầu gia hạn: pending
        $now = Carbon::now();
        $record->update([
            'renewal_status' => 'pending',
            'renewal_requested_at' => $now,
            'notes' => ($record->notes ? $record->notes . "\n" : '') . "[Yêu cầu gia hạn]: Độc giả {$user->name} đã gửi yêu cầu gia hạn +7 ngày lúc " . $now->format('d/m/Y H:i'),
        ]);

        // Đồng bộ sang bảng đối ứng nếu có
        if ($record instanceof Borrow) {
            BorrowRecord::where('user_id', $user->id)
                ->where('status', 'borrowing')
                ->where('renewal_status', 'none')
                ->update([
                    'renewal_status' => 'pending',
                    'renewal_requested_at' => $now,
                ]);
        } elseif ($record instanceof BorrowRecord && $record->user_id) {
            Borrow::where('user_id', $record->user_id)
                ->whereIn('status', ['approved', 'borrowing'])
                ->whereHas('details', fn($q) => $q->where('book_id', $record->book_id))
                ->update([
                    'renewal_status' => 'pending',
                    'renewal_requested_at' => $now,
                ]);
        }

        // Ghi AuditLog
        $bookTitle = $record instanceof BorrowRecord ? ($record->book->title ?? 'Sách') : ($record->code ?? 'Phiếu mượn #' . $record->id);
        AuditLogger::log(
            action: 'REQUEST_RENEWAL',
            description: "Độc giả {$user->name} đã gửi yêu cầu gia hạn thêm 7 ngày cho \"{$bookTitle}\".",
            targetType: get_class($record),
            targetId: $record->id,
            oldValues: ['renewal_status' => 'none'],
            newValues: ['renewal_status' => 'pending', 'renewal_requested_at' => $now->toDateTimeString()]
        );

        $msg = 'Đã gửi yêu cầu gia hạn (+7 ngày) đến Ban Quản Trị thành công!';
        return $request->wantsJson() || $request->ajax()
            ? response()->json(['success' => true, 'message' => $msg, 'renewal_status' => 'pending', 'borrow_id' => $record->id])
            : redirect()->back()->with('success', $msg);
    }

    /**
     * Dành cho Admin / Thủ thư: Phê duyệt yêu cầu gia hạn sách (+7 ngày).
     * Cộng thêm 7 ngày vào due_date, chuyển renewal_status = 'approved', tăng renewed_times = 1, ghi log nhật ký phiếu.
     */
    public function approveRenewal(Request $request, $borrow): JsonResponse|RedirectResponse
    {
        $admin = $request->user();
        if (!in_array($admin->role, ['admin', 'librarian'])) {
            abort(403, 'Chỉ Quản trị viên và Thủ thư mới có quyền phê duyệt gia hạn.');
        }

        $record = null;
        if ($borrow instanceof Borrow || $borrow instanceof BorrowRecord) {
            $record = $borrow;
        } else {
            $record = Borrow::find($borrow) ?? BorrowRecord::find($borrow) ?? Borrow::findOrFail($borrow);
        }

        if ($record->status !== 'borrowing' || $record->renewal_status !== 'pending') {
            $msg = 'Phiếu mượn này không có yêu cầu gia hạn đang chờ duyệt!';
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : redirect()->back()->with('error', $msg);
        }

        $currentDueDate = Carbon::parse($record->due_date ?: Carbon::now());
        $newDueDate = $currentDueDate->copy()->addDays(7);
        $now = Carbon::now();

        $record->update([
            'due_date' => $newDueDate->toDateString(),
            'renewal_status' => 'approved',
            'renewed_times' => ($record->renewed_times ?? 0) + 1,
            'notes' => ($record->notes ? $record->notes . "\n" : '') . "[Đồng ý gia hạn]: Thủ thư {$admin->name} đã duyệt gia hạn thêm 7 ngày đến " . $newDueDate->format('d/m/Y') . " lúc " . $now->format('d/m/Y H:i'),
        ]);

        // Đồng bộ sang bảng borrows mẹ nếu có
        if ($record instanceof BorrowRecord && $record->user_id) {
            Borrow::where('user_id', $record->user_id)
                ->whereIn('status', ['approved', 'borrowing'])
                ->whereHas('details', fn($q) => $q->where('book_id', $record->book_id))
                ->update([
                    'due_date' => $newDueDate->toDateString(),
                    'renewal_status' => 'approved',
                    'renewed_times' => ($record->renewed_times ?? 0) + 1,
                ]);
        }

        // Thưởng +15 EXP cho độc giả vì gia hạn sách
        if ($record->user_id) {
            $borrowUser = User::find($record->user_id);
            if ($borrowUser) {
                $borrowUser->awardExp(15);
            }
        }

        $bookTitle = $record instanceof BorrowRecord ? ($record->book->title ?? 'Sách') : 'Sách trong phiếu';
        AuditLogger::log(
            action: 'APPROVE_RENEWAL',
            description: "Thủ thư {$admin->name} đã duyệt yêu cầu gia hạn thêm 7 ngày cho cuốn \"{$bookTitle}\" của độc giả \"{$record->borrower_name}\" (Hạn mới: {$newDueDate->format('d/m/Y')}).",
            targetType: get_class($record),
            targetId: $record->id,
            oldValues: ['renewal_status' => 'pending', 'due_date' => $currentDueDate->toDateString()],
            newValues: ['renewal_status' => 'approved', 'due_date' => $newDueDate->toDateString(), 'renewed_times' => 1]
        );

        $msg = "Đồng ý gia hạn thành công! Hạn trả mới của cuốn sách là ngày " . $newDueDate->format('d/m/Y') . " (+7 ngày). Độc giả nhận được +15 EXP!";
        return $request->wantsJson() || $request->ajax()
            ? response()->json([
                'success' => true,
                'message' => $msg,
                'new_due_date' => $newDueDate->format('d/m/Y'),
                'renewal_status' => 'approved',
                'borrow_id' => $record->id,
            ])
            : redirect()->back()->with('success', $msg);
    }

    /**
     * Dành cho Admin / Thủ thư: Từ chối yêu cầu gia hạn sách.
     * Chuyển renewal_status = 'rejected', ghi nhận lý do từ chối.
     */
    public function rejectRenewal(Request $request, $borrow): JsonResponse|RedirectResponse
    {
        $admin = $request->user();
        if (!in_array($admin->role, ['admin', 'librarian'])) {
            abort(403, 'Chỉ Quản trị viên và Thủ thư mới có quyền từ chối gia hạn.');
        }

        $record = null;
        if ($borrow instanceof Borrow || $borrow instanceof BorrowRecord) {
            $record = $borrow;
        } else {
            $record = Borrow::find($borrow) ?? BorrowRecord::find($borrow) ?? Borrow::findOrFail($borrow);
        }

        if ($record->renewal_status !== 'pending') {
            $msg = 'Phiếu mượn này không có yêu cầu gia hạn đang chờ duyệt!';
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : redirect()->back()->with('error', $msg);
        }

        $reason = $request->input('reason', $request->input('reject_reason', 'Thủ thư từ chối gia hạn'));
        $now = Carbon::now();

        $record->update([
            'renewal_status' => 'rejected',
            'notes' => ($record->notes ? $record->notes . "\n" : '') . "[Từ chối gia hạn]: Thủ thư {$admin->name} đã từ chối yêu cầu gia hạn (Lý do: {$reason}) lúc " . $now->format('d/m/Y H:i'),
        ]);

        // Đồng bộ sang bảng borrows mẹ nếu có
        if ($record instanceof BorrowRecord && $record->user_id) {
            Borrow::where('user_id', $record->user_id)
                ->whereIn('status', ['approved', 'borrowing'])
                ->whereHas('details', fn($q) => $q->where('book_id', $record->book_id))
                ->update(['renewal_status' => 'rejected']);
        }

        $bookTitle = $record instanceof BorrowRecord ? ($record->book->title ?? 'Sách') : 'Sách trong phiếu';
        AuditLogger::log(
            action: 'REJECT_RENEWAL',
            description: "Thủ thư {$admin->name} đã từ chối yêu cầu gia hạn cuốn \"{$bookTitle}\" của độc giả \"{$record->borrower_name}\" (Lý do: {$reason}).",
            targetType: get_class($record),
            targetId: $record->id,
            oldValues: ['renewal_status' => 'pending'],
            newValues: ['renewal_status' => 'rejected', 'reason' => $reason]
        );

        $msg = "Đã từ chối yêu cầu gia hạn cho cuốn \"{$bookTitle}\" của độc giả \"{$record->borrower_name}\".";
        return $request->wantsJson() || $request->ajax()
            ? response()->json([
                'success' => true,
                'message' => $msg,
                'renewal_status' => 'rejected',
                'borrow_id' => $record->id,
            ])
            : redirect()->back()->with('success', $msg);
    }

    /**
     * VẤN ĐỀ 1: Dành cho Admin / Thủ thư: Duyệt yêu cầu mượn sách (pending -> borrowing).
     * - Lấy chính xác số lượng đăng ký mượn ($borrowQuantity).
     * - Kiểm tra tồn kho: if ($book->available_copies < $borrowQuantity) báo lỗi chuẩn.
     * - Trừ kho chính xác: $book->decrement('available_copies', $borrowQuantity).
     * - Cập nhật trạng thái chi tiết mượn/phiếu mượn thành Đã duyệt / Đang mượn.
     */
    public function approveRequest(Request $request, BorrowRecord $borrow): JsonResponse|RedirectResponse
    {
        if ($borrow->status !== 'pending') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phiếu mượn này không ở trạng thái chờ duyệt!'
                ], 422);
            }
            return redirect()->back()
                ->with('error', 'Phiếu mượn này không ở trạng thái chờ duyệt!');
        }

        return DB::transaction(function () use ($request, $borrow) {
            $book = Book::lockForUpdate()->findOrFail($borrow->book_id);
            $borrowQuantity = (int) ($borrow->quantity ?? 1);

            // Kiểm tra tồn kho của cuốn sách
            if ($book->available_copies < $borrowQuantity) {
                $errorMsg = "Không đủ sách trong kho để duyệt yêu cầu này (cần {$borrowQuantity}, chỉ còn {$book->available_copies})!";
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMsg
                    ], 422);
                }
                return redirect()->back()->with('error', $errorMsg);
            }

            // Trừ số lượng khả dụng trong kho (giữ nguyên tổng số lượng sở hữu)
            $book->decrement('available_copies', $borrowQuantity);

            // Cập nhật ngày mượn và hạn trả (+14 ngày từ lúc duyệt)
            $borrowDays = (int) $request->input('borrow_days', 14);
            $borrow->update([
                'borrow_date' => Carbon::now()->toDateString(),
                'due_date' => Carbon::now()->addDays($borrowDays)->toDateString(),
                'status' => 'borrowing',
                'notes' => $borrow->notes ? $borrow->notes . "\n[Duyệt bởi]: " . $request->user()->name : '[Duyệt bởi]: ' . $request->user()->name,
            ]);

            // Cập nhật chi tiết tương ứng trong bảng borrow_details và borrows mẹ nếu có
            if ($borrow->user_id) {
                BorrowDetail::where('book_id', $borrow->book_id)
                    ->where('status', 'pending')
                    ->whereHas('borrow', function ($q) use ($borrow) {
                        $q->where('user_id', $borrow->user_id);
                    })
                    ->limit(1)
                    ->update(['status' => 'approved']);

                $parentBorrows = Borrow::where('user_id', $borrow->user_id)
                    ->where('status', 'pending')
                    ->get();
                foreach ($parentBorrows as $pBorrow) {
                    $hasPending = $pBorrow->details()->where('status', 'pending')->exists();
                    if (!$hasPending) {
                        $pBorrow->update([
                            'status' => 'approved',
                            'borrow_status' => 'Đã mượn',
                            'approved_date' => Carbon::now()->toDateString(),
                            'due_date' => Carbon::now()->addDays($borrowDays)->toDateString(),
                        ]);
                    }
                }
            }

            $remainingCount = BorrowRecord::where('status', 'pending')->count();
            $message = "Đã phê duyệt yêu cầu mượn cuốn \"{$book->title}\" (SL: {$borrowQuantity} cuốn) cho độc giả \"{$borrow->borrower_name}\"! Đã trừ {$borrowQuantity} cuốn vào kho.";

            // Ghi AuditLog phê duyệt mượn sách
            AuditLogger::log(
                action: 'APPROVE_BORROW',
                description: "Thủ thư {$request->user()->name} đã phê duyệt yêu cầu mượn cuốn \"{$book->title}\" (SL: {$borrowQuantity} cuốn) cho độc giả \"{$borrow->borrower_name}\".",
                targetType: 'BorrowRecord',
                targetId: $borrow->id,
                oldValues: ['status' => 'pending'],
                newValues: ['status' => 'borrowing', 'quantity' => $borrowQuantity, 'book_title' => $book->title]
            );

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'remaining_pending' => $remainingCount,
                    'borrow_id' => $borrow->id,
                    'book_title' => $book->title,
                    'borrow_quantity' => $borrowQuantity,
                    'borrower_name' => $borrow->borrower_name
                ]);
            }

            return redirect()->back()->with('success', $message);
        });
    }

    /**
     * Phê duyệt theo phiếu mượn tổng (Borrow Slip).
     */
    public function approveSlip(Request $request, Borrow $borrow): JsonResponse|RedirectResponse
    {
        if ($borrow->status !== 'pending') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Phiếu mượn không ở trạng thái chờ duyệt!'], 422);
            }
            return redirect()->back()->with('error', 'Phiếu mượn không ở trạng thái chờ duyệt!');
        }

        return DB::transaction(function () use ($request, $borrow) {
            // Kiểm tra tồn kho cho từng chi tiết
            foreach ($borrow->details as $detail) {
                $book = Book::lockForUpdate()->find($detail->book_id);
                $borrowQuantity = $detail->quantity ?? 1;
                if (!$book || $book->available_copies < $borrowQuantity) {
                    $available = $book ? $book->available_copies : 0;
                    $msg = "Không đủ sách trong kho để duyệt yêu cầu này (cần {$borrowQuantity}, chỉ còn {$available})!";
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => $msg], 422);
                    }
                    return redirect()->back()->with('error', $msg);
                }
            }

            // Trừ số lượng khả dụng và chuyển status
            foreach ($borrow->details as $detail) {
                $book = Book::lockForUpdate()->find($detail->book_id);
                $borrowQuantity = $detail->quantity ?? 1;
                $book->decrement('available_copies', $borrowQuantity);
                $detail->update(['status' => 'approved']);
            }

            $borrow->update([
                'status' => 'approved',
                'borrow_status' => 'Đã mượn',
                'approved_date' => Carbon::now()->toDateString(),
                'due_date' => Carbon::now()->addDays(14)->toDateString(),
            ]);

            // Đồng bộ sang bảng borrow_records để trạng thái 2 bên luôn khớp nhau
            if ($borrow->user_id) {
                BorrowRecord::where('user_id', $borrow->user_id)
                    ->whereIn('book_id', $borrow->details->pluck('book_id'))
                    ->where('status', 'pending')
                    ->update([
                        'status' => 'borrowing',
                        'borrow_date' => Carbon::now()->toDateString(),
                        'due_date' => Carbon::now()->addDays(14)->toDateString(),
                    ]);
            }

            // Ghi AuditLog phê duyệt phiếu mượn
            AuditLogger::log(
                action: 'APPROVE_BORROW',
                description: "Thủ thư {$request->user()->name} đã phê duyệt phiếu mượn {$borrow->code} cho độc giả \"{$borrow->borrower_name}\" ({$borrow->total_quantity} cuốn).",
                targetType: 'Borrow',
                targetId: $borrow->id,
                oldValues: ['status' => 'pending'],
                newValues: ['status' => 'approved', 'total_quantity' => $borrow->total_quantity]
            );

            $msg = "Đã phê duyệt phiếu mượn {$borrow->code} thành công!";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }
            return redirect()->back()->with('success', $msg);
        });
    }

    /**
     * Dành cho Admin / Thủ thư: Từ chối yêu cầu mượn sách (pending -> rejected).
     * Hỗ trợ cả AJAX response (không reload trang) và Redirect back().
     */
    public function rejectRequest(Request $request, BorrowRecord $borrow): JsonResponse|RedirectResponse
    {
        if ($borrow->status !== 'pending') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phiếu mượn này không ở trạng thái chờ duyệt!'
                ], 422);
            }
            return redirect()->back()
                ->with('error', 'Phiếu mượn này không ở trạng thái chờ duyệt!');
        }

        $reason = $request->input('reject_reason', 'Thủ thư từ chối yêu cầu');

        $borrow->update([
            'status' => 'rejected',
            'notes' => $borrow->notes ? $borrow->notes . "\n[Lý do từ chối]: " . $reason : "[Lý do từ chối]: " . $reason,
        ]);

        $remainingCount = BorrowRecord::where('status', 'pending')->count();
        $message = "Đã từ chối yêu cầu mượn cuốn \"{$borrow->book->title}\" của độc giả \"{$borrow->borrower_name}\".";

        // Ghi AuditLog từ chối mượn sách
        AuditLogger::log(
            action: 'REJECT_BORROW',
            description: "Thủ thư {$request->user()->name} đã từ chối yêu cầu mượn cuốn \"{$borrow->book->title}\" của độc giả \"{$borrow->borrower_name}\" (Lý do: {$reason}).",
            targetType: 'BorrowRecord',
            targetId: $borrow->id,
            oldValues: ['status' => 'pending'],
            newValues: ['status' => 'rejected', 'reject_reason' => $reason]
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'remaining_pending' => $remainingCount,
                'borrow_id' => $borrow->id,
                'book_title' => $borrow->book->title ?? '',
                'borrower_name' => $borrow->borrower_name
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * VẤN ĐỀ 2: Quản trị viên/Thủ thư xác nhận thu tiền phạt của độc giả.
     * Cập nhật fine_status = 'paid', lưu thời gian nộp phạt.
     * Hệ thống tự động gỡ phong tỏa, độc giả lập tức được phép mượn sách lại bình thường.
     */
    public function payFine(Request $request, BorrowRecord $borrow): JsonResponse|RedirectResponse
    {
        if ($borrow->fine_amount <= 0 || $borrow->fine_status === 'paid') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phiếu mượn này không có tiền phạt cần thanh toán hoặc đã được nộp phạt!'
                ], 422);
            }
            return redirect()->back()->with('error', 'Phiếu mượn này không có tiền phạt cần thanh toán hoặc đã được nộp phạt!');
        }

        $borrow->update([
            'fine_status' => 'paid',
            'fine_paid_at' => Carbon::now(),
            'notes' => $borrow->notes ? $borrow->notes . "\n[Thu phạt]: Đã thu " . number_format($borrow->fine_amount, 0, ',', '.') . " VNĐ lúc " . Carbon::now()->format('d/m/Y H:i') . " bởi " . $request->user()->name : "[Thu phạt]: Đã thu " . number_format($borrow->fine_amount, 0, ',', '.') . " VNĐ lúc " . Carbon::now()->format('d/m/Y H:i') . " bởi " . $request->user()->name,
        ]);

        $formattedFine = number_format($borrow->fine_amount, 0, ',', '.') . ' VNĐ';
        $message = "Đã xác nhận thu {$formattedFine} tiền phạt từ độc giả \"{$borrow->borrower_name}\" thành công! Tài khoản độc giả đã được mở khóa quyền mượn sách.";

        // Ghi AuditLog thu tiền phạt
        $librarianName = $request->user()->name;
        AuditLogger::log(
            action: 'COLLECT_FINE',
            description: "Thủ thư {$librarianName} đã thu {$formattedFine} tiền phạt từ độc giả \"{$borrow->borrower_name}\" cho phiếu #{$borrow->id}.",
            targetType: 'BorrowRecord',
            targetId: $borrow->id,
            oldValues: ['fine_status' => 'unpaid', 'fine_amount' => $borrow->fine_amount],
            newValues: ['fine_status' => 'paid', 'fine_paid_at' => Carbon::now()->toDateTimeString()]
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'borrow_id' => $borrow->id,
                'fine_status' => 'paid',
                'fine_paid_at' => Carbon::now()->format('d/m/Y H:i'),
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Xóa / Hủy phiếu mượn: Nếu đang mượn thì hoàn trả lại số lượng kho sách trước khi xóa.
     */
    public function destroy(BorrowRecord $borrow): RedirectResponse
    {
        DB::transaction(function () use ($borrow) {
            // Nếu phiếu đang mượn mà bị xóa/hủy thì trả lại đúng số lượng sách vào kho khả dụng
            if ($borrow->status === 'borrowing') {
                $borrowQuantity = (int) ($borrow->quantity ?? 1);
                $book = Book::lockForUpdate()->find($borrow->book_id);
                if ($book) {
                    $book->increment('available_copies', $borrowQuantity);
                }
            }

            $borrow->delete();
        });

        return redirect()->route('borrows.index')
            ->with('success', 'Đã xóa phiếu mượn sách thành công (và hoàn kho nếu phiếu đang mượn)!');
    }

    /**
     * BƯỚC 3: Trang "THEO DÕI PHIẾU ĐĂNG KÝ MƯỢN" (Theo phiếu mượn & Theo đầu sách).
     */
    public function tracking(Request $request): View
    {
        $user = $request->user();
        $isAdmin = in_array($user->role, ['admin', 'librarian']);
        $mode = $request->input('mode', 'slip'); // 'slip' (Theo phiếu mượn) hoặc 'book' (Theo đầu sách)
        $status = $request->input('status', 'all'); // 'all', 'approved', 'pending', 'borrowing', 'not_borrowed', 'overdue'

        // Chế độ 1: THEO PHIẾU MƯỢN
        if ($mode === 'slip') {
            $query = Borrow::with(['details.book.category', 'user'])
                ->when(!$isAdmin, function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->when($status !== 'all', function ($q) use ($status) {
                    if ($status === 'approved') {
                        $q->whereIn('status', ['approved', 'borrowing']);
                    } elseif ($status === 'pending') {
                        $q->where('status', 'pending');
                    } elseif ($status === 'borrowing') {
                        $q->where('borrow_status', 'Đã mượn');
                    } elseif ($status === 'not_borrowed') {
                        $q->where('borrow_status', 'Chưa mượn');
                    } elseif ($status === 'overdue') {
                        $q->where('status', 'borrowing')
                          ->where('due_date', '<', Carbon::now()->toDateString());
                    }
                })
                ->latest();

            $slips = $query->paginate(10)->withQueryString();
            $bookItems = null;
        } else {
            // Chế độ 2: THEO ĐẦU SÁCH
            $query = BorrowDetail::with(['book.category', 'borrow.user'])
                ->when(!$isAdmin, function ($q) use ($user) {
                    $q->whereHas('borrow', function ($b) use ($user) {
                        $b->where('user_id', $user->id);
                    });
                })
                ->when($status !== 'all', function ($q) use ($status) {
                    $q->whereHas('borrow', function ($b) use ($status) {
                        if ($status === 'approved') {
                            $b->whereIn('status', ['approved', 'borrowing']);
                        } elseif ($status === 'pending') {
                            $b->where('status', 'pending');
                        } elseif ($status === 'borrowing') {
                            $b->where('borrow_status', 'Đã mượn');
                        } elseif ($status === 'not_borrowed') {
                            $b->where('borrow_status', 'Chưa mượn');
                        } elseif ($status === 'overdue') {
                            $b->where('status', 'borrowing')
                              ->where('due_date', '<', Carbon::now()->toDateString());
                        }
                    });
                })
                ->latest();

            $bookItems = $query->paginate(10)->withQueryString();
            $slips = null;
        }

        // Thống kê nhanh
        $baseBorrowCount = Borrow::when(!$isAdmin, fn($q) => $q->where('user_id', $user->id));
        $totalAll = (clone $baseBorrowCount)->count();
        $totalPending = (clone $baseBorrowCount)->where('status', 'pending')->count();
        $totalApproved = (clone $baseBorrowCount)->whereIn('status', ['approved', 'borrowing'])->count();
        $totalNotBorrowed = (clone $baseBorrowCount)->where('borrow_status', 'Chưa mượn')->count();
        $totalOverdue = (clone $baseBorrowCount)->where('status', 'borrowing')
            ->where('due_date', '<', Carbon::now()->toDateString())->count();

        return view('borrows.tracking', compact(
            'mode',
            'status',
            'slips',
            'bookItems',
            'totalAll',
            'totalPending',
            'totalApproved',
            'totalNotBorrowed',
            'totalOverdue'
        ));
    }

    /**
     * API xem chi tiết đầu sách trong một phiếu đăng ký mượn (cho Modal Popup).
     */
    public function slipDetail(Borrow $borrow): JsonResponse
    {
        $user = auth()->user();
        if ($user && !in_array($user->role, ['admin', 'librarian']) && (int) $borrow->user_id !== (int) $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xem chi tiết phiếu mượn này!',
            ], 403);
        }

        $borrow->load(['details.book.category', 'user']);

        $items = $borrow->details->map(function ($detail) {
            $book = $detail->book;
            return [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'category' => $book->category ? $book->category->name : 'Chung',
                'publisher' => $book->publisher ?? 'NXB Trẻ',
                'publish_year' => $book->publish_year ?? 2024,
                'quantity' => $detail->quantity,
                'cover_url' => $book->cover_url,
                'status' => $detail->status,
            ];
        });

        $isOverdue = $borrow->due_date ? Carbon::parse($borrow->due_date)->startOfDay()->isPast() : false;
        $isBorrowing = in_array($borrow->status, ['borrowing', 'approved']) || $borrow->borrow_status === 'Đã mượn';
        $canRenew = $isBorrowing && $borrow->borrow_status !== 'Chưa mượn' && !$isOverdue && ($borrow->renewal_status ?? 'none') === 'none' && ($borrow->renewed_times ?? 0) < 1;

        return response()->json([
            'success' => true,
            'id' => $borrow->id,
            'code' => $borrow->code,
            'borrower_name' => $borrow->borrower_name,
            'borrow_date' => $borrow->borrow_date ? $borrow->borrow_date->format('d/m/Y') : '—',
            'approved_date' => $borrow->approved_date ? $borrow->approved_date->format('d/m/Y') : '—',
            'due_date' => $borrow->due_date ? $borrow->due_date->format('d/m/Y') : '—',
            'status' => $borrow->status,
            'borrow_status' => $borrow->borrow_status,
            'renewal_status' => $borrow->renewal_status ?? 'none',
            'can_renew' => $canRenew,
            'total_quantity' => $borrow->total_quantity,
            'notes' => $borrow->notes,
            'items' => $items,
        ]);
    }
}

