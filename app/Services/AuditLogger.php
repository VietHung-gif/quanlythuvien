<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * Ghi lại nhật ký kiểm toán cho các hành động nhạy cảm trong hệ thống.
     *
     * @param string $action Loại hành động: 'APPROVE_BORROW', 'REJECT_BORROW', 'RETURN_BOOK', 'COLLECT_FINE', 'UPDATE_STOCK', 'DELETE_BOOK'...
     * @param string $description Mô tả chi tiết hành động
     * @param string|null $targetType Loại đối tượng bị tác động ('Borrow', 'Book', 'User'...)
     * @param int|string|null $targetId ID đối tượng bị tác động
     * @param array|null $oldValues Dữ liệu trước khi sửa
     * @param array|null $newValues Dữ liệu sau khi sửa
     * @return AuditLog|null
     */
    public static function log(
        string $action,
        string $description,
        ?string $targetType = null,
        $targetId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): ?AuditLog {
        try {
            $userId = Auth::id();
            $ip = Request::ip();
            $userAgent = Request::userAgent();

            return AuditLog::create([
                'user_id' => $userId,
                'action' => $action,
                'target_type' => $targetType,
                'target_id' => $targetId,
                'description' => $description,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
        } catch (\Throwable $e) {
            // Không để lỗi ghi log làm gián đoạn transaction chính của người dùng
            Log::error('Lỗi khi ghi AuditLog: ' . $e->getMessage(), [
                'action' => $action,
                'exception' => $e,
            ]);
            return null;
        }
    }
}
