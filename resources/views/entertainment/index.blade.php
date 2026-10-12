<x-app-layout>
    <div class="bg-slate-50 min-h-screen py-8" x-data="questsApp()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- THÔNG BÁO FLASH TOAST (ALPINE.JS) -->
            <div x-show="toast.show" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-4"
                 class="fixed top-6 right-6 z-50 max-w-md bg-white border-2 rounded-2xl shadow-2xl p-4 flex items-center gap-3"
                 :class="toast.type === 'success' ? 'border-emerald-300 text-emerald-950' : 'border-rose-300 text-rose-950'"
                 style="display: none;">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                     :class="toast.type === 'success' ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600'">
                    <template x-if="toast.type === 'success'">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </template>
                    <template x-if="toast.type !== 'success'">
                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </template>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-black uppercase tracking-wider" x-text="toast.type === 'success' ? 'THÀNH CÔNG' : 'THÔNG BÁO'"></p>
                    <p class="text-xs font-semibold text-slate-700 mt-0.5" x-text="toast.message"></p>
                </div>
                <button type="button" @click="toast.show = false" class="text-slate-400 hover:text-slate-700 p-1 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- ========================================================================= -->
            <!-- 1. KHỐI CẤP BẬC ĐỘC GIẢ (USER TIER & EXP PROGRESS SUMMARY)                  -->
            <!-- ========================================================================= -->
            <div class="bg-white border-2 border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs relative overflow-hidden">
                <!-- Nền trang trí mờ -->
                <div class="absolute -right-16 -top-16 w-64 h-64 bg-blue-50/70 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-slate-100">
                    <!-- Bên trái: Tiêu đề & Giới thiệu -->
                    <div class="space-y-1.5 max-w-2xl">
                        <span class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 text-[11px] font-black uppercase tracking-wider px-3 py-1 rounded-lg border border-blue-200 shadow-2xs">
                            <svg class="w-3.5 h-3.5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                            <span>HỆ THỐNG CẤP BẬC & TIẾN TRÌNH ĐỘC GIẢ</span>
                        </span>
                        
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-950 uppercase tracking-tight">
                            TIẾN TRÌNH TU LUYỆN TRI THỨC
                        </h1>
                        
                        <p class="text-slate-500 text-xs sm:text-sm font-medium leading-relaxed">
                            Vượt qua các thử thách từ Dễ đến Cực phẩm để tích lũy lượng EXP đột phá, thăng hạng đẳng cấp và khẳng định vị thế trên Bảng Phong Thần!
                        </p>
                    </div>

                    <!-- Bên phải: Nút Điểm danh & Chuỗi ngày liên tục -->
                    <div class="shrink-0 flex flex-col sm:flex-row items-center gap-3">
                        @auth
                            <div class="bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold px-3.5 py-2.5 rounded-2xl flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>
                                <span>Chuỗi điểm danh: <strong class="text-amber-600 font-black">{{ $streak }} ngày</strong></span>
                            </div>

                            <template x-if="canCheckIn">
                                <button type="button" 
                                        @click="doCheckin()"
                                        :disabled="isSubmitting"
                                        class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-black text-xs uppercase tracking-wider px-6 py-3.5 rounded-2xl shadow-sm hover:shadow-md transition flex items-center justify-center gap-2.5 cursor-pointer">
                                    <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span x-show="!isSubmitting">ĐIỂM DANH HÀNG NGÀY (+15 EXP)</span>
                                    <span x-show="isSubmitting">ĐANG XỬ LÝ...</span>
                                </button>
                            </template>
                            <template x-if="!canCheckIn">
                                <div class="bg-emerald-50 border-2 border-emerald-200 text-emerald-800 font-bold text-xs uppercase tracking-wider px-5 py-3 rounded-2xl flex items-center gap-2 shadow-2xs">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>HÔM NAY ĐÃ ĐIỂM DANH (+15 EXP)</span>
                                </div>
                            </template>
                        @else
                            <a href="{{ route('login') }}" 
                               class="w-full sm:w-auto bg-slate-900 hover:bg-blue-600 text-white font-black text-xs uppercase tracking-wider px-6 py-3.5 rounded-2xl shadow-sm transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                <span>ĐĂNG NHẬP ĐỂ ĐIỂM DANH (+15 EXP)</span>
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- 6 MỐC CẤP BẬC CAO CẤP (TIER MILESTONES) -->
                <div class="mt-6 space-y-4">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2.5">
                        @foreach($tiers as $idx => $t)
                            @php
                                $isCurrentTier = ($currentTier['name'] === $t['name']);
                                $isPassed = ($user && $user->exp >= $t['min']);
                            @endphp
                            <div class="p-3 rounded-2xl border-2 transition text-left flex items-center gap-2.5 
                                        {{ $isCurrentTier 
                                            ? 'bg-blue-50/90 border-blue-500 shadow-xs ring-1 ring-blue-400' 
                                            : ($isPassed ? 'bg-slate-50 border-slate-200' : 'bg-white border-slate-100 opacity-60') }}">
                                <span class="w-6 h-6 shrink-0 inline-flex items-center justify-center">{!! $t['icon'] !!}</span>
                                <div class="min-w-0">
                                    <div class="font-black text-[11px] uppercase tracking-wider truncate {{ $isCurrentTier ? 'text-blue-950 font-black' : 'text-slate-800' }}">
                                        {{ $t['name'] }}
                                    </div>
                                    <div class="text-[10px] text-slate-500 font-semibold truncate">
                                        {{ number_format($t['min']) }}+ EXP
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Thanh Progress Bar thăng cấp lớn -->
                    <div class="pt-2">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between text-xs font-bold text-slate-700 gap-1.5 mb-1.5">
                            <div>
                                Cấp hiện tại: 
                                @if($user && in_array($user->role, ['admin', 'librarian']))
                                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-600 via-amber-600 to-yellow-500 font-black uppercase tracking-tight">
                                        [ ĐỘC CÔ CẦU BẠI - QUẢN TRỊ VIÊN ]
                                    </span>
                                @else
                                    <span class="text-blue-600 font-black uppercase tracking-tight" x-text="'[ ' + userTierName + ' ]'">
                                        [ {{ $currentTier['name'] }} ]
                                    </span>
                                @endif
                                <span class="text-slate-400 mx-1">•</span>
                                <span>Hiện có: <strong class="text-slate-900 font-black" x-text="userExp.toLocaleString()">{{ $user ? number_format((int)$user->exp) : 0 }}</strong> EXP</span>
                            </div>

                            <div>
                                @if($user && in_array($user->role, ['admin', 'librarian']))
                                    <span class="inline-flex items-center gap-1.5 text-amber-600 font-black uppercase tracking-wide">
                                        <svg class="w-4 h-4 text-amber-500 fill-current" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                                        <span>ĐẲNG CẤP TỐI THƯỢNG - QUẢN TRỊ HỆ THỐNG (MAX LEVEL)</span>
                                    </span>
                                @else
                                    <template x-if="!isMaxTier">
                                        <span class="text-slate-500 font-semibold">
                                            Còn thiếu <strong class="text-blue-700 font-black" x-text="expNeeded.toLocaleString()">{{ number_format($expNeededForNext) }}</strong> EXP để đạt <strong class="text-slate-900 uppercase" x-text="'[' + nextTierName + ']'">[{{ $nextTier ? $nextTier['name'] : '' }}]</strong>
                                        </span>
                                    </template>
                                    <template x-if="isMaxTier">
                                        <span class="text-amber-600 font-black uppercase inline-flex items-center gap-1">
                                            <svg class="w-4 h-4 text-amber-500 fill-current" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                                            <span>ĐÃ ĐẠT CẤP ĐỘ CAO NHẤT [ ĐỘC CÔ CẦU BẠI ]</span>
                                        </span>
                                    </template>
                                @endif
                            </div>
                        </div>

                        <!-- Vỏ thanh tiến trình -->
                        <div class="h-4 bg-slate-100 rounded-full overflow-hidden p-0.5 border border-slate-200 relative shadow-inner">
                            @if($user && in_array($user->role, ['admin', 'librarian']))
                                <div class="h-full bg-gradient-to-r from-red-600 via-amber-500 to-yellow-400 rounded-full shadow-lg animate-pulse"
                                     style="width: 100%;">
                                </div>
                            @else
                                <div class="h-full bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-full transition-all duration-700 ease-out"
                                     :style="'width: ' + userTierPercent + '%'"
                                     style="width: {{ $tierPercent }}%;">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- 2. BANNER MÙA GIẢI & ĐẾM NGƯỢC (WEEKLY SEASON BANNER)                     -->
            <!-- ========================================================================= -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200/60 rounded-2xl p-4 flex flex-col md:flex-row items-center justify-between gap-3 shadow-sm">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-white border border-blue-200 flex items-center justify-center shadow-xs shrink-0">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V4a2 2 0 112 2h-2zm0 0V4a2 2 0 10-2 2h2zm-7 5h14a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-blue-600 text-white">MÙA GIẢI HÀNG TUẦN</span>
                            <span class="text-xs text-blue-700 font-bold">Tự động đặt lại vào 00:00 Chủ Nhật</span>
                        </div>
                        <div class="text-xs sm:text-sm font-bold text-slate-900 mt-0.5">
                            Đua Top 1 tuần nhận ngay <span class="text-emerald-700 font-black underline decoration-2">100 Điểm Đổi Quà</span> cùng đặc quyền mượn sách không giới hạn!
                        </div>
                    </div>
                </div>

                <!-- Đồng hồ đếm ngược -->
                <div class="bg-white border border-blue-200 px-4 py-2 rounded-xl flex items-center gap-3 shrink-0 shadow-xs">
                    <div class="text-right">
                        <span class="text-[10px] font-bold text-slate-500 uppercase block leading-none">Thời gian còn lại</span>
                        <span class="font-mono font-bold text-blue-700 text-sm" x-text="timeLeftString">Đang tính...</span>
                    </div>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- 3. KHỐI THÂN CHÍNH: CỘT TRÁI (NHIỆM VỤ PHÂN CẤP) & CỘT PHẢI (BẢNG PHONG THẦN) -->
            <!-- ========================================================================= -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <!-- --------------------------------------------------------------------- -->
                <!-- CỘT TRÁI (65% ~ lg:col-span-8): BẢNG NHIỆM VỤ PHÂN TẦNG ĐỘ KHÓ       -->
                <!-- --------------------------------------------------------------------- -->
                <div class="lg:col-span-8 space-y-6">

                    <!-- Thanh Tab Filter Phân Loại Nhiệm Vụ -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200">
                        <div>
                            <h2 class="text-xl font-black text-slate-950 uppercase tracking-tight flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><circle cx="12" cy="12" r="6" stroke-width="2"/><circle cx="12" cy="12" r="2" stroke-width="2"/></svg>
                                HỆ THỐNG NHIỆM VỤ & THỬ THÁCH
                            </h2>
                            <p class="text-xs text-slate-500 font-medium mt-0.5">
                                Hoàn thành từ nhiệm vụ dễ đến thử thách khó để nhận EXP tương xứng
                            </p>
                        </div>
                        
                        <!-- Bộ Tab Lọc -->
                        <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl">
                            <button type="button" 
                                    @click="activeTab = 'all'"
                                    :class="activeTab === 'all' ? 'bg-white text-blue-600 font-black shadow-xs' : 'text-slate-600 font-bold hover:text-slate-900'"
                                    class="px-3 py-1.5 rounded-lg text-xs transition cursor-pointer">
                                TẤT CẢ ({{ $totalQuestsCount }})
                            </button>
                            <button type="button" 
                                    @click="activeTab = 'easy'"
                                    :class="activeTab === 'easy' ? 'bg-white text-blue-600 font-black shadow-xs' : 'text-slate-600 font-bold hover:text-slate-900'"
                                    class="px-3 py-1.5 rounded-lg text-xs transition cursor-pointer">
                                DỄ ({{ count($questsData['easy']) }})
                            </button>
                            <button type="button" 
                                    @click="activeTab = 'medium'"
                                    :class="activeTab === 'medium' ? 'bg-white text-blue-600 font-black shadow-xs' : 'text-slate-600 font-bold hover:text-slate-900'"
                                    class="px-3 py-1.5 rounded-lg text-xs transition cursor-pointer">
                                TRUNG BÌNH ({{ count($questsData['medium']) }})
                            </button>
                            <button type="button" 
                                    @click="activeTab = 'hard'"
                                    :class="activeTab === 'hard' ? 'bg-white text-blue-600 font-black shadow-xs' : 'text-slate-600 font-bold hover:text-slate-900'"
                                    class="px-3 py-1.5 rounded-lg text-xs transition cursor-pointer">
                                KHÓ ({{ count($questsData['hard']) }})
                            </button>
                        </div>
                    </div>

                    <!-- 1. PHÂN KHU: NHIỆM VỤ HÀNG NGÀY (DỄ - 10 - 25 EXP) -->
                    <div x-show="activeTab === 'all' || activeTab === 'easy'" class="space-y-3.5">
                        <div class="flex items-center justify-between">
                            <span class="bg-slate-100 text-slate-800 text-[11px] font-black uppercase tracking-wider px-3 py-1 rounded-lg border border-slate-300 inline-flex items-center gap-1.5 shadow-2xs">
                                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                NHIỆM VỤ HÀNG NGÀY (DỄ • 10 - 25 EXP)
                            </span>
                            <span class="text-[11px] text-slate-400 font-semibold">Tự động làm mới vào 00:00</span>
                        </div>

                        <div class="grid grid-cols-1 gap-3.5">
                            @foreach($questsData['easy'] as $quest)
                                @include('entertainment.partials.quest_card', ['quest' => $quest])
                            @endforeach
                        </div>
                    </div>

                    <!-- 2. PHÂN KHU: NHIỆM VỤ TUẦN (TRUNG BÌNH - 40 - 80 EXP) -->
                    <div x-show="activeTab === 'all' || activeTab === 'medium'" class="space-y-3.5 pt-2">
                        <div class="flex items-center justify-between">
                            <span class="bg-blue-50 text-blue-800 text-[11px] font-black uppercase tracking-wider px-3 py-1 rounded-lg border border-blue-200 inline-flex items-center gap-1.5 shadow-2xs">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                NHIỆM VỤ TUẦN (TRUNG BÌNH • 40 - 80 EXP)
                            </span>
                            <span class="text-[11px] text-slate-400 font-semibold">Làm mới vào thứ Hai hàng tuần</span>
                        </div>

                        <div class="grid grid-cols-1 gap-3.5">
                            @foreach($questsData['medium'] as $quest)
                                @include('entertainment.partials.quest_card', ['quest' => $quest])
                            @endforeach
                        </div>
                    </div>

                    <!-- 3. PHÂN KHU: THỬ THÁCH ĐẶC BIỆT (KHÓ - 150 - 400 EXP) -->
                    <div x-show="activeTab === 'all' || activeTab === 'hard'" class="space-y-3.5 pt-2">
                        <div class="flex items-center justify-between">
                            <span class="bg-gradient-to-r from-red-50 via-amber-50 to-orange-50 text-red-900 text-[11px] font-black uppercase tracking-wider px-3 py-1 rounded-lg border border-red-200 inline-flex items-center gap-1.5 shadow-2xs">
                                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                THỬ THÁCH ĐẶC BIỆT (KHÓ & ĐỘT PHÁ • 150 - 400 EXP)
                            </span>
                            <span class="text-[11px] text-amber-700 font-bold">Thưởng lượng EXP cực lớn</span>
                        </div>

                        <div class="grid grid-cols-1 gap-3.5">
                            @foreach($questsData['hard'] as $quest)
                                @include('entertainment.partials.quest_card', ['quest' => $quest])
                            @endforeach
                        </div>
                    </div>

                </div>

                <!-- --------------------------------------------------------------------- -->
                <!-- CỘT PHẢI (35% ~ lg:col-span-4): TÀI KHOẢN, BAN QUẢN TRỊ & BẢNG PHONG THẦN -->
                <!-- --------------------------------------------------------------------- -->
                <div class="lg:col-span-4 space-y-5">

                    <!-- ================================================================= -->
                    <!-- 1. THẺ TỔNG QUAN TÀI KHOẢN CỦA BẠN (MY STATUS CARD)                 -->
                    <!-- ================================================================= -->
                    @auth
                        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm space-y-3.5">
                            <div class="flex items-center justify-between text-xs font-bold text-slate-500 uppercase tracking-wider">
                                <span class="flex items-center gap-1.5 text-slate-700 font-extrabold">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    TÀI KHOẢN CỦA BẠN
                                </span>
                                @if($isAdminUser)
                                    <span class="text-[10px] bg-rose-50 text-rose-700 border border-rose-200 px-2 py-0.5 rounded-md font-bold">ADMIN / THỦ THƯ</span>
                                @else
                                    <span class="text-[10px] bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded-md font-bold">Hạng #{{ $currentUserRank ?? '?' }}</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full overflow-hidden bg-slate-100 border border-slate-200 shrink-0 flex items-center justify-center font-bold text-slate-700 text-xs shadow-xs">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ mb_substr($user->name, 0, 1, 'UTF-8') }}
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-sm font-extrabold text-slate-900 truncate">
                                        {{ $user->name }}
                                    </div>
                                    <div class="text-xs text-slate-500 font-semibold truncate flex items-center gap-1 mt-0.5">
                                        <span>{!! $user->badge_icon !!}</span>
                                        <span class="text-blue-700 font-bold">{{ $isAdminUser ? 'Độc Cô Cầu Bại' : ($user->title_badge ?: 'Tân Thủ Nhập Môn') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Thống kê dạng 2 cột số liệu rõ ràng: [EXP Tuần này] và [Điểm đổi quà] -->
                            <div class="grid grid-cols-2 gap-2.5 pt-0.5">
                                <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-2.5 text-center">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase block">EXP Tuần này</span>
                                    <span class="text-base font-black text-blue-700 font-mono">
                                        {{ $isAdminUser ? '9,999,999' : number_format($user->weekly_exp ?? 0) }}
                                    </span>
                                </div>
                                <div class="bg-emerald-50/70 border border-emerald-200 rounded-xl p-2.5 text-center">
                                    <span class="text-[10px] font-bold text-emerald-700 uppercase block">Điểm đổi quà</span>
                                    <span class="text-base font-black text-emerald-700 font-mono flex items-center justify-center gap-1.5">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V4a2 2 0 112 2h-2zm0 0V4a2 2 0 10-2 2h2zm-7 4h14v9a2 2 0 01-2 2H7a2 2 0 01-2-2v-9z"/></svg>
                                        <span>{{ number_format($user->reward_points ?? 0) }}</span>
                                    </span>
                                </div>
                            </div>

                            <!-- Nút MƯỢN SÁCH (+50 EXP) và ĐIỂM DANH -->
                            <div class="grid grid-cols-2 gap-2 pt-0.5">
                                <a href="{{ route('books.index') }}" 
                                   class="h-10 px-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition flex items-center justify-center gap-1.5 shadow-2xs">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    <span>MƯỢN SÁCH (+50)</span>
                                </a>
                                <template x-if="canCheckIn">
                                    <button type="button" 
                                            @click="doCheckin()"
                                            :disabled="isSubmitting"
                                            class="h-10 px-3 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition flex items-center justify-center gap-1.5 cursor-pointer shadow-2xs">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        <span x-show="!isSubmitting">ĐIỂM DANH</span>
                                        <span x-show="isSubmitting">...</span>
                                    </button>
                                </template>
                                <template x-if="!canCheckIn">
                                    <div class="h-10 px-2 bg-slate-100 border border-slate-200 text-slate-500 font-bold text-[11px] uppercase tracking-wider rounded-xl flex items-center justify-center gap-1.5 select-none">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>ĐÃ ĐIỂM DANH</span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    @else
                        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm text-center space-y-2">
                            <p class="text-xs text-slate-600 font-medium">
                                Đăng nhập để xem vị thế cá nhân, tích lũy EXP đua top và nhận quà!
                            </p>
                            <a href="{{ route('login') }}" 
                               class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase px-4 py-2 rounded-xl transition shadow-2xs">
                                ĐĂNG NHẬP NGAY
                            </a>
                        </div>
                    @endauth

                    <!-- ================================================================= -->
                    <!-- 2. THẺ THÔNG TIN BAN QUẢN TRỊ ("BAN ĐIỀU HÀNH HỆ THỐNG")           -->
                    <!-- ================================================================= -->
                    @if(isset($adminUsers) && $adminUsers->isNotEmpty())
                        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                            <!-- Tiêu đề nhỏ gọn ở trên -->
                            <div class="flex items-center justify-between text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">
                                <span class="flex items-center gap-1.5 text-slate-700 font-extrabold">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    BAN QUẢN TRỊ TỐI CAO
                                </span>
                                <span class="text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full font-semibold">Đặc quyền hệ thống • Không đua rank</span>
                            </div>

                            <!-- Danh sách Admin/Thủ thư -->
                            <div class="space-y-2.5">
                                @foreach($adminUsers as $adm)
                                    <div class="p-2.5 rounded-xl border border-slate-200 bg-slate-50/60 hover:bg-slate-100/80 transition flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-3 min-w-0 flex-1">
                                            <!-- Avatar tròn kích thước vừa vặn (w-10 h-10) -->
                                            <div class="w-10 h-10 rounded-full overflow-hidden bg-white border border-slate-300 shrink-0 flex items-center justify-center font-bold text-slate-700 text-xs shadow-xs">
                                                @if($adm->avatar)
                                                    <img src="{{ asset('storage/' . $adm->avatar) }}" alt="{{ $adm->name }}" class="w-full h-full object-cover">
                                                @else
                                                    {{ mb_substr($adm->name, 0, 1, 'UTF-8') }}
                                                @endif
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                <!-- Tên tài khoản in đậm rõ nét & Huy hiệu vai trò -->
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-sm font-extrabold text-slate-900 truncate">{{ $adm->name }}</span>
                                                    @if($adm->role === 'admin')
                                                        <span class="bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-bold px-2 py-0.5 rounded-md shrink-0">ADMIN</span>
                                                    @else
                                                        <span class="bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-bold px-2 py-0.5 rounded-md shrink-0">THỦ THƯ</span>
                                                    @endif
                                                </div>
                                                <!-- Danh hiệu & EXP hiển thị 1 dòng duy nhất -->
                                                <div class="text-xs text-slate-500 font-semibold truncate mt-0.5 whitespace-nowrap">
                                                    Độc Cô Cầu Bại • 9,999,999 EXP
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-right shrink-0">
                                            <span class="text-xs font-bold text-amber-600 font-mono inline-flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                                                TỐI THƯỢNG
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- BẢNG PHONG THẦN ĐỘC GIẢ (CHỈ GỒM ĐỘC GIẢ THỰC THỤ) -->
                    <div class="bg-white border-2 border-slate-200 rounded-3xl p-6 shadow-xs sticky top-24 space-y-5">
                        
                        <div class="pb-3 border-b border-slate-100">
                            <span class="text-[10px] font-black uppercase tracking-wider text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded-md border border-amber-200 inline-flex items-center gap-1">
                                <svg class="w-3 h-3 text-amber-500" fill="currentColor" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                                CHIẾN TƯỚNG TRI THỨC
                            </span>
                            <h2 class="text-xl font-black text-slate-950 uppercase tracking-tight mt-1 flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.286L13 21l-2.286-6.857L5 12l5.714-2.286L13 3z"/></svg>
                                BẢNG PHONG THẦN
                            </h2>
                            <p class="text-xs text-slate-500 font-medium mt-0.5">
                                Vinh danh 10 độc giả đua TOP có công lực EXP cao nhất toàn thư viện
                            </p>
                        </div>

                        <!-- Danh sách Top 10 Độc GiẢ -->
                        <div class="space-y-2.5">
                            @forelse($topUsers as $index => $topUser)
                                @php
                                    $rank = $index + 1;
                                    $isTop1 = ($rank === 1);
                                    $isTop2 = ($rank === 2);
                                    $isTop3 = ($rank === 3);
                                    $isMe = ($user && $user->id === $topUser->id);
                                @endphp

                                <div class="p-3 rounded-2xl border-2 transition flex items-center justify-between gap-3
                                            {{ $isTop1 ? 'bg-amber-50/70 border-amber-300 shadow-2xs' : '' }}
                                            {{ $isTop2 ? 'bg-slate-50/90 border-slate-300' : '' }}
                                            {{ $isTop3 ? 'bg-orange-50/40 border-amber-200' : '' }}
                                            {{ !$isTop1 && !$isTop2 && !$isTop3 ? 'bg-white border-slate-100 hover:border-slate-200' : '' }}
                                            {{ $isMe ? 'ring-2 ring-blue-500 font-bold' : '' }}">
                                    
                                    <div class="flex items-center gap-3 min-w-0">
                                        <!-- Thứ hạng -->
                                        <div class="w-7 text-center shrink-0">
                                            @if($isTop1)
                                                <span class="w-6 h-6 rounded-full bg-amber-400 text-amber-950 font-black text-xs inline-flex items-center justify-center shadow-xs">1</span>
                                            @elseif($isTop2)
                                                <span class="w-6 h-6 rounded-full bg-slate-300 text-slate-800 font-black text-xs inline-flex items-center justify-center shadow-xs">2</span>
                                            @elseif($isTop3)
                                                <span class="w-6 h-6 rounded-full bg-amber-600 text-white font-black text-xs inline-flex items-center justify-center shadow-xs">3</span>
                                            @else
                                                <span class="text-xs font-black text-slate-400">#{{ $rank }}</span>
                                            @endif
                                        </div>

                                        <!-- Avatar lớn -->
                                        <div class="w-10 h-10 rounded-full overflow-hidden bg-slate-100 border-2 {{ $topUser->avatar_glow_frame }} shrink-0 flex items-center justify-center">
                                            @if($topUser->avatar)
                                                <img src="{{ asset('storage/' . $topUser->avatar) }}" 
                                                     alt="{{ $topUser->name }}" 
                                                     class="w-full h-full object-cover">
                                            @else
                                                <span class="text-xs font-black text-slate-600">
                                                    {{ mb_substr($topUser->name, 0, 1, 'UTF-8') }}
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Tên & Bậc danh hiệu chuẩn 6 cấp -->
                                        <div class="min-w-0">
                                            <div class="text-xs font-black text-slate-900 truncate uppercase flex items-center gap-1.5">
                                                <span>{{ $topUser->name }}</span>
                                                @if($isMe)
                                                    <span class="text-[9px] text-blue-600 bg-blue-100 px-1.5 py-0.2 rounded font-black">BẠN</span>
                                                @endif
                                            </div>
                                            <div class="text-[10px] text-slate-500 font-bold truncate flex items-center gap-1 mt-0.5">
                                                {!! $topUser->badge_icon !!} <span>{{ $topUser->title_badge ?: 'Tân Thủ Nhập Môn' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- EXP khủng -->
                                    <div class="text-right shrink-0">
                                        <span class="text-xs font-black {{ $isTop1 ? 'text-amber-800' : 'text-slate-900' }}">
                                            {{ number_format($topUser->exp) }}
                                        </span>
                                        <span class="text-[10px] text-slate-400 font-bold block -mt-0.5">EXP</span>
                                    </div>
                                </div>
                            @empty
                                <div class="p-6 text-center text-xs text-slate-400 font-medium">
                                    Chưa có dữ liệu độc giả trên Bảng Phong Thần
                                </div>
                            @endforelse
                        </div>

                        <!-- THẺ VỊ TRÍ CỦA BẠN DƯỚI BẢNG XẾP HẠNG -->
                        <div class="pt-2">
                            @auth
                                @if($isAdminUser)
                                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3 flex items-center justify-between gap-3 shadow-2xs">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span class="bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-bold px-2 py-0.5 rounded-md shrink-0 inline-flex items-center gap-1">
                                                <svg class="w-3 h-3 text-rose-600" fill="currentColor" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                                                ADMIN
                                            </span>
                                            <span class="text-xs font-bold text-slate-700 truncate">Vị thế Quản trị viên (Miễn đua rank)</span>
                                        </div>
                                        <span class="text-xs font-bold text-amber-600 font-mono shrink-0">9,999,999 EXP</span>
                                    </div>
                                @else
                                    <div class="bg-blue-50/90 border-2 border-blue-300 rounded-2xl p-3.5 flex items-center justify-between gap-3 shadow-2xs">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <span class="bg-blue-600 text-white font-black text-xs px-2.5 py-1 rounded-lg shrink-0">
                                                #{{ $currentUserRank ?? '?' }}
                                            </span>

                                            <div class="w-9 h-9 rounded-full overflow-hidden bg-white border border-blue-300 shrink-0 flex items-center justify-center">
                                                @if($user->avatar)
                                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                                @else
                                                    <span class="text-xs font-black text-blue-700">
                                                        {{ mb_substr($user->name, 0, 1, 'UTF-8') }}
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="min-w-0">
                                                <div class="text-xs font-black text-blue-950 uppercase truncate">
                                                    VỊ TRÍ CỦA BẠN
                                                </div>
                                                <div class="text-[10px] text-blue-700 font-bold truncate" x-text="userBadge">
                                                    {{ $user->title_badge }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-right shrink-0">
                                            <span class="text-xs font-black text-blue-900" x-text="userExp.toLocaleString() + ' EXP'">
                                                {{ number_format($user->exp) }} EXP
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3.5 text-center space-y-2">
                                    <p class="text-xs text-slate-500 font-medium">
                                        Đăng nhập để xem thứ hạng chính xác của bạn trên Bảng Phong Thần!
                                    </p>
                                    <a href="{{ route('login') }}" 
                                       class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase px-4 py-2 rounded-xl transition">
                                        ĐĂNG NHẬP NGAY
                                    </a>
                                </div>
                            @endauth
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- SCRIPT ALPINE.JS XỬ LÝ NHIỆM VỤ, ĐIỂM DANH & NHẬN THƯỞNG AJAX -->
    <script>
        function questsApp() {
            return {
                userExp: {{ $user ? (int)$user->exp : 0 }},
                userBadge: '{{ $isAdminUser ? "Độc Cô Cầu Bại" : $currentTier['badge'] }}',
                userTierName: '{{ $isAdminUser ? "ĐỘC CÔ CẦU BẠI" : $currentTier['name'] }}',
                nextTierName: '{{ $nextTier ? $nextTier['name'] : '' }}',
                userTierPercent: {{ $tierPercent }},
                expNeeded: {{ $expNeededForNext }},
                isMaxTier: {{ $isMaxTier ? 'true' : 'false' }},
                canCheckIn: {{ $canCheckIn ? 'true' : 'false' }},
                activeTab: 'all',
                isSubmitting: false,
                timeLeftString: 'Đang tính...',
                toast: {
                    show: false,
                    message: '',
                    type: 'success'
                },

                init() {
                    this.updateCountdown();
                    setInterval(() => this.updateCountdown(), 1000);
                },

                updateCountdown() {
                    const now = new Date();
                    const nextSunday = new Date();
                    const day = now.getDay();
                    const daysUntilSunday = (day === 0) ? 7 : (7 - day);
                    nextSunday.setDate(now.getDate() + daysUntilSunday);
                    nextSunday.setHours(0, 0, 0, 0);

                    const diff = nextSunday - now;
                    if (diff <= 0) {
                        this.timeLeftString = '00 ngày 00:00:00';
                        return;
                    }

                    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                    const pad = (n) => String(n).padStart(2, '0');
                    this.timeLeftString = `${days} ngày ${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
                },

                showToast(msg, type = 'success') {
                    this.toast.message = msg;
                    this.toast.type = type;
                    this.toast.show = true;
                    setTimeout(() => {
                        this.toast.show = false;
                    }, 4000);
                },

                // Điểm danh nhận thưởng nhanh
                async doCheckin() {
                    if (this.isSubmitting) return;
                    this.isSubmitting = true;

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        const res = await fetch('{{ route('entertainment.checkin') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });

                        const data = await res.json();
                        if (data.success) {
                            this.userExp = data.total_exp;
                            this.userBadge = data.badge;
                            this.canCheckIn = false;
                            this.showToast(data.message, 'success');
                            setTimeout(() => {
                                window.location.reload();
                            }, 1200);
                        } else {
                            this.showToast(data.message || 'Không thể điểm danh!', 'error');
                        }
                    } catch (err) {
                        this.showToast('Lỗi kết nối máy chủ, vui lòng thử lại!', 'error');
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                // Nhận thưởng cho nhiệm vụ đã hoàn thành
                async claimReward(questId, rewardExp, title) {
                    if (this.isSubmitting) return;
                    this.isSubmitting = true;

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        const res = await fetch('{{ route('entertainment.claim-reward') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                quest_id: questId
                            })
                        });

                        const data = await res.json();
                        if (data.success) {
                            this.userExp = data.total_exp;
                            this.userBadge = data.badge;
                            this.showToast(data.message, 'success');
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            this.showToast(data.message || 'Không thể nhận thưởng!', 'error');
                        }
                    } catch (err) {
                        this.showToast('Lỗi kết nối khi nhận thưởng!', 'error');
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            };
        }
    </script>
</x-app-layout>
