<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ResetWeeklyLeaderboard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboard:reset-weekly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset bảng xếp hạng hàng tuần và trao 100 Điểm Đổi Quà cho Top 1';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== [CYBER LEADERBOARD] BẮT ĐẦU RESET MÙA GIẢI HÀNG TUẦN ===');

        // 1. Tìm người đứng đầu bảng xếp hạng tuần có điểm weekly_exp > 0
        $topUser = User::where('weekly_exp', '>', 0)
            ->orderByDesc('weekly_exp')
            ->first();

        if ($topUser) {
            // 2. Trao thưởng +100 Điểm Đổi Quà cho Quán quân tuần
            $topUser->increment('reward_points', 100);

            $this->info("[TOP 1] QUÁN QUÂN TUẦN: {$topUser->name} ({$topUser->email})");
            $this->info("[EXP] EXP trong tuần: {$topUser->weekly_exp}");
            $this->info("[REWARD] Đã cộng ngay +100 Điểm Đổi Quà (Số dư mới: {$topUser->reward_points} Điểm).");
        } else {
            $this->comment('[INFO] Tuần này không có độc giả nào phát sinh điểm weekly_exp > 0 để trao thưởng.');
        }

        // 3. Reset toàn bộ weekly_exp của tất cả người dùng về 0
        $resetCount = User::where('weekly_exp', '>', 0)->update(['weekly_exp' => 0]);

        $this->info("[OK] Đã reset weekly_exp của {$resetCount} độc giả về 0.");
        $this->info('=== [CYBER LEADERBOARD] MÙA GIẢI MỚI ĐÃ CHÍNH THỨC BẮT ĐẦU! ===');

        return Command::SUCCESS;
    }
}
