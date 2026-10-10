<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Danh sách tài khoản người dùng và phân quyền hệ thống.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $role = $request->input('role', 'all');

        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($role !== 'all', function ($query) use ($role) {
                $query->where('role', $role);
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $totalUsers = User::count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalLibrarians = User::where('role', 'librarian')->count();
        $totalReaders = User::where('role', 'reader')->count();

        return view('admin.users.index', compact(
            'users',
            'search',
            'role',
            'totalUsers',
            'totalAdmins',
            'totalLibrarians',
            'totalReaders'
        ));
    }

    /**
     * Cập nhật phân quyền vai trò người dùng (Admin, Thủ thư, Độc giả).
     */
    public function updateRole(Request $request, User $user): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,librarian,reader',
        ], [
            'role.required' => 'Vui lòng chọn vai trò hợp lệ.',
            'role.in' => 'Vai trò không hợp lệ trong hệ thống.',
        ]);

        // Không cho phép tự hạ quyền của chính mình nếu là Admin
        if ($user->id === $request->user()->id && $validated['role'] !== 'admin') {
            $msg = 'Bạn không thể tự hạ quyền Quản trị viên của chính tài khoản mình đang đăng nhập!';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $oldRole = $user->role_name;
        $oldRoleKey = $user->role;
        $user->update(['role' => $validated['role']]);
        $newRole = $user->role_name;

        // Ghi AuditLog phân quyền người dùng
        AuditLogger::log(
            action: 'UPDATE_USER_ROLE',
            description: "Admin {$request->user()->name} đã thay đổi vai trò của người dùng \"{$user->name}\" từ [{$oldRole}] sang [{$newRole}].",
            targetType: 'User',
            targetId: $user->id,
            oldValues: ['role' => $oldRoleKey, 'role_name' => $oldRole],
            newValues: ['role' => $user->role, 'role_name' => $newRole]
        );

        $message = "Đã cập nhật vai trò cho người dùng \"{$user->name}\" từ [{$oldRole}] thành [{$newRole}] thành công!";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'user_id' => $user->id,
                'new_role' => $user->role,
                'new_role_name' => $newRole,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Xóa tài khoản người dùng (chỉ Admin tối cao).
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return redirect()->back()->with('error', 'Bạn không thể xóa tài khoản của chính mình!');
        }

        // Kiểm tra an toàn: Không cho phép xóa độc giả đang mượn sách hoặc có phiếu chờ duyệt
        if ($user->borrowRecords()->whereIn('status', ['borrowing', 'pending'])->exists()) {
            return redirect()->back()->with('error', "Không thể xóa tài khoản \"{$user->name}\" vì người này đang có sách mượn chưa hoàn trả hoặc phiếu mượn đang chờ duyệt!");
        }

        // Kiểm tra an toàn: Không cho phép xóa độc giả đang có tiền phạt chưa thanh toán
        if ($user->unpaid_fine > 0) {
            $formattedFine = number_format($user->unpaid_fine, 0, ',', '.') . ' VNĐ';
            return redirect()->back()->with('error', "Không thể xóa tài khoản \"{$user->name}\" vì người này đang nợ tiền phạt chưa thanh toán ({$formattedFine})!");
        }

        $userName = $user->name;
        $userEmail = $user->email;
        $userRole = $user->role_name;
        $userId = $user->id;
        $user->delete();

        // Ghi AuditLog xóa người dùng
        AuditLogger::log(
            action: 'DELETE_USER',
            description: "Admin {$request->user()->name} đã xóa tài khoản người dùng \"{$userName}\" ({$userEmail} - {$userRole}).",
            targetType: 'User',
            targetId: $userId,
            oldValues: ['name' => $userName, 'email' => $userEmail, 'role' => $userRole],
            newValues: null
        );

        return redirect()->back()->with('success', "Đã xóa tài khoản \"{$userName}\" khỏi hệ thống thành công!");
    }
}
