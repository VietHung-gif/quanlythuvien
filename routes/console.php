<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Lịch tự động reset bảng xếp hạng tuần & trao thưởng Top 1 vào 00:00 Chủ Nhật hàng tuần
Schedule::command('leaderboard:reset-weekly')->weeklyOn(0, '00:00');
