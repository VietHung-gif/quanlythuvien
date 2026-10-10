<nav x-data="{ openMobile: false, openDeposit: false }" class="sticky top-0 z-50 w-full bg-white/95 backdrop-blur-md border-b border-slate-200 transition-all duration-200 select-none">
    <!-- Header Container Full-width Thoáng Đãng Chuẩn Modern Minimalist -->
    <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16 sm:h-20">

        <!-- ========================================================================= -->
        <!-- KHỐI 1: KHỐI TRÁI - BRAND IDENTITY (STARBOY SHARP SPIKES & SPARKLE STAR)   -->
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
        <!-- KHỐI 2: KHỐI GIỮA - ĐIỀU HƯỚNG CỐT LÕI (CORE NAVIGATION - 4 LUỒNG DUY NHẤT) -->
        <!-- ========================================================================= -->
        <div class="hidden md:flex items-center gap-1 lg:gap-2 flex-1 justify-center mx-2 lg:mx-6">
            
            <!-- 1. [TRANG CHỦ] -->
            <a href="{{ url('/') }}"
               class="inline-flex items-center px-3 py-1.5 rounded-xl text-sm font-semibold transition-colors whitespace-nowrap {{ request()->is('/') ? 'bg-slate-100 text-indigo-600 font-bold' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                TRANG CHỦ
            </a>

            <!-- 2. [KHO SÁCH] -->
            <a href="{{ route('books.index') }}"
               class="inline-flex items-center px-3 py-1.5 rounded-xl text-sm font-semibold transition-colors whitespace-nowrap {{ request()->routeIs('books.*') ? 'bg-slate-100 text-indigo-600 font-bold' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                KHO SÁCH
            </a>

            <!-- 3. [MƯỢN / TRẢ SÁCH] -->
            @auth
                @if(in_array(Auth::user()->role, ['admin', 'librarian']))
                    <a href="{{ route('borrows.index') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-sm font-semibold transition-colors whitespace-nowrap {{ request()->routeIs('borrows.*') ? 'bg-slate-100 text-indigo-600 font-bold' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
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
                       class="inline-flex items-center px-3 py-1.5 rounded-xl text-sm font-semibold transition-colors whitespace-nowrap {{ request()->routeIs('borrows.*') ? 'bg-slate-100 text-indigo-600 font-bold' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        MƯỢN / TRẢ SÁCH
                    </a>
                @endif
            @else
                <a href="{{ route('books.index') }}"
                   class="inline-flex items-center px-3 py-1.5 rounded-xl text-sm font-semibold transition-colors whitespace-nowrap text-slate-600 hover:text-indigo-600 hover:bg-slate-50">
                    MƯỢN / TRẢ SÁCH
                </a>
            @endauth

            <!-- 4. [HỘI QUÁN TRI THỨC] (DROPDOWN: THẢO LUẬN, BẢNG PHONG THẦN / ĐUA TOP, NHIỆM VỤ) -->
            <div class="relative inline-flex items-center"
                 x-data="{ openCommunity: false }"
                 @mouseenter="openCommunity = true"
                 @mouseleave="openCommunity = false"
                 @click.outside="openCommunity = false"
                 @keydown.escape.window="openCommunity = false">
                
                <button type="button"
                        @click="openCommunity = !openCommunity"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-sm font-semibold transition-colors whitespace-nowrap cursor-pointer {{ (request()->routeIs('forum.*') || request()->routeIs('entertainment.*')) ? 'bg-slate-100 text-indigo-600 font-bold' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}"
                        :class="{ 'bg-slate-100 text-indigo-600': openCommunity }">
                    <span>HỘI QUÁN TRI THỨC</span>
                    <svg class="w-3.5 h-3.5 transition-transform duration-200 text-slate-400"
                         :class="{ 'rotate-180 text-indigo-600': openCommunity }"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- Lớp đệm chống hở chuột (Hover Gap Bridge Container) -->
                <div x-show="openCommunity"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                     x-cloak
                     style="display: none;"
                     class="absolute left-0 top-full pt-2 min-w-[250px] z-50">
                    
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-xl p-2 min-w-[250px] text-left space-y-1">
                        <!-- 1. Thảo luận cộng đồng -->
                        <a href="{{ route('forum.index') }}"
                           @click="openCommunity = false"
                           class="text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-indigo-600 rounded-xl px-3 py-2.5 flex items-center gap-3 transition {{ request()->routeIs('forum.*') ? 'bg-indigo-50/70 text-indigo-600 font-bold' : '' }}">
                            <span class="text-base shrink-0">💬</span>
                            <div class="min-w-0">
                                <div class="font-bold text-slate-900">Diễn đàn Thảo luận</div>
                                <div class="text-[11px] text-slate-400 font-normal">Giao lưu & chia sẻ kinh nghiệm</div>
                            </div>
                        </a>

                        <!-- 2. Bảng Phong Thần / Đua Top -->
                        <a href="{{ Route::has('entertainment.leaderboard') ? route('entertainment.leaderboard') : '#' }}"
                           @click="openCommunity = false"
                           class="text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-indigo-600 rounded-xl px-3 py-2.5 flex items-center gap-3 transition {{ request()->routeIs('entertainment.leaderboard') ? 'bg-indigo-50/70 text-indigo-600 font-bold' : '' }}">
                            <span class="text-base shrink-0">🏆</span>
                            <div class="min-w-0">
                                <div class="font-bold text-slate-900">Bảng Phong Thần / Đua Top</div>
                                <div class="text-[11px] text-slate-400 font-normal">Vinh danh độc giả & học giả</div>
                            </div>
                        </a>

                        <!-- 3. Nhiệm vụ & Điểm danh -->
                        <a href="{{ route('entertainment.index') }}"
                           @click="openCommunity = false"
                           class="text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-indigo-600 rounded-xl px-3 py-2.5 flex items-center gap-3 transition {{ request()->routeIs('entertainment.index') ? 'bg-indigo-50/70 text-indigo-600 font-bold' : '' }}">
                            <span class="text-base shrink-0">🎯</span>
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
        <!-- KHỐI 3: KHỐI PHẢI - HỆ SINH THÁI TIỆN ÍCH NGƯỜI DÙNG (GOM NHÓM TINH GỌN)    -->
        <!-- ========================================================================= -->
        <div class="flex items-center gap-2 sm:gap-2.5 shrink-0">
            
            @auth
                <!-- 1. CỤM VÍ THÔNG MINH (SMART WALLET CAPSULE - 1 VIÊN THUỐC DẪN VÀO TRANG NẠP TIỀN) -->
                <div class="flex items-center bg-emerald-50 border border-emerald-200 rounded-full pl-3 pr-1 py-1 gap-2 shadow-2xs shrink-0"
                     x-data="{ userBalance: '{{ number_format(Auth::user()->wallet->balance ?? 0) }}đ' }"
                     @wallet-updated.window="userBalance = $event.detail.formatted_balance"
                     @balance-updated.window="userBalance = $event.detail.formatted_balance">
                    <a href="{{ route('wallet.deposit') }}"
                       class="text-xs font-bold text-emerald-700 font-mono tracking-tight cursor-pointer hover:text-emerald-800 transition"
                       title="Bấm để vào trang Nạp tiền qua VietQR">
                        💰 <span x-text="userBalance">{{ number_format(Auth::user()->wallet->balance ?? 0) }}đ</span>
                    </a>
                    <a href="{{ route('wallet.deposit') }}"
                       title="Nạp tiền vào ví qua cổng VietQR Napas 247"
                       class="w-6 h-6 rounded-full bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-white flex items-center justify-center font-bold text-xs shadow-sm transition cursor-pointer">
                        +
                    </a>
                </div>

                <!-- 2. HUY HIỆU GÓI HỘI VIÊN (VIP BADGE HOẶC NÚT NÂNG CẤP) -->
                @if(Auth::user()->isVip())
                    <a href="{{ route('membership.index') }}"
                       title="Đặc quyền Gói Hội Viên Scholar VIP"
                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-400 text-slate-950 shadow-2xs hover:brightness-105 transition shrink-0">
                        <span>👑</span>
                        <span>VIP</span>
                    </a>
                @else
                    <a href="{{ route('membership.index') }}"
                       title="Nâng cấp Gói VIP để nhận đặc quyền"
                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium text-amber-700 bg-amber-50/80 border border-amber-300 hover:bg-amber-100 transition whitespace-nowrap shrink-0 shadow-2xs">
                        <span>👑</span>
                        <span>Nâng cấp VIP</span>
                    </a>
                @endif
            @endauth

            <!-- 3. NÚT GIỎ SÁCH BO GÓC NHỎ GỌN (COMPACT BOOK CART) -->
            <div x-data="{ cartCount: {{ count(session('cart', [])) }} }"
                 @cart-updated.window="cartCount = $event.detail.count"
                 class="inline-flex items-center shrink-0">
                <button type="button"
                        onclick="if (window.openCartDrawer) { window.openCartDrawer(); } else { window.dispatchEvent(new CustomEvent('open-cart')); }"
                        title="Giỏ sách mượn"
                        class="w-9 h-9 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-xl relative flex items-center justify-center text-slate-700 transition active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <template x-if="cartCount > 0">
                        <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-rose-500 text-white text-[10px] font-mono font-black flex items-center justify-center shadow-xs border border-white"
                              x-text="cartCount">
                            {{ count(session('cart', [])) }}
                        </span>
                    </template>
                </button>
            </div>

            <!-- 4. AVATAR USER & DROPDOWN MENU TINH TẾ (CHỨA CẢ CONTROL PANEL) -->
            @auth
                <div class="relative inline-flex items-center shrink-0" 
                     x-data="{ userMenu: false }"
                     @click.outside="userMenu = false"
                     @keydown.escape.window="userMenu = false">
                    
                    <!-- Nút Trigger Avatar tinh tế -->
                    <button type="button"
                            @click="userMenu = !userMenu"
                            class="group relative flex items-center gap-1.5 p-1 rounded-full hover:bg-slate-100 transition border border-slate-200 cursor-pointer bg-white"
                            :class="{ 'ring-2 ring-indigo-500/30 bg-slate-50': userMenu }">
                        <div class="relative w-8 h-8 shrink-0">
                            {{-- VƯƠNG MIỆN ADMIN HOÀNG GIA --}}
                            @if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->is_admin))
                                <span class="absolute -top-3.5 left-1/2 -translate-x-1/2 z-20 pointer-events-none transform -rotate-12 transition-transform group-hover:scale-110 drop-shadow-[0_2px_4px_rgba(245,158,11,0.5)]">
                                    <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24">
                                        <path d="M5 16L3 5L8.5 10L12 4L15.5 10L21 5L19 16H5M19 19C19 19.6 18.6 20 18 20H6C5.4 20 5 19.6 5 19V17H19V19Z" />
                                    </svg>
                                </span>
                            @endif

                            {{-- KHUNG AVATAR HIỆN TẠI --}}
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

                            {{-- CHẤM ONLINE XANH HIỆN CÓ --}}
                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                        </div>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200 pr-0.5" 
                             :class="{ 'rotate-180 text-indigo-600': userMenu }" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu Tinh Tế -->
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
                                    {{-- VƯƠNG MIỆN ADMIN HOÀNG GIA --}}
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
                                                👑 Quản trị viên
                                            </span>
                                        @elseif(Auth::user()->role === 'librarian')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                                📚 Thủ thư
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
                                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold {{ Auth::user()->badge_color }}">
                                                <span>{{ Auth::user()->badge_icon }}</span>
                                                <span>{{ Auth::user()->title_badge }}</span>
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
                               class="hover:bg-slate-100 text-slate-700 hover:text-indigo-600 font-medium text-xs p-2.5 rounded-xl flex items-center gap-2.5 transition">
                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>Hồ sơ cá nhân</span>
                            </a>

                            <!-- Mượn sách & Lịch sử mượn trả -->
                            <a href="{{ route('borrows.tracking') }}" 
                               @click="userMenu = false"
                               class="hover:bg-slate-100 text-slate-700 hover:text-indigo-600 font-medium text-xs p-2.5 rounded-xl flex items-center gap-2.5 transition">
                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                <span>Lịch sử & Theo dõi mượn sách</span>
                            </a>

                            <!-- Lịch sử giao dịch ví & Nạp tiền -->
                            <a href="{{ route('wallet.deposit') }}"
                               @click="userMenu = false"
                               class="w-full hover:bg-slate-100 text-slate-700 hover:text-emerald-700 font-medium text-xs p-2.5 rounded-xl flex items-center justify-between transition">
                                <span class="flex items-center gap-2.5">
                                    <span class="text-sm">💰</span>
                                    <span>Ví & Nạp tiền điện tử</span>
                                </span>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                    Nạp ngay
                                </span>
                            </a>

                            <!-- Gói Scholar VIP -->
                            <a href="{{ route('membership.index') }}" 
                               @click="userMenu = false"
                               class="hover:bg-amber-50 text-slate-700 hover:text-amber-900 font-medium text-xs p-2.5 rounded-xl flex items-center justify-between transition">
                                <span class="flex items-center gap-2.5">
                                    <span class="text-amber-500 text-sm">👑</span>
                                    <span>Gói Hội Viên Scholar VIP</span>
                                </span>
                                @if(Auth::user()->isVip())
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800 uppercase">ĐÃ KÍCH HOẠT</span>
                                @else
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-50 text-indigo-700 uppercase">NÂNG CẤP</span>
                                @endif
                            </a>
                        </div>

                        <!-- 3. [DÀNH CHO ADMIN / THỦ THƯ]: MỤC BẢNG ĐIỀU KHIỂN CONTROL PANEL -->
                        @if(in_array(Auth::user()->role, ['admin', 'librarian']))
                            <div class="border-t border-slate-100 pt-2 space-y-1">
                                <div class="px-2 text-[10px] font-black uppercase tracking-wider text-slate-400 flex items-center gap-1.5 mb-1">
                                    <span>⚙️</span>
                                    <span>BẢNG ĐIỀU KHIỂN CONTROL PANEL</span>
                                </div>
                                
                                <a href="{{ route('dashboard') }}"
                                   @click="userMenu = false"
                                   class="hover:bg-slate-100 text-slate-800 font-semibold text-xs p-2 rounded-xl flex items-center gap-2.5 transition {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                                    <div class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                        </svg>
                                    </div>
                                    <span>Tổng quan hệ thống (Dashboard)</span>
                                </a>

                                <a href="{{ route('admin.users.index') }}"
                                   @click="userMenu = false"
                                   class="hover:bg-slate-100 text-slate-800 font-semibold text-xs p-2 rounded-xl flex items-center gap-2.5 transition {{ request()->routeIs('admin.users.*') ? 'bg-amber-50 text-amber-700' : '' }}">
                                    <div class="w-6 h-6 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    </div>
                                    <span>Quản lý người dùng & Phân quyền</span>
                                </a>

                                <a href="{{ route('borrows.index') }}"
                                   @click="userMenu = false"
                                   class="hover:bg-slate-100 text-slate-800 font-semibold text-xs p-2 rounded-xl flex items-center gap-2.5 transition {{ request()->routeIs('borrows.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                                    <div class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <span>Quản lý mượn trả & Kho sách</span>
                                </a>

                                <a href="{{ route('admin.audit-logs.index') }}"
                                   @click="userMenu = false"
                                   class="hover:bg-slate-100 text-slate-800 font-semibold text-xs p-2 rounded-xl flex items-center gap-2.5 transition {{ request()->routeIs('admin.audit-logs.*') ? 'bg-emerald-50 text-emerald-700' : '' }}">
                                    <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <span>Nhật ký hệ thống (Audit Logs)</span>
                                </a>

                                <a href="{{ route('admin.finances.index') }}"
                                   @click="userMenu = false"
                                   class="hover:bg-slate-100 text-slate-800 font-semibold text-xs p-2 rounded-xl flex items-center gap-2.5 transition {{ request()->routeIs('admin.finances.*') ? 'bg-emerald-50 text-emerald-700' : '' }}">
                                    <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
                                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
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

            <!-- NÚT HAMBURGER MOBILE -->
            <button @click="openMobile = ! openMobile" 
                    type="button"
                    class="md:hidden inline-flex items-center justify-center p-2 rounded-xl text-slate-700 hover:text-slate-900 hover:bg-slate-100 border border-slate-200 focus:outline-none transition cursor-pointer shrink-0"
                    aria-label="Toggle navigation menu">
                <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{'hidden': openMobile, 'inline-flex': ! openMobile }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{'hidden': ! openMobile, 'inline-flex': openMobile }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
            <x-responsive-nav-link :href="url('/')" :active="request()->is('/')" class="text-xs uppercase tracking-wider font-bold text-slate-900 hover:text-indigo-600 hover:bg-slate-50">
                TRANG CHỦ
            </x-responsive-nav-link>

            <!-- 2. KHO SÁCH -->
            <x-responsive-nav-link :href="route('books.index')" :active="request()->routeIs('books.*')" class="text-xs uppercase tracking-wider font-bold text-slate-900 hover:text-indigo-600 hover:bg-slate-50">
                KHO SÁCH
            </x-responsive-nav-link>

            <!-- 3. MƯỢN / TRẢ SÁCH -->
            @auth
                @if(in_array(Auth::user()->role, ['admin', 'librarian']))
                    <x-responsive-nav-link :href="route('borrows.index')" :active="request()->routeIs('borrows.*')" class="text-xs uppercase tracking-wider font-bold text-slate-900 hover:text-indigo-600 hover:bg-slate-50">
                        MƯỢN / TRẢ SÁCH
                    </x-responsive-nav-link>
                @else
                    <x-responsive-nav-link :href="route('borrows.tracking')" :active="request()->routeIs('borrows.tracking')" class="text-xs uppercase tracking-wider font-bold text-slate-900 hover:text-indigo-600 hover:bg-slate-50">
                        MƯỢN / TRẢ SÁCH
                    </x-responsive-nav-link>
                @endif
            @else
                <x-responsive-nav-link :href="route('books.index')" class="text-xs uppercase tracking-wider font-bold text-slate-900 hover:text-indigo-600 hover:bg-slate-50">
                    MƯỢN / TRẢ SÁCH
                </x-responsive-nav-link>
            @endauth

            <!-- 4. HỘI QUÁN TRI THỨC MOBILE -->
            <div class="pt-2 pb-1 border-t border-slate-100">
                <div class="px-2 text-[11px] font-mono text-slate-400 font-bold uppercase tracking-wider mb-1">
                    🏛️ HỘI QUÁN TRI THỨC
                </div>
                <x-responsive-nav-link :href="route('forum.index')" :active="request()->routeIs('forum.*')" class="text-slate-900 text-xs font-semibold hover:bg-slate-100">
                    <span>💬 DIỄN ĐÀN THẢO LUẬN</span>
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="Route::has('entertainment.leaderboard') ? route('entertainment.leaderboard') : '#'" :active="request()->routeIs('entertainment.leaderboard')" class="text-slate-900 text-xs font-semibold hover:bg-slate-100">
                    <span>🏆 BẢNG PHONG THẦN / ĐUA TOP</span>
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('entertainment.index')" :active="request()->routeIs('entertainment.index')" class="text-slate-900 text-xs font-semibold hover:bg-slate-100">
                    <span>🎯 NHIỆM VỤ & ĐIỂM DANH</span>
                </x-responsive-nav-link>
            </div>

            <!-- 5. GÓI SCHOLAR VIP MOBILE -->
            <x-responsive-nav-link :href="route('membership.index')" :active="request()->routeIs('membership.*')" class="text-xs uppercase tracking-wider font-bold text-amber-700 hover:text-amber-900 hover:bg-amber-50/50">
                <span>👑 GÓI SCHOLAR VIP</span>
            </x-responsive-nav-link>

            <!-- 6. CONTROL PANEL MOBILE (DÀNH CHO ADMIN / THỦ THƯ) -->
            @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'librarian']))
                <div class="pt-2 pb-1 border-t border-slate-100">
                    <div class="px-2 text-[11px] font-mono text-slate-400 font-bold uppercase tracking-wider mb-1">
                        ⚙️ CONTROL PANEL (QUẢN TRỊ)
                    </div>
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-slate-900 text-xs font-semibold hover:bg-slate-100">
                        <span>📊 TỔNG QUAN HỆ THỐNG</span>
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" class="text-slate-900 text-xs font-semibold hover:bg-slate-100">
                        <span>👑 QUẢN LÝ NGƯỜI DÙNG</span>
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('borrows.index')" :active="request()->routeIs('borrows.*')" class="text-slate-900 text-xs font-semibold hover:bg-slate-100">
                        <span>📚 QUẢN LÝ MƯỢN TRẢ & KHO</span>
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.audit-logs.index')" :active="request()->routeIs('admin.audit-logs.*')" class="text-slate-900 text-xs font-semibold hover:bg-slate-100">
                        <span>📜 NHẬT KÝ HỆ THỐNG</span>
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.finances.index')" :active="request()->routeIs('admin.finances.*')" class="text-slate-900 text-xs font-semibold hover:bg-slate-100">
                        <span>💰 BÁO CÁO TÀI CHÍNH & QUỸ</span>
                    </x-responsive-nav-link>
                </div>
            @endif
        </div>

        <!-- THÔNG TIN ĐỘC GIẢ & NÚT ĐĂNG XUẤT MOBILE -->
        @auth
            <div class="pt-3 pb-4 border-t border-slate-100 px-4 space-y-3">
                <div class="flex items-center gap-3">
                    <div class="relative shrink-0">
                        {{-- VƯƠNG MIỆN ADMIN HOÀNG GIA --}}
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
                       class="text-xs font-bold text-emerald-700 font-mono tracking-tight flex-1">
                        💰 {{ number_format(Auth::user()->wallet->balance ?? (Auth::user()->getOrCreateWallet()->balance ?? 0)) }}đ
                    </a>
                    <a href="{{ route('wallet.deposit') }}"
                       @click="openMobile = false"
                       class="w-7 h-7 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                        +
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
