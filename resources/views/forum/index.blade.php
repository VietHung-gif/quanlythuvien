<x-app-layout>
    <div class="bg-slate-50 min-h-screen py-8" x-data="forumApp()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- THÔNG BÁO FLASH (SUCCESS / ERROR) -->
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border-2 border-emerald-200 rounded-2xl shadow-xs flex items-center justify-between text-emerald-800">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0 text-emerald-600 font-bold">
                            ✓
                        </div>
                        <span class="text-sm font-bold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-rose-50 border-2 border-rose-200 rounded-2xl shadow-xs flex items-center justify-between text-rose-800">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-rose-100 flex items-center justify-center shrink-0 text-rose-600 font-bold">
                            ✕
                        </div>
                        <span class="text-sm font-bold">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- ========================================================================= -->
            <!-- 1. BANNER TIÊU ĐỀ TINH GỌN (COMPACT HERO SECTION)                         -->
            <!-- ========================================================================= -->
            <div class="bg-white border-2 border-slate-200 rounded-3xl p-6 md:p-8 shadow-sm mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <!-- Cột bên trái: Badge nhỏ, Tiêu đề, Mô tả & Nguyên tắc cốt lõi inline -->
                <div class="flex-1 min-w-0">
                    <span class="inline-flex items-center gap-2 bg-slate-100 text-slate-800 text-[11px] font-black uppercase tracking-wider px-3 py-1 rounded-lg mb-3 border border-slate-200 shadow-2xs">
                        🏛️ HỘI QUÁN TRI THỨC — TIỂU ĐỘI 2P
                    </span>
                    
                    <h1 class="text-2xl md:text-3xl font-black text-slate-950 uppercase tracking-tight leading-tight">
                        GIAO LƯU, REVIEW SÁCH & LUẬN BÀN HỌC THUẬT
                    </h1>
                    
                    <p class="text-slate-600 text-sm md:text-base font-medium max-w-2xl mt-1 leading-relaxed">
                        Không gian đàm đạo kết nối những người yêu sách, chia sẻ góc nhìn chân thực về các tác phẩm trong kho lưu trữ, hỏi đáp tài liệu chuyên ngành và giao lưu học thuật văn minh.
                    </p>

                    <!-- Ghi chú nguyên tắc cốt lõi: Thanh thông báo tinh tế bên dưới mô tả -->
                    <div class="mt-3">
                        <div class="bg-amber-50/80 border border-amber-200/80 text-amber-900 rounded-xl px-3.5 py-2 text-xs font-semibold flex items-center gap-2 inline-flex shadow-2xs">
                            <span class="text-base shrink-0">🛡️</span>
                            <span>
                                <strong class="font-bold uppercase tracking-wide">NGUYÊN TẮC:</strong> 
                                Diễn đàn <strong class="text-slate-950 font-black">KHÔNG CỘNG EXP</strong> khi đăng bài hay bình luận để giữ môi trường thảo luận thuần khiết, loại bỏ spam cày cấp.
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Cột bên phải: Nút "+ ĐĂNG BÀI VIẾT MỚI" -->
                <div class="shrink-0 flex items-center self-start md:self-center">
                    @auth
                        <button type="button" 
                                @click="newPostModalOpen = true"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-black text-xs uppercase tracking-wider px-6 py-3.5 rounded-2xl shadow-md transition flex items-center gap-2 whitespace-nowrap cursor-pointer active:scale-95">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>+ ĐĂNG BÀI VIẾT MỚI</span>
                        </button>
                    @else
                        <a href="{{ route('login') }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white font-black text-xs uppercase tracking-wider px-6 py-3.5 rounded-2xl shadow-md transition flex items-center gap-2 whitespace-nowrap">
                            <span>ĐĂNG NHẬP ĐỂ GIAO LƯU &rarr;</span>
                        </a>
                    @endauth
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- 2. THANH BỘ LỌC CHỦ ĐỀ & TÌM KIẾM ĐỒNG BỘ CHIỀU CAO (FILTER & SEARCH BAR) -->
            <!-- ========================================================================= -->
            <div class="bg-white border-2 border-slate-200 rounded-2xl p-3 shadow-sm mb-6 flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
                
                <!-- Nhóm nút lọc danh mục (Pill Tabs) -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 lg:pb-0 scrollbar-thin select-none">
                    <!-- Tab [TẤT CẢ] -->
                    <a href="{{ route('forum.index', array_merge(request()->except('category', 'page'))) }}"
                       class="whitespace-nowrap uppercase tracking-wider {{ empty(request('category')) ? 'bg-slate-950 text-white font-black text-xs px-4 py-2.5 rounded-xl shadow-sm' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100 font-bold text-xs px-4 py-2.5 rounded-xl transition' }}">
                        TẤT CẢ ({{ $categories->sum('posts_count') }})
                    </a>

                    <!-- Tabs các danh mục động -->
                    @foreach($categories as $cat)
                        <a href="{{ route('forum.index', array_merge(request()->except('category', 'page'), ['category' => $cat->slug])) }}"
                           class="whitespace-nowrap uppercase tracking-wider flex items-center gap-1.5 {{ request('category') === $cat->slug ? 'bg-slate-950 text-white font-black text-xs px-4 py-2.5 rounded-xl shadow-sm' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100 font-bold text-xs px-4 py-2.5 rounded-xl transition' }}">
                            <span>{{ $cat->icon }}</span>
                            <span>{{ mb_strtoupper($cat->name, 'UTF-8') }}</span>
                            <span class="text-[10px] opacity-75 font-mono">({{ $cat->posts_count }})</span>
                        </a>
                    @endforeach
                </div>

                <!-- Nhóm tìm kiếm & sắp xếp (Đồng bộ chiều cao h-11) -->
                <form action="{{ route('forum.index') }}" method="GET" class="flex flex-wrap sm:flex-nowrap items-center gap-2.5 shrink-0">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif

                    <!-- Ô tìm kiếm từ khóa có kính lúp chuẩn lề -->
                    <div class="relative flex-1 sm:flex-initial">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" 
                               name="q" 
                               value="{{ request('q') }}" 
                               placeholder="Tìm kiếm bài viết..." 
                               class="pl-10 pr-4 h-11 bg-slate-50 border-2 border-slate-200 rounded-xl text-xs font-bold text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:border-blue-600 focus:ring-0 outline-none w-full sm:w-56 transition">
                    </div>

                    <!-- Dropdown "Mới nhất / Nhiều tương tác": Cao h-11 -->
                    <select name="sort" 
                            onchange="this.form.submit()"
                            class="h-11 bg-slate-50 border-2 border-slate-200 text-slate-900 font-bold rounded-xl px-3 text-xs cursor-pointer focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>⚡ MỚI NHẤT</option>
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>❤️ QUAN TÂM NHẤT</option>
                        <option value="comments" {{ request('sort') == 'comments' ? 'selected' : '' }}>💬 THẢO LUẬN SÔI NỔI</option>
                    </select>

                    <!-- Nút "LỌC": Cao h-11 -->
                    <button type="submit" 
                            class="h-11 px-5 rounded-xl text-xs font-black uppercase text-white bg-slate-900 hover:bg-slate-800 transition shadow-sm cursor-pointer whitespace-nowrap active:scale-95">
                        LỌC
                    </button>
                </form>
            </div>

            <!-- ========================================================================= -->
            <!-- 3. THIẾT KẾ THẺ BÀI VIẾT NÂNG CẤP (UPGRADED POST CARD)                    -->
            <!-- ========================================================================= -->
            <div class="space-y-4">
                @forelse($posts as $post)
                    <article class="bg-white border-2 {{ $post->is_pinned ? 'border-amber-300 border-l-8 border-l-amber-500 bg-gradient-to-r from-amber-50/30 to-white' : 'border-slate-200' }} hover:border-slate-300 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all duration-200 mb-5 relative group">
                        
                        <!-- HEADER THẺ: TÁC GIẢ + HUY HIỆU + THỐNG KÊ + MENU THAO TÁC 3 CHẤM -->
                        <div class="flex items-start justify-between gap-4">
                            
                            <!-- Cột bên trái: Avatar, Tên, Badges, Thời gian -->
                            <div class="flex items-center gap-3 flex-wrap min-w-0">
                                <!-- Avatar bo tròn sắc nét -->
                                <div class="w-11 h-11 rounded-full overflow-hidden ring-2 ring-slate-100 shrink-0 bg-slate-100 border border-slate-200 flex items-center justify-center">
                                    @if($post->user->avatar)
                                        <img src="{{ asset('storage/' . $post->user->avatar) }}" 
                                             alt="{{ $post->user->name }}" 
                                             class="w-11 h-11 rounded-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center font-bold text-xs text-blue-700 bg-blue-50">
                                            {{ mb_strtoupper(mb_substr($post->user->name, 0, 2, 'UTF-8'), 'UTF-8') }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Tên tác giả, Badge danh hiệu, Badge vai trò, Thời gian -->
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-extrabold text-slate-950 text-sm hover:text-blue-600 transition">
                                            {{ $post->user->name }}
                                        </span>

                                        <!-- Badge Danh Hiệu (Thách Đấu / Buff Bẩn) -->
                                        @if($post->user->title_badge)
                                            <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-md border border-amber-200/80">
                                                {{ $post->user->title_formatted ?? $post->user->title_badge }}
                                            </span>
                                        @endif

                                        <!-- Badge Vai Trò (QUẢN TRỊ VIÊN / THỦ THƯ / ĐỘC GIẢ) -->
                                        @if($post->user->isAdmin())
                                            <span class="bg-rose-100 text-rose-800 text-[10px] font-bold px-2 py-0.5 rounded-md border border-rose-200">
                                                👑 QUẢN TRỊ VIÊN
                                            </span>
                                        @elseif($post->user->isLibrarian())
                                            <span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-0.5 rounded-md border border-blue-200">
                                                📚 THỦ THƯ
                                            </span>
                                        @elseif($post->user->exp > 350)
                                            <span class="bg-indigo-100 text-indigo-800 text-[10px] font-bold px-2 py-0.5 rounded-md border border-indigo-200">
                                                🌟 HỌC GIẢ TRI THỨC
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Hàng meta phụ: Thời gian & Danh mục -->
                                    <div class="flex items-center gap-2 mt-0.5 text-xs text-slate-500 font-mono">
                                        <span>{{ $post->created_at->diffForHumans() }}</span>
                                        <span>•</span>
                                        <span class="font-sans text-slate-600 font-semibold flex items-center gap-1">
                                            <span>{{ $post->category->icon ?? '💬' }}</span>
                                            <span>{{ $post->category->name ?? 'Tổng hợp' }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Cột bên phải: Tương tác (Mắt, Chat, Tim) & Menu Quản trị 3 chấm gọn gàng -->
                            <div class="flex items-center gap-3 shrink-0">
                                
                                <!-- Thống kê tương tác: Lượt xem, Bình luận, Nút Thả tim -->
                                <div class="flex items-center gap-3 font-mono text-xs">
                                    <!-- Lượt xem -->
                                    <span title="Lượt xem" class="font-bold text-slate-700 text-xs flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <span>{{ number_format($post->views_count) }}</span>
                                    </span>

                                    <!-- Lượt bình luận -->
                                    <a href="{{ route('forum.show', $post->id) }}#comments" 
                                       title="Bình luận" 
                                       class="font-bold text-slate-700 hover:text-blue-600 text-xs flex items-center gap-1.5 transition">
                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                        <span>{{ number_format($post->comments_count) }}</span>
                                    </a>

                                    <!-- Nút Thả tim AJAX -->
                                    <button type="button" 
                                            @click="toggleLike({{ $post->id }})"
                                            id="like-btn-{{ $post->id }}"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold transition-all cursor-pointer border {{ $post->isLikedBy(auth()->user()) ? 'bg-rose-50 text-rose-600 border-rose-200' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border-slate-200' }}">
                                        <span>❤️</span>
                                        <span id="like-count-{{ $post->id }}" class="font-bold">{{ number_format($post->likes_count) }}</span>
                                    </button>
                                </div>

                                <!-- MENU HÀNH ĐỘNG QUẢN TRỊ (GOM VÀO DROPDOWN 3 CHẤM TINH TẾ) -->
                                @auth
                                    @if(auth()->id() === $post->user_id || in_array(auth()->user()->role, ['admin', 'librarian']))
                                        <div class="relative" x-data="{ menuOpen: false }" @click.outside="menuOpen = false">
                                            <button type="button" 
                                                    @click="menuOpen = !menuOpen" 
                                                    class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 flex items-center justify-center transition cursor-pointer border border-slate-200"
                                                    title="Tùy chọn quản trị">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                </svg>
                                            </button>

                                            <!-- Dropdown menu popup -->
                                            <div x-show="menuOpen" 
                                                 x-cloak 
                                                 style="display: none;"
                                                 x-transition:enter="transition ease-out duration-150"
                                                 x-transition:enter-start="opacity-0 scale-95"
                                                 x-transition:enter-end="opacity-100 scale-100"
                                                 x-transition:leave="transition ease-in duration-100"
                                                 x-transition:leave-start="opacity-100 scale-100"
                                                 x-transition:leave-end="opacity-0 scale-95"
                                                 class="absolute right-0 top-10 w-48 bg-white rounded-2xl shadow-xl border-2 border-slate-200 py-2 z-30">
                                                
                                                <!-- Sửa bài viết -->
                                                @if(auth()->id() === $post->user_id || auth()->user()->role === 'admin')
                                                    <a href="{{ route('forum.edit', $post->id) }}" 
                                                       class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-100 hover:text-blue-600 transition">
                                                        <span>✏️</span> Chỉnh sửa bài viết
                                                    </a>
                                                @endif

                                                <!-- Ghim / Bỏ ghim bài viết -->
                                                @if(in_array(auth()->user()->role, ['admin', 'librarian']))
                                                    <form action="{{ route('forum.pin', $post->id) }}" method="POST" class="block">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-100 hover:text-amber-600 transition text-left cursor-pointer">
                                                            <span>📌</span> {{ $post->is_pinned ? 'Bỏ ghim đầu trang' : 'Ghim bài lên đầu' }}
                                                        </button>
                                                    </form>
                                                @endif

                                                <!-- Xóa bài viết -->
                                                @if(auth()->id() === $post->user_id || auth()->user()->role === 'admin')
                                                    <form action="{{ route('forum.destroy', $post->id) }}" method="POST" class="block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này không?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-rose-600 hover:bg-rose-50 transition text-left cursor-pointer border-t border-slate-100">
                                                            <span>🗑️</span> Xóa bài viết
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endauth

                            </div>

                        </div>

                        <!-- PHẦN THÂN NỘI DUNG: HUY HIỆU GHIM + TIÊU ĐỀ + ĐOẠN TRÍCH -->
                        <div class="mt-3">
                            <!-- Huy hiệu bài viết đã ghim -->
                            @if($post->is_pinned)
                                <div class="mb-2">
                                    <span class="bg-amber-100 text-amber-900 border border-amber-300 font-black text-[10px] uppercase px-2.5 py-1 rounded-md inline-flex items-center gap-1">
                                        <span>📌</span>
                                        <span>THÔNG BÁO GHIM ĐẦU TRANG</span>
                                    </span>
                                </div>
                            @endif

                            <!-- Tiêu đề bài viết: Đen đậm to bản -->
                            <h2 class="text-lg md:text-xl font-black text-slate-950 hover:text-blue-600 transition leading-snug my-2.5 cursor-pointer">
                                <a href="{{ route('forum.show', $post->id) }}" class="inline-block hover:underline">
                                    {{ $post->title }}
                                </a>
                            </h2>

                            <!-- Đoạn trích nội dung: Chữ xám than sắc nét -->
                            <p class="text-slate-700 text-sm md:text-base leading-relaxed line-clamp-3 font-normal">
                                {{ $post->snippet ?? Str::limit(strip_tags($post->content), 240) }}
                            </p>
                        </div>

                        <!-- KHỐI ĐÍNH KÈM SÁCH ĐƯỢC NHẮC TỚI (NẾU CÓ) -->
                        @if($post->book)
                            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 mt-4 shadow-2xs">
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <!-- Bìa sách thu nhỏ Vector động -->
                                    <div class="w-10 h-14 shrink-0 rounded-md overflow-hidden shadow-xs">
                                        <x-book-cover :book="$post->book" class="w-full h-full rounded-none shadow-none" />
                                    </div>

                                    <div class="min-w-0">
                                        <div class="text-[10px] font-mono font-bold text-blue-700 uppercase tracking-wider">
                                            📖 SÁCH ĐƯỢC NHẮC TỚI:
                                        </div>
                                        <a href="{{ route('books.show', $post->book->id) }}" 
                                           class="text-slate-950 font-black text-xs uppercase truncate max-w-xs sm:max-w-md block hover:text-blue-600 transition" 
                                           title="{{ $post->book->title }}">
                                            {{ $post->book->title }}
                                        </a>
                                        <div class="text-[11px] text-slate-600 font-mono mt-0.5">
                                            Tác giả: <span class="text-slate-800 font-semibold">{{ $post->book->author }}</span> • Sẵn sàng: <span class="text-emerald-700 font-bold">{{ $post->book->quantity }} cuốn</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Nút "+ THÊM VÀO GIỎ MƯỢN" -->
                                @if($post->book->quantity > 0)
                                    <button type="button" 
                                            onclick="if (window.addToCart) { window.addToCart({{ $post->book->id }}); } else { alert('Vui lòng đợi giỏ sách khởi tạo!'); }"
                                            class="shrink-0 px-3.5 py-2 rounded-xl text-xs font-bold uppercase tracking-wider text-white bg-blue-600 hover:bg-blue-700 transition shadow-xs flex items-center gap-1.5 active:scale-95 cursor-pointer self-start sm:self-auto">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                        <span>+ THÊM VÀO GIỎ MƯỢN</span>
                                    </button>
                                @else
                                    <span class="text-[11px] font-bold text-slate-400 bg-slate-200/70 px-3 py-1.5 rounded-xl border border-slate-300">
                                        Tạm hết sách
                                    </span>
                                @endif
                            </div>
                        @endif

                    </article>
                @empty
                    <!-- Trạng thái trống khi không tìm thấy bài viết -->
                    <div class="bg-white rounded-3xl border-2 border-slate-200 p-12 text-center text-slate-500 space-y-3 shadow-sm">
                        <span class="text-4xl">📭</span>
                        <h3 class="text-lg font-black text-slate-900 tracking-tight">
                            CHƯA CÓ BÀI VIẾT NÀO TRONG MỤC NÀY
                        </h3>
                        <p class="text-sm text-slate-600 max-w-md mx-auto">
                            Hãy là người đầu tiên khai mở thảo luận tri thức bằng cách bấm "+ ĐĂNG BÀI VIẾT MỚI"!
                        </p>
                        @auth
                            <div class="pt-2">
                                <button type="button" 
                                        @click="newPostModalOpen = true" 
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl font-black text-xs uppercase tracking-wider text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition cursor-pointer">
                                    + BẮT ĐẦU THẢO LUẬN NGAY
                                </button>
                            </div>
                        @endauth
                    </div>
                @endforelse
            </div>

            <!-- ========================================================================= -->
            <!-- 4. PHÂN TRANG (PAGINATION)                                                -->
            <!-- ========================================================================= -->
            @if($posts->hasPages())
                <div class="p-4 bg-white rounded-2xl border-2 border-slate-200 shadow-xs">
                    {{ $posts->links() }}
                </div>
            @endif

        </div>

        <!-- ========================================================================= -->
        <!-- 5. MODAL ĐĂNG BÀI VIẾT MỚI (MODERN ACADEMIC LIGHT THEME)                   -->
        <!-- ========================================================================= -->
        <div x-show="newPostModalOpen" 
             x-cloak 
             style="display: none;"
             class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 py-8 text-center sm:block sm:p-0">
                
                <!-- Lớp nền tối mờ -->
                <div x-show="newPostModalOpen" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="newPostModalOpen = false"
                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <!-- Khối hộp thoại chính Modal -->
                <div x-show="newPostModalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom sm:align-middle bg-white rounded-3xl p-6 sm:p-8 max-w-2xl w-full text-left relative z-10 border-2 border-slate-200 shadow-2xl my-8">
                    
                    <form action="{{ route('forum.store') }}" method="POST" @submit="isSubmitting = true">
                        @csrf
                        
                        <!-- Tiêu đề Modal -->
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div>
                                <h3 class="text-xl font-black text-slate-950 uppercase tracking-tight flex items-center gap-2">
                                    <span class="text-blue-600">✍️</span> ĐĂNG BÀI VIẾT HỘI QUÁN
                                </h3>
                                <p class="text-xs text-slate-500 font-medium mt-0.5">
                                    Chia sẻ cảm nhận, review sách hoặc đặt câu hỏi học thuật cùng cộng đồng
                                </p>
                            </div>
                            <button type="button" 
                                    @click="newPostModalOpen = false" 
                                    class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-900 flex items-center justify-center transition cursor-pointer">
                                &times;
                            </button>
                        </div>

                        <div class="mt-5 space-y-4 text-sm">
                            <!-- Tiêu đề bài viết -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1">
                                    TIÊU ĐỀ BÀI VIẾT <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" 
                                       name="title" 
                                       required 
                                       minlength="5"
                                       placeholder="Ví dụ: Cảm nhận sâu sắc sau khi đọc xong cuốn Nhà Giả Kim..."
                                       class="w-full rounded-xl border-2 border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 font-semibold focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition placeholder:text-slate-400">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Chuyên mục -->
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1">
                                        CHUYÊN MỤC <span class="text-rose-500">*</span>
                                    </label>
                                    <select name="category_id" required class="w-full rounded-xl border-2 border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 font-semibold focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition cursor-pointer">
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Gắn thẻ sách (Searchable Book Combobox / Autocomplete Dropdown) -->
                                <div x-data="bookCombobox()" class="relative">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1">
                                        GẮN THẺ SÁCH (TÙY CHỌN):
                                    </label>

                                    <!-- Thẻ input hidden gửi dữ liệu book_id lên controller -->
                                    <input type="hidden" name="book_id" :value="selectedBookId">

                                    <!-- 1. TRẠNG THÁI KHI CHƯA CHỌN SÁCH: Ô INPUT TÌM KIẾM & DROPDOWN GỢI Ý -->
                                    <div x-show="!selectedBook" class="relative" @click.away="open = false">
                                        <input type="text"
                                               x-model="searchQuery"
                                               @focus="open = true"
                                               @input="open = true"
                                               @keydown.escape="open = false"
                                               placeholder="🔍 Gõ tên sách hoặc tác giả để tìm nhanh..."
                                               class="h-11 w-full bg-slate-50 border-2 border-slate-200 focus:bg-white focus:border-blue-600 rounded-xl px-4 text-xs font-bold text-slate-900 placeholder:text-slate-400 outline-none transition">

                                        <!-- Danh sách kết quả gợi ý xổ xuống (Dropdown Panel) -->
                                        <div x-show="open"
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 translate-y-1"
                                             x-transition:enter-end="opacity-100 translate-y-0"
                                             x-transition:leave="transition ease-in duration-100"
                                             x-transition:leave-start="opacity-100 translate-y-0"
                                             x-transition:leave-end="opacity-0 translate-y-1"
                                             class="absolute left-0 right-0 top-full bg-white border-2 border-slate-200 rounded-xl shadow-2xl mt-1.5 max-h-60 overflow-y-auto z-50 p-1.5 divide-y divide-slate-100"
                                             style="display: none;">
                                            <template x-for="book in filteredBooks" :key="book.id">
                                                <div @click="selectBook(book)"
                                                     class="hover:bg-blue-50 hover:text-blue-700 rounded-lg p-2 transition cursor-pointer flex items-center justify-between gap-2 group">
                                                    <div class="min-w-0 flex-1">
                                                        <div class="font-bold text-xs text-slate-900 group-hover:text-blue-700 truncate" x-text="book.title"></div>
                                                        <div class="text-[11px] text-slate-500 font-medium group-hover:text-blue-600/80 truncate">
                                                            <span x-text="'Tác giả: ' + (book.author || 'Chưa rõ')"></span>
                                                            <span class="mx-1">•</span>
                                                            <span x-text="'Tồn kho: ' + (book.quantity ?? 0)"></span>
                                                        </div>
                                                    </div>
                                                    <span class="text-[10px] font-bold text-blue-600 bg-blue-100/70 px-2 py-0.5 rounded-md opacity-0 group-hover:opacity-100 transition shrink-0">
                                                        Chọn ↵
                                                    </span>
                                                </div>
                                            </template>

                                            <!-- Thông báo khi không tìm thấy cuốn sách nào -->
                                            <div x-show="filteredBooks.length === 0" class="p-3 text-center text-xs text-slate-500 font-medium">
                                                Không tìm thấy cuốn sách nào phù hợp
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 2. TRẠNG THÁI KHI ĐÃ CHỌN SÁCH: THẺ SÁCH ĐÃ CHỌN (SELECTED BOOK BADGE) -->
                                    <div x-show="selectedBook" 
                                         x-transition:enter="transition ease-out duration-150"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         class="h-11 bg-blue-50 border border-blue-200 rounded-xl px-3 flex items-center justify-between gap-2.5 shadow-2xs"
                                         style="display: none;">
                                        <!-- Bên trái: Icon cuốn sách + Tên sách in hoa đậm nét + Tác giả -->
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 font-bold text-sm">
                                                📖
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-bold text-xs text-blue-950 uppercase tracking-tight truncate leading-tight" x-text="selectedBook ? selectedBook.title : ''"></div>
                                                <div class="text-[11px] text-blue-700 font-medium truncate leading-tight" x-text="selectedBook ? ('Tác giả: ' + (selectedBook.author || 'Chưa rõ')) : ''"></div>
                                            </div>
                                        </div>

                                        <!-- Bên phải: Nút ✕ Hủy chọn sách -->
                                        <button type="button" 
                                                @click="clearBook()"
                                                title="Hủy chọn sách"
                                                class="w-6 h-6 rounded-lg bg-white/90 hover:bg-rose-100 text-slate-400 hover:text-rose-600 border border-blue-200/80 hover:border-rose-300 flex items-center justify-center transition cursor-pointer shrink-0 font-bold text-xs">
                                            ✕
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Nội dung bài viết -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1">
                                    NỘI DUNG BÀI VIẾT <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="content" 
                                          rows="6" 
                                          required 
                                          minlength="5"
                                          placeholder="Chia sẻ cảm nhận, trích dẫn những câu văn tâm đắc, hoặc nội dung bạn muốn cùng mọi người thảo luận..."
                                          class="w-full rounded-xl border-2 border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 font-normal focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition leading-relaxed placeholder:text-slate-400"></textarea>
                                <div class="text-[11px] text-slate-500 font-medium mt-1">
                                    💡 Mẹo: Bài viết có gắn thẻ sách sẽ hiển thị nút "Thêm vào giỏ mượn" để các độc giả khác có thể mượn ngay!
                                </div>
                            </div>
                        </div>

                        <!-- Chân Modal -->
                        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-100">
                            <span class="text-xs text-amber-800 font-bold bg-amber-50 px-3 py-1 rounded-lg border border-amber-200">
                                🛡️ Đăng bài không cộng EXP để chống spam
                            </span>
                            
                            <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                                <button type="button" 
                                        @click="newPostModalOpen = false" 
                                        class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-100 border border-slate-200 transition cursor-pointer">
                                    HỦY BỎ
                                </button>
                                
                                <button type="submit" 
                                        :disabled="isSubmitting"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider px-5 py-2.5 rounded-xl shadow-xs transition cursor-pointer">
                                    <span x-show="!isSubmitting">XÁC NHẬN ĐĂNG BÀI</span>
                                    <span x-show="isSubmitting">ĐANG XỬ LÝ...</span>
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>

    <!-- SCRIPT XỬ LÝ HỘI QUÁN (THẢ TIM AJAX & COMBOBOX CHỌN SÁCH) -->
    <script>
        function bookCombobox() {
            return {
                open: false,
                searchQuery: '',
                selectedBook: null,
                books: {{ Js::from($books->map(fn($b) => [
                    'id' => $b->id,
                    'title' => $b->title,
                    'author' => $b->author,
                    'quantity' => $b->quantity ?? 0,
                ])) }},

                get selectedBookId() {
                    return this.selectedBook ? this.selectedBook.id : '';
                },

                init() {
                    const oldBookId = '{{ old('book_id') }}';
                    if (oldBookId) {
                        const found = this.books.find(b => b.id == oldBookId);
                        if (found) this.selectedBook = found;
                    }
                },

                removeAccents(str) {
                    if (!str) return '';
                    return str
                        .toLowerCase()
                        .normalize('NFD')
                        .replace(/[\u0300-\u036f]/g, '')
                        .replace(/[đĐ]/g, 'd')
                        .trim();
                },

                get filteredBooks() {
                    if (!this.searchQuery.trim()) {
                        return this.books.slice(0, 50);
                    }
                    const q = this.removeAccents(this.searchQuery);
                    return this.books.filter(b => {
                        const title = this.removeAccents(b.title || '');
                        const author = this.removeAccents(b.author || '');
                        return title.includes(q) || author.includes(q);
                    }).slice(0, 50);
                },

                selectBook(book) {
                    this.selectedBook = book;
                    this.open = false;
                    this.searchQuery = '';
                },

                clearBook() {
                    this.selectedBook = null;
                    this.searchQuery = '';
                    this.open = false;
                }
            };
        }

        function forumApp() {
            return {
                newPostModalOpen: false,
                isSubmitting: false,

                async toggleLike(postId) {
                    @guest
                        alert('Vui lòng đăng nhập để thả tim cho bài viết này!');
                        window.location.href = '{{ route('login') }}';
                        return;
                    @endguest

                    const likeBtn = document.getElementById(`like-btn-${postId}`);
                    const likeCountEl = document.getElementById(`like-count-${postId}`);

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        const res = await fetch(`/forum/${postId}/like`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });

                        const data = await res.json();
                        if (data.success) {
                            if (likeCountEl) likeCountEl.textContent = data.likes_count;
                            if (likeBtn) {
                                if (data.liked) {
                                    likeBtn.classList.remove('bg-slate-100', 'text-slate-700', 'border-slate-200');
                                    likeBtn.classList.add('bg-rose-50', 'text-rose-600', 'border-rose-200');
                                } else {
                                    likeBtn.classList.remove('bg-rose-50', 'text-rose-600', 'border-rose-200');
                                    likeBtn.classList.add('bg-slate-100', 'text-slate-700', 'border-slate-200');
                                }
                            }
                        }
                    } catch (err) {
                        console.error('Lỗi khi thả tim:', err);
                    }
                }
            };
        }
    </script>
</x-app-layout>
