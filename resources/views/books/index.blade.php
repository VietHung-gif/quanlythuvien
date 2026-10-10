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
            viewMode: 'grid', // 'grid' hoặc 'list'
            
            // State cho Quick View Book Modal
            quickViewOpen: false,
            quickViewCoverTier: 1,
            quickViewCoverSrc: '',
            quickViewBook: {
                id: null,
                code: '#000',
                title: '',
                author: '',
                publisher: '',
                publish_year: '',
                category: '',
                cover_image: '',
                cover_url: '',
                local_cover_url: '',
                available_copies: 0,
                description: '',
                show_url: '#'
            },

            openQuickView(bookData) {
                this.quickViewBook = bookData;
                this.quickViewCoverTier = (bookData && bookData.cover_image && !bookData.cover_image.includes('placehold.co')) ? 1 : 2;
                this.quickViewCoverSrc = this.quickViewCoverTier === 1 
                    ? (bookData.cover_url || bookData.local_cover_url) 
                    : (bookData.local_cover_url || '{{ asset('images/covers/default.svg') }}');
                this.quickViewOpen = true;
                document.body.classList.add('overflow-hidden');
            },

            handleQuickViewCoverError(imgEl) {
                if (this.quickViewCoverTier === 1) {
                    this.quickViewCoverTier = 2;
                    this.quickViewCoverSrc = this.quickViewBook ? (this.quickViewBook.local_cover_url || '{{ asset('images/covers/default.svg') }}') : '{{ asset('images/covers/default.svg') }}';
                    if (imgEl) imgEl.src = this.quickViewCoverSrc;
                } else {
                    this.quickViewCoverTier = 3;
                    if (imgEl) imgEl.style.display = 'none';
                }
            },

            closeQuickView() {
                this.quickViewOpen = false;
                document.body.classList.remove('overflow-hidden');
            },

            borrowFromQuickView() {
                if (!this.quickViewBook || !this.quickViewBook.id) return;
                const bId = this.quickViewBook.id;
                if (this.quickViewBook.available_copies <= 0) return;
                this.closeQuickView();
                if (window.addToCart) {
                    window.addToCart(bId);
                }
            }
         }"
         @keydown.escape.window="if(quickViewOpen) closeQuickView();">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- THÔNG BÁO FLASH -->
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl shadow-xs flex items-center justify-between text-emerald-800">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0 text-emerald-600 font-bold">
                            ✓
                        </div>
                        <span class="text-sm font-semibold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl shadow-xs flex items-center justify-between text-rose-800">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-rose-100 flex items-center justify-center shrink-0 text-rose-600 font-bold">
                            ✕
                        </div>
                        <span class="text-sm font-semibold">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- 3 THẺ THỐNG KÊ TỒN KHO GỌN GÀNG -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <a href="{{ route('books.index', ['stock' => 'all']) }}" 
                   class="bg-white hover:bg-slate-50 border {{ ($stock ?? 'all') === 'all' && !request('search') && !request('category_id') ? 'border-blue-600 ring-2 ring-blue-500/10' : 'border-slate-200' }} rounded-2xl p-4 transition shadow-xs group">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <span>Tất Cả Đầu Sách</span>
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                    </div>
                    <div class="mt-2 text-3xl font-black text-slate-900 group-hover:text-blue-600 transition tracking-tight">
                        {{ number_format($totalAll ?? $books->total()) }} <span class="text-xs font-semibold text-slate-500">cuốn</span>
                    </div>
                </a>

                <a href="{{ route('books.index', ['stock' => 'in_stock']) }}" 
                   class="bg-white hover:bg-slate-50 border {{ ($stock ?? '') === 'in_stock' ? 'border-emerald-600 ring-2 ring-emerald-500/10' : 'border-slate-200' }} rounded-2xl p-4 transition shadow-xs group">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <span>Sách Sẵn Sàng Cho Mượn</span>
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    </div>
                    <div class="mt-2 text-3xl font-black text-emerald-600 tracking-tight">
                        {{ number_format($totalInStock ?? 0) }} <span class="text-xs font-semibold text-slate-500">cuốn khả dụng</span>
                    </div>
                </a>

                <a href="{{ route('books.index', ['stock' => 'out_of_stock']) }}" 
                   class="bg-white hover:bg-slate-50 border {{ ($stock ?? '') === 'out_of_stock' ? 'border-rose-600 ring-2 ring-rose-500/10' : 'border-slate-200' }} rounded-2xl p-4 transition shadow-xs group">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <span>Tạm Hết Trong Kho</span>
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                    </div>
                    <div class="mt-2 text-3xl font-black text-rose-600 tracking-tight">
                        {{ number_format($totalOutOfStock ?? 0) }} <span class="text-xs font-semibold text-slate-500">cuốn đang mượn</span>
                    </div>
                </a>
            </div>

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

                        <!-- Dropdown lọc tình trạng tồn kho -->
                        <div class="lg:col-span-2">
                            <select name="stock"
                                    class="w-full rounded-xl text-sm text-slate-800 font-medium py-3 px-3.5 border border-slate-300 bg-slate-50 focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition">
                                <option value="all" {{ request('stock', 'all') === 'all' ? 'selected' : '' }}>Tất cả tình trạng</option>
                                <option value="in_stock" {{ request('stock') === 'in_stock' ? 'selected' : '' }}>🟢 Sẵn sàng mượn</option>
                                <option value="out_of_stock" {{ request('stock') === 'out_of_stock' ? 'selected' : '' }}>🔴 Tạm hết</option>
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
                                    ✕
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- THANH TIỆN ÍCH DƯỚI: SỐ LƯỢNG KẾT QUẢ & NÚT CHUYỂN ĐỔI CHẾ ĐỘ XEM (GRID/LIST) -->
                    <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2 text-slate-600 font-medium">
                            <span>Tìm thấy <strong class="text-slate-900 font-bold">{{ $books->total() }}</strong> đầu sách phù hợp</span>
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
                        $bookPayload = [
                            'id' => $book->id,
                            'code' => '#' . str_pad($book->id, 3, '0', STR_PAD_LEFT),
                            'title' => (string) $book->title,
                            'author' => (string) $book->author,
                            'publisher' => (string) ($book->publisher ?: 'Đang cập nhật'),
                            'publish_year' => (string) ($book->publish_year ?: 'N/A'),
                            'category' => (string) ($book->category ? $book->category->name : 'Tổng hợp'),
                            'cover_image' => (string) $book->cover_image,
                            'cover_url' => (string) $book->cover_url,
                            'category_slug' => (string) $book->category_slug,
                            'local_cover_url' => (string) $book->local_cover_url,
                            'available_copies' => (int) $book->available_copies,
                            'description' => (string) ($book->description ?: 'Tác phẩm chọn lọc trong kho lưu trữ của thư viện TIỂU ĐỘI 2P, hỗ trợ độc giả mượn đọc và nghiên cứu tài liệu.'),
                            'show_url' => route('books.show', $book->id),
                        ];
                    @endphp

                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs hover:shadow-xl transition-all duration-300 hover:-translate-y-1 flex flex-col h-full overflow-hidden group">
                        
                        <!-- PHẦN ẢNH BÌA SÁCH (Tỉ lệ chuẩn 3:4 E-Commerce - Bấm mở Quick View Modal) -->
                        <div class="relative aspect-[3/4] bg-slate-100 overflow-hidden cursor-pointer group/cover"
                             data-book="{{ json_encode($bookPayload) }}"
                             @click="openQuickView(JSON.parse($event.currentTarget.dataset.book))">
                            
                            <x-book-cover :book="$book" class="w-full h-full rounded-none shadow-none group-hover/cover:scale-105 transition-transform duration-300" />

                            <!-- Overlay Xem nhanh khi di chuột -->
                            <div class="absolute inset-0 bg-slate-900/35 opacity-0 group-hover/cover:opacity-100 transition-opacity duration-200 flex items-center justify-center pointer-events-none z-20">
                                <span class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-white bg-slate-900/85 backdrop-blur-xs flex items-center gap-1.5 shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span>Xem nhanh</span>
                                </span>
                            </div>

                            <!-- Huy hiệu tình trạng tồn kho ở góc trên bên phải -->
                            <div class="absolute top-2.5 right-2.5 z-20">
                                @if($book->available_copies > 0)
                                    <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-500 text-white shadow-sm flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                        <span>Còn {{ $book->available_copies }}</span>
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-rose-500 text-white shadow-sm">
                                        Hết sách
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- PHẦN NỘI DUNG CHI TIẾT (CARD BODY) -->
                        <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <!-- Tiêu đề sách: Chữ đen than chì đậm nét text-slate-900 (Bấm mở Quick View) -->
                                <h3 class="font-bold text-slate-900 text-base md:text-lg line-clamp-1 hover:text-blue-600 transition leading-snug cursor-pointer">
                                    <a href="{{ route('books.show', $book->id) }}" 
                                       data-book="{{ json_encode($bookPayload) }}"
                                       @click.prevent="openQuickView(JSON.parse($event.currentTarget.dataset.book))"
                                       title="{{ $book->title }}">
                                        {{ $book->title }}
                                    </a>
                                </h3>

                                <!-- Tác giả -->
                                <div class="text-xs sm:text-sm font-medium text-slate-500 line-clamp-1 mt-1 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span class="truncate">{{ $book->author }}</span>
                                </div>

                                <!-- Đoạn mô tả/tóm tắt sách (line-clamp-2 hoặc line-clamp-3) -->
                                <p class="text-xs sm:text-sm text-slate-600 line-clamp-2 md:line-clamp-3 leading-relaxed mt-2.5 font-normal">
                                    {{ $book->description ?: 'Tác phẩm chọn lọc trong kho lưu trữ của thư viện TIỂU ĐỘI 2P, hỗ trợ độc giả mượn đọc và nghiên cứu tài liệu.' }}
                                </p>
                            </div>

                            <!-- PHẦN CHÂN THẺ (CARD FOOTER): NÚT MƯỢN SÁCH & NÚT ADMIN -->
                            <div class="mt-4 pt-3 border-t border-slate-100">
                                @if($book->available_copies > 0)
                                    <button type="button"
                                            onclick="if(window.addToCart){ window.addToCart({{ $book->id }}); } else { alert('Giỏ sách đang khởi tạo!'); }"
                                            class="w-full bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-semibold py-2.5 px-3 rounded-xl transition flex items-center justify-center gap-2 shadow-xs active:scale-[0.98] cursor-pointer">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <span>Thêm vào giỏ mượn</span>
                                    </button>
                                @else
                                    <button type="button" 
                                            disabled
                                            class="w-full bg-slate-100 text-slate-400 text-xs sm:text-sm font-semibold py-2.5 px-3 rounded-xl flex items-center justify-center gap-2 cursor-not-allowed border border-slate-200">
                                        <span>Tạm hết sách</span>
                                    </button>
                                @endif

                                <!-- Menu nút phụ (Sửa / Xóa) dành riêng cho Admin/Librarian -->
                                @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'librarian']))
                                    <div class="flex items-center justify-end gap-1.5 pt-2 mt-2 border-t border-slate-100">
                                        <a href="{{ route('books.edit', $book->id) }}"
                                           title="Chỉnh sửa đầu sách"
                                           class="p-1.5 rounded-lg text-slate-500 hover:text-amber-700 hover:bg-amber-50 transition border border-transparent hover:border-amber-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        @if(Auth::user()->role === 'admin')
                                            <form action="{{ route('books.destroy', $book->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa cuốn sách &quot;{{ $book->title }}&quot; khỏi hệ thống?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        title="Xóa vĩnh viễn (Chỉ Admin)"
                                                        class="p-1.5 rounded-lg text-slate-500 hover:text-rose-700 hover:bg-rose-50 transition border border-transparent hover:border-rose-200 cursor-pointer">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                @empty
                    <div class="col-span-full p-12 bg-white rounded-2xl border border-slate-200 text-center space-y-3">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-blue-50 border border-blue-200 flex items-center justify-center text-2xl">
                            📚
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
                        $bookPayload = [
                            'id' => $book->id,
                            'code' => '#' . str_pad($book->id, 3, '0', STR_PAD_LEFT),
                            'title' => (string) $book->title,
                            'author' => (string) $book->author,
                            'publisher' => (string) ($book->publisher ?: 'Đang cập nhật'),
                            'publish_year' => (string) ($book->publish_year ?: 'N/A'),
                            'category' => (string) ($book->category ? $book->category->name : 'Tổng hợp'),
                            'cover_image' => (string) $book->cover_image,
                            'cover_url' => (string) $book->cover_url,
                            'category_slug' => (string) $book->category_slug,
                            'local_cover_url' => (string) $book->local_cover_url,
                            'available_copies' => (int) $book->available_copies,
                            'description' => (string) ($book->description ?: 'Tác phẩm chọn lọc trong kho lưu trữ của thư viện TIỂU ĐỘI 2P, hỗ trợ độc giả mượn đọc và nghiên cứu tài liệu.'),
                            'show_url' => route('books.show', $book->id),
                        ];
                    @endphp

                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs hover:shadow-md transition-all duration-200 p-4 sm:p-5 flex flex-col sm:flex-row gap-5 items-start sm:items-center justify-between group">
                        
                        <div class="flex items-start gap-4 flex-1 min-w-0">
                            <!-- Ảnh bìa Vector (Click mở Quick View) -->
                            <div class="w-20 h-28 sm:w-24 sm:h-32 shrink-0 rounded-xl overflow-hidden cursor-pointer relative group/cover shadow-xs"
                                 data-book="{{ json_encode($bookPayload) }}"
                                 @click="openQuickView(JSON.parse($event.currentTarget.dataset.book))">
                                <x-book-cover :book="$book" class="w-full h-full rounded-none shadow-none group-hover/cover:scale-105 transition-transform duration-300" />
                            </div>

                            <!-- Nội dung -->
                            <div class="min-w-0 flex-1 space-y-1.5">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $book->category ? $book->category->name : 'Tổng hợp' }}
                                    </span>
                                    @if($book->available_copies > 0)
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            🟢 Còn {{ $book->available_copies }} cuốn
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            🔴 Hết sách
                                        </span>
                                    @endif
                                </div>

                                <h3 class="font-bold text-slate-900 text-base sm:text-lg hover:text-blue-600 transition truncate cursor-pointer">
                                    <a href="{{ route('books.show', $book->id) }}"
                                       data-book="{{ json_encode($bookPayload) }}"
                                       @click.prevent="openQuickView(JSON.parse($event.currentTarget.dataset.book))">
                                        {{ $book->title }}
                                    </a>
                                </h3>

                                <div class="text-xs sm:text-sm font-medium text-slate-500">
                                    Tác giả: <span class="text-slate-700 font-semibold">{{ $book->author }}</span>
                                </div>

                                <p class="text-xs sm:text-sm text-slate-600 line-clamp-2 leading-relaxed">
                                    {{ $book->description ?: 'Tác phẩm chọn lọc trong kho lưu trữ của thư viện TIỂU ĐỘI 2P.' }}
                                </p>
                            </div>
                        </div>

                        <!-- Cột hành động bên phải -->
                        <div class="flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto gap-3 pt-3 sm:pt-0 border-t sm:border-t-0 border-slate-100 shrink-0">
                            <div class="flex items-center gap-2">
                                <button type="button"
                                        data-book="{{ json_encode($bookPayload) }}"
                                        @click="openQuickView(JSON.parse($event.currentTarget.dataset.book))"
                                        class="px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:text-blue-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition cursor-pointer">
                                    Xem nhanh
                                </button>

                                @if($book->available_copies > 0)
                                    <button type="button"
                                            onclick="if(window.addToCart){ window.addToCart({{ $book->id }}); } else { alert('Giỏ sách đang khởi tạo!'); }"
                                            class="bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-semibold py-2 px-4 rounded-xl transition flex items-center gap-1.5 shadow-xs whitespace-nowrap cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <span>Thêm vào giỏ mượn</span>
                                    </button>
                                @else
                                    <span class="text-xs font-semibold text-rose-600 px-3 py-1.5 rounded-lg bg-rose-50 border border-rose-200">
                                        Tạm hết sách
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

        <!-- ========================================================================= -->
        <!-- MODAL CHI TIẾT NHANH SÁCH (QUICK VIEW BOOK MODAL - E-COMMERCE 2 CỘT)      -->
        <!-- ========================================================================= -->
        <div x-show="quickViewOpen"
             x-cloak
             style="display: none;"
             class="fixed inset-0 z-50 overflow-y-auto" 
             role="dialog" 
             aria-modal="true">
            
            <div class="flex items-center justify-center min-h-screen px-3 sm:px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Lớp nền mờ (Backdrop: backdrop-blur-sm bg-slate-900/60) -->
                <div x-show="quickViewOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="closeQuickView()"
                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <!-- Khối Modal chính (2 Cột E-Commerce Chuẩn Hiện Đại) -->
                <div x-show="quickViewOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom sm:align-middle bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all w-full max-w-4xl border border-slate-200 my-6 relative z-10">

                    <!-- Nút đóng (X) ở góc trên bên phải -->
                    <button type="button" 
                            @click="closeQuickView()" 
                            class="absolute top-4 right-4 z-20 w-10 h-10 rounded-full bg-white/90 hover:bg-slate-100 text-slate-500 hover:text-slate-900 flex items-center justify-center transition border border-slate-200/80 shadow-xs cursor-pointer"
                            title="Đóng cửa sổ (Esc)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Bố cục 2 Cột E-commerce -->
                    <div class="grid grid-cols-1 md:grid-cols-12 max-h-[88vh] md:max-h-[82vh] overflow-y-auto">
                        
                        <!-- CỘT TRÁI (35-40% chiều rộng -> md:col-span-5): Ảnh bìa, Tồn kho, Mã định danh -->
                        <div class="md:col-span-5 p-6 sm:p-8 bg-slate-50/90 border-b md:border-b-0 md:border-r border-slate-200 flex flex-col items-center justify-between gap-5">
                            
                            <!-- Mã định danh sách -->
                            <div class="w-full flex items-center justify-between">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-bold text-slate-700 bg-white border border-slate-200 shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                    <span x-text="'Mã số: ' + (quickViewBook ? quickViewBook.code : '#000')"></span>
                                </span>

                                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider hidden sm:inline">
                                    Xem nhanh
                                </span>
                            </div>

                            <!-- Bìa sách Vector Động (Dynamic Vector Book Cover 100% Theo Tựa Sách) -->
                            <div class="w-full max-w-[240px] aspect-[3/4] rounded-2xl shadow-xl overflow-hidden border-2 border-white group relative">
                                <x-book-cover :isAlpine="true" class="w-full h-full" />
                            </div>

                            <!-- Trạng thái tồn kho: Huy hiệu nổi bật hiển thị số lượng khả dụng còn trên kệ -->
                            <div class="w-full text-center">
                                <template x-if="quickViewBook && quickViewBook.available_copies > 0">
                                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs sm:text-sm font-bold bg-emerald-50 text-emerald-800 border border-emerald-300 shadow-2xs">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span x-text="'Còn ' + quickViewBook.available_copies + ' cuốn sẵn sàng mượn'"></span>
                                    </span>
                                </template>
                                <template x-if="quickViewBook && quickViewBook.available_copies <= 0">
                                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs sm:text-sm font-bold bg-rose-50 text-rose-800 border border-rose-300 shadow-2xs">
                                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                        <span>Tạm hết sách trong kho</span>
                                    </span>
                                </template>
                            </div>

                        </div>

                        <!-- CỘT PHẢI (60-65% chiều rộng -> md:col-span-7): Tiêu đề, Thể loại, Metadata Grid, Overview, Nút hành động -->
                        <div class="md:col-span-7 p-6 sm:p-8 flex flex-col justify-between bg-white space-y-6">
                            
                            <div class="space-y-4">
                                <!-- Thể loại (Category Badge) -->
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="px-3 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-800 border border-blue-200 tracking-wide"
                                          x-text="quickViewBook ? quickViewBook.category : 'Tổng hợp'">
                                    </span>
                                    <span class="text-xs font-semibold text-slate-500">
                                        Ấn phẩm Thư viện TIỂU ĐỘI 2P
                                    </span>
                                </div>

                                <!-- Tiêu đề sách: Chữ in đậm màu đen tuyền rõ nét (text-2xl md:text-3xl font-black text-slate-950) -->
                                <h2 class="text-2xl md:text-3xl font-black text-slate-950 tracking-tight leading-snug"
                                    x-text="quickViewBook ? quickViewBook.title : ''">
                                </h2>

                                <!-- Hàng thông tin học thuật (Metadata Grid) -->
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                                    <!-- Tác giả (Author) -->
                                    <div>
                                        <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Tác giả</span>
                                        <span class="font-bold text-slate-900 text-sm mt-0.5 block truncate"
                                              :title="quickViewBook ? quickViewBook.author : ''"
                                              x-text="quickViewBook ? quickViewBook.author : 'Chưa cập nhật'">
                                        </span>
                                    </div>

                                    <!-- Năm xuất bản (Publish Year) -->
                                    <div>
                                        <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Năm xuất bản</span>
                                        <span class="font-bold text-slate-900 text-sm mt-0.5 block"
                                              x-text="quickViewBook ? quickViewBook.publish_year : 'N/A'">
                                        </span>
                                    </div>

                                    <!-- Nhà xuất bản (Publisher) -->
                                    <div class="col-span-2 sm:col-span-1">
                                        <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Nhà xuất bản</span>
                                        <span class="font-bold text-slate-900 text-sm mt-0.5 block truncate"
                                              :title="quickViewBook ? quickViewBook.publisher : ''"
                                              x-text="quickViewBook ? quickViewBook.publisher : 'Đang cập nhật'">
                                        </span>
                                    </div>
                                </div>

                                <!-- Phần tóm tắt nội dung (Overview / Description) -->
                                <div>
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800 mb-2 flex items-center gap-2">
                                        <span>📖</span>
                                        <span>TÓM TẮT NỘI DUNG CUỐN SÁCH</span>
                                    </h4>
                                    <div class="p-4 rounded-2xl bg-slate-50/70 border border-slate-200 text-slate-700 leading-relaxed text-sm md:text-base max-h-48 overflow-y-auto">
                                        <p class="whitespace-pre-line" x-text="quickViewBook ? quickViewBook.description : ''"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Nhóm nút hành động ở đáy Modal -->
                            <div class="pt-5 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                                <a :href="quickViewBook?.id ? ('/books/' + quickViewBook.id) : (quickViewBook?.show_url || '#')" 
                                   id="modal-detail-link"
                                   class="text-xs sm:text-sm font-bold text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1.5 order-last sm:order-first transition py-2">
                                    <span>Xem trang chi tiết đầy đủ</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>

                                <div class="flex items-center gap-2.5 w-full sm:w-auto">
                                    <!-- Nút phụ: Đóng cửa sổ -->
                                    <button type="button" 
                                            @click="closeQuickView()"
                                            class="flex-1 sm:flex-none bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-3 px-5 rounded-xl transition cursor-pointer text-sm text-center">
                                        Đóng cửa sổ
                                    </button>

                                    <!-- Nút chính: Thêm vào giỏ mượn / Tạm hết -->
                                    <template x-if="quickViewBook && quickViewBook.available_copies > 0">
                                        <button type="button"
                                                @click="borrowFromQuickView()"
                                                class="flex-1 sm:flex-none bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow-sm transition flex items-center justify-center gap-2 cursor-pointer text-sm active:scale-[0.98]">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <span>Thêm vào giỏ mượn</span>
                                        </button>
                                    </template>

                                    <template x-if="quickViewBook && quickViewBook.available_copies <= 0">
                                        <button type="button"
                                                disabled
                                                class="flex-1 sm:flex-none bg-slate-200 text-slate-400 font-bold py-3 px-6 rounded-xl cursor-not-allowed text-sm">
                                            Tạm hết sách
                                        </button>
                                    </template>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </div>


    </div>
</x-app-layout>
