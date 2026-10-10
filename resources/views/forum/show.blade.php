<x-app-layout>
    @php
        $catBadgeClasses = match($post->category->color ?? 'indigo') {
            'pink' => 'bg-rose-50 text-rose-700 border-rose-200/80',
            'amber' => 'bg-amber-50 text-amber-800 border-amber-200/80',
            'emerald' => 'bg-emerald-50 text-emerald-800 border-emerald-200/80',
            'purple' => 'bg-purple-50 text-purple-800 border-purple-200/80',
            default => 'bg-indigo-50 text-indigo-700 border-indigo-200/80',
        };
    @endphp

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <!-- 3. THANH ĐIỀU HƯỚNG BREADCRUMB CHUẨN HIỆN ĐẠI -->
            <nav class="flex items-center flex-wrap gap-1.5 text-xs text-slate-500 font-medium">
                <a href="{{ url('/') }}" class="hover:text-indigo-600 transition flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Trang chủ</span>
                </a>
                <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('forum.index') }}" class="hover:text-indigo-600 transition">
                    Hội quán tri thức
                </a>
                <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('forum.index', ['category' => $post->category->slug]) }}" class="hover:text-indigo-600 transition">
                    {{ $post->category->name }}
                </a>
                <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-slate-800 font-semibold truncate max-w-xs sm:max-w-md" title="{{ $post->title }}">
                    {{ Str::limit($post->title, 45) }}
                </span>
            </nav>

            <!-- Huy hiệu chuyên mục & trạng thái bài viết -->
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('forum.index', ['category' => $post->category->slug]) }}" 
                   class="px-3 py-1 rounded-full text-xs font-semibold {{ $catBadgeClasses }} border transition hover:opacity-90 inline-flex items-center gap-1.5">
                    <span>{{ $post->category->icon }}</span>
                    <span>{{ $post->category->name }}</span>
                </a>
                @if($post->is_pinned)
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 inline-flex items-center gap-1">
                        📌 Đã ghim
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10" x-data="forumPostDetail()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">

                <!-- ========================================================= -->
                <!-- CỘT CHÍNH (NỘI DUNG BÀI VIẾT & KHU VỰC BÌNH LUẬN)         -->
                <!-- ========================================================= -->
                <div class="lg:col-span-2 space-y-6 sm:space-y-8">

                    <!-- 1. CỘT NỘI DUNG CHÍNH (MAIN ARTICLE CARD) -->
                    <article class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-6 relative">
                        
                        <!-- Header tác giả & Metadata -->
                        <div class="flex items-start justify-between gap-4 pb-6 border-b border-slate-100">
                            <div class="flex items-center gap-3.5">
                                <!-- Avatar tác giả bo tròn lớn w-12 h-12 -->
                                <div class="relative w-12 h-12 rounded-full overflow-hidden border border-slate-200 bg-slate-100 shrink-0 shadow-2xs">
                                    @if($post->user->avatar)
                                        <img src="{{ asset('storage/' . $post->user->avatar) }}" 
                                             alt="{{ $post->user->name }}" 
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center font-bold text-sm text-indigo-700 bg-indigo-50">
                                            {{ mb_strtoupper(mb_substr($post->user->name, 0, 2, 'UTF-8'), 'UTF-8') }}
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <!-- Tên tác giả & các huy hiệu tinh giản -->
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-bold text-slate-900 text-base">
                                            {{ $post->user->name }}
                                        </span>

                                        <!-- Badge vai trò quản trị thanh lịch (chỉ hiển thị 1 badge) -->
                                        @if($post->user->isAdmin())
                                            <span class="bg-indigo-50 text-indigo-700 border border-indigo-200/60 text-[11px] font-semibold px-2 py-0.5 rounded-full inline-flex items-center gap-1">
                                                👑 Quản trị viên
                                            </span>
                                        @elseif($post->user->isLibrarian())
                                            <span class="bg-blue-50 text-blue-700 border border-blue-200/60 text-[11px] font-semibold px-2 py-0.5 rounded-full inline-flex items-center gap-1">
                                                📚 Thủ thư
                                            </span>
                                        @endif

                                        <!-- Cấp bậc danh hiệu: màu vàng đồng nhẹ bo viền siêu mảnh -->
                                        <span class="bg-amber-50/70 text-amber-800 border border-amber-200/60 text-[11px] font-medium px-2 py-0.5 rounded-full inline-flex items-center gap-1">
                                            <span>{{ $post->user->badge_icon }}</span>
                                            <span>{{ $post->user->title_badge ?: 'Tân Thủ Nhập Môn' }}</span>
                                        </span>

                                        <!-- Tag Tác giả bài viết -->
                                        <span class="bg-slate-100 text-slate-600 border border-slate-200/60 text-[10px] font-medium px-2 py-0.5 rounded-full">
                                            ✍️ Tác giả bài viết
                                        </span>
                                    </div>

                                    <!-- Thời gian đăng & Lượt xem -->
                                    <div class="text-xs text-slate-400 mt-1 flex items-center flex-wrap gap-2 sm:gap-3">
                                        <span>Đăng {{ $post->created_at->diffForHumans() }} ({{ $post->created_at->format('d/m/Y H:i') }})</span>
                                        <span>•</span>
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            {{ number_format($post->views_count) }} lượt xem
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Menu icon 3 chấm thao tác Quản trị / Tác giả tinh tế -->
                            @auth
                                @if(auth()->id() === $post->user_id || in_array(auth()->user()->role, ['admin', 'librarian']))
                                    <div class="relative" x-data="{ postActions: false }" @click.outside="postActions = false">
                                        <button type="button" 
                                                @click="postActions = !postActions"
                                                class="p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition border border-slate-200/80 cursor-pointer"
                                                title="Thao tác bài viết">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                        </button>

                                        <!-- Dropdown Menu thao tác -->
                                        <div x-show="postActions" 
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="transform opacity-0 scale-95"
                                             x-transition:enter-end="transform opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="transform opacity-100 scale-100"
                                             x-transition:leave-end="transform opacity-0 scale-95"
                                             x-cloak
                                             class="absolute right-0 mt-1.5 w-44 bg-white rounded-2xl shadow-xl border border-slate-200 py-1.5 z-30">
                                            @if(auth()->check() && (auth()->id() === $post->user_id || auth()->user()->role === 'admin'))
                                                <a href="{{ route('forum.edit', $post->id) }}" 
                                                   class="flex items-center gap-2 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition">
                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                    </svg>
                                                    Chỉnh sửa bài
                                                </a>
                                            @endif

                                            @if(in_array(auth()->user()->role, ['admin', 'librarian']))
                                                <form action="{{ route('forum.pin', $post->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 transition text-left cursor-pointer">
                                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                                        </svg>
                                                        {{ $post->is_pinned ? 'Bỏ ghim bài' : 'Ghim bài viết' }}
                                                    </button>
                                                </form>
                                            @endif

                                            <form action="{{ route('forum.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này vĩnh viễn?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-medium text-slate-700 hover:text-rose-600 hover:bg-rose-50 transition text-left cursor-pointer">
                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    Xóa bài viết
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @endauth
                        </div>

                        <!-- Tiêu đề lớn bài viết chuẩn Editorial Typography -->
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">
                            {{ $post->title }}
                        </h1>

                        <!-- Toàn bộ nội dung bài viết: Độ tương phản cao, êm mắt khi đọc lâu -->
                        <div class="text-slate-700 text-base leading-relaxed tracking-normal font-normal space-y-4 break-words whitespace-pre-line">
                            {{ $post->content }}
                        </div>

                        <!-- THIẾT KẾ LẠI THẺ "SÁCH ĐƯỢC NHẮC TỚI TRONG BÀI" (BOOK MENTION WIDGET) -->
                        @if($post->book)
                            <div class="bg-gradient-to-r from-slate-50 to-indigo-50/30 border border-indigo-100 rounded-2xl p-5 mt-8 flex flex-col md:flex-row gap-5 items-center justify-between shadow-sm">
                                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 sm:gap-5 text-center sm:text-left flex-1 min-w-0">
                                    <!-- Bìa sách: Đổ bóng 3D nhẹ -->
                                    <a href="{{ route('books.show', $post->book->id) }}" class="shrink-0 group">
                                        <img src="{{ $post->book->cover_url }}" 
                                             alt="{{ $post->book->title }}" 
                                             class="w-20 h-28 object-cover rounded-xl shadow-md border border-slate-200 transition-transform duration-200 group-hover:scale-105">
                                    </a>

                                    <div class="space-y-2 flex-1 min-w-0">
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-100/70 text-indigo-700 border border-indigo-200/60 w-fit">
                                            📖 Sách được nhắc tới trong bài
                                        </div>
                                        <h4 class="text-lg font-black text-slate-900 truncate hover:text-indigo-600 transition" title="{{ $post->book->title }}">
                                            <a href="{{ route('books.show', $post->book->id) }}">
                                                {{ $post->book->title }}
                                            </a>
                                        </h4>
                                        <p class="text-xs text-slate-500">
                                            Tác giả: <span class="font-medium text-slate-700">{{ $post->book->author }}</span> • NXB: {{ $post->book->publisher ?: 'Đang cập nhật' }} ({{ $post->book->publish_year ?: 'N/A' }})
                                        </p>
                                        <div>
                                            @if($post->book->quantity > 0)
                                                <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 border border-emerald-200/60 px-2.5 py-1 rounded-full w-fit inline-flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Còn {{ $post->book->quantity }} cuốn khả dụng
                                                </span>
                                            @else
                                                <span class="text-xs font-semibold text-rose-600 bg-rose-50 border border-rose-200/60 px-2.5 py-1 rounded-full w-fit inline-flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                    Tạm hết sách trong kho
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Nhóm nút CTA -->
                                <div class="flex flex-wrap items-center justify-center gap-2.5 shrink-0 pt-2 sm:pt-0">
                                    <button type="button" 
                                            onclick="if (window.addToCart) { window.addToCart({{ $post->book->id }}); } else { alert('Vui lòng đợi giỏ sách khởi tạo!'); }"
                                            class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                        <span>THÊM VÀO GIỎ SÁCH</span>
                                    </button>

                                    <a href="{{ route('books.show', $post->book->id) }}" 
                                       class="bg-white border border-slate-200 text-slate-700 text-xs font-semibold px-4 py-2.5 rounded-xl hover:bg-slate-50 transition flex items-center gap-1.5 shadow-2xs">
                                        <span>XEM CHI TIẾT</span>
                                        <span>&rarr;</span>
                                    </a>
                                </div>
                            </div>
                        @endif

                        <!-- Thanh tương tác: Nút Thả tim + Chia sẻ -->
                        <div class="pt-6 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <!-- Nút Thả tim AJAX hiện đại -->
                                <button type="button" 
                                        @click="toggleLike({{ $post->id }})"
                                        id="detail-like-btn"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer border {{ $post->isLikedBy(auth()->user()) ? 'bg-rose-50 text-rose-600 border-rose-200 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-rose-50/50 hover:text-rose-600 hover:border-rose-200 shadow-2xs' }}">
                                    <svg class="w-4 h-4 {{ $post->isLikedBy(auth()->user()) ? 'text-rose-500 fill-rose-500' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                    <span id="detail-like-label">{{ $post->isLikedBy(auth()->user()) ? 'ĐÃ THÍCH' : 'THẢ TIM' }}</span>
                                    <span id="detail-like-count" class="font-mono text-xs font-semibold ml-0.5">({{ $post->likes_count }})</span>
                                </button>

                                <!-- Nút Chia sẻ liên kết -->
                                <button type="button" 
                                        @click="copyPostUrl()"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-medium text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition cursor-pointer shadow-2xs">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                    </svg>
                                    <span x-text="copied ? 'Đã sao chép liên kết!' : 'Chia sẻ'">Chia sẻ</span>
                                </button>
                            </div>

                            <div class="text-xs text-slate-500 font-medium flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                <span><strong class="text-slate-800 font-bold">{{ $post->comments->count() }}</strong> bình luận thảo luận</span>
                            </div>
                        </div>

                    </article>

                    <!-- KHU VỰC BÌNH LUẬN & THẢO LUẬN (COMMENTS SECTION) -->
                    <section class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 space-y-6 shadow-sm">
                        
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight flex items-center gap-2">
                                <span>💬</span> BÌNH LUẬN & ĐÀM ĐẠO ({{ $post->comments->count() }})
                            </h3>
                            <span class="text-[11px] text-amber-700 font-medium bg-amber-50 border border-amber-200/60 px-2.5 py-1 rounded-full">
                                🛡️ Bình luận không cộng EXP
                            </span>
                        </div>

                        <!-- Khung Nhập Bình Luận -->
                        @auth
                            <form action="{{ route('forum.comment', $post->id) }}" method="POST" class="space-y-3">
                                @csrf
                                <div class="flex items-start gap-3.5">
                                    <div class="w-10 h-10 rounded-full overflow-hidden border border-slate-200 bg-slate-100 shrink-0 shadow-2xs">
                                        @if(auth()->user()->avatar)
                                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" 
                                                 alt="{{ auth()->user()->name }}" 
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center font-bold text-xs text-indigo-700 bg-indigo-50">
                                                {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 2, 'UTF-8'), 'UTF-8') }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex-1 space-y-2.5">
                                        <textarea name="content" 
                                                  rows="3" 
                                                  required 
                                                  minlength="2"
                                                  placeholder="Viết cảm nhận, trao đổi quan điểm của bạn cùng các độc giả khác..."
                                                  class="w-full rounded-2xl border border-slate-200 bg-slate-50/70 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-800 placeholder-slate-400 p-3.5 transition leading-relaxed"></textarea>
                                        
                                        <div class="flex items-center justify-between pt-1">
                                            <span class="text-xs text-slate-400">
                                                Chia sẻ văn minh, tôn trọng người đọc.
                                            </span>
                                            <button type="submit" 
                                                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-sm transition cursor-pointer">
                                                GỬI BÌNH LUẬN
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 text-center space-y-3">
                                <p class="text-xs text-slate-600">
                                    Bạn cần đăng nhập để tham gia đàm đạo cùng các độc giả trong thư viện!
                                </p>
                                <a href="{{ route('login') }}" class="inline-flex items-center px-5 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm transition">
                                    ĐĂNG NHẬP NGAY &rarr;
                                </a>
                            </div>
                        @endauth

                        <!-- Danh Sách Các Bình Luận -->
                        <div class="space-y-3.5 pt-2">
                            @forelse($post->comments as $comment)
                                <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-100 space-y-2.5 hover:bg-slate-50 hover:border-slate-200 transition">
                                    
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-3">
                                            <!-- Avatar Người Bình Luận -->
                                            <div class="w-9 h-9 rounded-full overflow-hidden border border-slate-200 bg-white shrink-0 shadow-2xs">
                                                @if($comment->user->avatar)
                                                    <img src="{{ asset('storage/' . $comment->user->avatar) }}" 
                                                         alt="{{ $comment->user->name }}" 
                                                         class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center font-bold text-xs text-indigo-700 bg-indigo-50">
                                                        {{ mb_strtoupper(mb_substr($comment->user->name, 0, 2, 'UTF-8'), 'UTF-8') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div>
                                                <div class="flex flex-wrap items-center gap-1.5">
                                                    <span class="font-bold text-slate-900 text-xs">
                                                        {{ $comment->user->name }}
                                                    </span>

                                                    @if($comment->user_id === $post->user_id)
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200/60">
                                                            ✍️ Tác giả
                                                        </span>
                                                    @endif

                                                    @if($comment->user->isAdmin())
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200/60">
                                                            👑 Admin
                                                        </span>
                                                    @elseif($comment->user->isLibrarian())
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200/60">
                                                            📚 Thủ thư
                                                        </span>
                                                    @endif

                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-600 border border-slate-200/60">
                                                        {{ $comment->user->badge_icon }} {{ $comment->user->title_badge ?: 'Tân Thủ' }}
                                                    </span>
                                                </div>
                                                <div class="text-[11px] text-slate-400 mt-0.5">
                                                    {{ $comment->created_at->diffForHumans() }}
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Nút xóa bình luận (Chỉ tác giả bình luận hoặc Admin/Librarian) -->
                                        @auth
                                            @if(auth()->id() === $comment->user_id || in_array(auth()->user()->role, ['admin', 'librarian']))
                                                <form action="{{ route('forum.comment.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('Xác nhận xóa bình luận này?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-xs text-slate-400 hover:text-rose-600 hover:bg-rose-50 px-2 py-1 rounded-lg transition cursor-pointer" 
                                                            title="Xóa bình luận">
                                                        &times; Xóa
                                                    </button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>

                                    <!-- Nội dung bình luận -->
                                    <p class="text-xs sm:text-sm text-slate-700 leading-relaxed pl-12 break-words whitespace-pre-line font-normal">
                                        {{ $comment->content }}
                                    </p>

                                </div>
                            @empty
                                <div class="text-center py-8 text-slate-400 text-xs">
                                    Chưa có bình luận nào. Hãy là người đầu tiên mở lời đàm đạo!
                                </div>
                            @endforelse
                        </div>

                    </section>

                </div>

                <!-- ========================================================= -->
                <!-- 2. CỘT PHẢI (SIDEBAR WIDGETS)                             -->
                <!-- ========================================================= -->
                <div class="space-y-6">

                    <!-- Widget: Bài viết cùng chuyên mục -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2 mb-3.5">
                            <span class="w-5 h-5 rounded-md bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs">📌</span>
                            <span>BÀI VIẾT CÙNG CHUYÊN MỤC</span>
                        </h3>

                        <div class="space-y-2.5">
                            @forelse($relatedPosts as $rel)
                                <a href="{{ route('forum.show', $rel->id) }}" 
                                   class="block p-3 rounded-xl bg-slate-50/70 hover:bg-indigo-50/40 border border-slate-100 hover:border-indigo-200 transition group">
                                    <div class="text-xs font-bold text-slate-800 group-hover:text-indigo-600 line-clamp-2 transition leading-snug">
                                        {{ $rel->title }}
                                    </div>
                                    <div class="flex items-center justify-between text-[11px] text-slate-400 mt-2">
                                        <span class="truncate max-w-[120px]">Bởi: {{ $rel->user->name }}</span>
                                        <span class="shrink-0">❤️ {{ $rel->likes_count }} • 💬 {{ $rel->comments_count ?? $rel->comments()->count() }}</span>
                                    </div>
                                </a>
                            @empty
                                <div class="text-xs text-slate-400 py-2">
                                    Không có bài viết liên quan khác.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Widget: Nội Quy & Văn Hóa Đàm Đạo (Nền Light sáng tinh tế) -->
                    <div class="bg-amber-50/50 border border-amber-200/80 rounded-2xl p-5 shadow-sm space-y-3">
                        <h4 class="text-amber-900 font-bold text-sm flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center text-xs">🛡️</span>
                            <span>NỘI QUY HỘI QUÁN</span>
                        </h4>
                        <ul class="text-xs text-amber-900/80 space-y-2.5 leading-relaxed font-normal">
                            <li class="flex items-start gap-2">
                                <span class="text-amber-500 mt-0.5 shrink-0 font-bold">✓</span>
                                <span>Chia sẻ cảm nhận chân thực, văn minh và tôn trọng tác quyền.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-amber-500 mt-0.5 shrink-0 font-bold">✓</span>
                                <span>Tuyệt đối không phát tán liên kết rác, quảng cáo bán hàng bên ngoài.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-amber-500 mt-0.5 shrink-0 font-bold">✓</span>
                                <span>Gắn thẻ sách chính xác để cộng đồng độc giả dễ dàng tra cứu và mượn sách.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-amber-500 mt-0.5 shrink-0 font-bold">✓</span>
                                <span><strong class="font-bold text-amber-900">Không spam:</strong> Diễn đàn thảo luận không tích lũy điểm EXP cày cấp.</span>
                            </li>
                        </ul>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- Alpine.js Logic cho tương tác Thả tim & Sao chép URL -->
    <script>
        function forumPostDetail() {
            return {
                copied: false,

                async toggleLike(postId) {
                    @guest
                        alert('Vui lòng đăng nhập để thả tim cho bài viết này!');
                        window.location.href = '{{ route('login') }}';
                        return;
                    @endguest

                    const likeBtn = document.getElementById('detail-like-btn');
                    const likeCountEl = document.getElementById('detail-like-count');
                    const likeLabelEl = document.getElementById('detail-like-label');

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
                            if (likeCountEl) likeCountEl.textContent = `(${data.likes_count})`;
                            if (likeBtn) {
                                if (data.liked) {
                                    likeBtn.className = 'inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer border bg-rose-50 text-rose-600 border-rose-200 shadow-xs';
                                    if (likeLabelEl) likeLabelEl.textContent = 'ĐÃ THÍCH';
                                } else {
                                    likeBtn.className = 'inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer border bg-white text-slate-600 border-slate-200 hover:bg-rose-50/50 hover:text-rose-600 hover:border-rose-200 shadow-2xs';
                                    if (likeLabelEl) likeLabelEl.textContent = 'THẢ TIM';
                                }
                            }
                        }
                    } catch (err) {
                        console.error('Lỗi thả tim:', err);
                    }
                },

                copyPostUrl() {
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(window.location.href);
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2500);
                    }
                }
            };
        }
    </script>
</x-app-layout>
