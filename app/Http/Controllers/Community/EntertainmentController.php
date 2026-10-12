<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\BorrowRecord;
use App\Models\ForumComment;
use App\Models\ForumPost;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EntertainmentController extends Controller
{
    /**
     * 1. GIAO DIỆN CHÍNH: CHUỖI THỬ THÁCH ĐỌC SÁCH & BẢNG PHONG THẦN
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        if ($user) {
            $user->updateMartialTitle();
        }

        // Tính toán thông số 6 cấp bậc EXP cao cấp từ User::TIERS
        $isAdminUser = $user && in_array($user->role, ['admin', 'librarian']);
        $exp = $user ? (int) $user->exp : 0;
        $tiers = User::TIERS;
        $currentTierIndex = 0;

        foreach ($tiers as $idx => $t) {
            if ($exp >= $t['min']) {
                $currentTierIndex = $idx;
            }
        }

        if ($isAdminUser) {
            $currentTierIndex = count($tiers) - 1; // Tier 6: ĐỘC CÔ CẦU BẠI
            $exp = 9999999;
        }

        $currentTier = $tiers[$currentTierIndex];
        $isMaxTier = $isAdminUser || ($currentTierIndex === count($tiers) - 1);
        $nextTier = $isMaxTier ? null : $tiers[$currentTierIndex + 1];
        $expNeededForNext = $isMaxTier ? 0 : max(0, $nextTier['min'] - $exp);

        $tierMin = $currentTier['min'];
        $tierMax = $isMaxTier ? 10000 : $nextTier['min'];
        $tierRange = max(1, $tierMax - $tierMin);
        $tierPercent = $isMaxTier ? 100 : min(100, max(0, (int) round((($exp - $tierMin) / $tierRange) * 100)));

        // Dữ liệu danh sách nhiệm vụ phân tầng độ khó (Dễ / Trung Bình / Khó)
        $questsData = $this->getQuestsData($user);
        $allQuests = array_merge($questsData['easy'], $questsData['medium'], $questsData['hard']);
        $totalCompletedQuests = collect($allQuests)->where('is_completed', true)->count();
        $totalQuestsCount = count($allQuests);

        // BẢNG PHONG THẦN: Top 10 độc giả có EXP cao nhất (Loại bỏ tài khoản Quản trị viên để độc giả đua top)
        $topUsers = User::whereNotIn('role', ['admin', 'librarian'])->orderByDesc('exp')->take(10)->get();
        foreach ($topUsers as $u) {
            $u->updateMartialTitle();
        }

        // KHUNG QUẢN TRỊ VIÊN TỐI CAO: Danh sách Ban Điều Hành / Admin
        $adminUsers = User::whereIn('role', ['admin', 'librarian'])->orderBy('role')->orderByDesc('exp')->get();
        foreach ($adminUsers as $adm) {
            $adm->updateMartialTitle();
        }

        $currentUserRank = null;
        if ($user) {
            if ($isAdminUser) {
                $currentUserRank = 'ADMIN';
            } else {
                $currentUserRank = User::whereNotIn('role', ['admin', 'librarian'])->where('exp', '>', $user->exp)->count() + 1;
            }
        }

        $canCheckIn = $user ? $user->canCheckInToday() : false;
        $streak = $user ? (int) Cache::get("user_{$user->id}_checkin_streak", ($user->last_checkin_at && $user->last_checkin_at->isToday()) ? 1 : 0) : 0;

        return view('entertainment.index', compact(
            'user',
            'tiers',
            'currentTier',
            'nextTier',
            'isMaxTier',
            'expNeededForNext',
            'tierPercent',
            'questsData',
            'totalCompletedQuests',
            'totalQuestsCount',
            'topUsers',
            'adminUsers',
            'isAdminUser',
            'currentUserRank',
            'canCheckIn',
            'streak'
        ));
    }

    /**
     * 2. TÍNH TOÁN TIẾN ĐỘ VÀ TRẠNG THÁI TỪNG NHIỆM VỤ THEO 3 NHÓM ĐỘ KHÓ
     */
    protected function getQuestsData(?User $user): array
    {
        // Khi chưa đăng nhập: Trả về mẫu danh sách nhiệm vụ
        if (! $user) {
            return [
                'easy' => [
                    [
                        'id' => 'daily_checkin',
                        'title' => 'Điểm danh hàng ngày',
                        'description' => 'Một chạm nhận ngay phòng đọc và năng lượng học tập.',
                        'reward_exp' => 15,
                        'current' => 0,
                        'target' => 1,
                        'unit' => 'lần',
                        'percent' => 0,
                        'is_completed' => false,
                        'is_claimed' => false,
                        'icon' => 'calendar',
                        'difficulty' => 'easy',
                        'difficulty_label' => 'DỄ',
                        'badge_color' => 'bg-slate-100 text-slate-700 border-slate-300 font-bold',
                        'action_type' => 'auth',
                        'action_text' => 'ĐĂNG NHẬP ĐỂ ĐIỂM DANH',
                        'action_url' => route('login'),
                    ],
                    [
                        'id' => 'daily_view_books',
                        'title' => 'Thị sát kho sách',
                        'description' => 'Bấm xem chi tiết ít nhất 3 đầu sách bất kỳ trong Kho Sách.',
                        'reward_exp' => 20,
                        'current' => 0,
                        'target' => 3,
                        'unit' => 'cuốn sách',
                        'percent' => 0,
                        'is_completed' => false,
                        'is_claimed' => false,
                        'icon' => 'search',
                        'difficulty' => 'easy',
                        'difficulty_label' => 'DỄ',
                        'badge_color' => 'bg-slate-100 text-slate-700 border-slate-300 font-bold',
                        'action_type' => 'link',
                        'action_text' => 'TỚI KHO SÁCH',
                        'action_url' => route('books.index'),
                    ],
                ],
                'medium' => [
                    [
                        'id' => 'medium_borrow_approved',
                        'title' => 'Mượn sách đọc ngay',
                        'description' => 'Tạo và được duyệt ít nhất 1 phiếu mượn sách trong tuần.',
                        'reward_exp' => 50,
                        'current' => 0,
                        'target' => 1,
                        'unit' => 'phiếu duyệt',
                        'percent' => 0,
                        'is_completed' => false,
                        'is_claimed' => false,
                        'icon' => 'book',
                        'difficulty' => 'medium',
                        'difficulty_label' => 'TRUNG BÌNH',
                        'badge_color' => 'bg-blue-100 text-blue-800 border-blue-300 font-black',
                        'action_type' => 'link',
                        'action_text' => 'TỚI KHO SÁCH',
                        'action_url' => route('books.index'),
                    ],
                    [
                        'id' => 'medium_forum_engagement',
                        'title' => 'Đàm đạo tri thức',
                        'description' => 'Viết 1 bài chia sẻ hoặc 2 bình luận thảo luận trong Hội Quán Tri Thức.',
                        'reward_exp' => 60,
                        'current' => 0,
                        'target' => 2,
                        'unit' => 'hoạt động',
                        'percent' => 0,
                        'is_completed' => false,
                        'is_claimed' => false,
                        'icon' => 'chat',
                        'difficulty' => 'medium',
                        'difficulty_label' => 'TRUNG BÌNH',
                        'badge_color' => 'bg-indigo-100 text-indigo-800 border-indigo-300 font-black',
                        'action_type' => 'link',
                        'action_text' => 'TỚI HỘI QUÁN',
                        'action_url' => route('forum.index'),
                    ],
                ],
                'hard' => [
                    [
                        'id' => 'hard_category_specialist',
                        'title' => 'Chuyên sâu lĩnh vực',
                        'description' => 'Mượn từ 3 cuốn sách cùng một thể loại nghiên cứu.',
                        'reward_exp' => 150,
                        'current' => 0,
                        'target' => 3,
                        'unit' => 'cuốn cùng thể loại',
                        'percent' => 0,
                        'is_completed' => false,
                        'is_claimed' => false,
                        'icon' => 'collection',
                        'difficulty' => 'hard',
                        'difficulty_label' => 'THỬ THÁCH',
                        'badge_color' => 'bg-amber-100 text-amber-900 border-amber-400 font-black',
                        'action_type' => 'link',
                        'action_text' => 'MƯỢN SÁCH NGAY',
                        'action_url' => route('books.index'),
                    ],
                    [
                        'id' => 'hard_five_clean_returns',
                        'title' => 'Độc giả chuẩn mực',
                        'description' => 'Hoàn thành 5 lượt mượn trả liên tiếp đúng hạn, không nộp phạt.',
                        'reward_exp' => 250,
                        'current' => 0,
                        'target' => 5,
                        'unit' => 'lượt trả chuẩn',
                        'percent' => 0,
                        'is_completed' => false,
                        'is_claimed' => false,
                        'icon' => 'shield',
                        'difficulty' => 'hard',
                        'difficulty_label' => 'CAO CẤP',
                        'badge_color' => 'bg-orange-100 text-orange-900 border-orange-400 font-black',
                        'action_type' => 'link',
                        'action_text' => 'PHIẾU MƯỢN CỦA TÔI',
                        'action_url' => route('borrows.my'),
                    ],
                    [
                        'id' => 'hard_streak_14_days',
                        'title' => 'Bền bỉ học vấn',
                        'description' => 'Duy trì chuỗi điểm danh liên tục 14 ngày để rèn luyện tinh thần kiên trì.',
                        'reward_exp' => 400,
                        'current' => 0,
                        'target' => 14,
                        'unit' => 'ngày liên tục',
                        'percent' => 0,
                        'is_completed' => false,
                        'is_claimed' => false,
                        'icon' => 'flame',
                        'difficulty' => 'hard',
                        'difficulty_label' => 'CỰC PHẨM',
                        'badge_color' => 'bg-gradient-to-r from-red-600 to-amber-600 text-white font-black shadow-xs',
                        'action_type' => 'auth',
                        'action_text' => 'ĐĂNG NHẬP ĐỂ ĐIỂM DANH',
                        'action_url' => route('login'),
                    ],
                ],
            ];
        }

        $today = now()->toDateString();
        $startOfWeek = now()->startOfWeek();

        // -------------------------------------------------------------
        // NHÓM DỄ (10 - 25 EXP):
        // -------------------------------------------------------------
        // 1. Điểm danh hàng ngày (+15 EXP)
        $hasCheckedInToday = $user->last_checkin_at && $user->last_checkin_at->isToday();
        $dailyCheckinCurrent = $hasCheckedInToday ? 1 : 0;
        $dailyCheckinCompleted = $hasCheckedInToday;
        $dailyCheckinClaimed = $hasCheckedInToday;

        // 2. Thị sát kho sách (+20 EXP - Xem 3 cuốn sách hôm nay)
        $viewedKey = "user_{$user->id}_viewed_books_{$today}";
        $viewedBooks = session($viewedKey, []);
        $viewedCount = min(3, count($viewedBooks));
        $viewBooksCompleted = $viewedCount >= 3;
        $viewBooksClaimed = (bool) Cache::get("quest_claimed_{$user->id}_daily_view_books_{$today}", false);

        // -------------------------------------------------------------
        // NHÓM TRUNG BÌNH (40 - 80 EXP):
        // -------------------------------------------------------------
        // 3. Mượn sách đọc ngay (+50 EXP - Được duyệt 1 phiếu mượn trong tuần)
        $approvedBorrows = Borrow::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'borrowed', 'returned'])
            ->where('created_at', '>=', $startOfWeek)
            ->count();
        if ($approvedBorrows === 0) {
            $approvedBorrows = BorrowRecord::where('user_id', $user->id)
                ->whereIn('status', ['borrowed', 'returned'])
                ->where('created_at', '>=', $startOfWeek)
                ->count();
        }
        $borrowCurrent = min(1, $approvedBorrows);
        $borrowCompleted = $approvedBorrows >= 1;
        $borrowClaimed = (bool) Cache::get("quest_claimed_{$user->id}_medium_borrow_approved_".$startOfWeek->toDateString(), false);

        // 4. Đàm đạo tri thức (+60 EXP - 1 bài viết hoặc 2 bình luận trong tuần)
        $postsCount = ForumPost::where('user_id', $user->id)->where('created_at', '>=', $startOfWeek)->count();
        $commentsCount = ForumComment::where('user_id', $user->id)->where('created_at', '>=', $startOfWeek)->count();
        $forumPoints = ($postsCount * 2) + $commentsCount;
        $forumCurrent = min(2, $forumPoints);
        $forumCompleted = $forumPoints >= 2;
        $forumClaimed = (bool) Cache::get("quest_claimed_{$user->id}_medium_forum_engagement_".$startOfWeek->toDateString(), false);

        // -------------------------------------------------------------
        // NHÓM KHÓ & THỬ THÁCH (150 - 400 EXP):
        // -------------------------------------------------------------
        // 5. Chuyên sâu lĩnh vực (+150 EXP - Mượn từ 3 cuốn cùng 1 thể loại)
        $maxCategoryBooks = (int) (DB::table('borrow_details')
            ->join('borrows', 'borrow_details.borrow_id', '=', 'borrows.id')
            ->join('books', 'borrow_details.book_id', '=', 'books.id')
            ->where('borrows.user_id', $user->id)
            ->whereIn('borrows.status', ['approved', 'borrowed', 'returned'])
            ->groupBy('books.category_id')
            ->selectRaw('COUNT(DISTINCT books.id) as total')
            ->orderByDesc('total')
            ->value('total') ?? 0);

        if ($maxCategoryBooks === 0) {
            $maxCategoryBooks = (int) (DB::table('borrow_records')
                ->join('books', 'borrow_records.book_id', '=', 'books.id')
                ->where('borrow_records.user_id', $user->id)
                ->groupBy('books.category_id')
                ->selectRaw('COUNT(DISTINCT books.id) as total')
                ->orderByDesc('total')
                ->value('total') ?? 0);
        }
        $specialistCurrent = min(3, $maxCategoryBooks);
        $specialistCompleted = $maxCategoryBooks >= 3;
        $specialistClaimed = (bool) Cache::get("quest_claimed_{$user->id}_hard_category_specialist", false);

        // 6. Độc giả chuẩn mực (+250 EXP - 5 lượt trả liên tiếp đúng hạn, không bị phạt)
        $cleanReturnsCount = Borrow::where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('status', 'returned')->orWhere('borrow_status', 'returned');
            })
            ->where(function ($q) {
                $q->whereNull('fine_amount')->orWhere('fine_amount', '<=', 0);
            })
            ->count();
        if ($cleanReturnsCount === 0) {
            $cleanReturnsCount = BorrowRecord::where('user_id', $user->id)
                ->where('status', 'returned')
                ->where(function ($q) {
                    $q->whereNull('fine_amount')->orWhere('fine_amount', '<=', 0);
                })
                ->count();
        }
        $cleanReturnsCurrent = min(5, (int) $cleanReturnsCount);
        $cleanReturnsCompleted = $cleanReturnsCount >= 5;
        $cleanReturnsClaimed = (bool) Cache::get("quest_claimed_{$user->id}_hard_five_clean_returns", false);

        // 7. Bền bỉ học vấn (+400 EXP - Chuỗi điểm danh liên tục 14 ngày)
        $streak = (int) Cache::get("user_{$user->id}_checkin_streak", ($user->last_checkin_at && $user->last_checkin_at->isToday()) ? 1 : 0);
        $streakCurrent = min(14, $streak);
        $streakCompleted = $streak >= 14;
        $streakClaimed = (bool) Cache::get("quest_claimed_{$user->id}_hard_streak_14_days", false);

        return [
            'easy' => [
                [
                    'id' => 'daily_checkin',
                    'title' => 'Điểm danh hàng ngày',
                    'description' => 'Một chạm nhận ngay phòng đọc và năng lượng học tập.',
                    'reward_exp' => 15,
                    'current' => $dailyCheckinCurrent,
                    'target' => 1,
                    'unit' => 'lần',
                    'percent' => $dailyCheckinCompleted ? 100 : 0,
                    'is_completed' => $dailyCheckinCompleted,
                    'is_claimed' => $dailyCheckinClaimed,
                    'icon' => 'calendar',
                    'difficulty' => 'easy',
                    'difficulty_label' => 'DỄ',
                    'badge_color' => 'bg-slate-100 text-slate-700 border-slate-300 font-bold',
                    'action_type' => 'checkin',
                    'action_text' => 'ĐIỂM DANH (+15 EXP)',
                    'action_url' => null,
                ],
                [
                    'id' => 'daily_view_books',
                    'title' => 'Thị sát kho sách',
                    'description' => 'Bấm xem chi tiết ít nhất 3 đầu sách bất kỳ trong Kho Sách.',
                    'reward_exp' => 20,
                    'current' => $viewedCount,
                    'target' => 3,
                    'unit' => 'cuốn sách',
                    'percent' => (int) round(($viewedCount / 3) * 100),
                    'is_completed' => $viewBooksCompleted,
                    'is_claimed' => $viewBooksClaimed,
                    'icon' => 'search',
                    'difficulty' => 'easy',
                    'difficulty_label' => 'DỄ',
                    'badge_color' => 'bg-slate-100 text-slate-700 border-slate-300 font-bold',
                    'action_type' => 'claim',
                    'action_text' => 'TỚI KHO SÁCH',
                    'action_url' => route('books.index'),
                ],
            ],
            'medium' => [
                [
                    'id' => 'medium_borrow_approved',
                    'title' => 'Mượn sách đọc ngay',
                    'description' => 'Tạo và được duyệt ít nhất 1 phiếu mượn sách trong tuần.',
                    'reward_exp' => 50,
                    'current' => $borrowCurrent,
                    'target' => 1,
                    'unit' => 'phiếu duyệt',
                    'percent' => $borrowCompleted ? 100 : 0,
                    'is_completed' => $borrowCompleted,
                    'is_claimed' => $borrowClaimed,
                    'icon' => 'book',
                    'difficulty' => 'medium',
                    'difficulty_label' => 'TRUNG BÌNH',
                    'badge_color' => 'bg-blue-100 text-blue-800 border-blue-300 font-black',
                    'action_type' => 'claim',
                    'action_text' => 'TỚI KHO SÁCH',
                    'action_url' => route('books.index'),
                ],
                [
                    'id' => 'medium_forum_engagement',
                    'title' => 'Đàm đạo tri thức',
                    'description' => 'Viết 1 bài chia sẻ hoặc 2 bình luận thảo luận trong Hội Quán Tri Thức.',
                    'reward_exp' => 60,
                    'current' => $forumCurrent,
                    'target' => 2,
                    'unit' => 'hoạt động',
                    'percent' => (int) round(($forumCurrent / 2) * 100),
                    'is_completed' => $forumCompleted,
                    'is_claimed' => $forumClaimed,
                    'icon' => 'chat',
                    'difficulty' => 'medium',
                    'difficulty_label' => 'TRUNG BÌNH',
                    'badge_color' => 'bg-indigo-100 text-indigo-800 border-indigo-300 font-black',
                    'action_type' => 'claim',
                    'action_text' => 'TỚI HỘI QUÁN',
                    'action_url' => route('forum.index'),
                ],
            ],
            'hard' => [
                [
                    'id' => 'hard_category_specialist',
                    'title' => 'Chuyên sâu lĩnh vực',
                    'description' => 'Mượn từ 3 cuốn sách cùng một thể loại nghiên cứu.',
                    'reward_exp' => 150,
                    'current' => $specialistCurrent,
                    'target' => 3,
                    'unit' => 'cuốn cùng thể loại',
                    'percent' => (int) round(($specialistCurrent / 3) * 100),
                    'is_completed' => $specialistCompleted,
                    'is_claimed' => $specialistClaimed,
                    'icon' => 'collection',
                    'difficulty' => 'hard',
                    'difficulty_label' => 'THỬ THÁCH',
                    'badge_color' => 'bg-amber-100 text-amber-900 border-amber-400 font-black',
                    'action_type' => 'claim',
                    'action_text' => 'MƯỢN SÁCH NGAY',
                    'action_url' => route('books.index'),
                ],
                [
                    'id' => 'hard_five_clean_returns',
                    'title' => 'Độc giả chuẩn mực',
                    'description' => 'Hoàn thành 5 lượt mượn trả liên tiếp đúng hạn, không nộp phạt.',
                    'reward_exp' => 250,
                    'current' => $cleanReturnsCurrent,
                    'target' => 5,
                    'unit' => 'lượt trả chuẩn',
                    'percent' => (int) round(($cleanReturnsCurrent / 5) * 100),
                    'is_completed' => $cleanReturnsCompleted,
                    'is_claimed' => $cleanReturnsClaimed,
                    'icon' => 'shield',
                    'difficulty' => 'hard',
                    'difficulty_label' => 'CAO CẤP',
                    'badge_color' => 'bg-orange-100 text-orange-900 border-orange-400 font-black',
                    'action_type' => 'claim',
                    'action_text' => 'PHIẾU MƯỢN CỦA TÔI',
                    'action_url' => route('borrows.my'),
                ],
                [
                    'id' => 'hard_streak_14_days',
                    'title' => 'Bền bỉ học vấn',
                    'description' => 'Duy trì chuỗi điểm danh liên tục 14 ngày để rèn luyện tinh thần kiên trì.',
                    'reward_exp' => 400,
                    'current' => $streakCurrent,
                    'target' => 14,
                    'unit' => 'ngày liên tục',
                    'percent' => (int) round(($streakCurrent / 14) * 100),
                    'is_completed' => $streakCompleted,
                    'is_claimed' => $streakClaimed,
                    'icon' => 'flame',
                    'difficulty' => 'hard',
                    'difficulty_label' => 'CỰC PHẨM',
                    'badge_color' => 'bg-gradient-to-r from-red-600 to-amber-600 text-white font-black shadow-xs',
                    'action_type' => 'claim',
                    'action_text' => 'ĐIỂM DANH HÀNG NGÀY',
                    'action_url' => null,
                ],
            ],
        ];
    }

    /**
     * 3. XỬ LÝ NHẬN THƯỞNG NHIỆM VỤ (CLAIM REWARD)
     */
    public function claimReward(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để nhận thưởng!',
            ], 401);
        }

        $questId = $request->input('quest_id');
        $questsData = $this->getQuestsData($user);
        $allQuests = array_merge($questsData['easy'], $questsData['medium'], $questsData['hard']);

        $quest = collect($allQuests)->firstWhere('id', $questId);
        if (! $quest) {
            return response()->json([
                'success' => false,
                'message' => 'Nhiệm vụ không tồn tại trong hệ thống!',
            ], 404);
        }

        if ($quest['is_claimed']) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã nhận phần thưởng cho nhiệm vụ này rồi!',
            ], 400);
        }

        if (! $quest['is_completed']) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa hoàn thành đủ tiến trình của nhiệm vụ này!',
            ], 400);
        }

        // Lưu trạng thái đã nhận thưởng vào Cache tương ứng theo độ khó
        if ($quest['difficulty'] === 'easy') {
            $cacheKey = "quest_claimed_{$user->id}_{$questId}_".now()->toDateString();
            Cache::put($cacheKey, true, now()->endOfDay());
        } elseif ($quest['difficulty'] === 'medium') {
            $cacheKey = "quest_claimed_{$user->id}_{$questId}_".now()->startOfWeek()->toDateString();
            Cache::put($cacheKey, true, now()->endOfWeek());
        } else {
            $cacheKey = "quest_claimed_{$user->id}_{$questId}";
            Cache::put($cacheKey, true, now()->addMonths(1)); // Thử thách đặc biệt: Lưu mốc thưởng
        }

        // Cộng điểm EXP cho tài khoản
        $expResult = $user->awardExp((int) $quest['reward_exp']);

        return response()->json([
            'success' => true,
            'message' => "Chúc mừng! Bạn nhận thành công +{$quest['reward_exp']} EXP từ nhiệm vụ \"{$quest['title']}\"!",
            'added_exp' => $quest['reward_exp'],
            'total_exp' => $user->exp,
            'weekly_exp' => $user->weekly_exp,
            'badge' => $user->title_badge,
            'badge_color' => $user->badge_color,
            'badge_icon' => $user->badge_icon,
            'badge_upgraded' => $expResult['badge_upgraded'],
            'new_badge' => $expResult['new_badge'],
            'quest_id' => $questId,
        ]);
    }

    /**
     * 4. CƠ CHẾ ĐIỂM DANH MỖI NGÀY (+15 EXP) & THEO DÕI CHUỖI LIÊN TỤC (STREAK)
     */
    public function checkin(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để điểm danh nhận EXP!',
            ], 401);
        }

        if (! $user->canCheckInToday()) {
            return response()->json([
                'success' => false,
                'message' => 'Hôm nay bạn đã điểm danh rồi, hãy quay lại vào ngày mai nhé!',
                'already_checked_in' => true,
                'can_checkin' => false,
            ], 400);
        }

        // Cập nhật chuỗi điểm danh liên tục (Streak)
        $yesterday = now()->subDay()->toDateString();
        $lastDate = $user->last_checkin_at ? $user->last_checkin_at->toDateString() : null;
        $streakKey = "user_{$user->id}_checkin_streak";
        $currentStreak = (int) Cache::get($streakKey, 0);

        if ($lastDate === $yesterday) {
            $newStreak = $currentStreak + 1;
        } else {
            $newStreak = 1;
        }
        Cache::forever($streakKey, $newStreak);

        // Cộng +15 EXP chuẩn cho nhóm Dễ
        $expResult = $user->awardExp(15);
        $user->last_checkin_at = now();
        $user->save();

        return response()->json([
            'success' => true,
            'message' => "Điểm danh thành công! Bạn nhận được +15 EXP công lực. (Chuỗi ngày: {$newStreak} ngày)",
            'added_exp' => 15,
            'streak' => $newStreak,
            'total_exp' => $user->exp,
            'weekly_exp' => $user->weekly_exp,
            'badge' => $user->title_badge,
            'badge_color' => $user->badge_color,
            'badge_icon' => $user->badge_icon,
            'badge_upgraded' => $expResult['badge_upgraded'],
            'new_badge' => $expResult['new_badge'],
            'can_checkin' => false,
        ]);
    }

    /**
     * 5. BẢNG PHONG THẦN "MỌT SÁCH CHIẾN TƯỚNG" (LEADERBOARD RIÊNG)
     */
    public function leaderboard(Request $request): View
    {
        $tab = $request->input('tab', 'weekly');
        $weeklyTopUsers = User::whereNotIn('role', ['admin', 'librarian'])->orderByDesc('weekly_exp')->take(10)->get();
        $allTimeTopUsers = User::whereNotIn('role', ['admin', 'librarian'])->orderByDesc('exp')->take(10)->get();

        foreach ($weeklyTopUsers as $u) {
            $u->updateMartialTitle();
        }
        foreach ($allTimeTopUsers as $u) {
            $u->updateMartialTitle();
        }

        $adminUsers = User::whereIn('role', ['admin', 'librarian'])->orderBy('role')->orderByDesc('exp')->get();
        foreach ($adminUsers as $adm) {
            $adm->updateMartialTitle();
        }

        $topUsers = ($tab === 'all') ? $allTimeTopUsers : $weeklyTopUsers;
        $currentUser = Auth::user();
        $currentUserRank = null;
        $currentUserWeeklyRank = null;

        if ($currentUser) {
            $currentUser->updateMartialTitle();
            if (in_array($currentUser->role, ['admin', 'librarian'])) {
                $currentUserRank = 'ADMIN';
                $currentUserWeeklyRank = 'ADMIN';
            } else {
                $currentUserRank = User::whereNotIn('role', ['admin', 'librarian'])->where('exp', '>', $currentUser->exp)->count() + 1;
                $currentUserWeeklyRank = User::whereNotIn('role', ['admin', 'librarian'])->where('weekly_exp', '>', $currentUser->weekly_exp)->count() + 1;
            }
        }

        return view('entertainment.leaderboard', compact(
            'topUsers',
            'weeklyTopUsers',
            'allTimeTopUsers',
            'adminUsers',
            'currentUser',
            'currentUserRank',
            'currentUserWeeklyRank',
            'tab'
        ));
    }
}
