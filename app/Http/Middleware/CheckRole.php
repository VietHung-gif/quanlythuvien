<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Kiểm tra quyền hạn (Role Authorization) của người dùng trước khi truy cập Route.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string  ...$roles  Danh sách các quyền được phép (ví dụ: 'admin', 'librarian')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Bắt buộc người dùng phải đăng nhập
        if (! $request->user()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng đăng nhập để thực hiện thao tác này.',
                ], 401);
            }

            return redirect()->route('login');
        }

        // 2. Kiểm tra vai trò của tài khoản
        $allowedRoles = [];
        foreach ($roles as $role) {
            foreach (explode(',', (string) $role) as $r) {
                $trimmed = trim($r);
                if ($trimmed !== '') {
                    $allowedRoles[] = $trimmed;
                }
            }
        }

        if (! in_array($request->user()->role, $allowedRoles, true)) {
            // Nếu là API / AJAX request: Trả về JSON 403 Forbidden
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền thực hiện thao tác này!',
                ], 403);
            }

            // Nếu là truy cập web thông thường: Chuyển hướng về trang chủ kèm flash message lỗi
            return redirect()->route('home')
                ->with('error', 'Bạn không có quyền truy cập khu vực này!');
        }

        return $next($request);
    }
}
