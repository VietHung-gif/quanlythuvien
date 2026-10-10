<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\BorrowRecord;
use App\Models\LibraryFund;
use App\Models\Transaction;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinancialReportController extends Controller
{
    /**
     * Dashboard Quản trị Báo cáo Tài chính & Ngân sách Thư viện
     */
    public function index(?Request $request = null): View
    {
        // 1. Quỹ Cơ sở hạ tầng & Server AI
        $totalServerFund = LibraryFund::where('allocated_for', 'server_cloud')->sum('amount');

        // 2. Tổng Doanh thu Phạt & Hội viên (Dòng tiền tự chủ từ mượn/trả & VIP)
        $totalFineCollected = Transaction::where('type', 'fine_payment')->where('status', 'completed')->sum('amount');
        $totalMembershipFees = Transaction::whereIn('type', ['membership_fee', 'membership_upgrade'])->where('status', 'completed')->sum('amount');
        $totalRevenue = $totalFineCollected + $totalMembershipFees;

        // 3. Tiền cọc sách đang lưu giữ (Escrow/Float - các phiếu đang mượn)
        $totalEscrowDeposit = BorrowRecord::where('status', 'borrowing')->sum('deposit_amount');
        if ($totalEscrowDeposit == 0) {
            $totalEscrowDeposit = Borrow::where('status', 'borrowing')->sum('deposit_amount');
        }

        // 4. Tổng Quỹ tài trợ & Hiến tặng
        $totalSponsorship = LibraryFund::sum('amount');

        // Chi tiết danh sách phân bổ các quỹ
        $funds = LibraryFund::latest()->get();

        // Thống kê phân bổ theo mục đích
        $allocations = LibraryFund::selectRaw('allocated_for, SUM(amount) as total')
            ->groupBy('allocated_for')
            ->pluck('total', 'allocated_for')
            ->toArray();

        // 20 Giao dịch tài chính gần nhất
        $recentTransactions = Transaction::with('user')
            ->latest()
            ->paginate(15);

        return view('admin.finances.index', compact(
            'totalServerFund',
            'totalFineCollected',
            'totalMembershipFees',
            'totalRevenue',
            'totalEscrowDeposit',
            'totalSponsorship',
            'funds',
            'allocations',
            'recentTransactions'
        ));
    }

    /**
     * Ghi nhận thêm khoản tài trợ / hiến tặng từ đối tác, cựu sinh viên, mạnh thường quân
     */
    public function storeSponsorship(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'source_name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:100000'],
            'allocated_for' => ['required', 'in:server_cloud,book_procurement,staff_salary,facility_maintenance'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'source_name.required' => 'Vui lòng nhập tên đơn vị / cá nhân tài trợ.',
            'amount.min' => 'Số tiền tài trợ tối thiểu là 100.000 VNĐ.',
            'allocated_for.required' => 'Vui lòng chọn danh mục phân bổ quỹ.',
        ]);

        $fund = LibraryFund::create($validated);

        // Ghi nhận thêm 1 transaction dạng grant_donation nếu admin là user
        $user = $request->user();
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'grant_donation',
            'amount' => $validated['amount'],
            'status' => 'completed',
            'description' => "Tiếp nhận tài trợ từ {$validated['source_name']}: " . ($validated['notes'] ?? 'Không có ghi chú'),
            'reference_id' => 'FUND_' . $fund->id,
        ]);

        AuditLogger::log(
            action: 'CREATE_SPONSORSHIP',
            description: "Thủ thư {$user->name} đã ghi nhận khoản tài trợ " . number_format($validated['amount'], 0, ',', '.') . " VNĐ từ \"{$validated['source_name']}\".",
            targetType: get_class($fund),
            targetId: $fund->id,
            newValues: $validated
        );

        return redirect()->route('admin.finances.index')->with('success', "Ghi nhận thành công khoản tài trợ " . number_format($validated['amount'], 0, ',', '.') . " VNĐ từ \"{$validated['source_name']}\"!");
    }
}
