<nav x-data="{ openMobile: false, openDeposit: false }" class="sticky top-0 z-50 w-full bg-white/95 backdrop-blur-md border-b border-slate-200 transition-all duration-200 select-none">
    <!-- Header Container Full-width Thoáng Đãng Chuẩn Modern Minimalist -->
    <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16 sm:h-20">

        <!-- ========================================================================= -->
        <!-- KHỐI 1: KHỐI TRÁI - BRAND IDENTITY                                        -->
        <!-- ========================================================================= -->
        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group select-none py-1">
                {{-- KHỐI ICON NGÔI SAO TRI THỨC --}}
                <div class="relative w-10 h-10 rounded-2xl bg-indigo-50/80 border border-indigo-100 flex items-center justify-center shadow-sm group-hover:bg-indigo-100/80 group-hover:shadow transition-all shrink-0">
                    <svg viewBox="0 0 100 100" class="w-6 h-6 transform group-hover:rotate-12 transition-transform duration-300" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="starGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#4F46E5" />
                                <stop offset="100%" stop-color="#F59E0B" />
                            </linearGradient>
                        </defs>
                        <!-- Ngôi sao 4 cánh chính vuốt nhọn tinh tế -->
                        <path d="M50 5 Q50 42 12 50 Q50 58 50 95 Q50 58 88 50 Q50 42 50 5 Z" fill="url(#starGrad)" />
                        <!-- Tia sáng phụ 4 góc chéo -->
                        <path d="M50 28 Q50 45 32 50 Q50 55 50 72 Q50 55 68 50 Q50 45 50 28 Z" fill="#FBBF24" opacity="0.8" />
                        <!-- Điểm sáng trung tâm -->
                        <circle cx="50" cy="50" r="4" fill="#FFFFFF" />
                    </svg>
                </div>

                {{-- TYPOGRAPHY THƯƠNG HIỆU --}}
                <div class="flex flex-col">
                    <div class="text-xl font-black italic tracking-tight text-slate-900 flex items-center leading-none">
                        TIỂU ĐỘI<span class="text-indigo-600 ml-1.5">2P</span>
                    </div>
                    <span class="text-[9px] font-bold tracking-widest text-slate-400 uppercase mt-1">
                        THƯ VIỆN SỐ
                    </span>
                </div>
            </a>
        </div>

        <!-- ========================================================================= -->
        <!-- KHỐI 2: KHỐI GIỮA - ĐIỀU HƯỚNG CỐT LÕI (CORE NAVIGATION)                  -->
        <!-- ========================================================================= -->
        <div class="hidden md:flex items-center gap-1 lg:gap-2 flex-1 justify-center mx-2 lg:mx-6">
            
            <!-- 1. [TRANG CHỦ] -->
            <a href="{{ url('/') }}"
               class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-sm font-semibold transition-colors whitespace-nowrap {{ request()->is('/') ? 'bg-slate-100 text-indigo-600 font-bold' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                <svg class="w-4 h-4 shrink-0 {{ request()->is('/') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                <span>TRANG CHỦ</span>
            </a>

            <!-- 2. [KHO SÁCH] -->
            <a href="{{ route('books.index') }}"
               class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-sm font-semibold transition-colors whitespace-nowrap {{ request()->routeIs('books.*') ? 'bg-slate-100 text-indigo-600 font-bold' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('books.*') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                </svg>
                <span>KHO SÁCH</span>
            </a>

            <!-- 3. [MƯỢN / TRẢ SÁCH] -->
            @auth
                @if(in_array(Auth::user()->role, ['admin', 'librarian']))
                    <a href="{{ route('borrows.index') }}"
                       class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-sm font-semibold transition-colors whitespace-nowrap {{ request()->routeIs('borrows.*') ? 'bg-slate-100 text-indigo-600 font-bold' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('borrows.*') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                        </svg>
                        <span>MƯỢN / TRẢ SÁCH</span>
                        @php
                            $navPendingCount = \App\Models\BorrowRecord::where('status', 'pending')->count();
                        @endphp
                        <span id="header-pending-badge" class="pending-count-badge px-1.5 py-0.5 rounded-full text-[10px] font-mono font-black bg-amber-100 text-amber-800 {{ $navPendingCount > 0 ? '' : 'hidden' }}">
                            {{ $navPendingCount }}
                        </span>
                    </a>
                @else
                    <a href="{{ route('borrows.tracking') }}"
                       class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-sm font-semibold transition-colors whitespace-nowrap {{ request()->routeIs('borrows.*') ? 'bg-slate-100 text-indigo-600 font-bold' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('borrows.*') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                        </svg>
                        <span>MƯỢN / TRẢ SÁCH</span>
                    </a>
                @endif
            @else
                <a href="{{ route('books.index') }}"
                   class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-sm font-semibold transition-colors whitespace-nowrap text-slate-600 hover:text-indigo-600 hover:bg-slate-50">
                    <svg class="w-4 h-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                    </svg>
                    <span>MƯỢN / TRẢ SÁCH</span>
                </a>
            @endauth

            <!-- 4. [HỘI QUÁN TRI THỨC] (DROPDOWN) -->
            <div class="relative inline-flex items-center"
                 x-data="{ openCommunity: false }"
                 @mouseenter="openCommunity = true"
                 @mouseleave="openCommunity = false"
                 @click.outside="openCommunity = false"
                 @keydown.escape.window="openCommunity = false">
                
                <button type="button"
                        @click="openCommunity = !openCommunity"
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-sm font-semibold transition-colors whitespace-nowrap cursor-pointer {{ (request()->routeIs('forum.*') || request()->routeIs('entertainment.*')) ? 'bg-slate-100 text-indigo-600 font-bold' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}"
                        :class="{ 'bg-slate-100 text-indigo-600': openCommunity }">
                    <svg class="w-4 h-4 shrink-0 {{ (request()->routeIs('forum.*') || request()->routeIs('entertainment.*')) ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                    <span>HỘI QUÁN TRI THỨC</span>
                    <svg class="w-3.5 h-3.5 transition-transform duration-200 text-slate-400"
                         :class="{ 'rotate-180 text-indigo-600': openCommunity }"
                         fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>

                <!-- Dropdown Container -->
                <div x-show="openCommunity"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                     x-cloak
                     style="display: none;"
                     class="absolute left-0 top-full pt-2 min-w-[260px] z-50">
                    
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-xl p-2 min-w-[260px] text-left space-y-1">
                        <!-- 1. Thảo luận cộng đồng -->
                        <a href="{{ route('forum.index') }}"
                           @click="openCommunity = false"
                           class="text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-indigo-600 rounded-xl px-3 py-2.5 flex items-center gap-3 transition {{ request()->routeIs('forum.*') ? 'bg-indigo-50/70 text-indigo-600 font-bold' : '' }}">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v4.018Z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-slate-900">Diễn đàn Thảo luận</div>
                                <div class="text-[11px] text-slate-400 font-normal">Giao lưu & chia sẻ kinh nghiệm</div>
                            </div>
                        </a>

                        <!-- 2. Bảng Phong Thần / Đua Top -->
                        <a href="{{ Route::has('entertainment.leaderboard') ? route('entertainment.leaderboard') : '#' }}"
                           @click="openCommunity = false"
                           class="text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-indigo-600 rounded-xl px-3 py-2.5 flex items-center gap-3 transition {{ request()->routeIs('entertainment.leaderboard') ? 'bg-indigo-50/70 text-indigo-600 font-bold' : '' }}">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.004 0H9.496m5.004 0a3.375 3.375 0 0 0 3.375-3.375V4.875H6.125v7.125A3.375 3.375 0 0 0 9.5 15.375m0-10.5V3.375m5 1.5V3.375" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-slate-900">Bảng Phong Thần / Đua Top</div>
                                <div class="text-[11px] text-slate-400 font-normal">Vinh danh độc giả & học giả</div>
                            </div>
                        </a>

                        <!-- 3. Nhiệm vụ & Điểm danh -->
                        <a href="{{ route('entertainment.index') }}"
                           @click="openCommunity = false"
                           class="text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-indigo-600 rounded-xl px-3 py-2.5 flex items-center gap-3 transition {{ request()->routeIs('entertainment.index') ? 'bg-indigo-50/70 text-indigo-600 font-bold' : '' }}">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-2.17 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-slate-900">Nhiệm vụ & Điểm danh</div>
                                <div class="text-[11px] text-slate-400 font-normal">Tích lũy xu tri thức mỗi ngày</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- KHỐI 3: KHỐI PHẢI - TIỆN ÍCH NGƯỜI DÙNG (VÍ, VIP, GIỎ SÁCH, TÀI KHOẢN)    -->
        <!-- ========================================================================= -->
        <div class="flex items-center gap-2 sm:gap-2.5 shrink-0">
            
            @auth
                <!-- 1. CỤM VÍ THÔNG MINH (SMART WALLET CAPSULE) -->
                <div class="flex items-center bg-emerald-50 border border-emerald-200 rounded-full pl-3 pr-1 py-1 gap-2 shadow-2xs shrink-0"
                     x-data="{ userBalance: '{{ number_format(Auth::user()->wallet->balance ?? 0) }}đ' }"
                     @wallet-updated.window="userBalance = $event.detail.formatted_balance"
                     @balance-updated.window="userBalance = $event.detail.formatted_balance">
                    <a href="{{ route('wallet.deposit') }}"
                       class="text-xs font-bold text-emerald-700 font-mono tracking-tight cursor-pointer hover:text-emerald-800 transition flex items-center gap-1.5"
                       title="Bấm để vào trang Nạp tiền qua VietQR">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 9m18 0V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v3" />
                        </svg>
                        <span x-text="userBalance">{{ number_format(Auth::user()->wallet->balance ?? 0) }}đ</span>
                    </a>
                    <a href="{{ route('wallet.deposit') }}"
                       title="Nạp tiền vào ví qua cổng VietQR Napas 247"
                       class="w-6 h-6 rounded-full bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-white flex items-center justify-center font-bold text-xs shadow-sm transition cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </a>
                </div>

                <!-- 2. HUY HIỆU GÓI HỘI VIÊN (VIP BADGE) -->
                @if(Auth::user()->isVip())
                    <a href="{{ route('membership.index') }}"
                       title="Đặc quyền Gói Hội Viên Scholar VIP"
                       class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-400 text-slate-950 shadow-2xs hover:brightness-105 transition shrink-0">
                        <svg class="w-3.5 h-3.5 text-slate-950 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M9 4.5a.75.75 0 0 1 .721.544l.813 2.846a3.75 3.75 0 0 0 2.576 2.576l2.846.813a.75.75 0 0 1 0 1.442l-2.846.813a3.75 3.75 0 0 0-2.576 2.576l-.813 2.846a.75.75 0 0 1-1.442 0l-.813-2.846a3.75 3.75 0 0 0-2.576-2.576l-2.846-.813a.75.75 0 0 1 0-1.442l2.846-.813A3.75 3.75 0 0 0 7.466 7.89l.813-2.846A.75.75 0 0 1 9 4.5ZM18 1.5a.75.75 0 0 1 .728.568l.258 1.036c.236.94.97 1.674 1.91 1.91l1.036.258a.75.75 0 0 1 0 1.456l-1.036.258c-.94.236-1.674.97-1.91 1.91l-.258 1.036a.75.75 0 0 1-1.456 0l-.258-1.036a2.625 2.625 0 0 0-1.91-1.91l-1.036-.258a.75.75 0 0 1 0-1.456l1.036-.258a2.625 2.625 0 0 0 1.91-1.91l.258-1.036A.75.75 0 0 1 18 1.5ZM16.5 15a.75.75 0 0 1 .712.513l.394 1.183c.15.447.505.803.952.952l1.183.394a.75.75 0 0 1 0 1.424l-1.183.394a1.875 1.875 0 0 0-.952.952l-.394 1.183a.75.75 0 0 1-1.424 0l-.394-1.183a1.875 1.875 0 0 0-.952-.952l-1.183-.394a.75.75 0 0 1 0-1.424l1.183-.394a1.875 1.875 0 0 0 .952-.952l.394-1.183A.75.75 0 0 1 16.5 15Z" clip-rule="evenodd" />
                        </svg>
                        <span>VIP</span>
                    </a>
                @else
                    <a href="{{ route('membership.index') }}"
                       title="Nâng cấp Gói VIP để nhận đặc quyền"
                       class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium text-amber-700 bg-amber-50/80 border border-amber-300 hover:bg-amber-100 transition whitespace-nowrap shrink-0 shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M9 4.5a.75.75 0 0 1 .721.544l.813 2.846a3.75 3.75 0 0 0 2.576 2.576l2.846.813a.75.75 0 0 1 0 1.442l-2.846.813a3.75 3.75 0 0 0-2.576 2.576l-.813 2.846a.75.75 0 0 1-1.442 0l-.813-2.846a3.75 3.75 0 0 0-2.576-2.576l-2.846-.813a.75.75 0 0 1 0-1.442l2.846-.813A3.75 3.75 0 0 0 7.466 7.89l.813-2.846A.75.75 0 0 1 9 4.5ZM18 1.5a.75.75 0 0 1 .728.568l.258 1.036c.236.94.97 1.674 1.91 1.91l1.036.258a.75.75 0 0 1 0 1.456l-1.036.258c-.94.236-1.674.97-1.91 1.91l-.258 1.036a.75.75 0 0 1-1.456 0l-.258-1.036a2.625 2.625 0 0 0-1.91-1.91l-1.036-.258a.75.75 0 0 1 0-1.456l1.036-.258a2.625 2.625 0 0 0 1.91-1.91l.258-1.036A.75.75 0 0 1 18 1.5ZM16.5 15a.75.75 0 0 1 .712.513l.394 1.183c.15.447.505.803.952.952l1.183.394a.75.75 0 0 1 0 1.424l-1.183.394a1.875 1.875 0 0 0-.952.952l-.394 1.183a.75.75 0 0 1-1.424 0l-.394-1.183a1.875 1.875 0 0 0-.952-.952l-1.183-.394a.75.75 0 0 1 0-1.424l1.183-.394a1.875 1.875 0 0 0 .952-.952l.394-1.183A.75.75 0 0 1 16.5 15Z" clip-rule="evenodd" />
                        </svg>
                        <span>Nâng cấp VIP</span>
                    </a>
                @endif
            @endauth

            <!-- 3. NÚT GIỎ SÁCH (SHOPPING CART) -->
            <div class="inline-flex items-center shrink-0">
                <a href="{{ route('cart.index') }}"
                   id="cart-trigger-btn"
                   title="Xem giỏ sách mượn"
                   class="w-9 h-9 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-xl relative flex items-center justify-center text-slate-700 transition active:scale-95 cursor-pointer">
                    <svg class="w-5 h-5 text-slate-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                    </svg>
                    @php $currentCartCount = count(session('cart', [])); @endphp
                    <span id="cart-counter-badge"
                          data-cart-badge
                          class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-rose-500 text-white text-[10px] font-mono font-black flex items-center justify-center shadow-xs border border-white {{ $currentCartCount > 0 ? '' : 'hidden' }}">
                        {{ $currentCartCount }}
                    </span>
                </a>
            </div>

            <!-- 4. AVATAR USER & DROPDOWN MENU -->
            @auth
                <div class="relative inline-flex items-center shrink-0" 
                     x-data="{ userMenu: false }"
                     @click.outside="userMenu = false"
                     @keydown.escape.window="userMenu = false">
                    
                    <!-- Nút Trigger Avatar -->
                    <button type="button"
                            @click="userMenu = !userMenu"
                            class="group relative flex items-center gap-1.5 p-1 rounded-full hover:bg-slate-100 transition border border-slate-200 cursor-pointer bg-white"
                            :class="{ 'ring-2 ring-indigo-500/30 bg-slate-50': userMenu }">
                        <div class="relative w-8 h-8 shrink-0">
                            {{-- VƯƠNG MIỆN ADMIN --}}
                            @if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->is_admin))
                                <span class="absolute -top-3.5 left-1/2 -translate-x-1/2 z-20 pointer-events-none transform -rotate-12 transition-transform group-hover:scale-110 drop-shadow-[0_2px_4px_rgba(245,158,11,0.5)]">
                                    <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24">
                                        <path d="M5 16L3 5L8.5 10L12 4L15.5 10L21 5L19 16H5M19 19C19 19.6 18.6 20 18 20H6C5.4 20 5 19.6 5 19V17H19V19Z" />
                                    </svg>
                                </span>
                            @endif

                            {{-- KHUNG AVATAR --}}
                            <div class="relative w-8 h-8 rounded-full overflow-hidden border border-slate-200">
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" 
                                         alt="{{ Auth::user()->name }}" 
                                         class="w-full h-full object-cover">
                                @elseif(isset(Auth::user()->avatar_url))
                                    <img src="{{ Auth::user()->avatar_url }}" 
                                         alt="{{ Auth::user()->name }}" 
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-indigo-50 text-indigo-700 font-bold text-xs flex items-center justify-center">
                                        {{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 2, 'UTF-8'), 'UTF-8') }}
                                    </div>
                                @endif
                            </div>

                            {{-- CHẤM ONLINE XANH --}}
                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                        </div>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200 pr-0.5" 
                             :class="{ 'rotate-180 text-indigo-600': userMenu }" 
                             fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="userMenu"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                         x-cloak
                         style="display: none;"
                         class="absolute right-0 top-full mt-2 w-72 sm:w-80 bg-white rounded-2xl shadow-2xl border border-slate-200 p-3.5 z-50 text-left space-y-3">
                        
                        <!-- 1. THÔNG TIN TÀI KHOẢN & HẠNG THÀNH VIÊN -->
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="relative shrink-0">
                                    @if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->is_admin))
                                        <span class="absolute -top-3.5 left-1/2 -translate-x-1/2 z-20 pointer-events-none transform -rotate-12 drop-shadow-[0_2px_4px_rgba(245,158,11,0.5)]">
                                            <svg class="w-6 h-6 text-amber-400 fill-amber-400" viewBox="0 0 24 24">
                                                <path d="M5 16L3 5L8.5 10L12 4L15.5 10L21 5L19 16H5M19 19C19 19.6 18.6 20 18 20H6C5.4 20 5 19.6 5 19V17H19V19Z" />
                                            </svg>
                                        </span>
                                    @endif
                                    <div class="w-11 h-11 rounded-full overflow-hidden border border-slate-200 bg-white flex items-center justify-center">
                                        @if(Auth::user()->avatar)
                                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" 
                                                 alt="{{ Auth::user()->name }}" 
                                                 class="w-11 h-11 rounded-full object-cover">
                                        @elseif(isset(Auth::user()->avatar_url))
                                            <img src="{{ Auth::user()->avatar_url }}" 
                                                 alt="{{ Auth::user()->name }}" 
                                                 class="w-11 h-11 rounded-full object-cover">
                                        @else
                                            <span class="font-black text-sm text-indigo-600">
                                                {{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 2, 'UTF-8'), 'UTF-8') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-slate-900 text-sm truncate" title="{{ Auth::user()->name }}">
                                        {{ Auth::user()->name }}
                                    </div>
                                    <div class="text-xs text-slate-500 font-mono truncate" title="{{ Auth::user()->email }}">
                                        {{ Auth::user()->email }}
                                    </div>
                                    <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                        @if(Auth::user()->role === 'admin')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                Quản trị viên
                                            </span>
                                        @elseif(Auth::user()->role === 'librarian')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                                Thủ thư
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-200/80 text-slate-700">
                                                Độc giả
                                            </span>
                                        @endif

                                        @if(Auth::user()->isVip())
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-400 text-slate-950">
                                                VIP
                                            </span>
                                        @endif

                                        @if(Auth::user()->title_badge)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold shrink-0 max-w-full shadow-2xs {{ Auth::user()->badge_color }}">
                                                <span class="shrink-0 flex items-center leading-none">{!! Auth::user()->badge_icon !!}</span>
                                                <span class="truncate leading-tight">{{ Auth::user()->title_badge }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. LỊCH SỬ GIAO DỊCH VÍ & MƯỢN SÁCH & HỒ SƠ -->
                        <div class="space-y-0.5">
                            <!-- Hồ sơ cá nhân -->
                            <a href="{{ route('profile.edit') }}" 
                               @click="userMenu = false"
                               class="group hover:bg-slate-100 text-slate-700 hover:text-indigo-600 font-medium text-xs p-2.5 rounded-xl flex items-center gap-2.5 transition">
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-indigo-600 shrink-0 transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                                <span>Hồ sơ cá nhân</span>
                            </a>

                            <!-- Mượn sách & Lịch sử mượn trả -->
                            <a href="{{ route('borrows.tracking') }}" 
                               @click="userMenu = false"
                               class="group hover:bg-slate-100 text-slate-700 hover:text-indigo-600 font-medium text-xs p-2.5 rounded-xl flex items-center gap-2.5 transition">
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-indigo-600 shrink-0 transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                </svg>
                                <span>Lịch sử & Theo dõi mượn sách</span>
                            </a>

                            <!-- Lịch sử giao dịch ví & Nạp tiền -->
                            <a href="{{ route('wallet.deposit') }}"
                               @click="userMenu = false"
                               class="group w-full hover:bg-slate-100 text-slate-700 hover:text-emerald-700 font-medium text-xs p-2.5 rounded-xl flex items-center justify-between transition">
                                <span class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 9m18 0V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v3" />
                                    </svg>
                                    <span>Ví & Nạp tiền điện tử</span>
                                </span>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                    Nạp ngay
                                </span>
                            </a>

                            <!-- Gói Scholar VIP -->
                            <a href="{{ route('membership.index') }}" 
                               @click="userMenu = false"
                               class="group hover:bg-amber-50 text-slate-700 hover:text-amber-900 font-medium text-xs p-2.5 rounded-xl flex items-center justify-between transition">
                                <span class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M9 4.5a.75.75 0 0 1 .721.544l.813 2.846a3.75 3.75 0 0 0 2.576 2.576l2.846.813a.75.75 0 0 1 0 1.442l-2.846.813a3.75 3.75 0 0 0-2.576 2.576l-.813 2.846a.75.75 0 0 1-1.442 0l-.813-2.846a3.75 3.75 0 0 0-2.576-2.576l-2.846-.813a.75.75 0 0 1 0-1.442l2.846-.813A3.75 3.75 0 0 0 7.466 7.89l.813-2.846A.75.75 0 0 1 9 4.5ZM18 1.5a.75.75 0 0 1 .728.568l.258 1.036c.236.94.97 1.674 1.91 1.91l1.036.258a.75.75 0 0 1 0 1.456l-1.036.258c-.94.236-1.674.97-1.91 1.91l-.258 1.036a.75.75 0 0 1-1.456 0l-.258-1.036a2.625 2.625 0 0 0-1.91-1.91l-1.036-.258a.75.75 0 0 1 0-1.456l1.036-.258a2.625 2.625 0 0 0 1.91-1.91l.258-1.036A.75.75 0 0 1 18 1.5ZM16.5 15a.75.75 0 0 1 .712.513l.394 1.183c.15.447.505.803.952.952l1.183.394a.75.75 0 0 1 0 1.424l-1.183.394a1.875 1.875 0 0 0-.952.952l-.394 1.183a.75.75 0 0 1-1.424 0l-.394-1.183a1.875 1.875 0 0 0-.952-.952l-1.183-.394a.75.75 0 0 1 0-1.424l1.183-.394a1.875 1.875 0 0 0 .952-.952l.394-1.183A.75.75 0 0 1 16.5 15Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Gói Hội Viên Scholar VIP</span>
                                </span>
                                @if(Auth::user()->isVip())
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800 uppercase">ĐÃ KÍCH HOẠT</span>
                                @else
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-50 text-indigo-700 uppercase">NÂNG CẤP</span>
                                @endif
                            </a>
                        </div>

                        <!-- 3. [DÀNH CHO ADMIN / THỦ THƯ]: CONTROL PANEL -->
                        @if(in_array(Auth::user()->role, ['admin', 'librarian']))
                            <div class="border-t border-slate-100 pt-2 space-y-1">
                                <div class="px-2 text-[10px] font-black uppercase tracking-wider text-slate-400 flex items-center gap-1.5 mb-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.27.1.06-.12l-.773.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.15.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.27-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.11v-1.093c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.205l-.527-.737a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <span>BẢNG ĐIỀU KHIỂN CONTROL PANEL</span>
                                </div>
                                
                                <a href="{{ route('dashboard') }}"
                                   @click="userMenu = false"
                                   class="hover:bg-slate-100 text-slate-800 font-semibold text-xs p-2 rounded-xl flex items-center gap-2.5 transition {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                                    <div class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                                        </svg>
                                    </div>
                                    <span>Tổng quan hệ thống (Dashboard)</span>
                                </a>

                                <a href="{{ route('admin.users.index') }}"
                                   @click="userMenu = false"
                                   class="hover:bg-slate-100 text-slate-800 font-semibold text-xs p-2 rounded-xl flex items-center gap-2.5 transition {{ request()->routeIs('admin.users.*') ? 'bg-amber-50 text-amber-700' : '' }}">
                                    <div class="w-6 h-6 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                        </svg>
                                    </div>
                                    <span>Quản lý người dùng & Phân quyền</span>
                                </a>

                                <a href="{{ route('borrows.index') }}"
                                   @click="userMenu = false"
                                   class="hover:bg-slate-100 text-slate-800 font-semibold text-xs p-2 rounded-xl flex items-center gap-2.5 transition {{ request()->routeIs('borrows.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                                    <div class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                        </svg>
                                    </div>
                                    <span>Quản lý mượn trả & Kho sách</span>
                                </a>

                                <a href="{{ route('admin.audit-logs.index') }}"
                                   @click="userMenu = false"
                                   class="hover:bg-slate-100 text-slate-800 font-semibold text-xs p-2 rounded-xl flex items-center gap-2.5 transition {{ request()->routeIs('admin.audit-logs.*') ? 'bg-emerald-50 text-emerald-700' : '' }}">
                                    <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                    </div>
                                    <span>Nhật ký hệ thống (Audit Logs)</span>
                                </a>

                                <a href="{{ route('admin.finances.index') }}"
                                   @click="userMenu = false"
                                   class="hover:bg-slate-100 text-slate-800 font-semibold text-xs p-2 rounded-xl flex items-center gap-2.5 transition {{ request()->routeIs('admin.finances.*') ? 'bg-emerald-50 text-emerald-700' : '' }}">
                                    <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v8.25m0 0a60.207 60.207 0 0 0 15.797 2.101c.727.198 1.453-.342 1.453-1.096V15M3 6h.75A.75.75 0 0 1 4.5 6.75v.75m0 0h15m-15 0a2.25 2.25 0 0 0-2.25 2.25v3a2.25 2.25 0 0 0 2.25 2.25m15-7.5v.75a.75.75 0 0 1 .75.75H21m0 0v3a2.25 2.25 0 0 1-2.25 2.25H4.5m16.5-5.25a2.25 2.25 0 0 0-2.25-2.25h-.75m3 3h-.75A.75.75 0 0 1 20.25 9v-.75" />
                                        </svg>
                                    </div>
                                    <span>Tài chính & Ngân sách thư viện</span>
                                </a>
                            </div>
                        @endif

                        <!-- 4. NÚT ĐĂNG XUẤT -->
                        <div class="border-t border-slate-100 pt-2">
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit" 
                                        class="hover:bg-rose-50 text-rose-600 font-bold text-xs p-2 rounded-xl flex items-center gap-2.5 transition w-full text-left cursor-pointer">
                                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                                    </svg>
                                    <span>Đăng xuất</span>
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            @else
                <!-- CỤM [ĐĂNG NHẬP] & [ĐĂNG KÝ] (CHO KHÁCH VÃNG LAI) -->
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('login') }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs uppercase tracking-wider font-bold text-slate-700 hover:text-indigo-600 hover:bg-slate-100 transition">
                        ĐĂNG NHẬP
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="px-3.5 py-1.5 rounded-xl text-xs uppercase tracking-wider font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-xs transition">
                            ĐĂNG KÝ
                        </a>
                    @endif
                </div>
            @endauth

            <!-- NÚT HAMBURGER MOBILE (HEROICONS BARS 3 / X MARK) -->
            <button @click="openMobile = ! openMobile" 
                    type="button"
                    class="md:hidden inline-flex items-center justify-center p-2 rounded-xl text-slate-700 hover:text-slate-900 hover:bg-slate-100 border border-slate-200 focus:outline-none transition cursor-pointer shrink-0"
                    aria-label="Toggle navigation menu">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                    <path :class="{'hidden': openMobile, 'inline-flex': ! openMobile }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    <path :class="{'hidden': ! openMobile, 'inline-flex': openMobile }" class="hidden" stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MENU ĐIỀU HƯỚNG TRÊN THIẾT BỊ DI ĐỘNG (MOBILE NAVIGATION MENU)              -->
    <!-- ========================================================================= -->
    <div :class="{'block': openMobile, 'hidden': ! openMobile}" class="hidden md:hidden bg-white border-b border-slate-200">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <!-- 1. TRANG CHỦ -->
            <x-responsive-nav-link :href="url('/')" :active="request()->is('/')" class="text-xs uppercase tracking-wider font-bold text-slate-900 hover:text-indigo-600 hover:bg-slate-50 flex items-center gap-2.5">
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                <span>TRANG CHỦ</span>
            </x-responsive-nav-link>

            <!-- 2. KHO SÁCH -->
            <x-responsive-nav-link :href="route('books.index')" :active="request()->routeIs('books.*')" class="text-xs uppercase tracking-wider font-bold text-slate-900 hover:text-indigo-600 hover:bg-slate-50 flex items-center gap-2.5">
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                </svg>
                <span>KHO SÁCH</span>
            </x-responsive-nav-link>

            <!-- 3. MƯỢN / TRẢ SÁCH -->
            @auth
                @if(in_array(Auth::user()->role, ['admin', 'librarian']))
                    <x-responsive-nav-link :href="route('borrows.index')" :active="request()->routeIs('borrows.*')" class="text-xs uppercase tracking-wider font-bold text-slate-900 hover:text-indigo-600 hover:bg-slate-50 flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                        </svg>
                        <span>MƯỢN / TRẢ SÁCH</span>
                    </x-responsive-nav-link>
                @else
                    <x-responsive-nav-link :href="route('borrows.tracking')" :active="request()->routeIs('borrows.tracking')" class="text-xs uppercase tracking-wider font-bold text-slate-900 hover:text-indigo-600 hover:bg-slate-50 flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                        </svg>
                        <span>MƯỢN / TRẢ SÁCH</span>
                    </x-responsive-nav-link>
                @endif
            @else
                <x-responsive-nav-link :href="route('books.index')" class="text-xs uppercase tracking-wider font-bold text-slate-900 hover:text-indigo-600 hover:bg-slate-50 flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                    </svg>
                    <span>MƯỢN / TRẢ SÁCH</span>
                </x-responsive-nav-link>
            @endauth

            <!-- NÚT MỞ GIỎ SÁCH TRÊN MOBILE -->
            <a href="{{ route('cart.index') }}"
               class="w-full text-left px-4 py-2.5 rounded-xl text-xs uppercase tracking-wider font-bold text-slate-800 hover:text-indigo-600 hover:bg-slate-50 flex items-center justify-between transition cursor-pointer">
                <span class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                    </svg>
                    <span>GIỎ SÁCH MƯỢN</span>
                </span>
                <span data-cart-badge class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700 font-mono {{ $currentCartCount > 0 ? '' : 'hidden' }}">
                    {{ $currentCartCount }}
                </span>
            </a>

            <!-- 4. HỘI QUÁN TRI THỨC MOBILE -->
            <div class="pt-2 pb-1 border-t border-slate-100">
                <div class="px-2 text-[11px] font-mono text-slate-400 font-bold uppercase tracking-wider mb-1 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                    <span>HỘI QUÁN TRI THỨC</span>
                </div>
                <x-responsive-nav-link :href="route('forum.index')" :active="request()->routeIs('forum.*')" class="text-slate-900 text-xs font-semibold hover:bg-slate-100 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v4.018Z" />
                    </svg>
                    <span>DIỄN ĐÀN THẢO LUẬN</span>
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="Route::has('entertainment.leaderboard') ? route('entertainment.leaderboard') : '#'" :active="request()->routeIs('entertainment.leaderboard')" class="text-slate-900 text-xs font-semibold hover:bg-slate-100 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.004 0H9.496m5.004 0a3.375 3.375 0 0 0 3.375-3.375V4.875H6.125v7.125A3.375 3.375 0 0 0 9.5 15.375m0-10.5V3.375m5 1.5V3.375" />
                    </svg>
                    <span>BẢNG PHONG THẦN / ĐUA TOP</span>
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('entertainment.index')" :active="request()->routeIs('entertainment.index')" class="text-slate-900 text-xs font-semibold hover:bg-slate-100 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-2.17 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                    </svg>
                    <span>NHIỆM VỤ & ĐIỂM DANH</span>
                </x-responsive-nav-link>
            </div>

            <!-- 5. GÓI SCHOLAR VIP MOBILE -->
            <x-responsive-nav-link :href="route('membership.index')" :active="request()->routeIs('membership.*')" class="text-xs uppercase tracking-wider font-bold text-amber-700 hover:text-amber-900 hover:bg-amber-50/50 flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M9 4.5a.75.75 0 0 1 .721.544l.813 2.846a3.75 3.75 0 0 0 2.576 2.576l2.846.813a.75.75 0 0 1 0 1.442l-2.846.813a3.75 3.75 0 0 0-2.576 2.576l-.813 2.846a.75.75 0 0 1-1.442 0l-.813-2.846a3.75 3.75 0 0 0-2.576-2.576l-2.846-.813a.75.75 0 0 1 0-1.442l2.846-.813A3.75 3.75 0 0 0 7.466 7.89l.813-2.846A.75.75 0 0 1 9 4.5ZM18 1.5a.75.75 0 0 1 .728.568l.258 1.036c.236.94.97 1.674 1.91 1.91l1.036.258a.75.75 0 0 1 0 1.456l-1.036.258c-.94.236-1.674.97-1.91 1.91l-.258 1.036a.75.75 0 0 1-1.456 0l-.258-1.036a2.625 2.625 0 0 0-1.91-1.91l-1.036-.258a.75.75 0 0 1 0-1.456l1.036-.258a2.625 2.625 0 0 0 1.91-1.91l.258-1.036A.75.75 0 0 1 18 1.5ZM16.5 15a.75.75 0 0 1 .712.513l.394 1.183c.15.447.505.803.952.952l1.183.394a.75.75 0 0 1 0 1.424l-1.183.394a1.875 1.875 0 0 0-.952.952l-.394 1.183a.75.75 0 0 1-1.424 0l-.394-1.183a1.875 1.875 0 0 0-.952-.952l-1.183-.394a.75.75 0 0 1 0-1.424l1.183-.394a1.875 1.875 0 0 0 .952-.952l.394-1.183A.75.75 0 0 1 16.5 15Z" clip-rule="evenodd" />
                </svg>
                <span>GÓI SCHOLAR VIP</span>
            </x-responsive-nav-link>

            <!-- 6. CONTROL PANEL MOBILE -->
            @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'librarian']))
                <div class="pt-2 pb-1 border-t border-slate-100">
                    <div class="px-2 text-[11px] font-mono text-slate-400 font-bold uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.27.1.06-.12l-.773.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.15.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.27-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.11v-1.093c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.205l-.527-.737a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <span>CONTROL PANEL (QUẢN TRỊ)</span>
                    </div>
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-slate-900 text-xs font-semibold hover:bg-slate-100 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                        </svg>
                        <span>TỔNG QUAN HỆ THỐNG</span>
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" class="text-slate-900 text-xs font-semibold hover:bg-slate-100 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                        <span>QUẢN LÝ NGƯỜI DÙNG</span>
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('borrows.index')" :active="request()->routeIs('borrows.*')" class="text-slate-900 text-xs font-semibold hover:bg-slate-100 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                        <span>QUẢN LÝ MƯỢN TRẢ & KHO</span>
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.audit-logs.index')" :active="request()->routeIs('admin.audit-logs.*')" class="text-slate-900 text-xs font-semibold hover:bg-slate-100 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span>NHẬT KÝ HỆ THỐNG</span>
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.finances.index')" :active="request()->routeIs('admin.finances.*')" class="text-slate-900 text-xs font-semibold hover:bg-slate-100 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v8.25m0 0a60.207 60.207 0 0 0 15.797 2.101c.727.198 1.453-.342 1.453-1.096V15M3 6h.75A.75.75 0 0 1 4.5 6.75v.75m0 0h15m-15 0a2.25 2.25 0 0 0-2.25 2.25v3a2.25 2.25 0 0 0 2.25 2.25m15-7.5v.75a.75.75 0 0 1 .75.75H21m0 0v3a2.25 2.25 0 0 1-2.25 2.25H4.5m16.5-5.25a2.25 2.25 0 0 0-2.25-2.25h-.75m3 3h-.75A.75.75 0 0 1 20.25 9v-.75" />
                        </svg>
                        <span>BÁO CÁO TÀI CHÍNH & QUỸ</span>
                    </x-responsive-nav-link>
                </div>
            @endif
        </div>

        <!-- THÔNG TIN ĐỘC GIẢ & NÚT ĐĂNG XUẤT MOBILE -->
        @auth
            <div class="pt-3 pb-4 border-t border-slate-100 px-4 space-y-3">
                <div class="flex items-center gap-3">
                    <div class="relative shrink-0">
                        @if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->is_admin))
                            <span class="absolute -top-3.5 left-1/2 -translate-x-1/2 z-20 pointer-events-none transform -rotate-12 drop-shadow-[0_2px_4px_rgba(245,158,11,0.5)]">
                                <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24">
                                    <path d="M5 16L3 5L8.5 10L12 4L15.5 10L21 5L19 16H5M19 19C19 19.6 18.6 20 18 20H6C5.4 20 5 19.6 5 19V17H19V19Z" />
                                </svg>
                            </span>
                        @endif
                        <div class="w-10 h-10 rounded-full border border-slate-200 bg-indigo-50 flex items-center justify-center font-bold text-xs text-indigo-700 shadow-2xs overflow-hidden">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-full h-full object-cover rounded-full">
                            @elseif(isset(Auth::user()->avatar_url))
                                <img src="{{ Auth::user()->avatar_url }}" class="w-full h-full object-cover rounded-full">
                            @else
                                {{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 2, 'UTF-8'), 'UTF-8') }}
                            @endif
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs uppercase tracking-wider font-bold text-slate-900 truncate">
                            {{ Auth::user()->name }}
                        </div>
                        <div class="text-[11px] text-slate-500 font-mono truncate">{{ Auth::user()->email }}</div>
                    </div>
                    @if(Auth::user()->isVip())
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-400 text-slate-950 uppercase shrink-0">VIP</span>
                    @endif
                </div>

                <!-- Cụm ví thông minh trên mobile dẫn sang trang nạp tiền -->
                <div class="flex items-center bg-emerald-50 border border-emerald-200 rounded-full pl-3 pr-1 py-1 gap-2 shadow-2xs">
                    <a href="{{ route('wallet.deposit') }}"
                       @click="openMobile = false"
                       class="text-xs font-bold text-emerald-700 font-mono tracking-tight flex-1 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 9m18 0V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v3" />
                        </svg>
                        <span>{{ number_format(Auth::user()->wallet->balance ?? (Auth::user()->getOrCreateWallet()->balance ?? 0)) }}đ</span>
                    </a>
                    <a href="{{ route('wallet.deposit') }}"
                       @click="openMobile = false"
                       class="w-7 h-7 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </a>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <a href="{{ route('profile.edit') }}" class="flex-1 py-2 text-center text-xs uppercase tracking-wider font-bold rounded-xl bg-slate-100 text-slate-800 hover:bg-slate-200 transition">
                        HỒ SƠ
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full py-2 text-center text-xs uppercase tracking-wider font-bold rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 transition cursor-pointer">
                            ĐĂNG XUẤT
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="p-4 space-y-2 border-t border-slate-100">
                <a href="{{ route('login') }}" class="w-full py-2.5 px-4 rounded-xl text-xs uppercase tracking-wider font-bold text-slate-800 bg-slate-100 flex items-center justify-center">
                    ĐĂNG NHẬP
                </a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="w-full py-2.5 px-4 rounded-xl text-xs uppercase tracking-wider font-bold text-white bg-indigo-600 flex items-center justify-center">
                        ĐĂNG KÝ
                    </a>
                @endif
            </div>
        @endauth
    </div>
</nav>
