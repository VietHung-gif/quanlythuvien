<x-app-layout>
    <div class="bg-slate-50 min-h-screen py-8" x-data="{ viewMode: 'grid' }">
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
            <!-- 1. TIÊU ĐỀ & KHỐI BANNER ĐẦU TRANG (HEADER SECTION)                       -->
            <!-- ========================================================================= -->
            <div class="bg-white border-2 border-slate-200 rounded-3xl p-6 sm:p-8 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <!-- Cột bên trái: Huy hiệu, Tiêu đề, Mô tả -->
                <div class="space-y-1.5 flex-1 min-w-0">
                    <span class="bg-slate-900 text-white font-black text-xs uppercase px-3.5 py-1.5 rounded-lg tracking-wider inline-block mb-2 shadow-xs">
                        🏷️ PHÂN HỆ PHÂN LOẠI & LƯU TRỮ — TIỂU ĐỘI 2P
                    </span>
                    
                    <h1 class="text-3xl md:text-4xl font-black text-slate-950 uppercase tracking-tight leading-tight">
                        KHÁM PHÁ THỂ LOẠI SÁCH
                    </h1>
                    
                    <p class="text-slate-600 font-medium text-sm md:text-base leading-relaxed mt-1 max-w-2xl">
                        Phân loại và định hướng hàng nghìn ấn bản tri thức theo từng chuyên ngành nghiên cứu, văn học và sáng tạo nghệ thuật.
                    </p>
                </div>

                <!-- Cột bên phải: Nút "+ THÊM THỂ LOẠI MỚI" (Dành cho Admin/Librarian) -->
                @if(Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isLibrarian()))
                    <div class="shrink-0">
                        <a href="{{ route('categories.create') }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider px-5 py-3 rounded-2xl shadow-sm transition flex items-center gap-2 cursor-pointer active:scale-95 whitespace-nowrap">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>+ THÊM THỂ LOẠI MỚI</span>
                        </a>
                    </div>
                @endif
            </div>

            <!-- ========================================================================= -->
            <!-- THANH TÌM KIẾM & BỘ CÔNG CỤ (SEARCH & VIEW SWITCHER)                      -->
            <!-- ========================================================================= -->
            <div class="bg-white border-2 border-slate-200 rounded-2xl p-4 shadow-sm flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                
                <!-- Ô tìm kiếm bo tròn hiện đại -->
                <form method="GET" action="{{ route('categories.index') }}" class="flex items-center gap-2.5 flex-1 max-w-xl">
                    <div class="relative flex-1">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Tìm kiếm thể loại hoặc nội dung mô tả..."
                               class="bg-white border-2 border-slate-200 text-slate-900 font-semibold placeholder:text-slate-400 rounded-2xl px-5 py-2.5 pl-11 text-sm w-full shadow-xs focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition">
                        <svg class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    
                    <button type="submit" 
                            class="bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs uppercase tracking-wider px-4 py-2.5 rounded-xl shadow-xs transition cursor-pointer shrink-0">
                        TÌM KIẾM
                    </button>

                    @if(request('search'))
                        <a href="{{ route('categories.index') }}" 
                           class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase px-3.5 py-2.5 rounded-xl border border-slate-200 transition shrink-0" 
                           title="Xóa bộ lọc tìm kiếm">
                            ✕ HỦY
                        </a>
                    @endif
                </form>

                <!-- Nút chuyển đổi linh hoạt: [⊞ LƯỚI THẺ KHÁM PHÁ] & [☰ DANH SÁCH TỐI GIẢN] -->
                <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider hidden md:inline">
                        CHẾ ĐỘ XEM:
                    </span>
                    <div class="inline-flex p-1 bg-slate-100 border border-slate-200 rounded-xl select-none">
                        <button type="button" 
                                @click="viewMode = 'grid'"
                                :class="viewMode === 'grid' ? 'bg-white text-slate-950 shadow-xs font-black' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition cursor-pointer uppercase tracking-wider">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            <span>LƯỚI THẺ</span>
                        </button>
                        <button type="button" 
                                @click="viewMode = 'list'"
                                :class="viewMode === 'list' ? 'bg-white text-slate-950 shadow-xs font-black' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition cursor-pointer uppercase tracking-wider">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <span>DANH SÁCH</span>
                        </button>
                    </div>
                </div>

            </div>

            <!-- ========================================================================= -->
            <!-- PALETTE SYSTEM & LOGIC PHỐI MÀU / ICON THEO CHỦ ĐỀ                       -->
            <!-- ========================================================================= -->
            @php
                $getCategoryTheme = function($cat) {
                    $name = mb_strtolower($cat->name, 'UTF-8');
                    
                    if (str_contains($name, 'manga') || str_contains($name, 'light novel') || str_contains($name, 'truyện')) {
                        return [
                            'icon' => '🌸',
                            'badge' => 'Manga & Light Novel',
                            'bg_glow' => 'from-pink-500/20 via-rose-500/10 to-transparent',
                            'icon_bg' => 'bg-pink-50 text-pink-600 ring-1 ring-pink-200',
                            'accent_bar' => 'bg-pink-500',
                        ];
                    }
                    if (str_contains($name, 'lập trình') || str_contains($name, 'kỹ thuật phần mềm') || str_contains($name, 'cntt') || str_contains($name, 'software')) {
                        return [
                            'icon' => '💻',
                            'badge' => 'Kỹ Thuật Phần Mềm',
                            'bg_glow' => 'from-sky-500/20 via-blue-500/10 to-transparent',
                            'icon_bg' => 'bg-sky-50 text-sky-600 ring-1 ring-sky-200',
                            'accent_bar' => 'bg-sky-500',
                        ];
                    }
                    if (str_contains($name, 'trí tuệ nhân tạo') || str_contains($name, 'ai') || str_contains($name, 'dữ liệu')) {
                        return [
                            'icon' => '🤖',
                            'badge' => 'AI & Data Science',
                            'bg_glow' => 'from-indigo-500/20 via-purple-500/10 to-transparent',
                            'icon_bg' => 'bg-indigo-50 text-indigo-600 ring-1 ring-indigo-200',
                            'accent_bar' => 'bg-indigo-500',
                        ];
                    }
                    if (str_contains($name, 'an toàn') || str_contains($name, 'hacker') || str_contains($name, 'bảo mật')) {
                        return [
                            'icon' => '🛡️',
                            'badge' => 'An Ninh Mạng & Hacker',
                            'bg_glow' => 'from-cyan-500/20 via-teal-500/10 to-transparent',
                            'icon_bg' => 'bg-cyan-50 text-cyan-600 ring-1 ring-cyan-200',
                            'accent_bar' => 'bg-cyan-500',
                        ];
                    }
                    if (str_contains($name, 'cyberpunk') || str_contains($name, 'sci-fi') || str_contains($name, 'viễn tưởng')) {
                        return [
                            'icon' => '⚡',
                            'badge' => 'Cyberpunk & Sci-Fi',
                            'bg_glow' => 'from-fuchsia-500/20 via-purple-500/10 to-transparent',
                            'icon_bg' => 'bg-fuchsia-50 text-fuchsia-600 ring-1 ring-fuchsia-200',
                            'accent_bar' => 'bg-fuchsia-500',
                        ];
                    }
                    if (str_contains($name, 'tâm lý') || str_contains($name, 'bản thân')) {
                        return [
                            'icon' => '💡',
                            'badge' => 'Tâm Lý & Phát Triển',
                            'bg_glow' => 'from-amber-500/20 via-orange-500/10 to-transparent',
                            'icon_bg' => 'bg-amber-50 text-amber-600 ring-1 ring-amber-200',
                            'accent_bar' => 'bg-amber-500',
                        ];
                    }
                    if (str_contains($name, 'kinh tế') || str_contains($name, 'tài chính') || str_contains($name, 'đầu tư')) {
                        return [
                            'icon' => '📈',
                            'badge' => 'Kinh Tế & Đầu Tư',
                            'bg_glow' => 'from-emerald-500/20 via-teal-500/10 to-transparent',
                            'icon_bg' => 'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200',
                            'accent_bar' => 'bg-emerald-500',
                        ];
                    }
                    if (str_contains($name, 'vũ trụ') || str_contains($name, 'lượng tử') || str_contains($name, 'vật lý')) {
                        return [
                            'icon' => '🚀',
                            'badge' => 'Vũ Trụ & Lượng Tử',
                            'bg_glow' => 'from-blue-500/20 via-indigo-500/10 to-transparent',
                            'icon_bg' => 'bg-blue-50 text-blue-600 ring-1 ring-blue-200',
                            'accent_bar' => 'bg-blue-600',
                        ];
                    }
                    if (str_contains($name, 'y học') || str_contains($name, 'sức khỏe') || str_contains($name, 'dinh dưỡng')) {
                        return [
                            'icon' => '🩺',
                            'badge' => 'Y Học & Dinh Dưỡng',
                            'bg_glow' => 'from-teal-500/20 via-emerald-500/10 to-transparent',
                            'icon_bg' => 'bg-teal-50 text-teal-600 ring-1 ring-teal-200',
                            'accent_bar' => 'bg-teal-500',
                        ];
                    }
                    if (str_contains($name, 'não bộ') || str_contains($name, 'thần kinh')) {
                        return [
                            'icon' => '🧠',
                            'badge' => 'Khoa Học Não Bộ',
                            'bg_glow' => 'from-purple-500/20 via-violet-500/10 to-transparent',
                            'icon_bg' => 'bg-purple-50 text-purple-600 ring-1 ring-purple-200',
                            'accent_bar' => 'bg-purple-500',
                        ];
                    }
                    if (str_contains($name, 'môi trường') || str_contains($name, 'trái đất')) {
                        return [
                            'icon' => '🌍',
                            'badge' => 'Khoa Học Trái Đất',
                            'bg_glow' => 'from-green-500/20 via-emerald-500/10 to-transparent',
                            'icon_bg' => 'bg-green-50 text-green-600 ring-1 ring-green-200',
                            'accent_bar' => 'bg-green-500',
                        ];
                    }
                    if (str_contains($name, 'lịch sử') || str_contains($name, 'quân sự') || str_contains($name, 'chiến lược')) {
                        return [
                            'icon' => '⚔️',
                            'badge' => 'Lịch Sử & Chiến Lược',
                            'bg_glow' => 'from-rose-500/20 via-red-500/10 to-transparent',
                            'icon_bg' => 'bg-rose-50 text-rose-600 ring-1 ring-rose-200',
                            'accent_bar' => 'bg-rose-500',
                        ];
                    }
                    if (str_contains($name, 'triết học') || str_contains($name, 'nhân sinh')) {
                        return [
                            'icon' => '⚖️',
                            'badge' => 'Triết Học Nhân Sinh',
                            'bg_glow' => 'from-amber-600/20 via-stone-500/10 to-transparent',
                            'icon_bg' => 'bg-stone-100 text-stone-700 ring-1 ring-stone-200',
                            'accent_bar' => 'bg-stone-600',
                        ];
                    }
                    if (str_contains($name, 'nghệ thuật') || str_contains($name, 'thiết kế') || str_contains($name, 'kiến trúc') || str_contains($name, 'ui/ux')) {
                        return [
                            'icon' => '🎨',
                            'badge' => 'Mỹ Thuật & Thiết Kế',
                            'bg_glow' => 'from-violet-500/20 via-fuchsia-500/10 to-transparent',
                            'icon_bg' => 'bg-violet-50 text-violet-600 ring-1 ring-violet-200',
                            'accent_bar' => 'bg-violet-500',
                        ];
                    }
                    if (str_contains($name, 'văn học') || str_contains($name, 'tiểu thuyết')) {
                        return [
                            'icon' => '📚',
                            'badge' => 'Văn Học Kinh Điển',
                            'bg_glow' => 'from-amber-500/20 via-yellow-500/10 to-transparent',
                            'icon_bg' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
                            'accent_bar' => 'bg-amber-600',
                        ];
                    }

                    // Fallback theo ID cho các thể loại mới thêm
                    $fallbacks = [
                        ['icon' => '📖', 'badge' => 'Tri Thức Khái Quát', 'bg_glow' => 'from-blue-500/20 via-indigo-500/10 to-transparent', 'icon_bg' => 'bg-blue-50 text-blue-600 ring-1 ring-blue-200', 'accent_bar' => 'bg-blue-500'],
                        ['icon' => '✨', 'badge' => 'Chuyên Ngành Đặc Tuyển', 'bg_glow' => 'from-purple-500/20 via-pink-500/10 to-transparent', 'icon_bg' => 'bg-purple-50 text-purple-600 ring-1 ring-purple-200', 'accent_bar' => 'bg-purple-500'],
                        ['icon' => '🎯', 'badge' => 'Lĩnh Vực Trọng Điểm', 'bg_glow' => 'from-emerald-500/20 via-teal-500/10 to-transparent', 'icon_bg' => 'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200', 'accent_bar' => 'bg-emerald-500'],
                    ];
                    return $fallbacks[$cat->id % count($fallbacks)];
                };
            @endphp

            <!-- ========================================================================= -->
            <!-- 2. LƯỚI THẺ THỂ LOẠI KIỂU WEB TRUYỆN TRANH (CATEGORY CARDS GRID)         -->
            <!-- ========================================================================= -->
            <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mt-6">
                @forelse($categories as $category)
                    @php $theme = $getCategoryTheme($category); @endphp

                    <article class="group relative bg-white border-2 border-slate-200/90 hover:border-slate-400 rounded-3xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1.5 flex flex-col justify-between overflow-hidden cursor-pointer">
                        
                        <!-- Dải màu gradient mờ nhẹ (Glow Effect) ở góc -->
                        <div class="absolute -top-12 -right-12 w-36 h-36 bg-gradient-to-bl {{ $theme['bg_glow'] }} rounded-full blur-2xl pointer-events-none group-hover:scale-125 transition-transform duration-500"></div>
                        
                        <!-- Dải accent bar mảnh tinh tế ở mép trên thẻ -->
                        <div class="absolute top-0 left-0 right-0 h-1.5 {{ $theme['accent_bar'] }} opacity-80 group-hover:opacity-100 transition-opacity duration-300"></div>

                        <!-- Link toàn thẻ (Click anywhere to explore books) -->
                        <a href="{{ route('books.index', ['category_id' => $category->id]) }}" 
                           class="absolute inset-0 z-0" 
                           aria-label="Khám phá {{ $category->name }}"></a>

                        <!-- Hàng trên: Icon minh họa + Huy hiệu & Công cụ Admin -->
                        <div class="relative z-10 flex items-start justify-between gap-3">
                            <!-- Icon minh họa lớn dạng vector/emoji trong khối bo góc mềm mại -->
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition duration-300 shrink-0 shadow-2xs {{ $theme['icon_bg'] }}">
                                <span>{{ $theme['icon'] }}</span>
                            </div>

                            <!-- Công cụ Sửa / Xóa cho Admin hoặc Badge chuyên đề -->
                            <div class="flex items-center gap-1.5">
                                @if(Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isLibrarian()))
                                    <div class="flex items-center gap-1 bg-white/95 backdrop-blur-xs p-1 rounded-xl border border-slate-200 shadow-2xs" 
                                         onclick="event.stopPropagation()">
                                        <a href="{{ route('categories.edit', $category) }}" 
                                           class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition" 
                                           title="Chỉnh sửa thể loại">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        @if(Auth::user()->isAdmin())
                                            <form action="{{ route('categories.destroy', $category) }}" 
                                                  method="POST" 
                                                  class="inline" 
                                                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa thể loại &quot;{{ $category->name }}&quot; không?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" 
                                                        title="Xóa thể loại (Chỉ Admin)">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-[10px] font-mono font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200">
                                        #{{ str_pad($category->id, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Phần thông tin thể loại: Tên in hoa đậm nét + Mô tả dễ đọc -->
                        <div class="relative z-10 flex-1 my-3">
                            <h2 class="font-black text-slate-950 text-lg group-hover:text-blue-600 transition line-clamp-1 mt-3">
                                {{ $category->name }}
                            </h2>

                            <p class="text-xs md:text-sm text-slate-600 leading-relaxed line-clamp-2 mt-2 font-normal">
                                {{ $category->description ?: 'Tập hợp các ấn phẩm tri thức học thuật, tài liệu nghiên cứu và tác phẩm tiêu biểu.' }}
                            </p>
                        </div>

                        <!-- Thống kê & Huy hiệu số lượng sách (Badge hình viên thuốc) -->
                        <div class="relative z-10 pt-3 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto">
                            <!-- Huy hiệu số lượng đầu sách -->
                            <span class="bg-blue-50 text-blue-700 border border-blue-200 font-extrabold text-xs px-3 py-1 rounded-full inline-flex items-center gap-1.5 shadow-2xs">
                                <span>📚</span>
                                <span>{{ number_format($category->books_count) }} đầu sách</span>
                            </span>

                            <!-- Nhãn Khám phá -->
                            <span class="text-xs font-bold text-slate-400 group-hover:text-blue-600 group-hover:translate-x-1 transition-all flex items-center gap-1 uppercase tracking-wider">
                                <span>KHÁM PHÁ</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </span>
                        </div>

                    </article>
                @empty
                    <!-- Trạng thái trống khi không có kết quả -->
                    <div class="col-span-full bg-white rounded-3xl border-2 border-slate-200 p-12 text-center text-slate-500 space-y-3 shadow-sm">
                        <span class="text-4xl">📭</span>
                        <h3 class="text-lg font-black text-slate-900 tracking-tight">
                            KHÔNG TÌM THẤY THỂ LOẠI SÁCH PHÙ HỢP
                        </h3>
                        <p class="text-sm text-slate-600 max-w-md mx-auto">
                            Không có thể loại nào khớp với từ khóa tìm kiếm của bạn. Hãy thử từ khóa khác hoặc xóa bộ lọc để hiển thị tất cả.
                        </p>
                        <div class="pt-2">
                            <a href="{{ route('categories.index') }}" 
                               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl font-bold text-xs uppercase tracking-wider text-white bg-slate-900 hover:bg-slate-800 shadow-sm transition">
                                XEM TẤT CẢ THỂ LOẠI
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- ========================================================================= -->
            <!-- 3. CHẾ ĐỘ XEM DANH SÁCH TỐI GIẢN (COMPACT LIST VIEW)                      -->
            <!-- ========================================================================= -->
            <div x-show="viewMode === 'list'" style="display: none;" class="bg-white border-2 border-slate-200 rounded-3xl overflow-hidden shadow-sm mt-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                        <thead class="bg-slate-100/90 text-slate-700 uppercase text-xs font-bold tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4 w-16 text-center">STT</th>
                                <th class="px-6 py-4">THỂ LOẠI & CHỦ ĐỀ</th>
                                <th class="px-6 py-4">NỘI DUNG MÔ TẢ</th>
                                <th class="px-6 py-4 text-center">SỐ ĐẦU SÁCH</th>
                                <th class="px-6 py-4 text-center">HÀNH ĐỘNG</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($categories as $index => $category)
                                @php $theme = $getCategoryTheme($category); @endphp
                                <tr class="hover:bg-slate-50/80 transition-colors group">
                                    <td class="px-6 py-4 text-center font-mono font-bold text-xs text-slate-400">
                                        #{{ str_pad(($categories->firstItem() ?? 1) + $index, 2, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0 {{ $theme['icon_bg'] }}">
                                                {{ $theme['icon'] }}
                                            </div>
                                            <div>
                                                <a href="{{ route('books.index', ['category_id' => $category->id]) }}" 
                                                   class="font-black text-slate-950 text-sm group-hover:text-blue-600 transition block hover:underline">
                                                    {{ $category->name }}
                                                </a>
                                                <span class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">
                                                    {{ $theme['badge'] }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 text-xs max-w-md">
                                        <p class="line-clamp-2 leading-relaxed">
                                            {{ $category->description ?: '—' }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold {{ $category->books_count > 0 ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-400 border border-slate-200' }}">
                                            📚 {{ number_format($category->books_count) }} cuốn
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('books.index', ['category_id' => $category->id]) }}" 
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs uppercase tracking-wider border border-blue-200 transition">
                                                <span>KHO SÁCH</span>
                                                <span>→</span>
                                            </a>

                                            @if(Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isLibrarian()))
                                                <a href="{{ route('categories.edit', $category) }}" 
                                                   class="p-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-blue-600 border border-slate-200 text-xs transition" 
                                                   title="Chỉnh sửa">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>

                                                @if(Auth::user()->isAdmin())
                                                    <form action="{{ route('categories.destroy', $category) }}" 
                                                          method="POST" 
                                                          class="inline" 
                                                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa thể loại &quot;{{ $category->name }}&quot;?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="p-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-xs transition cursor-pointer" 
                                                                title="Xóa thể loại">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                        Không tìm thấy thể loại nào phù hợp
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- 4. PHÂN TRANG (PAGINATION)                                                -->
            <!-- ========================================================================= -->
            @if ($categories->hasPages())
                <div class="p-4 bg-white rounded-2xl border-2 border-slate-200 shadow-xs mt-6">
                    {{ $categories->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
