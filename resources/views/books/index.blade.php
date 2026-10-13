<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-black text-2xl sm:text-3xl text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-blue-50 border border-blue-200 text-blue-600 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </span>
                    <span>KHO SÁCH THƯ VIỆN</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 font-medium mt-1">Khám phá hàng nghìn ấn bản tri thức chọn lọc, tra cứu và đăng ký mượn trực tuyến</p>
            </div>

            @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'librarian']))
                <div class="flex items-center gap-3 flex-wrap">
                    <a href="{{ route('books.create') }}" 
                       class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl font-bold text-xs tracking-wider uppercase text-white bg-blue-600 hover:bg-blue-700 shadow-xs hover:shadow transition active:scale-[0.98]">
                        <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>THÊM SÁCH MỚI</span>
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="bg-slate-50 min-h-screen py-8" 
         x-data="{
            viewMode: 'grid' // 'grid' hoặc 'list'
         }">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- THÔNG BÁO FLASH -->
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl shadow-xs flex items-center justify-between text-emerald-800">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0 text-emerald-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </div>
                        <span class="text-sm font-semibold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl shadow-xs flex items-center justify-between text-rose-800">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-rose-100 flex items-center justify-center shrink-0 text-rose-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <span class="text-sm font-semibold">{{ session('error') }}</span>
                    </div>
                </div>
            @endif


            <!-- ========================================================================= -->
            <!-- 1. THANH BỘ LỌC & TÌM KIẾM NHANH (STICKY FILTER BAR)                      -->
            <!-- ========================================================================= -->
            <div class="sticky top-20 z-30 bg-white/95 backdrop-blur-md rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5 transition">
                <form method="GET" action="{{ route('books.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 items-center">
                        
                        <!-- Ô tìm kiếm từ khóa với icon kính lúp -->
                        <div class="lg:col-span-5 relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Tìm kiếm theo tên sách, tác giả..."
                                   class="pl-11 pr-4 w-full rounded-xl text-sm placeholder:text-slate-400 py-3 border border-slate-300 bg-slate-50 text-slate-900 font-medium focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition">
                        </div>

                        <!-- Dropdown chọn Thể loại / Danh mục -->
                        <div class="lg:col-span-3">
                            <select name="category_id"
                                    class="w-full rounded-xl text-sm text-slate-800 font-medium py-3 px-3.5 border border-slate-300 bg-slate-50 focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition">
                                <option value="">-- Tất cả thể loại --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }} ({{ $cat->books_count ?? $cat->books->count() }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Dropdown lọc tình trạng tồn kho (3 phân cấp: Còn - Xanh lá, Sắp hết - Vàng, Hết - Đỏ) -->
                        <div class="lg:col-span-2" x-data="{ currentStock: '{{ request('stock', 'all') }}' }">
                            <select name="stock"
                                    x-model="currentStock"
                                    :class="{
                                        'text-emerald-700 font-bold border-emerald-400 bg-emerald-50/60 ring-2 ring-emerald-400/20': currentStock === 'in_stock',
                                        'text-amber-700 font-bold border-amber-400 bg-amber-50/60 ring-2 ring-amber-400/20': currentStock === 'low_stock',
                                        'text-rose-700 font-bold border-rose-400 bg-rose-50/60 ring-2 ring-rose-400/20': currentStock === 'out_of_stock',
                                        'text-slate-800 font-medium border-slate-300 bg-slate-50': currentStock === 'all'
                                    }"
                                    class="w-full rounded-xl text-sm py-3 px-3.5 border {{ request('stock') === 'in_stock' ? 'text-emerald-700 font-bold border-emerald-400 bg-emerald-50/60' : (request('stock') === 'low_stock' ? 'text-amber-700 font-bold border-amber-400 bg-amber-50/60' : (request('stock') === 'out_of_stock' ? 'text-rose-700 font-bold border-rose-400 bg-rose-50/60' : 'text-slate-800 font-medium border-slate-300 bg-slate-50')) }} focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition">
                                <option value="all" class="text-slate-800 font-medium" style="color: #334155;">Tất cả tình trạng</option>
                                <option value="in_stock" class="text-emerald-600 font-bold" style="color: #16a34a; font-weight: 700; background-color: #ffffff;">🟢 Còn sách (Khả dụng)</option>
                                <option value="low_stock" class="text-amber-600 font-bold" style="color: #d97706; font-weight: 700; background-color: #ffffff;">🟡 Sắp hết (Ưu tiên)</option>
                                <option value="out_of_stock" class="text-rose-600 font-bold" style="color: #dc2626; font-weight: 700; background-color: #ffffff;">🔴 Hết sách (Tạm khóa)</option>
                            </select>
                        </div>

                        <!-- Nút Lọc Sách & Đặt lại -->
                        <div class="lg:col-span-2 flex items-center gap-2">
                            <button type="submit" 
                                    class="flex-1 py-3 px-4 rounded-xl font-bold text-xs tracking-wider uppercase flex items-center justify-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white shadow-sm transition active:scale-[0.98] cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                </svg>
                                <span>LỌC SÁCH</span>
                            </button>

                            @if(request('search') || request('category_id') || (request('stock') && request('stock') !== 'all'))
                                <a href="{{ route('books.index') }}"
                                   title="Xóa bộ lọc"
                                   class="py-3 px-3.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition shrink-0 flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 text-slate-500 hover:text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- THANH TIỆN ÍCH DƯỚI: NÚT CHUYỂN ĐỔI CHẾ ĐỘ XEM (GRID/LIST) -->
                    <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2 text-slate-600 font-medium">
                            @if(request('search') || request('category_id') || (request('stock') && request('stock') !== 'all'))
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    Đang áp dụng bộ lọc
                                </span>
                            @endif
                        </div>

                        <!-- Chuyển đổi chế độ xem Grid / List -->
                        <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200">
                            <button type="button"
                                    @click="viewMode = 'grid'"
                                    :class="viewMode === 'grid' ? 'bg-white text-blue-600 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                    class="px-3 py-1.5 rounded-lg text-xs flex items-center gap-1.5 transition cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                                <span>Lưới Thẻ (Grid)</span>
                            </button>

                            <button type="button"
                                    @click="viewMode = 'list'"
                                    :class="viewMode === 'list' ? 'bg-white text-blue-600 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                    class="px-3 py-1.5 rounded-lg text-xs flex items-center gap-1.5 transition cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                                <span>Danh Sách (List)</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ========================================================================= -->
            <!-- 2. KHỐI THẺ SÁCH KIỂU E-COMMERCE (BOOK PRODUCT CARD GRID)                   -->
            <!-- ========================================================================= -->
            
            <!-- CHẾ ĐỘ 1: LƯỚI THẺ SÁCH (PRODUCT CARD GRID) -->
            <div x-show="viewMode === 'grid'" 
                 class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse ($books as $book)
                    @php
                        $copies = (int) ($book->available_copies ?? $book->quantity ?? 0);
                        $isOutOfStock = $copies <= 0;
                        $isLowStock = $copies > 0 && $copies <= 10;
                        $canSeeStock = Auth::check() && in_array(Auth::user()->role, ['admin', 'librarian']);
                        $canManageBook = $canSeeStock;
                    @endphp
                    <div class="{{ $isOutOfStock && !$canManageBook ? 'bg-slate-50/90 border border-dashed border-slate-300 rounded-xl overflow-hidden shadow-none opacity-45 grayscale-[50%] select-none cursor-not-allowed' : ($isOutOfStock && $canManageBook ? 'bg-white border-2 border-rose-300/80 rounded-xl overflow-hidden shadow-sm hover:shadow-md ring-1 ring-rose-200' : 'bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md') }} transition-all duration-300 flex flex-col group h-full relative">
                        
                        <!-- 1. PHẦN HÌNH ẢNH BÌA SÁCH (TỈ LỆ DỌC CHUẨN PORTRAIT TRÊN NỀN SLATE-50) -->
                        <a href="{{ ($isOutOfStock && !$canManageBook) ? 'javascript:void(0)' : route('books.show', $book->id) }}" 
                           class="relative bg-slate-50 aspect-[3/4] p-4 flex items-center justify-center border-b border-slate-100 overflow-hidden block group/cover {{ ($isOutOfStock && !$canManageBook) ? 'cursor-not-allowed pointer-events-none' : 'cursor-pointer' }}"
                           title="{{ $book->title }}">
                            
                            <img src="{{ Str::startsWith($book->cover_image, ['http://', 'https://']) ? $book->cover_image : ($book->cover_image ? asset('storage/' . ltrim($book->cover_image, '/')) : asset('images/covers/default.jpg')) }}" 
                                 alt="{{ $book->title }}" 
                                 loading="lazy"
                                 onerror="this.onerror=null; this.src='{{ asset('images/covers/default.jpg') }}';"
                                 style="transform: translateZ(0); backface-visibility: hidden;"
                                 class="w-auto h-full max-h-full object-contain rounded-sm shadow-md {{ ($isOutOfStock && !$canManageBook) ? 'opacity-60' : 'group-hover/cover:scale-105' }} transition-transform duration-300" />

                            <!-- Nhãn thể loại nằm góc trên bên trái -->
                            @if($book->category)
                                <span class="absolute top-3 left-3 px-2 py-0.5 rounded text-[11px] font-semibold bg-white/95 backdrop-blur-xs text-slate-700 border border-slate-200/80 shadow-2xs truncate max-w-[75%]">
                                    {{ $book->category->name }}
                                </span>
                            @endif

                            <!-- Nhãn trạng thái tồn kho góc trên bên phải (Chỉ Admin mới thấy số sách còn lại) -->
                            @if($copies > 10)
                                <span class="absolute top-3 right-3 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-2xs flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Còn sách{{ $canSeeStock ? " ({$copies})" : '' }}</span>
                                </span>
                            @elseif($copies > 0)
                                <span class="absolute top-3 right-3 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200/80 shadow-2xs flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    <span>Sắp hết{{ $canSeeStock ? " ({$copies})" : '' }}</span>
                                </span>
                            @else
                                <span class="absolute top-3 right-3 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200/80 shadow-2xs flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    <span>Hết sách{{ $canSeeStock ? ' (0)' : '' }}</span>
                                </span>
                            @endif
                        </a>

                        <!-- 2. PHẦN THÔNG TIN CHI TIẾT (EDITORIAL LIGHT CONTENT) -->
                        <div class="p-4 flex-1 flex flex-col">
                            <!-- Tiêu đề sách: Rõ nét, chữ đậm vừa phải, giới hạn 2 dòng (line-clamp-2) -->
                            <h3 class="font-semibold {{ ($isOutOfStock && !$canManageBook) ? 'text-slate-400' : 'text-slate-800 group-hover:text-indigo-600' }} text-base leading-snug mb-1 line-clamp-2 min-h-[2.5rem] transition-colors">
                                <a href="{{ ($isOutOfStock && !$canManageBook) ? 'javascript:void(0)' : route('books.show', $book->id) }}" title="{{ $book->title }}" class="{{ ($isOutOfStock && !$canManageBook) ? 'cursor-not-allowed' : '' }}">
                                    {{ $book->title }}
                                </a>
                            </h3>

                            <!-- Tác giả: Chữ nhỏ, màu xám nhạt, có icon User/Pen -->
                            <p class="text-sm text-slate-400 mb-2.5 flex items-center gap-1.5 line-clamp-1">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                                <span class="truncate">{{ $book->author }}</span>
                            </p>

                            <!-- Thông tin bổ trợ: Mã ấn bản & NXB -->
                            <div class="mt-auto pt-2.5 flex items-center justify-between text-xs text-slate-400 border-t border-slate-100">
                                <span class="font-mono">#{{ str_pad($book->id, 3, '0', STR_PAD_LEFT) }}</span>
                                <span class="truncate max-w-[60%] text-right text-slate-400">{{ $book->publisher ?: 'NXB Tri Thức' }}</span>
                            </div>
                        </div>

                        <!-- 3. PHẦN NÚT TƯƠNG TÁC (ACTION BUTTON Ở CHÂN THẺ) -->
                        <div class="p-4 pt-0 mt-auto {{ ($isOutOfStock && !$canManageBook) ? 'bg-transparent' : 'bg-white' }}">
                            @if(!$isOutOfStock)
                                <button type="button" 
                                        class="btn-add-to-cart w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-4 rounded-lg flex items-center justify-center gap-2 transition-colors text-sm shadow-xs active:scale-[0.98] cursor-pointer" 
                                        data-book-id="{{ $book->id }}">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <span>Thêm vào giỏ mượn</span>
                                </button>
                            @elseif($canManageBook)
                                <!-- DÀNH CHO ADMIN & THỦ THƯ KHI HẾT SÁCH: NÚT BỔ SUNG SỐ LƯỢNG CHO ĐỘC GIẢ MƯỢN -->
                                <button type="button" 
                                        @click="$dispatch('open-stock-modal', { id: {{ $book->id }}, title: '{{ addslashes($book->title) }}', currentQuantity: {{ (int)$book->quantity }}, editUrl: '{{ route('books.edit', $book->id) }}' })"
                                        class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-2.5 px-3 rounded-lg flex items-center justify-center gap-1.5 transition-colors text-xs sm:text-sm shadow-xs active:scale-[0.98] cursor-pointer"
                                        title="Nhập số lượng sách để mở mượn cho độc giả">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>+ Nhập số lượng sách</span>
                                </button>
                            @else
                                <div class="w-full py-2.5 px-4 rounded-lg bg-slate-200/70 border border-slate-300 text-slate-400 font-semibold flex items-center justify-center gap-2 text-xs select-none cursor-not-allowed pointer-events-none">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                    <span>Hết hàng (Không thể chọn)</span>
                                </div>
                            @endif

                            <!-- Menu thao tác nhanh dành cho Quản trị viên (Admin/Librarian) -->
                            @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'librarian']))
                                <div class="flex items-center justify-end gap-1.5 pt-2 mt-2 border-t border-slate-100">
                                    <a href="{{ route('books.edit', $book->id) }}"
                                       title="Chỉnh sửa đầu sách"
                                       class="p-1.5 rounded-md text-slate-400 hover:text-amber-700 hover:bg-amber-50 transition border border-transparent hover:border-amber-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    @if(Auth::user()->role === 'admin')
                                        <form action="{{ route('books.destroy', $book->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa cuốn sách &quot;{{ $book->title }}&quot; khỏi hệ thống?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    title="Xóa vĩnh viễn (Chỉ Admin)"
                                                    class="p-1.5 rounded-md text-slate-400 hover:text-rose-700 hover:bg-rose-50 transition border border-transparent hover:border-rose-200 cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </div>

                    </div>
                @empty
                    <div class="col-span-full p-12 bg-white rounded-2xl border border-slate-200 text-center space-y-3">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shadow-sm">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">Không tìm thấy đầu sách nào</h3>
                        <p class="text-sm text-slate-500 max-w-md mx-auto">
                            Không có kết quả nào khớp với các tiêu chí tìm kiếm hoặc bộ lọc hiện tại. Hãy thử tìm từ khóa khác hoặc xóa bộ lọc.
                        </p>
                        <div class="pt-2">
                            <a href="{{ route('books.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition">
                                Xóa bộ lọc & Xem tất cả
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- CHẾ ĐỘ 2: DANH SÁCH CHI TIẾT (LIST VIEW) -->
            <div x-show="viewMode === 'list'" 
                 x-cloak
                 style="display: none;"
                 class="space-y-4">
                @forelse ($books as $book)
                    @php
                        $copies = (int) ($book->available_copies ?? $book->quantity ?? 0);
                        $isOutOfStock = $copies <= 0;
                        $isLowStock = $copies > 0 && $copies <= 10;
                        $canSeeStock = Auth::check() && in_array(Auth::user()->role, ['admin', 'librarian']);
                        $canManageBook = $canSeeStock;
                    @endphp
                    <div class="{{ $isOutOfStock && !$canManageBook ? 'bg-slate-50/90 border border-dashed border-slate-300 shadow-none opacity-45 grayscale-[50%] select-none cursor-not-allowed' : ($isOutOfStock && $canManageBook ? 'bg-white border-2 border-rose-300/80 shadow-xs hover:shadow-md ring-1 ring-rose-200' : 'bg-white border border-slate-200/90 shadow-xs hover:shadow-md') }} rounded-2xl transition-all duration-200 p-4 sm:p-5 flex flex-col sm:flex-row gap-5 items-start sm:items-center justify-between group">
                        
                        <div class="flex items-start gap-4 flex-1 min-w-0">
                            <!-- Ảnh bìa sách (Click chuyển thẳng sang trang Chi tiết) -->
                            <a href="{{ ($isOutOfStock && !$canManageBook) ? 'javascript:void(0)' : route('books.show', $book->id) }}"
                               class="w-20 h-28 sm:w-24 sm:h-32 shrink-0 rounded-xl overflow-hidden relative group/cover shadow-xs block {{ ($isOutOfStock && !$canManageBook) ? 'cursor-not-allowed pointer-events-none' : 'cursor-pointer' }} bg-slate-50 p-1.5 border border-slate-100 flex items-center justify-center"
                               title="{{ $book->title }}">
                                <img src="{{ Str::startsWith($book->cover_image, ['http://', 'https://']) ? $book->cover_image : ($book->cover_image ? asset('storage/' . ltrim($book->cover_image, '/')) : asset('images/covers/default.jpg')) }}"
                                     alt="{{ $book->title }}"
                                     loading="lazy"
                                     onerror="this.onerror=null; this.src='{{ asset('images/covers/default.jpg') }}';"
                                     style="transform: translateZ(0); backface-visibility: hidden;"
                                     class="w-auto h-full max-h-full object-contain rounded-sm shadow-xs {{ ($isOutOfStock && !$canManageBook) ? 'opacity-60' : 'group-hover/cover:scale-105' }} transition-transform duration-300" />
                            </a>

                            <!-- Nội dung -->
                            <div class="min-w-0 flex-1 space-y-1.5">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $book->category ? $book->category->name : 'Tổng hợp' }}
                                    </span>
                                    @if($copies > 10)
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Còn sách{{ $canSeeStock ? " ({$copies} cuốn)" : '' }}</span>
                                        </span>
                                    @elseif($copies > 0)
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            <span>Sắp hết{{ $canSeeStock ? " ({$copies} cuốn)" : '' }}</span>
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            <span>Hết sách{{ $canSeeStock ? ' (0 cuốn)' : '' }}</span>
                                        </span>
                                    @endif
                                </div>

                                <h3 class="font-bold {{ ($isOutOfStock && !$canManageBook) ? 'text-slate-400' : 'text-slate-900 hover:text-indigo-600' }} text-base sm:text-lg transition truncate">
                                    <a href="{{ ($isOutOfStock && !$canManageBook) ? 'javascript:void(0)' : route('books.show', $book->id) }}" title="{{ $book->title }}" class="{{ ($isOutOfStock && !$canManageBook) ? 'cursor-not-allowed' : 'hover:text-indigo-600' }} transition">
                                        {{ $book->title }}
                                    </a>
                                </h3>

                                <div class="text-xs sm:text-sm font-medium text-slate-400">
                                    Tác giả: <span class="{{ ($isOutOfStock && !$canManageBook) ? 'text-slate-500' : 'text-slate-700 font-semibold' }}">{{ $book->author }}</span>
                                </div>

                                <p class="text-xs sm:text-sm text-slate-400 line-clamp-2 leading-relaxed">
                                    {{ $book->description ?: 'Tác phẩm chọn lọc trong kho lưu trữ của thư viện TIỂU ĐỘI 2P.' }}
                                </p>
                            </div>
                        </div>

                        <!-- Cột hành động bên phải -->
                        <div class="flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto gap-3 pt-3 sm:pt-0 border-t sm:border-t-0 border-slate-100 shrink-0">
                            <div class="flex items-center gap-2">
                                @if(!$isOutOfStock)
                                    <a href="{{ route('books.show', $book->id) }}"
                                       class="px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:text-indigo-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition">
                                        Chi tiết
                                    </a>

                                    <button type="button" 
                                            class="btn-add-to-cart bg-indigo-600 hover:bg-indigo-700 text-white text-xs sm:text-sm font-semibold py-2 px-4 rounded-xl transition flex items-center gap-1.5 shadow-xs whitespace-nowrap cursor-pointer active:scale-[0.98]" 
                                            data-book-id="{{ $book->id }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <span>Thêm vào giỏ mượn</span>
                                    </button>
                                @elseif($canManageBook)
                                    <a href="{{ route('books.show', $book->id) }}"
                                       class="px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:text-indigo-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition">
                                        Chi tiết
                                    </a>

                                    <button type="button" 
                                            @click="$dispatch('open-stock-modal', { id: {{ $book->id }}, title: '{{ addslashes($book->title) }}', currentQuantity: {{ (int)$book->quantity }}, editUrl: '{{ route('books.edit', $book->id) }}' })"
                                            class="bg-amber-500 hover:bg-amber-600 text-white text-xs sm:text-sm font-semibold py-2 px-4 rounded-xl transition flex items-center gap-1.5 shadow-xs whitespace-nowrap cursor-pointer active:scale-[0.98]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>+ Nhập số lượng sách</span>
                                    </button>
                                @else
                                    <span class="bg-slate-200/70 border border-slate-300 text-slate-400 font-semibold py-2 px-3 rounded-xl flex items-center justify-center gap-1.5 text-xs whitespace-nowrap select-none cursor-not-allowed pointer-events-none">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                        <span>Hết hàng (Không thể chọn)</span>
                                    </span>
                                @endif
                            </div>

                            @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'librarian']))
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('books.edit', $book->id) }}" class="text-xs font-semibold text-amber-700 hover:underline">Sửa</a>
                                    @if(Auth::user()->role === 'admin')
                                        <span class="text-slate-300">|</span>
                                        <form action="{{ route('books.destroy', $book->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa cuốn sách này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-rose-600 hover:underline cursor-pointer">Xóa</button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </div>

                    </div>
                @empty
                    <!-- Đã hiển thị ở grid -->
                @endforelse
            </div>

            <!-- ========================================================================= -->
            <!-- 3. PHÂN TRANG (PAGINATION)                                                -->
            <!-- ========================================================================= -->
            @if ($books->hasPages())
                <div class="pt-6 pb-2">
                    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
                        {{ $books->links() }}
                    </div>
                </div>
            @endif

        </div>
    </div>

    @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'librarian']))
        <!-- ========================================================================= -->
        <!-- MODAL BỔ SUNG SỐ LƯỢNG SÁCH VÀO KHO (DÀNH CHO ADMIN & THỦ THƯ)           -->
        <!-- ========================================================================= -->
        <div x-data="{
                isOpen: false,
                bookId: null,
                bookTitle: '',
                currentQty: 0,
                newQuantity: 10,
                editUrl: '',
                open(data) {
                    this.bookId = data.id;
                    this.bookTitle = data.title;
                    this.currentQty = data.currentQuantity;
                    this.newQuantity = Math.max(10, data.currentQuantity + 10);
                    this.editUrl = data.editUrl;
                    this.isOpen = true;
                    this.$nextTick(() => {
                        if (this.$refs.qtyInput) this.$refs.qtyInput.focus();
                    });
                },
                close() {
                    this.isOpen = false;
                }
             }"
             @open-stock-modal.window="open($event.detail)"
             @keydown.escape.window="close()"
             x-cloak>
            
            <!-- Backdrop -->
            <div x-show="isOpen"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50"></div>

            <!-- Dialog Container -->
            <div x-show="isOpen"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
                
                <div @click.outside="close()"
                     class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full overflow-hidden text-left">
                    
                    <!-- Header -->
                    <div class="p-5 bg-gradient-to-r from-amber-50 to-orange-50 border-b border-amber-200/60 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-xs shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-base text-slate-900">Bổ Sung Số Lượng Sách</h3>
                                <p class="text-xs text-amber-800 font-medium">Quyền hạn: Admin & Thủ thư</p>
                            </div>
                        </div>
                        <button type="button" @click="close()" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form :action="'{{ url('/books') }}/' + bookId + '/stock'" method="POST" class="p-5 space-y-4">
                        @csrf
                        @method('PATCH')

                        <!-- Tên sách -->
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Đầu sách được chọn</span>
                            <p class="text-sm font-semibold text-slate-800 line-clamp-2" x-text="bookTitle"></p>
                            <p class="text-xs text-rose-600 font-medium mt-1 flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                <span>Tình trạng hiện tại: Đã hết bản in (0 cuốn)</span>
                            </p>
                        </div>

                        <!-- Ô nhập số lượng mới -->
                        <div>
                            <label for="stock_quantity_input" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Số lượng tồn kho mới (cuốn) <span class="text-rose-500">*</span>
                            </label>
                            <input x-ref="qtyInput"
                                   id="stock_quantity_input"
                                   type="number"
                                   name="quantity"
                                   x-model.number="newQuantity"
                                   min="1"
                                   max="10000"
                                   required
                                   placeholder="Nhập số lượng sách cần bổ sung..."
                                   class="w-full rounded-xl border-2 border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/20 text-base font-bold text-slate-900 py-3 px-3.5 transition" />
                            <p class="text-[11px] text-slate-500 mt-1.5">
                                💡 Sau khi lưu, cuốn sách sẽ chuyển sang trạng thái <strong>Còn sách</strong> để độc giả có thể tiếp tục gửi yêu cầu mượn.
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                            <a :href="editUrl"
                               class="text-xs text-indigo-600 hover:text-indigo-800 font-medium hover:underline flex items-center gap-1">
                                <span>Sửa toàn bộ thông tin</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>

                            <div class="flex items-center gap-2">
                                <button type="button" @click="close()"
                                        class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                                    Đóng
                                </button>
                                <button type="submit"
                                        class="px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider bg-amber-500 hover:bg-amber-600 text-white transition shadow-sm active:scale-95 cursor-pointer flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Lưu & Mở Mượn</span>
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endif
</x-app-layout>
