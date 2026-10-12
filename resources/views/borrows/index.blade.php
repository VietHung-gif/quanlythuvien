<x-app-layout>
    <div class="bg-slate-50 min-h-screen py-8" x-data="borrowManager()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- ========================================================================= -->
            <!-- 1. BANNER TIÊU ĐỀ & THANH ĐIỀU HƯỚNG NHANH (HERO SECTION)                   -->
            <!-- ========================================================================= -->
            <div class="bg-white border-2 border-slate-300 rounded-3xl p-6 sm:p-8 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-1.5">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 text-xs font-black uppercase tracking-wider">
                        <svg class="w-4 h-4 text-blue-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>PHÂN HỆ LƯU THÔNG & BÀN THỦ THƯ SỐ</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">
                        QUẢN LÝ MƯỢN / TRẢ SÁCH & KIỂM SOÁT LƯU THÔNG
                    </h1>
                    <p class="text-sm font-semibold text-slate-600 max-w-3xl">
                        Theo dõi luồng sách mượn, cảnh báo hạn trả công thái học, gia hạn trực tuyến & xử lý vi phạm tài liệu tức thì.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <a href="{{ route('borrows.create') }}" 
                       class="h-12 px-6 bg-blue-600 hover:bg-blue-700 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-sm transition flex items-center gap-2.5 active:scale-95 cursor-pointer whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>+ LẬP PHIẾU MƯỢN MỚI</span>
                    </a>

                    <a href="{{ route('books.index') }}" 
                       class="h-12 px-5 bg-slate-100 hover:bg-slate-200 border-2 border-slate-300 text-slate-800 font-black text-xs uppercase tracking-wider rounded-2xl transition flex items-center gap-2 active:scale-95 whitespace-nowrap">
                        <svg class="w-4 h-4 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span>KHO SÁCH</span>
                    </a>
                </div>
            </div>

            <!-- THÔNG BÁO FLASH TRUYỀN THỐNG (SUCCESS / ERROR) -->
            @if (session('success'))
                <div class="p-5 bg-emerald-50 border-2 border-emerald-300 rounded-2xl shadow-xs flex items-center justify-between text-emerald-950">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-200 flex items-center justify-center shrink-0 text-emerald-800">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-base font-bold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="p-5 bg-rose-50 border-2 border-rose-300 rounded-2xl shadow-xs flex items-center justify-between text-rose-950">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-200 flex items-center justify-center shrink-0 text-rose-800">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <span class="text-base font-bold">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- BANNER CẢNH BÁO KHI CÓ YÊU CẦU MƯỢN CHỜ DUYỆT -->
            @if($totalPending > 0)
                <div id="pending-alert-banner" class="bg-amber-100 border-2 border-amber-400 rounded-3xl p-5 sm:p-6 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start sm:items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-amber-200 text-amber-900 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-amber-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-black text-lg sm:text-xl text-amber-950 leading-tight">
                                HỆ THỐNG CÓ <span class="text-amber-800 underline pending-count-badge" id="banner-pending-count">{{ $totalPending }}</span> YÊU CẦU MƯỢN SÁCH ĐANG CHỜ DUYỆT!
                            </div>
                            <div class="text-sm font-bold text-amber-900 mt-1">
                                Vui lòng kiểm tra và duyệt phiếu để độc giả có thể đến nhận sách trực tiếp tại quầy thư viện.
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('borrows.index', ['status' => 'pending']) }}" 
                       class="h-12 px-6 bg-amber-600 hover:bg-amber-700 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-sm transition flex items-center justify-center gap-2 shrink-0 cursor-pointer active:scale-95 whitespace-nowrap">
                        <span>XEM & DUYỆT NGAY</span>
                        <span>→</span>
                    </a>
                </div>
            @endif

            <!-- BANNER CẢNH BÁO KHI CÓ YÊU CẦU GIA HẠN CHỜ DUYỆT -->
            @if(isset($totalRenewalPending) && $totalRenewalPending > 0)
                <div class="bg-purple-100 border-2 border-purple-400 rounded-3xl p-5 sm:p-6 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start sm:items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-purple-200 text-purple-900 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-purple-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-black text-lg sm:text-xl text-purple-950 leading-tight">
                                HỆ THỐNG CÓ <span class="text-purple-800 underline font-mono">{{ $totalRenewalPending }}</span> YÊU CẦU GIA HẠN SÁCH (+7 NGÀY) ĐANG CHỜ DUYỆT!
                            </div>
                            <div class="text-sm font-bold text-purple-900 mt-1">
                                Độc giả đã chủ động gửi yêu cầu gia hạn trực tuyến. Vui lòng xem xét và duyệt hạn trả mới cho bạn đọc.
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('borrows.index', ['status' => 'renewal_pending']) }}" 
                       class="h-12 px-6 bg-purple-600 hover:bg-purple-700 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-sm transition flex items-center justify-center gap-2 shrink-0 cursor-pointer active:scale-95 whitespace-nowrap">
                        <span>XEM & DUYỆT GIA HẠN</span>
                        <span>→</span>
                    </a>
                </div>
            @endif

            <!-- ========================================================================= -->
            <!-- 2. KHỐI THỐNG KÊ TỔNG QUAN DỄ NHÌN (BIG ACCESSIBLE STATS - 5 CARDS)         -->
            <!-- ========================================================================= -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- THẺ 1: CHỜ DUYỆT (AMBER) -->
                <a href="{{ route('borrows.index', ['status' => 'pending']) }}" 
                   class="bg-white border-2 {{ request('status') === 'pending' ? 'border-amber-500 ring-4 ring-amber-100 bg-amber-50/20' : 'border-slate-300 hover:border-amber-400' }} rounded-2xl p-5 shadow-sm flex items-center justify-between transition group cursor-pointer">
                    <div>
                        <div class="text-xs font-black tracking-wider text-slate-700 uppercase flex items-center gap-2">
                            <span>CHỜ DUYỆT</span>
                            @if($totalPending > 0)
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span>
                            @endif
                        </div>
                        <div class="text-3xl md:text-4xl font-black text-slate-950 mt-1 font-mono pending-count-badge" id="stat-pending-count">
                            {{ $totalPending }}
                        </div>
                        <div class="text-[11px] font-bold text-amber-700 mt-1">Cần xử lý trực tuyến</div>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-900 border-2 border-amber-300 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition">
                        <svg class="w-7 h-7 text-amber-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </a>

                <!-- THẺ 2: ĐANG MƯỢN (BLUE) -->
                <a href="{{ route('borrows.index', ['status' => 'borrowing']) }}" 
                   class="bg-white border-2 {{ request('status') === 'borrowing' ? 'border-blue-500 ring-4 ring-blue-100 bg-blue-50/20' : 'border-slate-300 hover:border-blue-400' }} rounded-2xl p-5 shadow-sm flex items-center justify-between transition group cursor-pointer">
                    <div>
                        <div class="text-xs font-black tracking-wider text-slate-700 uppercase">
                            ĐANG MƯỢN
                        </div>
                        <div class="text-3xl md:text-4xl font-black text-slate-950 mt-1 font-mono">
                            {{ $totalActive }}
                        </div>
                        <div class="text-[11px] font-bold text-blue-700 mt-1">Độc giả đang cầm giữ</div>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-900 border-2 border-blue-300 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition">
                        <svg class="w-7 h-7 text-blue-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                </a>

                <!-- THẺ 3: QUÁ HẠN (ROSE/RED) -->
                <a href="{{ route('borrows.index', ['status' => 'overdue']) }}" 
                   class="bg-white border-2 {{ request('status') === 'overdue' ? 'border-rose-500 ring-4 ring-rose-100 bg-rose-50/30' : ($totalOverdue > 0 ? 'bg-rose-50/20 border-rose-300 hover:border-rose-400' : 'border-slate-300 hover:border-rose-400') }} rounded-2xl p-5 shadow-sm flex items-center justify-between transition group cursor-pointer">
                    <div>
                        <div class="text-xs font-black tracking-wider text-slate-700 uppercase flex items-center gap-2">
                            <span>QUÁ HẠN</span>
                            @if($totalOverdue > 0)
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></span>
                            @endif
                        </div>
                        <div class="text-3xl md:text-4xl font-black text-rose-600 mt-1 font-mono">
                            {{ $totalOverdue }}
                        </div>
                        <div class="text-[11px] font-bold text-rose-700 mt-1">Cần thu hồi & phạt</div>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-rose-100 text-rose-900 border-2 border-rose-300 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition">
                        <svg class="w-7 h-7 text-rose-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </a>

                <!-- THẺ 4: ĐÃ HOÀN TẤT (EMERALD) -->
                <a href="{{ route('borrows.index', ['status' => 'returned']) }}" 
                   class="bg-white border-2 {{ request('status') === 'returned' ? 'border-emerald-500 ring-4 ring-emerald-100 bg-emerald-50/20' : 'border-slate-300 hover:border-emerald-400' }} rounded-2xl p-5 shadow-sm flex items-center justify-between transition group cursor-pointer">
                    <div>
                        <div class="text-xs font-black tracking-wider text-slate-700 uppercase">
                            ĐÃ HOÀN TẤT
                        </div>
                        <div class="text-3xl md:text-4xl font-black text-slate-950 mt-1 font-mono">
                            {{ $totalReturned }}
                        </div>
                        <div class="text-[11px] font-bold text-emerald-700 mt-1">Đã nhập lại kho</div>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-900 border-2 border-emerald-300 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition">
                        <svg class="w-7 h-7 text-emerald-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </a>

                <!-- THẺ 5: TIỀN PHẠT (PURPLE/VIOLET) -->
                <div class="bg-white border-2 border-slate-300 hover:border-purple-400 rounded-2xl p-5 shadow-sm flex items-center justify-between transition">
                    <div>
                        <div class="text-xs font-black tracking-wider text-slate-700 uppercase">
                            TIỀN PHẠT
                        </div>
                        <div class="text-2xl sm:text-3xl font-black text-purple-950 mt-1 font-mono">
                            {{ number_format($totalFines, 0, ',', '.') }} đ
                        </div>
                        <div class="text-[11px] font-bold text-purple-700 mt-1">Tổng cộng hệ thống</div>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-purple-100 text-purple-900 border-2 border-purple-300 flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-7 h-7 text-purple-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- 3. BÀN TRẢ SÁCH NHANH & BỘ LỌC CÔNG THÁI HỌC (QUICK RETURN DESK)           -->
            <!-- ========================================================================= -->
            <div class="bg-white border-2 border-slate-300 rounded-3xl p-5 sm:p-6 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-3 h-3 rounded-full bg-blue-600"></span>
                        <h2 class="text-base sm:text-lg font-black text-slate-950 uppercase tracking-tight">
                            BÀN TIẾP NHẬN & TRA CỨU TẠI QUẦY (QUICK COMMAND DESK)
                        </h2>
                    </div>
                    <span class="text-xs font-bold text-slate-500 font-mono">
                        Hỗ trợ quét Mã vạch ISBN, Mã phiếu #PKM, SĐT bạn đọc hoặc tên sách
                    </span>
                </div>

                <form method="GET" action="{{ route('borrows.index') }}" class="flex flex-col lg:flex-row items-stretch gap-3">
                    <!-- Ô NHẬP TỪ KHÓA TRA CỨU NHANH -->
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Quét mã vạch / Nhập mã phiếu #PKM0012, SĐT, Tên độc giả hoặc tựa sách..."
                               class="w-full h-13 bg-slate-50 focus:bg-white border-2 border-slate-300 focus:border-blue-600 rounded-2xl pl-12 pr-4 text-sm font-bold text-slate-950 placeholder:text-slate-400 placeholder:font-normal outline-none transition">
                    </div>

                    <!-- DROPDOWN TRẠNG THÁI -->
                    <div class="w-full lg:w-64">
                        <select name="status"
                                class="w-full h-13 bg-slate-50 focus:bg-white border-2 border-slate-300 focus:border-blue-600 rounded-2xl px-4 text-sm font-bold text-slate-800 outline-none transition cursor-pointer">
                            <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>-- TẤT CẢ TRẠNG THÁI --</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>CHỜ DUYỆT MƯỢN ({{ $totalPending }})</option>
                            <option value="renewal_pending" {{ request('status') === 'renewal_pending' ? 'selected' : '' }}>CHỜ DUYỆT GIA HẠN ({{ $totalRenewalPending ?? 0 }})</option>
                            <option value="borrowing" {{ request('status') === 'borrowing' ? 'selected' : '' }}>ĐANG MƯỢN ({{ $totalActive }})</option>
                            <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>CẢNH BÁO QUÁ HẠN ({{ $totalOverdue }})</option>
                            <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>ĐÃ HOÀN TẤT ({{ $totalReturned }})</option>
                        </select>
                    </div>

                    <!-- CỤM NÚT SUBMIT / RESET -->
                    <div class="flex items-center gap-2">
                        <button type="submit" 
                                class="h-13 px-7 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase tracking-wider rounded-2xl shadow-sm transition flex items-center justify-center gap-2 whitespace-nowrap active:scale-95 cursor-pointer shrink-0">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            <span>TRA CỨU / LỌC</span>
                        </button>

                        @if(request('search') || (request('status') && request('status') !== 'all'))
                            <a href="{{ route('borrows.index') }}"
                               class="h-13 px-4 bg-slate-100 hover:bg-slate-200 border-2 border-slate-300 text-slate-800 text-xs font-bold uppercase rounded-2xl transition flex items-center gap-1.5 justify-center whitespace-nowrap shrink-0"
                                title="Xóa toàn bộ bộ lọc">
                                <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                <span>XÓA LỌC</span>
                            </a>
                        @endif
                    </div>
                </form>

                <!-- CÁC THẺ LỌC NHANH DẠNG PILLS (QUICK FILTER PILLS) -->
                <div class="flex flex-wrap items-center gap-2 pt-1 text-xs">
                    <span class="font-bold text-slate-500 uppercase tracking-wider text-[11px] mr-1">Lọc nhanh:</span>
                    <a href="{{ route('borrows.index') }}" 
                       class="px-3 py-1.5 rounded-xl font-bold uppercase transition {{ (!request('status') || request('status') === 'all') ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-300' }}">
                        Tất cả ({{ $borrows->total() }})
                    </a>
                    <a href="{{ route('borrows.index', ['status' => 'pending']) }}" 
                       class="px-3 py-1.5 rounded-xl font-bold uppercase transition flex items-center gap-1.5 {{ request('status') === 'pending' ? 'bg-amber-600 text-white shadow-xs' : 'bg-amber-50 text-amber-900 hover:bg-amber-100 border border-amber-300' }}">
                        <span class="w-2 h-2 rounded-full bg-amber-500 {{ $totalPending > 0 ? 'animate-ping' : '' }}"></span>
                        <span>Chờ duyệt ({{ $totalPending }})</span>
                    </a>
                    <a href="{{ route('borrows.index', ['status' => 'renewal_pending']) }}" 
                       class="px-3 py-1.5 rounded-xl font-bold uppercase transition flex items-center gap-1.5 {{ request('status') === 'renewal_pending' ? 'bg-purple-600 text-white shadow-xs' : 'bg-purple-50 text-purple-900 hover:bg-purple-100 border border-purple-300' }}">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span>Chờ duyệt gia hạn ({{ $totalRenewalPending ?? 0 }})</span>
                        @if(($totalRenewalPending ?? 0) > 0)
                            <span class="w-2 h-2 rounded-full bg-purple-400 animate-ping"></span>
                        @endif
                    </a>
                    <a href="{{ route('borrows.index', ['status' => 'borrowing']) }}" 
                       class="px-3 py-1.5 rounded-xl font-bold uppercase transition flex items-center gap-1.5 {{ request('status') === 'borrowing' ? 'bg-blue-600 text-white shadow-xs' : 'bg-blue-50 text-blue-900 hover:bg-blue-100 border border-blue-300' }}">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        <span>Đang mượn ({{ $totalActive }})</span>
                    </a>
                    <a href="{{ route('borrows.index', ['status' => 'overdue']) }}" 
                       class="px-3 py-1.5 rounded-xl font-bold uppercase transition flex items-center gap-1.5 {{ request('status') === 'overdue' ? 'bg-rose-600 text-white shadow-xs' : 'bg-rose-50 text-rose-900 hover:bg-rose-100 border border-rose-300' }}">
                        <span class="w-2 h-2 rounded-full bg-rose-500 {{ $totalOverdue > 0 ? 'animate-pulse' : '' }}"></span>
                        <span>Quá hạn ({{ $totalOverdue }})</span>
                    </a>
                    <a href="{{ route('borrows.index', ['status' => 'returned']) }}" 
                       class="px-3 py-1.5 rounded-xl font-bold uppercase transition flex items-center gap-1.5 {{ request('status') === 'returned' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-900 hover:bg-emerald-100 border border-emerald-300' }}">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Đã trả ({{ $totalReturned }})</span>
                    </a>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- 4. DANH SÁCH THẺ MƯỢN THÔNG MINH (SMART BORROW CARD LIST)                   -->
            <!-- ========================================================================= -->
            <div class="space-y-5" id="borrows-container">
                @forelse ($borrows as $index => $borrow)
                    @php
                        $isOverdue = $borrow->isOverdue();
                        $overdueDays = $borrow->getOverdueDays();
                        $estFine = $borrow->calculateEstimatedFine();
                        $hasRenewed = str_contains($borrow->notes ?? '', '[Gia hạn]');
                        
                        // Tính số ngày còn lại & tiến độ mượn sách
                        $now = Carbon\Carbon::now()->startOfDay();
                        $borrowDate = $borrow->borrow_date ? Carbon\Carbon::parse($borrow->borrow_date)->startOfDay() : null;
                        $dueDate = $borrow->due_date ? Carbon\Carbon::parse($borrow->due_date)->startOfDay() : null;
                        
                        $daysLeft = $dueDate ? $now->diffInDays($dueDate, false) : null;
                        
                        // Tính phần trăm tiến trình thời gian mượn sách
                        $progressPercent = 0;
                        if ($borrowDate && $dueDate) {
                            $totalLoanDuration = max(1, $borrowDate->diffInDays($dueDate));
                            if ($borrow->status === 'returned' && $borrow->return_date) {
                                $progressPercent = 100;
                            } elseif ($isOverdue) {
                                $progressPercent = 100;
                            } else {
                                $elapsed = max(0, $borrowDate->diffInDays($now));
                                $progressPercent = min(100, max(0, round(($elapsed / $totalLoanDuration) * 100)));
                            }
                        }
                    @endphp

                    <!-- THẺ PHIẾU MƯỢN THÔNG MINH (SMART BORROW CARD) -->
                    <article id="borrow-row-{{ $borrow->id }}" 
                             class="bg-white border-2 {{ $isOverdue ? 'border-rose-400 border-l-8 border-l-rose-600 bg-rose-50/15' : ($borrow->status === 'pending' ? 'border-amber-400 border-l-8 border-l-amber-500 bg-amber-50/15' : ($borrow->status === 'returned' ? 'border-slate-300 bg-slate-50/40' : 'border-slate-300 hover:border-blue-400')) }} rounded-3xl p-6 sm:p-7 shadow-sm hover:shadow-md transition">
                        
                        <!-- HEADER CỦA THẺ: MÃ PHIẾU, ĐỘC GIẢ & BADGE ĐẾM NGƯỢC THÔNG MINH -->
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200 pb-5">
                            <div class="flex flex-wrap items-center gap-3">
                                <!-- Mã phiếu to rõ dạng Monospace -->
                                <span class="font-mono font-black text-slate-900 text-base md:text-lg bg-slate-100 px-3.5 py-1.5 rounded-xl border border-slate-300 shadow-2xs">
                                    #PKM{{ str_pad($borrow->id, 4, '0', STR_PAD_LEFT) }}
                                </span>

                                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    STT: #{{ ($borrows->firstItem() ?? 1) + $index }}
                                </span>

                                <span class="text-xs font-bold text-slate-600 font-mono flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Lập phiếu:</span>
                                    <strong class="text-slate-900">{{ $borrow->borrow_date ? $borrow->borrow_date->format('d/m/Y') : '—' }}</strong>
                                </span>

                                @php
                                    $isVipBorrow = ($borrow->user && $borrow->user->isVip()) || ($borrow->deposit_fee == 0 && in_array($borrow->delivery_method, ['counter_priority', 'home_delivery']));
                                    $shippingAddr = $borrow->shipping_address ?: ($borrow->user ? $borrow->user->delivery_address : null);
                                @endphp

                                @if($isVipBorrow)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-gradient-to-r from-amber-500 to-indigo-600 text-white text-xs font-black shadow-xs tracking-tight">
                                        <svg class="w-3.5 h-3.5 text-amber-200" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/>
                                        </svg>
                                        <span>[SCHOLAR VIP]</span>
                                    </span>
                                @endif

                                @if($borrow->delivery_method === 'home_delivery' || ($isVipBorrow && str_contains($borrow->notes ?? '', 'SHIP TẬN NƠI')))
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-blue-600 text-white text-xs font-bold shadow-xs">
                                        <svg class="w-3.5 h-3.5 text-blue-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                        </svg>
                                        <span>[SHIP TẬN NƠI: {{ Str::limit($shippingAddr ?: 'Theo địa chỉ', 35) }}]</span>
                                    </span>
                                @elseif($borrow->delivery_method === 'counter_priority' || ($isVipBorrow && str_contains($borrow->notes ?? '', 'ƯU TIÊN')))
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-amber-100 text-amber-900 border border-amber-300 text-xs font-bold shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                        <span>[LÀN ƯU TIÊN QUẦY]</span>
                                    </span>
                                @endif

                                @if($borrow->user_id)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-800 text-[11px] font-bold">
                                        <svg class="w-3 h-3 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span>Độc giả #{{ $borrow->user_id }}</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-slate-100 border border-slate-200 text-slate-600 text-[11px] font-bold">
                                        <svg class="w-3 h-3 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                        <span>Khách vãng lai</span>
                                    </span>
                                @endif
                            </div>

                            <!-- DYNAMIC COUNTDOWN BADGE (HUY HIỆU ĐẾM NGƯỢC THỜI HẠN) -->
                            <div>
                                @if($borrow->status === 'pending')
                                    <span class="px-4 py-2 rounded-2xl text-xs sm:text-sm font-black uppercase tracking-wider bg-amber-100 text-amber-950 border-2 border-amber-400 inline-flex items-center gap-2 shadow-2xs">
                                        <span class="w-2.5 h-2.5 rounded-full bg-amber-600 animate-ping"></span>
                                        <span>CHỜ DUYỆT TRỰC TUYẾN</span>
                                    </span>
                                @elseif($borrow->status === 'returned')
                                    <span class="px-4 py-2 rounded-2xl text-xs sm:text-sm font-black uppercase tracking-wider bg-emerald-100 text-emerald-950 border-2 border-emerald-400 inline-flex items-center gap-2 shadow-2xs">
                                        <svg class="w-4 h-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span>ĐÃ TRẢ SÁCH VÀO KHO</span>
                                    </span>
                                @elseif($borrow->status === 'rejected')
                                    <span class="px-4 py-2 rounded-2xl text-xs sm:text-sm font-black uppercase tracking-wider bg-slate-200 text-slate-800 border-2 border-slate-400 inline-flex items-center gap-2 shadow-2xs">
                                        <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        <span>ĐÃ TỪ CHỐI</span>
                                    </span>
                                @elseif($borrow->status === 'borrowing' && $borrow->renewal_status === 'pending')
                                    <span class="px-4 py-2 rounded-2xl text-xs sm:text-sm font-black uppercase tracking-wider bg-purple-100 text-purple-950 border-2 border-purple-400 inline-flex items-center gap-2 shadow-2xs animate-pulse">
                                        <span class="w-2.5 h-2.5 rounded-full bg-purple-600 animate-ping"></span>
                                        <span>YÊU CẦU GIA HẠN (+7 NGÀY) CHỜ DUYỆT</span>
                                    </span>
                                @elseif($isOverdue)
                                    <span class="px-4 py-2 rounded-2xl text-xs sm:text-sm font-black uppercase tracking-wider bg-rose-100 text-rose-950 border-2 border-rose-500 inline-flex items-center gap-2 shadow-2xs animate-pulse">
                                        <svg class="w-4 h-4 text-rose-600 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        <span>QUÁ HẠN {{ $overdueDays }} NGÀY • PHẠT {{ number_format($estFine, 0, ',', '.') }} đ</span>
                                    </span>
                                @elseif($daysLeft !== null && $daysLeft == 0)
                                    <span class="px-4 py-2 rounded-2xl text-xs sm:text-sm font-black uppercase tracking-wider bg-rose-100 text-rose-950 border-2 border-rose-400 inline-flex items-center gap-2 shadow-2xs">
                                        <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                                        <span>HẠN HÔM NAY — TRẢ SÁCH NGAY</span>
                                    </span>
                                @elseif($daysLeft !== null && $daysLeft <= 2)
                                    <span class="px-4 py-2 rounded-2xl text-xs sm:text-sm font-black uppercase tracking-wider bg-amber-100 text-amber-950 border-2 border-amber-400 inline-flex items-center gap-2 shadow-2xs">
                                        <span class="w-2.5 h-2.5 rounded-full bg-amber-600"></span>
                                        <span>SẮP ĐẾN HẠN — CÒN {{ $daysLeft }} NGÀY</span>
                                    </span>
                                @else
                                    <span class="px-4 py-2 rounded-2xl text-xs sm:text-sm font-black uppercase tracking-wider bg-emerald-100 text-emerald-950 border-2 border-emerald-400 inline-flex items-center gap-2 shadow-2xs">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                                        <span>TRONG HẠN AN TOÀN — CÒN {{ max(0, (int)$daysLeft) }} NGÀY</span>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- BODY CỦA THẺ: 2 CỘT (CỘT TRÁI: NGƯỜI MƯỢN & SÁCH; CỘT PHẢI: TIMELINE TIẾN ĐỘ & PHÍ PHẠT) -->
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 pt-5">
                            
                            <!-- CỘT 1: THÔNG TIN SÁCH & ĐỘC GIẢ (7 CỘT) -->
                            <div class="lg:col-span-7 space-y-4">
                                <div class="flex items-start gap-4">
                                    <!-- Bìa sách Vector động thu nhỏ sắc nét -->
                                    <div class="w-20 h-28 shrink-0 rounded-2xl overflow-hidden shadow-xs border-2 border-slate-200">
                                        <x-book-cover :book="$borrow->book" class="w-full h-full rounded-none shadow-none" />
                                    </div>

                                    <div class="flex-1 min-w-0 space-y-1.5">
                                        <!-- Tên sách to đậm, màu xanh than học thuật -->
                                        <h3 class="text-lg sm:text-xl font-black text-blue-950 leading-snug hover:text-blue-600 transition line-clamp-2">
                                            <a href="{{ route('books.show', $borrow->book_id) }}" class="hover:underline">
                                                {{ $borrow->book->title }}
                                            </a>
                                        </h3>

                                        <!-- Tác giả & Số lượng -->
                                        <div class="text-sm font-semibold text-slate-700">
                                            Tác giả: <span class="font-black text-slate-950">{{ $borrow->book->author }}</span>
                                            @if($borrow->book->category)
                                                <span class="inline-block ml-2 px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-xs font-bold">
                                                    {{ $borrow->book->category->name }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="text-xs font-black text-blue-900 flex items-center gap-2 pt-0.5">
                                            <span>Số lượng mượn:</span>
                                            <span class="px-2.5 py-0.5 rounded-lg bg-blue-100 text-blue-950 font-black">{{ $borrow->quantity ?? 1 }} cuốn</span>
                                            @if($hasRenewed)
                                                <span class="px-2 py-0.5 rounded-md bg-purple-100 text-purple-900 border border-purple-200 text-[11px] font-bold inline-flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-purple-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <span>Đã gia hạn 1 lần (+7 ngày)</span>
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Người mượn & Liên lạc trực quan -->
                                        <div class="pt-2 border-t border-slate-100 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs">
                                            <div class="font-black text-slate-950 text-sm flex items-center gap-1.5">
                                                <svg class="w-4 h-4 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                                <span>{{ $borrow->borrower_name }}</span>
                                                @if($isVipBorrow)
                                                    <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-900 border border-amber-300 text-[10px] font-black uppercase">
                                                        VIP
                                                    </span>
                                                @endif
                                            </div>
                                            @if($borrow->borrower_phone)
                                                <a href="tel:{{ $borrow->borrower_phone }}" class="text-blue-700 hover:underline font-bold flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                    </svg>
                                                    <span>{{ $borrow->borrower_phone }}</span>
                                                </a>
                                            @endif
                                            @if($borrow->borrower_email)
                                                <a href="mailto:{{ $borrow->borrower_email }}" class="text-slate-600 hover:underline font-medium truncate max-w-xs flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                    </svg>
                                                    <span>{{ $borrow->borrower_email }}</span>
                                                </a>
                                            @endif

                                            <!-- Tiền cọc sách -->
                                            <div class="w-full pt-1 flex items-center gap-2">
                                                @if($isVipBorrow)
                                                    <span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-200 inline-flex items-center gap-1.5 text-[11px] font-bold">
                                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                                        </svg>
                                                        <span>Tiền cọc: 0đ (Đặc quyền VIP miễn cọc)</span>
                                                    </span>
                                                @else
                                                    <span class="text-slate-700 bg-slate-100 px-2 py-0.5 rounded-lg border border-slate-200 inline-flex items-center gap-1.5 text-[11px] font-bold">
                                                        <svg class="w-3.5 h-3.5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                        </svg>
                                                        <span>Tiền cọc: {{ number_format($borrow->deposit_fee ?: ($borrow->deposit_amount ?: 50000), 0, ',', '.') }} đ</span>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        @if($borrow->delivery_method === 'home_delivery' || ($isVipBorrow && $shippingAddr))
                                            <div class="p-2.5 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-950 flex items-start gap-2">
                                                <svg class="w-4 h-4 text-blue-700 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                                </svg>
                                                <div>
                                                    <strong class="font-bold text-blue-900">Địa chỉ giao sách tận nơi:</strong>
                                                    <span class="text-blue-950 ml-1">{{ $shippingAddr }}</span>
                                                </div>
                                            </div>
                                        @elseif($borrow->delivery_method === 'counter_priority')
                                            <div class="p-2 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-950 flex items-center gap-1.5 font-bold">
                                                <svg class="w-4 h-4 text-amber-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                </svg>
                                                <span>Làn ưu tiên quầy: Soạn sẵn sách trong 30 phút để độc giả VIP lấy ngay.</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- GHI CHÚ NẾU CÓ -->
                                @if($borrow->notes)
                                    <div class="p-3 bg-slate-50 border-2 border-slate-200 rounded-2xl text-xs text-slate-700 font-medium whitespace-pre-line">
                                        <div class="flex items-center gap-1.5 font-black text-slate-900 mb-1">
                                            <svg class="w-3.5 h-3.5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span>Nhật ký phiếu:</span>
                                        </div>
                                        {{ $borrow->notes }}
                                    </div>
                                @endif
                            </div>

                            <!-- CỘT 2: THANH TIẾN TRÌNH THỜI HẠN (LOAN TIMELINE BAR) & PHÍ PHẠT (5 CỘT) -->
                            <div class="lg:col-span-5 flex flex-col justify-between space-y-3 bg-slate-50/70 p-4 rounded-2xl border border-slate-200">
                                
                                <!-- LOAN TIMELINE BAR SECTION -->
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-black text-slate-800 uppercase tracking-wider">TIẾN TRÌNH THỜI HẠN MƯỢN</span>
                                        <span class="font-mono font-black {{ $isOverdue ? 'text-rose-600' : 'text-blue-700' }}">
                                            {{ $progressPercent }}% thời gian
                                        </span>
                                    </div>

                                    <!-- THANH PROGRESS BAR -->
                                    <div class="w-full h-3.5 bg-slate-200 rounded-full overflow-hidden p-0.5 border border-slate-300">
                                        <div class="h-full rounded-full transition-all duration-500 {{ $isOverdue ? 'bg-rose-600 animate-pulse' : ($progressPercent > 80 ? 'bg-amber-500' : 'bg-emerald-500') }}" 
                                             style="width: {{ $progressPercent }}%"></div>
                                    </div>

                                    <!-- 2 ĐẦU MỐC THỜI GIAN CỦA TIMELINE -->
                                    <div class="flex items-center justify-between text-[11px] font-mono text-slate-600 pt-0.5">
                                        <div>
                                            <span class="text-slate-400 block text-[10px] uppercase font-bo                                <!-- THÔNG TIN TRẢ THỰC TẾ (NẾU ĐÃ TRẢ) -->
                                @if($borrow->status === 'returned')
                                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-950 font-bold flex items-center justify-between">
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                            </svg>
                                            <span>Ngày trả thực tế: <strong class="font-black font-mono">{{ $borrow->return_date ? $borrow->return_date->format('d/m/Y') : '—' }}</strong></span>
                                        </div>
                                        <span class="text-[11px] font-black text-emerald-700 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span>ĐÃ NHẬP KHO</span>
                                        </span>
                                    </div>
                                @endif

                                <!-- KHỐI THÔNG BÁO TIỀN PHẠT NẾU QUÁ HẠN HOẶC CÓ TIỀN PHẠT -->
                                @if($borrow->fine_amount > 0 || $isOverdue)
                                    @php
                                        $displayFine = $borrow->fine_amount > 0 ? $borrow->fine_amount : $estFine;
                                    @endphp
                                    <div class="p-3.5 {{ $borrow->fine_status === 'paid' ? 'bg-emerald-50 border-2 border-emerald-300 text-emerald-950' : 'bg-rose-50 border-2 border-rose-400 text-rose-950' }} rounded-2xl flex items-center justify-between gap-3 shadow-2xs">
                                        <div class="space-y-0.5">
                                            <div class="text-xs font-black uppercase flex items-center gap-1.5">
                                                @if($borrow->fine_status === 'paid')
                                                    <svg class="w-4 h-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <span>TIỀN PHẠT ĐÃ THANH TOÁN</span>
                                                @else
                                                    <svg class="w-4 h-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                    </svg>
                                                    <span>TIỀN PHẠT QUÁ HẠN</span>
                                                @endif
                                            </div>
                                            <div class="text-[11px] font-bold text-slate-700">
                                                Mức phạt: 5.000 đ/ngày quá hạn
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-base sm:text-lg font-black font-mono {{ $borrow->fine_status === 'paid' ? 'text-emerald-700' : 'text-rose-700' }}">
                                                {{ number_format($displayFine, 0, ',', '.') }} đ
                                            </div>
                                            <span class="inline-block text-[10px] font-black uppercase px-2 py-0.5 rounded-md {{ $borrow->fine_status === 'paid' ? 'bg-emerald-200 text-emerald-900' : 'bg-rose-200 text-rose-900' }}">
                                                {{ $borrow->fine_status === 'paid' ? 'ĐÃ THU TẠI QUẦY' : 'CHƯA THANH TOÁN' }}
                                            </span>
                                        </div>
                                    </div>
                                @endif

                            </div>

                        </div>

                        <!-- FOOTER CỦA THẺ: CÁC NÚT THAO TÁC CÔNG THÁI HỌC (ACTION TOOLBAR) -->
                        <div class="mt-5 pt-4 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3">
                            
                            <div class="text-xs font-bold text-slate-500">
                                @if($borrow->status === 'borrowing')
                                    <span>Hành động tại quầy: Tiếp nhận trả sách hoặc gia hạn online</span>
                                @elseif($borrow->status === 'pending')
                                    <span>Yêu cầu trực tuyến: Cần thủ thư duyệt trước khi giao sách</span>
                                @else
                                    <span>Hồ sơ đã lưu trữ trong nhật ký lưu thông</span>
                                @endif
                            </div>

                            <!-- CỤM NÚT HÀNH ĐỘNG CHÍNH -->
                            <div class="flex flex-wrap items-center gap-2">
                                
                                <!-- 1. HÀNH ĐỘNG CHO TRẠNG THÁI CHỜ DUYỆT (PENDING) -->
                                @if($borrow->status === 'pending')
                                    <form action="{{ route('borrows.approve', $borrow->id) }}" 
                                          method="POST" 
                                          class="inline-block"
                                          @submit.prevent="handleApprove($event, {{ $borrow->id }}, '{{ addslashes($borrow->book->title) }}', '{{ addslashes($borrow->borrower_name) }}')">
                                        @csrf
                                        <button type="submit"
                                                class="h-12 px-6 rounded-2xl text-xs font-black uppercase tracking-wider text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition cursor-pointer flex items-center gap-2 active:scale-95">
                                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span>DUYỆT MƯỢN</span>
                                        </button>
                                    </form>

                                    <button type="button"
                                            @click="openRejectModal('{{ route('borrows.reject', $borrow->id) }}', '{{ addslashes($borrow->book->title) }}', '{{ addslashes($borrow->borrower_name) }}', {{ $borrow->id }})"
                                            class="h-12 px-5 rounded-2xl text-xs font-black uppercase tracking-wider text-rose-700 bg-rose-100 hover:bg-rose-200 border-2 border-rose-300 transition shadow-2xs cursor-pointer flex items-center gap-1.5 active:scale-95">
                                        <svg class="w-4 h-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        <span>TỪ CHỐI</span>
                                    </button>
                                @endif

                                <!-- 2. HÀNH ĐỘNG CHO TRẠNG THÁI ĐANG MƯỢN (BORROWING) -->
                                @if($borrow->status === 'borrowing')
                                    <!-- NẾU CÓ YÊU CẦU GIA HẠN ĐANG CHỜ DUYỆT (pending) -->
                                    @if($borrow->renewal_status === 'pending')
                                        <!-- Nút xanh: [ĐỒNG Ý GIA HẠN] -->
                                        <form action="{{ route('admin.borrows.approve-renewal', $borrow->id) }}" 
                                              method="POST" 
                                              class="inline-block"
                                              @submit.prevent="handleApproveRenewal($event, {{ $borrow->id }}, '{{ addslashes($borrow->book->title) }}', '{{ addslashes($borrow->borrower_name) }}')">
                                            @csrf
                                            <button type="submit"
                                                    class="h-12 px-5 rounded-2xl text-xs font-black uppercase tracking-wider text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition cursor-pointer flex items-center gap-2 active:scale-95"
                                                    title="Đồng ý gia hạn thêm 7 ngày (Thưởng +15 EXP cho độc giả)">
                                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                <span>ĐỒNG Ý GIA HẠN</span>
                                            </button>
                                        </form>

                                        <!-- Nút đỏ viền mảnh: [TỪ CHỐI GIA HẠN] -->
                                        <form action="{{ route('admin.borrows.reject-renewal', $borrow->id) }}" 
                                              method="POST" 
                                              class="inline-block"
                                              @submit.prevent="handleRejectRenewal($event, {{ $borrow->id }}, '{{ addslashes($borrow->book->title) }}', '{{ addslashes($borrow->borrower_name) }}')">
                                            @csrf
                                            <button type="submit"
                                                    class="h-12 px-4 rounded-2xl text-xs font-black uppercase tracking-wider text-rose-700 bg-rose-50 hover:bg-rose-100 border-2 border-rose-300 transition shadow-2xs cursor-pointer flex items-center gap-1.5 active:scale-95"
                                                    title="Từ chối yêu cầu gia hạn sách">
                                                <svg class="w-4 h-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                <span>TỪ CHỐI GIA HẠN</span>
                                            </button>
                                        </form>
                                    @elseif($borrow->renewal_status === 'approved')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span>ĐÃ ĐƯỢC GIA HẠN (+7 NGÀY)</span>
                                        </span>
                                    @elseif($borrow->renewal_status === 'rejected')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200" title="{{ $borrow->notes }}">
                                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            <span>ĐÃ TỪ CHỐI GIA HẠN</span>
                                        </span>
                                    @endif

                                    <!-- NÚT TRẢ SÁCH CHI TIẾT (MỞ MODAL) -->
                                    <button type="button"
                                            @click="openReturnModal(
                                                '{{ route('borrows.return', $borrow->id) }}',
                                                '{{ addslashes($borrow->book->title) }}',
                                                '{{ addslashes($borrow->borrower_name) }}',
                                                '{{ $borrow->due_date ? $borrow->due_date->format('d/m/Y') : '' }}',
                                                {{ $overdueDays }},
                                                {{ $estFine }},
                                                {{ $borrow->deposit_amount > 0 ? (float) $borrow->deposit_amount : 50000 }}
                                            )"
                                            class="h-12 px-6 rounded-2xl text-xs font-black uppercase tracking-wider text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition cursor-pointer flex items-center gap-2 active:scale-95">
                                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                        </svg>
                                        <span>XÁC NHẬN TRẢ SÁCH</span>
                                    </button>
                                @endif

                                <!-- 3. NÚT THU TIỀN PHẠT TẠI QUẦY (KHI CÓ TIỀN PHẠT CHƯA THU) -->
                                @if($borrow->fine_amount > 0 && $borrow->fine_status === 'unpaid')
                                    <form action="{{ route('borrows.payFine', $borrow->id) }}" 
                                          method="POST" 
                                          class="inline-block" 
                                          onsubmit="return confirm('Xác nhận đã thu đủ số tiền phạt {{ number_format($borrow->fine_amount, 0, ',', '.') }} VNĐ từ độc giả {{ addslashes($borrow->borrower_name) }}?');">
                                        @csrf
                                        <button type="submit" 
                                                class="h-12 px-5 rounded-2xl text-xs font-black uppercase tracking-wider text-white bg-amber-600 hover:bg-amber-700 shadow-sm transition cursor-pointer flex items-center gap-2 active:scale-95" 
                                                title="Xác nhận thu tiền phạt tại quầy">
                                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            <span>THU PHẠT ({{ number_format($borrow->fine_amount, 0, ',', '.') }} đ)</span>
                                        </button>
                                    </form>
                                @endif

                                <!-- 4. NÚT XÓA BẢN GHI (CHỈ DÀNH CHO ADMIN / THỦ THƯ) -->
                                <form action="{{ route('borrows.destroy', $borrow->id) }}" 
                                      method="POST" 
                                      class="inline-block" 
                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa phiếu mượn này khỏi hệ thống? (Nếu đang mượn thì tồn kho sẽ được hoàn trả)');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="h-12 w-12 rounded-2xl text-slate-400 hover:text-rose-600 bg-slate-100 hover:bg-rose-50 border-2 border-slate-200 transition flex items-center justify-center cursor-pointer active:scale-95" 
                                            title="Xóa phiếu mượn">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>

                            </div>

                        </div>

                    </article>
                @empty
                    <!-- TRẠNG THÁI TRỐNG -->
                    <div class="bg-white rounded-3xl border-2 border-slate-300 p-12 text-center text-slate-500 space-y-4 shadow-sm">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center border border-slate-200">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-black text-slate-900 tracking-tight">
                            CHƯA CÓ PHIẾU MƯỢN SÁCH NÀO PHÙ HỢP
                        </h3>
                        <p class="text-sm font-semibold text-slate-600 max-w-md mx-auto">
                            Không tìm thấy phiếu mượn nào theo điều kiện tìm kiếm hiện tại. Bạn có thể xóa bộ lọc hoặc lập phiếu mượn mới cho bạn đọc.
                        </p>
                        <div class="pt-2 flex flex-wrap items-center justify-center gap-3">
                            <a href="{{ route('borrows.index') }}" 
                               class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-wider text-white bg-slate-900 hover:bg-slate-800 shadow-sm transition">
                                HIỂN THỊ TẤT CẢ PHIẾU
                            </a>
                            <a href="{{ route('borrows.create') }}" 
                               class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-wider text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                                + LẬP PHIẾU MỚI
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- ========================================================================= -->
            <!-- 5. THANH PHÂN TRANG RÕ RÀNG (EASY PAGINATION CHO MỌI ĐỘ TUỔI)              -->
            <!-- ========================================================================= -->
            @if ($borrows->hasPages())
                <div class="bg-white border-2 border-slate-300 rounded-3xl p-5 sm:p-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                    <!-- Nút Trang trước -->
                    @if ($borrows->onFirstPage())
                        <span class="opacity-50 cursor-not-allowed px-6 py-3.5 rounded-2xl border-2 border-slate-200 font-black text-xs uppercase text-slate-400 bg-slate-100 flex items-center gap-2 select-none">
                            ← TRANG TRƯỚC
                        </span>
                    @else
                        <a href="{{ $borrows->previousPageUrl() }}" 
                           class="px-6 py-3.5 rounded-2xl border-2 border-slate-300 hover:border-blue-600 font-black text-xs uppercase text-slate-900 hover:text-blue-600 bg-white hover:bg-blue-50 shadow-sm transition flex items-center gap-2 cursor-pointer active:scale-95">
                            ← TRANG TRƯỚC
                        </a>
                    @endif

                    <!-- Hiển thị số trang hiện tại to rõ -->
                    <div class="text-center font-black text-slate-900 text-sm sm:text-base">
                        TRANG <span class="text-blue-600 font-mono text-xl sm:text-2xl">{{ $borrows->currentPage() }}</span> / <span class="font-mono text-xl sm:text-2xl">{{ $borrows->lastPage() }}</span>
                        <div class="text-xs font-semibold text-slate-500 font-normal mt-0.5">
                            (Tổng cộng {{ number_format($borrows->total()) }} phiếu mượn tài liệu)
                        </div>
                    </div>

                    <!-- Nút Trang sau -->
                    @if ($borrows->hasMorePages())
                        <a href="{{ $borrows->nextPageUrl() }}" 
                           class="px-6 py-3.5 rounded-2xl border-2 border-slate-300 hover:border-blue-600 font-black text-xs uppercase text-slate-900 hover:text-blue-600 bg-white hover:bg-blue-50 shadow-sm transition flex items-center gap-2 cursor-pointer active:scale-95">
                            TRANG SAU →
                        </a>
                    @else
                        <span class="opacity-50 cursor-not-allowed px-6 py-3.5 rounded-2xl border-2 border-slate-200 font-black text-xs uppercase text-slate-400 bg-slate-100 flex items-center gap-2 select-none">
                            TRANG SAU →
                        </span>
                    @endif
                </div>
            @endif

        </div>

        <!-- ========================================================================= -->
        <!-- MODAL TRẢ SÁCH (ACCESSIBLE LIGHT RETURN DIALOG)                           -->
        <!-- ========================================================================= -->
        <div x-show="returnModalOpen"
             x-cloak
             style="display: none;"
             class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 text-center">
                <div x-show="returnModalOpen"
                     @click="returnModalOpen = false"
                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

                <div x-show="returnModalOpen"
                     class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full text-left relative z-10 border-2 border-slate-300 shadow-2xl my-8">

                    <form :action="actionUrl" method="POST">
                        @csrf
                        <div class="text-center pb-4 border-b border-slate-200">
                            <h3 class="text-2xl font-black text-slate-950 uppercase tracking-tight flex items-center justify-center gap-2">
                                <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                </svg>
                                <span>XÁC NHẬN HOÀN TRẢ SÁCH</span>
                            </h3>
                            <p class="text-sm text-slate-600 font-bold mt-1">
                                Độc giả: <span class="text-blue-600 font-black" x-text="borrowerName"></span><br>
                                Cuốn sách: <span class="text-slate-900 font-black" x-text="bookTitle"></span>
                            </p>
                        </div>

                        <div class="mt-5 space-y-4 text-sm">
                            <!-- BẢNG ĐỐI SOÁT TÀI CHÍNH MINH BẠCH (FINANCIAL RECONCILIATION) -->
                            <div class="p-4 bg-slate-50 border-2 border-slate-200 rounded-2xl space-y-2">
                                <div class="flex items-center justify-between text-[11px] font-black uppercase text-slate-500 tracking-wider">
                                    <span>ĐỐI SOÁT TÀI CHÍNH MƯỢN / TRẢ</span>
                                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                                    </svg>
                                </div>
                                <div class="space-y-1.5 text-xs font-bold">
                                    <div class="flex items-center justify-between text-slate-700">
                                        <span>• Tiền cọc ban đầu (Tạm giữ):</span>
                                        <span class="font-mono text-emerald-700 font-black" x-text="'+' + new Intl.NumberFormat('vi-VN').format(depositAmount) + ' đ'">+50.000 đ</span>
                                    </div>
                                    <div class="flex items-center justify-between text-slate-700">
                                        <span>• Phí phạt trễ hạn (<span x-text="overdueDays"></span> ngày trễ):</span>
                                        <span class="font-mono font-black" :class="fineEstimate > 0 ? 'text-rose-600' : 'text-slate-500'" x-text="'-' + new Intl.NumberFormat('vi-VN').format(fineEstimate) + ' đ'">-0 đ</span>
                                    </div>
                                    <div class="pt-2 border-t border-slate-200 flex items-center justify-between text-xs">
                                        <span class="font-black text-slate-900 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-blue-600 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                            </svg>
                                            <span>Tiền hoàn trả thực tế về ví:</span>
                                        </span>
                                        <span class="font-mono font-black text-sm"
                                              :class="(depositAmount - fineEstimate) >= 0 ? 'text-emerald-700' : 'text-rose-600'"
                                              x-text="(depositAmount - fineEstimate >= 0 ? '+' : '') + new Intl.NumberFormat('vi-VN').format(depositAmount - fineEstimate) + ' đ'">
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <template x-if="overdueDays > 0">
                                <div class="p-4 bg-rose-100 border-2 border-rose-400 rounded-2xl text-rose-950">
                                    <div class="font-black text-base flex items-center gap-2">
                                        <svg class="w-5 h-5 text-rose-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        <span>SÁCH BỊ QUÁ HẠN <span x-text="overdueDays"></span> NGÀY!</span>
                                    </div>
                                    <div class="text-xs font-bold mt-1">
                                        Hệ thống sẽ tự động trừ phí phạt từ tiền cọc & hoàn phần dư về ví độc giả.
                                    </div>
                                </div>
                            </template>

                            <template x-if="overdueDays <= 0">
                                <div class="p-3 bg-emerald-50 border-2 border-emerald-300 rounded-2xl text-emerald-950 text-xs font-bold flex items-center gap-2">
                                    <svg class="w-5 h-5 text-emerald-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>Trả sách đúng hạn! Hoàn 100% tiền cọc về ví & thưởng <strong>+50 EXP</strong> cho độc giả.</span>
                                </div>
                            </template>

                            <div>
                                <label class="block text-xs font-black uppercase text-slate-800 mb-1">
                                    NGÀY THỰC TẾ TRẢ SÁCH:
                                </label>
                                <input type="date"
                                       name="return_date"
                                       value="{{ date('Y-m-d') }}"
                                       required
                                       class="h-12 bg-white border-2 border-slate-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 w-full rounded-2xl text-sm font-bold py-2 px-4 text-slate-900">
                            </div>

                            <div>
                                <label class="block text-xs font-black uppercase text-slate-800 mb-1">
                                    TIỀN PHẠT THỰC THU (VNĐ):
                                </label>
                                <input type="number"
                                       name="custom_fine"
                                       :value="fineEstimate"
                                       min="0"
                                       step="1000"
                                       class="h-12 bg-white border-2 border-slate-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 w-full rounded-2xl text-sm font-mono font-bold py-2 px-4 text-slate-900">
                            </div>

                            <div>
                                <label class="block text-xs font-black uppercase text-slate-800 mb-1">
                                    GHI CHÚ KHI NHẬN LẠI SÁCH:
                                </label>
                                <textarea name="return_notes"
                                          rows="2"
                                          placeholder="Tình trạng sách khi nhận lại (nguyên vẹn, rách bìa...)"
                                          class="bg-white border-2 border-slate-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 w-full rounded-2xl text-xs font-normal py-2.5 px-4 text-slate-900"></textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                            <button type="button"
                                    @click="returnModalOpen = false"
                                    class="h-12 px-5 rounded-2xl font-bold text-xs uppercase text-slate-700 bg-slate-100 hover:bg-slate-200 border-2 border-slate-300 transition cursor-pointer">
                                HỦY BỎ
                            </button>
                            <button type="submit"
                                    class="h-12 px-6 rounded-2xl font-black text-xs uppercase tracking-wider bg-blue-600 hover:bg-blue-700 text-white shadow-sm transition cursor-pointer">
                                XÁC NHẬN NHẬP KHO
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL TỪ CHỐI YÊU CẦU MƯỢN (ACCESSIBLE LIGHT REJECT DIALOG)                -->
        <!-- ========================================================================= -->
        <div x-show="rejectModalOpen"
             x-cloak
             style="display: none;"
             class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 text-center">
                <div x-show="rejectModalOpen"
                     @click="rejectModalOpen = false"
                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

                <div x-show="rejectModalOpen"
                     class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full text-left relative z-10 border-2 border-slate-300 shadow-2xl my-8">

                    <form :action="rejectUrl" method="POST" @submit.prevent="handleRejectSubmit($event)">
                        @csrf
                        <div class="text-center pb-4 border-b border-slate-200">
                            <h3 class="text-2xl font-black text-rose-600 uppercase tracking-tight flex items-center justify-center gap-2">
                                <svg class="w-6 h-6 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                                <span>TỪ CHỐI YÊU CẦU MƯỢN</span>
                            </h3>
                            <p class="text-sm text-slate-600 font-bold mt-1">
                                Độc giả: <span class="text-slate-950 font-black" x-text="borrowerName"></span><br>
                                Cuốn sách: <span class="text-blue-600 font-bold" x-text="bookTitle"></span>
                            </p>
                        </div>

                        <div class="mt-5 space-y-4 text-sm">
                            <!-- CÁC LÝ DO MẪU GỢI Ý NHANH -->
                            <div>
                                <label class="block text-xs font-black uppercase text-slate-800 mb-1.5">
                                    CHỌN NHANH LÝ DO MẪU:
                                </label>
                                <div class="flex flex-wrap gap-1.5">
                                    <button type="button" 
                                            @click="setRejectReason('Sách hiện đang trong đợt kiểm kê và bảo quản định kỳ.')"
                                            class="px-2.5 py-1 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-[11px] font-bold text-slate-800 transition cursor-pointer flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                        <span>Đang kiểm kê</span>
                                    </button>
                                    <button type="button" 
                                            @click="setRejectReason('Tài khoản còn tiền phạt chưa thanh toán, vui lòng nộp phạt trước.')"
                                            class="px-2.5 py-1 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-[11px] font-bold text-slate-800 transition cursor-pointer flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <span>Chưa nộp phạt</span>
                                    </button>
                                    <button type="button" 
                                            @click="setRejectReason('Đã đạt giới hạn số lượng sách mượn cùng lúc theo nội quy.')"
                                            class="px-2.5 py-1 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-[11px] font-bold text-slate-800 transition cursor-pointer flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        <span>Quá giới hạn mượn</span>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-black uppercase text-slate-800 mb-1">
                                    NỘI DUNG LÝ DO TỪ CHỐI: <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="reject_reason"
                                          x-model="rejectReason"
                                          rows="3"
                                          required
                                          placeholder="Nhập lý do từ chối để hệ thống thông báo cho độc giả..."
                                          class="bg-white border-2 border-slate-300 focus:border-rose-600 focus:ring-2 focus:ring-rose-100 w-full rounded-2xl text-xs font-normal py-2.5 px-4 text-slate-900"></textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                            <button type="button"
                                    @click="rejectModalOpen = false"
                                    class="h-12 px-5 rounded-2xl font-bold text-xs uppercase text-slate-700 bg-slate-100 hover:bg-slate-200 border-2 border-slate-300 transition cursor-pointer">
                                ĐÓNG LẠI
                            </button>
                            <button type="submit"
                                    class="h-12 px-6 rounded-2xl font-black text-xs uppercase tracking-wider bg-rose-600 hover:bg-rose-700 text-white shadow-sm transition cursor-pointer">
                                XÁC NHẬN TỪ CHỐI
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- TOAST CONTAINER CHO THÔNG BÁO TỨC THÌ (SMOOTH NOTIFICATIONS) -->
        <div class="fixed bottom-6 right-6 z-50 flex flex-col gap-2.5 max-w-sm pointer-events-none">
            <template x-for="toast in toasts" :key="toast.id">
                <div x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-2 scale-90"
                     class="pointer-events-auto flex items-start gap-3 p-4 rounded-2xl border-2 border-slate-300 bg-white shadow-2xl"
                     :class="{
                         'border-l-8 border-l-emerald-500': toast.type === 'success',
                         'border-l-8 border-l-rose-500': toast.type === 'error',
                         'border-l-8 border-l-purple-500': toast.type === 'purple',
                         'border-l-8 border-l-amber-500': toast.type === 'info',
                     }">
                    <div class="shrink-0 mt-0.5">
                        <template x-if="toast.type === 'success'">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </template>
                        <template x-if="toast.type === 'purple'">
                            <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </template>
                        <template x-if="toast.type === 'error'">
                            <svg class="w-6 h-6 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </template>
                        <template x-if="toast.type === 'info'">
                            <svg class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </template>
                    </div>
                    <div class="flex-1 text-xs">
                        <div class="font-black uppercase tracking-wider text-slate-900" x-text="toast.type === 'success' ? 'THÀNH CÔNG' : (toast.type === 'purple' ? 'GIA HẠN THÀNH CÔNG' : (toast.type === 'info' ? 'THÔNG BÁO' : 'LỖI HỆ THỐNG'))"></div>
                        <div class="mt-0.5 font-bold leading-relaxed text-slate-700" x-text="toast.message"></div>
                    </div>
                    <button type="button" @click="removeToast(toast.id)" class="text-slate-400 hover:text-slate-800 text-xl font-bold leading-none cursor-pointer">&times;</button>
                </div>
            </template>
        </div>

    </div>

    <!-- JAVASCRIPT / ALPINE.JS CHO BORROWS MANAGER -->
    <script>
        function borrowManager() {
            return {
                returnModalOpen: false,
                rejectModalOpen: false,
                actionUrl: '',
                rejectUrl: '',
                currentBorrowId: null,
                bookTitle: '',
                borrowerName: '',
                dueDate: '',
                overdueDays: 0,
                fineEstimate: 0,
                depositAmount: 50000,
                rejectReason: 'Sách đang trong đợt kiểm kê hoặc độc giả cần thanh toán tiền phạt cũ trước.',
                toasts: [],

                setRejectReason(reason) {
                    this.rejectReason = reason;
                },

                openReturnModal(url, title, name, due, days, fine, deposit = 50000) {
                    this.actionUrl = url;
                    this.bookTitle = title;
                    this.borrowerName = name;
                    this.dueDate = due;
                    this.overdueDays = days;
                    this.fineEstimate = fine;
                    this.depositAmount = deposit || 50000;
                    this.returnModalOpen = true;
                },

                openRejectModal(url, title, name, borrowId = null) {
                    this.rejectUrl = url;
                    this.bookTitle = title;
                    this.borrowerName = name;
                    this.currentBorrowId = borrowId;
                    this.rejectReason = 'Sách đang trong đợt kiểm kê hoặc độc giả cần thanh toán tiền phạt cũ trước.';
                    this.rejectModalOpen = true;
                },

                showToast(message, type = 'success') {
                    const id = Date.now() + Math.random();
                    this.toasts.push({ id, message, type });
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, 4000);

                    if (window.toast && typeof window.toast === 'function') {
                        window.toast(message, type);
                    }
                },

                removeToast(id) {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                },

                async handleApprove(event, borrowId, bookTitle, borrowerName) {
                    if (!confirm(`XÁC NHẬN: Phê duyệt cho độc giả "${borrowerName}" mượn cuốn sách "${bookTitle}"?\nHệ thống sẽ trừ 1 cuốn vào kho sách khả dụng.`)) {
                        return;
                    }

                    const form = event.target;
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalText = submitBtn ? submitBtn.innerHTML : '';
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<svg class="w-4 h-4 mr-1.5 animate-spin inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Đang duyệt...';
                    }

                    try {
                        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: new FormData(form)
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            this.showToast(data.message || 'Đã phê duyệt thành công!', 'success');
                            this.animateRowRemoval(borrowId);
                            if (data.remaining_pending !== undefined) {
                                this.updatePendingBadges(data.remaining_pending);
                            }
                        } else {
                            this.showToast(data.message || 'Có lỗi xảy ra khi phê duyệt', 'error');
                        }
                    } catch (err) {
                        console.error(err);
                        this.showToast('Không thể kết nối đến máy chủ. Vui lòng thử lại!', 'error');
                    } finally {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }
                    }
                },

                async handleApproveRenewal(event, borrowId, bookTitle, borrowerName) {
                    if (!confirm(`XÁC NHẬN: Đồng ý duyệt gia hạn thêm 7 ngày mượn cho độc giả "${borrowerName}" (cuốn sách "${bookTitle}")?\nHạn trả mới sẽ được cộng thêm 7 ngày và độc giả nhận +15 EXP.`)) {
                        return;
                    }

                    const form = event.target;
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalText = submitBtn ? submitBtn.innerHTML : '';
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = 'Đang duyệt...';
                    }

                    try {
                        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: new FormData(form)
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            this.showToast(data.message || 'Đã duyệt gia hạn thành công (+7 ngày)!', 'success');
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            this.showToast(data.message || 'Có lỗi xảy ra khi duyệt gia hạn', 'error');
                        }
                    } catch (err) {
                        console.error(err);
                        this.showToast('Không thể kết nối đến máy chủ. Vui lòng thử lại!', 'error');
                    } finally {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }
                    }
                },

                async handleRejectRenewal(event, borrowId, bookTitle, borrowerName) {
                    const reason = prompt(`Nhập lý do TỪ CHỐI gia hạn cuốn sách "${bookTitle}" của độc giả "${borrowerName}":`, 'Không đủ điều kiện gia hạn');
                    if (reason === null) {
                        return;
                    }

                    const form = event.target;
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalText = submitBtn ? submitBtn.innerHTML : '';
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = 'Đang xử lý...';
                    }

                    try {
                        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

                        const formData = new FormData(form);
                        formData.append('reason', reason || 'Thủ thư từ chối gia hạn');

                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            this.showToast(data.message || 'Đã từ chối yêu cầu gia hạn sách.', 'info');
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            this.showToast(data.message || 'Có lỗi xảy ra khi từ chối gia hạn', 'error');
                        }
                    } catch (err) {
                        console.error(err);
                        this.showToast('Không thể kết nối đến máy chủ!', 'error');
                    } finally {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }
                    }
                },

                async handleRejectSubmit(event) {
                    const form = event.target;
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalText = submitBtn ? submitBtn.innerHTML : '';
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = 'Đang xử lý...';
                    }

                    try {
                        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: new FormData(form)
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            this.rejectModalOpen = false;
                            this.showToast(data.message || 'Đã từ chối yêu cầu mượn thành công!', 'info');
                            if (data.borrow_id || this.currentBorrowId) {
                                this.animateRowRemoval(data.borrow_id || this.currentBorrowId);
                            }
                            if (data.remaining_pending !== undefined) {
                                this.updatePendingBadges(data.remaining_pending);
                            }
                        } else {
                            this.showToast(data.message || 'Có lỗi xảy ra khi từ chối', 'error');
                        }
                    } catch (err) {
                        console.error(err);
                        this.showToast('Không thể kết nối đến máy chủ!', 'error');
                    } finally {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }
                    }
                },

                animateRowRemoval(borrowId) {
                    const row = document.getElementById(`borrow-row-${borrowId}`);
                    if (row) {
                        row.style.transition = 'all 0.4s ease';
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(20px)';
                        setTimeout(() => {
                            row.remove();
                            const container = document.getElementById('borrows-container');
                            if (container && container.querySelectorAll('article').length === 0) {
                                const emptyDiv = document.createElement('div');
                                emptyDiv.className = 'bg-white rounded-3xl border-2 border-slate-300 p-12 text-center text-slate-500 space-y-3 shadow-sm';
                                emptyDiv.innerHTML = `
                                    <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center border border-emerald-200">
                                        <svg class="w-7 h-7 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-black text-slate-900 tracking-tight">ĐÃ XỬ LÝ HẾT CÁC YÊU CẦU MƯỢN</h3>
                                    <p class="text-sm font-bold text-emerald-600 mt-1">Tất cả phiếu mượn đang chờ duyệt đã được giải quyết xong!</p>
                                `;
                                container.appendChild(emptyDiv);
                            }
                        }, 400);
                    }
                },

                updatePendingBadges(count) {
                    const badges = document.querySelectorAll('.pending-count-badge');
                    badges.forEach(b => b.textContent = count);

                    const bannerEl = document.getElementById('pending-alert-banner');
                    const bannerCountEl = document.getElementById('banner-pending-count');
                    if (bannerCountEl) bannerCountEl.textContent = count;
                    if (bannerEl && count <= 0) {
                        bannerEl.style.transition = 'all 0.5s ease';
                        bannerEl.style.opacity = '0';
                        bannerEl.style.transform = 'translateY(-10px)';
                        setTimeout(() => bannerEl.remove(), 500);
                    }

                    const statPendingEl = document.getElementById('stat-pending-count');
                    if (statPendingEl) statPendingEl.textContent = count;

                    const headerBadge = document.getElementById('header-pending-badge');
                    if (headerBadge) {
                        if (count > 0) {
                            headerBadge.textContent = count;
                            headerBadge.classList.remove('hidden');
                        } else {
                            headerBadge.classList.add('hidden');
                        }
                    }
                }
            };
        }

        if (window.Alpine) {
            window.Alpine.data('borrowManager', borrowManager);
        } else {
            document.addEventListener('alpine:init', () => {
                window.Alpine.data('borrowManager', borrowManager);
            });
        }
    </script>
</x-app-layout>
