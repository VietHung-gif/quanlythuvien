<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\BorrowRecord;
use App\Models\LibraryFund;
use App\Models\Transaction;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    /**
     * Phí gói Scholar VIP theo tháng (VNĐ)
     */
    const VIP_MONTHLY_FEE = 49000;

    /**
     * Phí gói Scholar VIP theo kỳ 3 tháng (VNĐ)
     */
    const VIP_TERM_FEE = 129000;


    /**
     * Tiền cọc sách mặc định mỗi cuốn đối với bạn đọc thường (VNĐ)
     */
    const DEFAULT_BOOK_DEPOSIT = 50000;

    /**
     * Trang Bảng quy định & Nâng cấp Gói hội viên (/membership)
     */
    public function membership(Request $request): \Illuminate\View\View
    {
        $user = $request->user() ?: \Illuminate\Support\Facades\Auth::user();
        $wallet = $user ? $user->getOrCreateWallet() : null;
        $transactions = $user ? $user->transactions()->whereIn('type', ['membership_upgrade', 'membership_fee', 'deposit'])->take(5)->get() : collect();

        return view('membership.index', compact('user', 'wallet', 'transactions'));
    }

    /**
     * Lấy thông tin ví và lịch sử giao dịch gần nhất (API cho modal)
     */
    public function info(Request $request): JsonResponse
    {
        $user = $request->user() ?: \Illuminate\Support\Facades\Auth::user();
        $wallet = $user->getOrCreateWallet();

        $transactions = $user->transactions()
            ->take(15)
            ->get()
            ->map(function ($tx) {
                return [
                    'id' => $tx->id,
                    'type' => $tx->type,
                    'type_name' => $tx->type_name,
                    'amount' => (float) $tx->amount,
                    'formatted_amount' => ($tx->is_positive ? '+' : '-') . number_format($tx->amount, 0, ',', '.') . ' đ',
                    'is_positive' => $tx->is_positive,
                    'status' => $tx->status,
                    'description' => $tx->description,
                    'reference_id' => $tx->reference_id,
                    'created_at' => $tx->created_at ? $tx->created_at->format('d/m/Y H:i') : '',
                ];
            });

        $expiresAt = $user->vip_expires_at ?? $user->membership_expires_at;

        return response()->json([
            'success' => true,
            'balance' => (float) $wallet->balance,
            'formatted_balance' => number_format($wallet->balance, 0, ',', '.') . ' đ',
            'membership_type' => $user->isVip() ? 'vip' : 'standard',
            'membership_tier' => $user->isVip() ? 'vip' : 'standard',
            'is_vip' => $user->isVip(),
            'max_borrow_limit' => $user->maxBorrowLimit(),
            'borrow_duration_days' => $user->borrowDurationDays(),
            'delivery_address' => $user->delivery_address,
            'phone_number' => $user->phone_number,
            'membership_expires_at' => $expiresAt ? $expiresAt->format('d/m/Y') : null,
            'vip_expires_at' => $expiresAt ? $expiresAt->format('d/m/Y') : null,
            'transactions' => $transactions,
        ]);
    }

    /**
     * Trang / Tab nạp tiền vào ví điện tử (/wallet/deposit)
     */
    public function deposit(Request $request): \Illuminate\View\View|JsonResponse|RedirectResponse
    {
        if ($request->isMethod('post')) {
            return $this->processDeposit($request);
        }

        $user = $request->user() ?: \Illuminate\Support\Facades\Auth::user();
        $wallet = $user ? $user->getOrCreateWallet() : null;
        $transactions = $user ? $user->transactions()->where('type', 'deposit')->latest()->take(5)->get() : collect();
        if ($transactions->isEmpty() && $user) {
            $transactions = $user->transactions()->latest()->take(5)->get();
        }

        return view('wallet.deposit', compact('user', 'wallet', 'transactions'));
    }

    /**
     * Xử lý nạp tiền (POST /wallet/deposit)
     */
    public function processDeposit(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:10000', 'max:5000000'],
        ], [
            'amount.min' => 'Số tiền nạp tối thiểu là 10.000 VNĐ.',
            'amount.max' => 'Số tiền nạp tối đa mỗi lần là 5.000.000 VNĐ.',
        ]);

        $amount = (float) $validated['amount'];
        $user = $request->user() ?: \Illuminate\Support\Facades\Auth::user();
        $wallet = $user->getOrCreateWallet();

        $refId = 'VQR_' . date('Ymd') . '_' . strtoupper(substr(uniqid(), -6));
        $tx = $wallet->deposit(
            amount: $amount,
            description: "Nạp tiền vào ví qua cổng thanh toán VietQR Napas 247",
            referenceId: $refId
        );

        AuditLogger::log(
            action: 'WALLET_DEPOSIT',
            description: "Người dùng {$user->name} đã nạp " . number_format($amount, 0, ',', '.') . " VNĐ vào ví số dư.",
            targetType: get_class($wallet),
            targetId: $wallet->id,
            newValues: ['amount' => $amount, 'ref_id' => $refId]
        );

        $msg = "Giao dịch thành công! Đã nạp +" . number_format($amount, 0, ',', '.') . "đ vào ví";
        return ($request->wantsJson() || $request->ajax() || $request->isJson() || $request->expectsJson() || $request->header('Accept') === 'application/json' || $request->routeIs('wallet.deposit-mock'))
            ? response()->json([
                'success' => true,
                'message' => $msg,
                'amount' => $amount,
                'balance' => (float) $wallet->balance,
                'formatted_balance' => number_format($wallet->balance, 0, ',', '.') . ' đ',
                'transaction' => [
                    'id' => $tx->id,
                    'reference_id' => $tx->reference_id,
                    'amount' => $tx->amount,
                    'formatted_amount' => '+' . number_format($tx->amount, 0, ',', '.') . ' đ',
                    'created_at' => $tx->created_at ? $tx->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i'),
                ],
            ])
            : redirect()->back()->with('success', $msg);
    }

    /**
     * Nâng cấp gói hội viên Scholar VIP (Gói tháng 49.000đ hoặc Gói kỳ 129.000đ)
     */
    public function upgradeVip(Request $request): JsonResponse|RedirectResponse
    {
        $user = $request->user() ?: \Illuminate\Support\Facades\Auth::user();
        $wallet = $user->getOrCreateWallet();

        // Xác định gói đăng ký
        $plan = $request->input('plan', 'monthly'); // 'monthly' hoặc 'term'
        if ($plan === 'term') {
            $fee = self::VIP_TERM_FEE; // 129.000đ
            $days = 90; // 3 tháng (1 kỳ học)
            $planTitle = 'Gói Kỳ (129.000đ / 3 tháng)';
            $expBonus = 300;
        } else {
            $fee = self::VIP_MONTHLY_FEE; // 49.000đ
            $days = 30; // 1 tháng
            $planTitle = 'Gói Tháng (49.000đ / 30 ngày)';
            $expBonus = 150;
        }

        if ($wallet->balance < $fee) {
            $msg = 'Số dư ví không đủ (' . number_format($wallet->balance, 0, ',', '.') . ' đ). Bạn cần tối thiểu ' . number_format($fee, 0, ',', '.') . ' đ để kích hoạt ' . $planTitle . '!';
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg, 'require_deposit' => true], 422)
                : redirect()->back()->with('error', $msg);
        }

        return DB::transaction(function () use ($user, $wallet, $fee, $days, $planTitle, $expBonus, $request) {
            $refId = 'VIP_' . strtoupper(substr(uniqid(), -6));
            $wallet->withdraw(
                amount: $fee,
                type: 'membership_upgrade',
                description: "Nâng cấp Scholar VIP - {$planTitle}",
                referenceId: $refId
            );

            $currentExpiry = ($user->vip_expires_at && $user->vip_expires_at->isFuture())
                ? $user->vip_expires_at
                : (($user->membership_expires_at && $user->membership_expires_at->isFuture()) ? $user->membership_expires_at : Carbon::now());
            $newExpiry = $currentExpiry->copy()->addDays($days);

            $user->update([
                'membership_type' => 'vip',
                'membership_tier' => 'vip',
                'vip_expires_at' => $newExpiry,
                'membership_expires_at' => $newExpiry,
                'delivery_address' => $request->input('delivery_address', $user->delivery_address),
                'phone_number' => $request->input('phone_number', $user->phone_number),
            ]);

            // Thưởng EXP công lực khi nâng cấp
            $user->awardExp($expBonus);

            // Ghi nhận dòng tiền vào Quỹ cơ sở hạ tầng & Server AI (ngân sách học đường thực tế)
            LibraryFund::create([
                'source_name' => "Doanh thu phí hội viên Scholar VIP ({$user->name})",
                'amount' => $fee,
                'allocated_for' => 'server_cloud',
                'notes' => "Độc giả {$user->name} ({$user->email}) đăng ký {$planTitle} đến {$newExpiry->format('d/m/Y')}",
            ]);

            AuditLogger::log(
                action: 'UPGRADE_VIP',
                description: "Độc giả {$user->name} đã kích hoạt {$planTitle} thành công với mức phí " . number_format($fee, 0, ',', '.') . " VNĐ.",
                targetType: get_class($user),
                targetId: $user->id,
                newValues: ['membership_type' => 'vip', 'expires_at' => $newExpiry->toDateTimeString(), 'fee' => $fee]
            );

            $msg = "Chúc mừng bạn đã nâng cấp thành công lên SCHOLAR VIP ({$planTitle})! Hạn dùng đến ngày " . $newExpiry->format('d/m/Y') . ". Bạn nhận được đặc quyền mượn tối đa 15 cuốn, miễn cọc 0đ, ship tận nhà và x2 EXP!";
            return $request->wantsJson() || $request->ajax()
                ? response()->json([
                    'success' => true,
                    'message' => $msg,
                    'balance' => (float) $wallet->balance,
                    'formatted_balance' => number_format($wallet->balance, 0, ',', '.') . ' đ',
                    'is_vip' => true,
                    'membership_type' => 'vip',
                    'membership_tier' => 'vip',
                    'expires_at' => $newExpiry->format('d/m/Y'),
                    'max_borrow_limit' => 15,
                ])
                : redirect()->back()->with('success', $msg);
        });
    }

    /**
     * Nộp phạt quá hạn online trực tiếp từ ví
     */
    public function payFineFromWallet(Request $request, $borrow): JsonResponse|RedirectResponse
    {
        $user = $request->user() ?: Auth::user();
        $record = $borrow instanceof BorrowRecord ? $borrow : (BorrowRecord::find($borrow) ?? Borrow::findOrFail($borrow));

        if ($record->user_id && $record->user_id !== $user->id && !in_array($user->role, ['admin', 'librarian'])) {
            abort(403, 'Bạn không có quyền thao tác trên khoản phạt này.');
        }

        $fineAmount = (float) ($record->fine_amount > 0 ? $record->fine_amount : (method_exists($record, 'calculateEstimatedFine') ? $record->calculateEstimatedFine() : 0));

        if ($fineAmount <= 0) {
            $msg = 'Phiếu mượn này không có tiền phạt cần thanh toán.';
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : redirect()->back()->with('error', $msg);
        }

        $wallet = $user->getOrCreateWallet();
        if ($wallet->balance < $fineAmount) {
            $msg = 'Số dư ví (' . number_format($wallet->balance, 0, ',', '.') . ' đ) không đủ thanh toán khoản phạt ' . number_format($fineAmount, 0, ',', '.') . ' đ. Vui lòng nạp thêm tiền!';
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg, 'require_deposit' => true], 422)
                : redirect()->back()->with('error', $msg);
        }

        return DB::transaction(function () use ($user, $wallet, $record, $fineAmount, $request) {
            $refId = 'FINE_PKM' . str_pad($record->id, 4, '0', STR_PAD_LEFT);
            $wallet->withdraw(
                amount: $fineAmount,
                type: 'fine_payment',
                description: "Nộp tiền phạt quá hạn cho phiếu mượn #PKM" . str_pad($record->id, 4, '0', STR_PAD_LEFT),
                referenceId: $refId
            );

            $now = Carbon::now();
            $record->update([
                'fine_amount' => $fineAmount,
                'fine_status' => 'paid',
                'is_fine_paid' => true,
                'fine_paid_at' => $now,
            ]);

            // Đồng bộ sang bảng borrows mẹ nếu là BorrowRecord
            if ($record instanceof BorrowRecord && $record->user_id) {
                Borrow::where('user_id', $record->user_id)
                    ->whereHas('details', fn($q) => $q->where('book_id', $record->book_id))
                    ->update([
                        'fine_amount' => $fineAmount,
                        'fine_status' => 'paid',
                        'is_fine_paid' => true,
                        'fine_paid_at' => $now,
                    ]);
            }

            // Ghi nhận dòng tiền vào Quỹ Bảo trì & Tu bổ sách
            LibraryFund::create([
                'source_name' => "Doanh thu tiền phạt quá hạn lưu thông (#PKM{$record->id})",
                'amount' => $fineAmount,
                'allocated_for' => 'facility_maintenance',
                'notes' => "Độc giả {$record->borrower_name} thanh toán phí phạt trễ hạn online qua ví số dư",
            ]);

            $msg = "Thanh toán tiền phạt " . number_format($fineAmount, 0, ',', '.') . " VNĐ thành công!";
            return $request->wantsJson() || $request->ajax()
                ? response()->json([
                    'success' => true,
                    'message' => $msg,
                    'balance' => (float) $wallet->balance,
                    'formatted_balance' => number_format($wallet->balance, 0, ',', '.') . ' đ',
                ])
                : redirect()->back()->with('success', $msg);
        });
    }
}
