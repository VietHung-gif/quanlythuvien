<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    /**
     * Hiển thị danh sách Nhật ký kiểm toán hệ thống (Audit Logs) - Độc quyền cho Admin.
     */
    public function index(Request $request): View
    {
        $userId = $request->input('user_id');
        $action = $request->input('action');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $search = $request->input('search');

        $query = AuditLog::with('user')->latest();

        // Lọc theo người thực hiện
        if (!empty($userId)) {
            $query->where('user_id', $userId);
        }

        // Lọc theo loại hành động
        if (!empty($action) && $action !== 'all') {
            $query->where('action', $action);
        }

        // Lọc theo từ khóa mô tả
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Lọc theo khoảng ngày
        if (!empty($fromDate)) {
            $query->whereDate('created_at', '>=', Carbon::parse($fromDate)->toDateString());
        }
        if (!empty($toDate)) {
            $query->whereDate('created_at', '<=', Carbon::parse($toDate)->toDateString());
        }

        // Phân trang 20 dòng
        $logs = $query->paginate(20)->withQueryString();

        // Danh sách các Thủ thư và Admin để lọc
        $staffUsers = User::whereIn('role', ['admin', 'librarian'])->orderBy('name')->get();

        // Danh sách các action có sẵn trong hệ thống
        $actionTypes = [
            'APPROVE_BORROW' => ['label' => 'Duyệt Mượn Sách', 'icon' => '✅'],
            'REJECT_BORROW' => ['label' => 'Từ Chối Mượn', 'icon' => '❌'],
            'RETURN_BOOK' => ['label' => 'Xác Nhận Trả Sách', 'icon' => '🔄'],
            'COLLECT_FINE' => ['label' => 'Thu Tiền Phạt', 'icon' => '💵'],
            'UPDATE_STOCK' => ['label' => 'Cập Nhật Tồn Kho', 'icon' => '📦'],
            'DELETE_BOOK' => ['label' => 'Xóa Đầu Sách', 'icon' => '🗑️'],
            'UPDATE_USER_ROLE' => ['label' => 'Phân Quyền User', 'icon' => '👑'],
        ];

        // Thống kê nhanh
        $stats = [
            'total' => AuditLog::count(),
            'collect_fine' => AuditLog::where('action', 'COLLECT_FINE')->count(),
            'approve_borrow' => AuditLog::where('action', 'APPROVE_BORROW')->count(),
            'update_stock' => AuditLog::where('action', 'UPDATE_STOCK')->count(),
            'today' => AuditLog::whereDate('created_at', Carbon::today())->count(),
        ];

        return view('admin.audit_logs.index', compact(
            'logs',
            'staffUsers',
            'actionTypes',
            'stats',
            'userId',
            'action',
            'fromDate',
            'toDate',
            'search'
        ));
    }
}
