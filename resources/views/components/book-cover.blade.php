@props([
    'book' => null,
    'class' => '',
    'imgClass' => '',
    'size' => 'md',
    'isAlpine' => false,
    'alpineVar' => 'quickViewBook',
])

@php
    // Chuẩn hóa dữ liệu sách (hỗ trợ Eloquent Model, Array, stdClass)
    $bookObj = is_array($book) ? (object) $book : $book;
    
    $title = (string) ($bookObj->title ?? 'Tác phẩm');
    $author = (string) ($bookObj->author ?? 'TIỂU ĐỘI 2P');
    $publisher = (string) (!empty($bookObj->publisher) ? $bookObj->publisher : 'NXB Tri Thức');
    $publishYear = (string) (!empty($bookObj->publish_year) ? $bookObj->publish_year : date('Y'));
    $bookIdNum = (int) ($bookObj->id ?? 1);
    
    // Tên thể loại
    $catName = '';
    if (isset($bookObj->category)) {
        $catName = is_object($bookObj->category) ? ($bookObj->category->name ?? '') : (string) $bookObj->category;
    } elseif (isset($bookObj->category_name)) {
        $catName = (string) $bookObj->category_name;
    }
    
    $norm = mb_strtolower($catName, 'UTF-8');
    
    // 1. BẢNG MÀU HỌC THUẬT SANG TRỌNG (LUXURIOUS ACADEMIC COLOR PALETTES)
    $palettes = [
        'navy' => [
            'name' => 'Xanh Navy Sâu',
            'bg' => 'from-slate-950 via-slate-900 to-blue-950',
            'accent' => 'text-blue-400',
            'border' => 'border-blue-500/20',
            'badge' => 'bg-blue-500/20 text-blue-200 border-blue-400/30',
            'foil' => 'from-blue-400/40 via-sky-300 to-blue-400/40',
            'seal' => 'text-blue-400/70',
        ],
        'burgundy' => [
            'name' => 'Đỏ Burgundy',
            'bg' => 'from-stone-950 via-rose-950 to-red-950',
            'accent' => 'text-rose-400',
            'border' => 'border-rose-500/20',
            'badge' => 'bg-rose-500/20 text-rose-200 border-rose-400/30',
            'foil' => 'from-rose-400/40 via-amber-300 to-rose-400/40',
            'seal' => 'text-rose-400/70',
        ],
        'emerald' => [
            'name' => 'Xanh Ngọc Lục Bảo',
            'bg' => 'from-slate-950 via-emerald-950 to-teal-950',
            'accent' => 'text-emerald-400',
            'border' => 'border-emerald-500/20',
            'badge' => 'bg-emerald-500/20 text-emerald-200 border-emerald-400/30',
            'foil' => 'from-emerald-400/40 via-teal-300 to-emerald-400/40',
            'seal' => 'text-emerald-400/70',
        ],
        'slate' => [
            'name' => 'Xám Than Chì',
            'bg' => 'from-zinc-950 via-slate-900 to-neutral-950',
            'accent' => 'text-slate-300',
            'border' => 'border-slate-500/20',
            'badge' => 'bg-slate-700/30 text-slate-200 border-slate-500/30',
            'foil' => 'from-slate-400/40 via-zinc-200 to-slate-400/40',
            'seal' => 'text-slate-400/70',
        ],
        'coffee' => [
            'name' => 'Nâu Cà Phê',
            'bg' => 'from-stone-950 via-amber-950 to-stone-900',
            'accent' => 'text-amber-400',
            'border' => 'border-amber-500/20',
            'badge' => 'bg-amber-600/20 text-amber-200 border-amber-500/30',
            'foil' => 'from-amber-400/40 via-yellow-200 to-amber-400/40',
            'seal' => 'text-amber-400/70',
        ],
        'indigo' => [
            'name' => 'Tím Hoàng Gia',
            'bg' => 'from-slate-950 via-indigo-950 to-purple-950',
            'accent' => 'text-indigo-300',
            'border' => 'border-indigo-500/20',
            'badge' => 'bg-indigo-500/20 text-indigo-200 border-indigo-400/30',
            'foil' => 'from-indigo-400/40 via-purple-300 to-indigo-400/40',
            'seal' => 'text-indigo-400/70',
        ],
        'cyan' => [
            'name' => 'Xanh Lam Cyber',
            'bg' => 'from-slate-950 via-slate-900 to-cyan-950',
            'accent' => 'text-cyan-400',
            'border' => 'border-cyan-500/20',
            'badge' => 'bg-cyan-500/20 text-cyan-200 border-cyan-400/30',
            'foil' => 'from-cyan-400/40 via-sky-300 to-cyan-400/40',
            'seal' => 'text-cyan-400/70',
        ],
    ];

    $paletteKeys = array_keys($palettes);

    // 2. PHÂN LOẠI PATTERN & BẢNG MÀU ƯU TIÊN THEO THỂ LOẠI
    $patternType = 'general';
    $preferredPalette = 'navy';

    if (str_contains($norm, 'lập trình') || str_contains($norm, 'phần mềm') || str_contains($norm, 'cntt') || str_contains($norm, 'công nghệ')) {
        $patternType = 'tech';
        $preferredPalette = 'cyan';
    } elseif (str_contains($norm, 'khoa học') || str_contains($norm, 'vũ trụ') || str_contains($norm, 'vật lý') || str_contains($norm, 'thiên văn')) {
        $patternType = 'science';
        $preferredPalette = 'indigo';
    } elseif (str_contains($norm, 'văn học') || str_contains($norm, 'tiểu thuyết') || str_contains($norm, 'thơ') || str_contains($norm, 'kinh điển')) {
        $patternType = 'literature';
        $preferredPalette = 'burgundy';
    } elseif (str_contains($norm, 'kinh tế') || str_contains($norm, 'tài chính') || str_contains($norm, 'đầu tư') || str_contains($norm, 'kinh doanh')) {
        $patternType = 'economics';
        $preferredPalette = 'navy';
    } elseif (str_contains($norm, 'tâm lý') || str_contains($norm, 'kỹ năng') || str_contains($norm, 'bản thân')) {
        $patternType = 'psychology';
        $preferredPalette = 'emerald';
    } elseif (str_contains($norm, 'triết học') || str_contains($norm, 'tư tưởng') || str_contains($norm, 'nhân sinh')) {
        $patternType = 'philosophy';
        $preferredPalette = 'slate';
    } elseif (str_contains($norm, 'lịch sử') || str_contains($norm, 'quân sự') || str_contains($norm, 'chiến lược')) {
        $patternType = 'history';
        $preferredPalette = 'coffee';
    } elseif (str_contains($norm, 'y học') || str_contains($norm, 'sức khỏe') || str_contains($norm, 'dinh dưỡng')) {
        $patternType = 'medicine';
        $preferredPalette = 'emerald';
    } elseif (str_contains($norm, 'nghệ thuật') || str_contains($norm, 'thiết kế') || str_contains($norm, 'kiến trúc') || str_contains($norm, 'ui/ux')) {
        $patternType = 'art';
        $preferredPalette = 'indigo';
    }

    // 3. TÍNH TOÁN HASH COLOR ĐỘNG (Đa dạng, không trùng lặp ngay cả cùng danh mục)
    $hashVal = abs(crc32($title . $bookIdNum));
    $hashIndex = $hashVal % count($paletteKeys);
    
    // Xen kẽ giữa palette đặc trưng của thể loại và palette băm ngẫu nhiên theo ID
    $paletteKey = ($bookIdNum % 2 === 0) ? $preferredPalette : $paletteKeys[$hashIndex];
    $palette = $palettes[$paletteKey] ?? $palettes['navy'];
    
    $categoryTag = !empty($catName) ? mb_strtoupper($catName, 'UTF-8') : 'HỌC THUẬT';
    $uniqueId = 'cov-' . $bookIdNum . '-' . substr(md5($title . $bookIdNum), 0, 4);
@endphp

@if($isAlpine)
    {{-- ========================================================================= --}}
    {{-- PHIÊN BẢN VECTOR COVER TƯƠNG TÁC CHO MODAL XEM NHANH (ALPINE.JS DYNAMIC)  --}}
    {{-- ========================================================================= --}}
    <div {{ $attributes->merge(['class' => 'relative aspect-[3/4] w-full h-full overflow-hidden rounded-xl border-l-4 border-black/35 shadow-md bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-white select-none transition-all duration-300 ' . $class]) }}
         :class="[
            'from-slate-950 via-slate-900 to-blue-950',
            'from-stone-950 via-rose-950 to-red-950',
            'from-slate-950 via-emerald-950 to-teal-950',
            'from-zinc-950 via-slate-900 to-neutral-950',
            'from-stone-950 via-amber-950 to-stone-900',
            'from-slate-950 via-indigo-950 to-purple-950',
            'from-slate-950 via-slate-900 to-cyan-950'
         ][({{ $alpineVar }} ? ({{ $alpineVar }}.id || 1) : 1) % 7]">

        <!-- 1. Hiệu ứng gáy sách vật lý (Physical Book Spine & Crease) -->
        <div class="absolute inset-y-0 left-0 w-3.5 sm:w-4 bg-gradient-to-r from-black/60 via-black/25 to-transparent pointer-events-none z-20 border-r border-white/5"></div>
        <div class="absolute inset-y-0 left-3.5 sm:left-4 w-[1px] bg-white/15 pointer-events-none z-20"></div>
        <div class="absolute inset-y-0 left-4 sm:left-[17px] w-[1px] bg-black/35 pointer-events-none z-20"></div>

        <!-- 2. Khung viền chỉ vàng/bạc tinh tế (Inner Ornamental Border) -->
        <div class="absolute inset-2 sm:inset-2.5 border border-white/15 rounded-lg pointer-events-none z-10"></div>
        <div class="absolute inset-3 sm:inset-3.5 border border-white/10 rounded-md pointer-events-none z-10"></div>

        <!-- 3. Họa tiết hình học nền SVG chìm tinh tế -->
        <svg class="absolute inset-0 w-full h-full opacity-10 pointer-events-none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="alpine-pattern-{{ $uniqueId }}" width="32" height="32" patternUnits="userSpaceOnUse">
                    <path d="M16 0 L32 16 L16 32 L0 16 Z" fill="none" stroke="currentColor" stroke-width="0.75" />
                    <circle cx="16" cy="16" r="1.5" fill="currentColor" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#alpine-pattern-{{ $uniqueId }})" />
        </svg>

        <!-- 4. Khối nội dung bìa sách chuẩn học thuật -->
        <div class="relative z-10 w-full h-full p-4 sm:p-5 flex flex-col justify-between overflow-hidden">
            
            <!-- ĐẦU BÌA: Huy hiệu thư viện & Mã ấn bản -->
            <div class="pl-2 space-y-1">
                <div class="flex items-center justify-between gap-1 text-[9px] font-mono tracking-widest text-white/50 uppercase">
                    <span class="truncate">TIỂU ĐỘI 2P ARCHIVE</span>
                    <span class="shrink-0" x-text="{{ $alpineVar }} ? {{ $alpineVar }}.code : '#000'"></span>
                </div>
                <div class="pt-0.5">
                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold tracking-wider uppercase border bg-white/10 text-white/90 border-white/20 truncate max-w-[85%]"
                          x-text="{{ $alpineVar }} ? {{ $alpineVar }}.category : 'HỌC THUẬT'">
                    </span>
                </div>
                <div class="w-10 h-0.5 bg-gradient-to-r from-amber-300/60 to-transparent rounded-full opacity-70"></div>
            </div>

            <!-- GIỮA BÌA: Tiêu đề cuốn sách in đậm, chống tràn -->
            <div class="my-auto py-2 pl-2">
                <div class="text-[8px] uppercase tracking-widest text-white/40 font-mono mb-1">
                    OFFICIAL ARCHIVE EDITION
                </div>
                <h4 class="font-serif font-black text-white text-sm sm:text-base leading-snug line-clamp-3 uppercase tracking-wide drop-shadow-md"
                    x-text="{{ $alpineVar }} ? {{ $alpineVar }}.title : 'Đang tải...'">
                </h4>
                <div class="w-8 h-0.5 bg-white/20 rounded-full mt-2"></div>
            </div>

            <!-- CHÂN BÌA: Tác giả, Nhà xuất bản, Năm phát hành & Con dấu mộc thư viện -->
            <div class="pt-2 border-t border-white/15 pl-2 flex items-end justify-between gap-2">
                <div class="min-w-0 flex-1 truncate">
                    <div class="text-[8px] uppercase tracking-wider text-white/50 font-semibold font-mono">
                        TÁC GIẢ
                    </div>
                    <div class="text-xs sm:text-sm font-bold text-white/95 truncate"
                         x-text="{{ $alpineVar }} ? {{ $alpineVar }}.author : 'TIỂU ĐỘI 2P'">
                    </div>
                    <div class="text-[8px] sm:text-[9px] text-white/50 font-mono truncate mt-0.5"
                         x-text="({{ $alpineVar }} ? ({{ $alpineVar }}.publisher || 'NXB Tri Thức') : 'NXB') + ' • ' + ({{ $alpineVar }} ? ({{ $alpineVar }}.publish_year || '2024') : '2024')">
                    </div>
                </div>

                <!-- Dấu Mộc Học Thuật -->
                <div class="shrink-0 opacity-70">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full border border-white/30 flex items-center justify-center p-1 bg-white/5 text-white/80">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                </div>
            </div>

        </div>

    </div>
@else
    {{-- ========================================================================= --}}
    {{-- PHIÊN BẢN BLADE VECTOR BOOK COVER ĐỘC LẬP (100% VECTOR - RENDER TỨC THÌ) --}}
    {{-- ========================================================================= --}}
    <div {{ $attributes->merge(['class' => 'relative aspect-[3/4] w-full h-full overflow-hidden rounded-xl border-l-4 border-black/35 shadow-md hover:shadow-xl transition-all duration-300 bg-gradient-to-br ' . $palette['bg'] . ' text-white select-none ' . $class]) }}>
        
        <!-- 1. Hiệu ứng gáy sách vật lý (Book Spine Shadow & Crease Depth) -->
        <div class="absolute inset-y-0 left-0 w-3.5 sm:w-4 bg-gradient-to-r from-black/60 via-black/25 to-transparent pointer-events-none z-20 border-r border-white/5"></div>
        <div class="absolute inset-y-0 left-3.5 sm:left-4 w-[1px] bg-white/15 pointer-events-none z-20"></div>
        <div class="absolute inset-y-0 left-4 sm:left-[17px] w-[1px] bg-black/35 pointer-events-none z-20"></div>

        <!-- 2. Khung viền chỉ vàng/bạc sang trọng (Double Ornamental Border) -->
        <div class="absolute inset-2 sm:inset-2.5 border border-white/15 rounded-lg pointer-events-none z-10"></div>
        <div class="absolute inset-3 sm:inset-3.5 border border-white/10 rounded-md pointer-events-none z-10"></div>

        <!-- 3. Họa tiết hình học trừu tượng chìm theo từng thể loại (Abstract Geometric Pattern) -->
        @if($patternType === 'tech')
            <!-- Công nghệ / CNTT: Mạch điện & Grid vi mạch -->
            <svg class="absolute inset-0 w-full h-full opacity-15 pointer-events-none {{ $palette['accent'] }}" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="pat-{{ $uniqueId }}" width="36" height="36" patternUnits="userSpaceOnUse">
                        <path d="M0 18h12l6 6h18M18 0v12l6 6v18M36 18h-8l-4-4H0" fill="none" stroke="currentColor" stroke-width="0.75" />
                        <circle cx="12" cy="18" r="2" fill="currentColor" />
                        <circle cx="24" cy="24" r="1.5" fill="currentColor" />
                        <circle cx="24" cy="12" r="1.5" fill="currentColor" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#pat-{{ $uniqueId }})" />
            </svg>
        @elseif($patternType === 'science')
            <!-- Khoa học / Vũ trụ: Quỹ đạo nguyên tử & Vòng elip hành tinh -->
            <svg class="absolute inset-0 w-full h-full opacity-15 pointer-events-none {{ $palette['accent'] }}" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="pat-{{ $uniqueId }}" width="48" height="48" patternUnits="userSpaceOnUse">
                        <ellipse cx="24" cy="24" rx="20" ry="8" fill="none" stroke="currentColor" stroke-width="0.75" transform="rotate(-30 24 24)" />
                        <ellipse cx="24" cy="24" rx="20" ry="8" fill="none" stroke="currentColor" stroke-width="0.75" transform="rotate(30 24 24)" />
                        <circle cx="24" cy="24" r="2.5" fill="currentColor" opacity="0.8" />
                        <circle cx="10" cy="14" r="1.5" fill="currentColor" />
                        <circle cx="38" cy="34" r="1.5" fill="currentColor" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#pat-{{ $uniqueId }})" />
            </svg>
        @elseif($patternType === 'literature')
            <!-- Văn học / Kinh điển: Sóng lượn cổ điển & Vòm sách -->
            <svg class="absolute inset-0 w-full h-full opacity-15 pointer-events-none {{ $palette['accent'] }}" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="pat-{{ $uniqueId }}" width="40" height="24" patternUnits="userSpaceOnUse">
                        <path d="M 0 12 C 10 0, 30 24, 40 12 M 0 24 C 10 12, 30 36, 40 24 M 0 0 C 10 -12, 30 12, 40 0" fill="none" stroke="currentColor" stroke-width="0.75" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#pat-{{ $uniqueId }})" />
            </svg>
        @elseif($patternType === 'economics')
            <!-- Kinh tế / Tài chính: Biểu đồ tăng trưởng & Lưới Isometric -->
            <svg class="absolute inset-0 w-full h-full opacity-15 pointer-events-none {{ $palette['accent'] }}" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="pat-{{ $uniqueId }}" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M0 40 L12 28 L22 32 L34 14 L40 6" fill="none" stroke="currentColor" stroke-width="1" />
                        <path d="M0 20 H40 M0 40 H40 M20 0 V40 M40 0 V40" fill="none" stroke="currentColor" stroke-width="0.5" stroke-dasharray="2 2" />
                        <polygon points="34,14 40,6 38,15" fill="currentColor" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#pat-{{ $uniqueId }})" />
            </svg>
        @elseif($patternType === 'psychology' || $patternType === 'philosophy')
            <!-- Tâm lý / Triết học: Vòng tròn đồng tâm & Mê cung trí tuệ -->
            <svg class="absolute inset-0 w-full h-full opacity-15 pointer-events-none {{ $palette['accent'] }}" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="pat-{{ $uniqueId }}" width="40" height="40" patternUnits="userSpaceOnUse">
                        <circle cx="20" cy="20" r="16" fill="none" stroke="currentColor" stroke-width="0.75" />
                        <circle cx="20" cy="20" r="10" fill="none" stroke="currentColor" stroke-width="0.75" />
                        <circle cx="20" cy="20" r="4" fill="none" stroke="currentColor" stroke-width="0.75" />
                        <path d="M0 20h40M20 0v40" fill="none" stroke="currentColor" stroke-width="0.5" stroke-dasharray="1 3" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#pat-{{ $uniqueId }})" />
            </svg>
        @elseif($patternType === 'history')
            <!-- Lịch sử: Đường đồng mức địa hình & La bàn cổ điển -->
            <svg class="absolute inset-0 w-full h-full opacity-15 pointer-events-none {{ $palette['accent'] }}" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="pat-{{ $uniqueId }}" width="44" height="44" patternUnits="userSpaceOnUse">
                        <path d="M0 22 Q11 5 22 22 T44 22 M0 11 Q11 -6 22 11 T44 11 M0 33 Q11 16 22 33 T44 33" fill="none" stroke="currentColor" stroke-width="0.75" />
                        <polygon points="22,14 24,20 30,22 24,24 22,30 20,24 14,22 20,20" fill="currentColor" opacity="0.5" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#pat-{{ $uniqueId }})" />
            </svg>
        @elseif($patternType === 'medicine')
            <!-- Y học: Cấu trúc phân tử & Lục giác mạng lưới -->
            <svg class="absolute inset-0 w-full h-full opacity-15 pointer-events-none {{ $palette['accent'] }}" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="pat-{{ $uniqueId }}" width="32" height="32" patternUnits="userSpaceOnUse">
                        <path d="M 0 8 L 8 0 L 24 0 L 32 8 L 32 24 L 24 32 L 8 32 L 0 24 Z" fill="none" stroke="currentColor" stroke-width="0.75" />
                        <circle cx="16" cy="16" r="2.5" fill="currentColor" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#pat-{{ $uniqueId }})" />
            </svg>
        @else
            <!-- Mặc định / Học thuật: Lưới hình thoi Guilloche cổ điển -->
            <svg class="absolute inset-0 w-full h-full opacity-12 pointer-events-none {{ $palette['accent'] }}" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="pat-{{ $uniqueId }}" width="28" height="28" patternUnits="userSpaceOnUse">
                        <path d="M14 0 L28 14 L14 28 L0 14 Z" fill="none" stroke="currentColor" stroke-width="0.75" />
                        <circle cx="14" cy="14" r="1.5" fill="currentColor" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#pat-{{ $uniqueId }})" />
            </svg>
        @endif

        <!-- 4. KHỐI BỐ CỤC CHỮ IN TRÊN BÌA (TYPOGRAPHY & METADATA LAYOUT) -->
        <div class="relative z-10 w-full h-full p-4 sm:p-5 flex flex-col justify-between overflow-hidden">
            
            <!-- ĐẦU BÌA: Tag thể loại + Dấu ấn lưu trữ TIỂU ĐỘI 2P ARCHIVE -->
            <div class="pl-2 space-y-1">
                <div class="flex items-center justify-between gap-1 text-[9px] font-mono tracking-widest text-white/50 uppercase">
                    <span class="truncate">TIỂU ĐỘI 2P ARCHIVE</span>
                    <span class="shrink-0">#{{ str_pad($bookIdNum, 3, '0', STR_PAD_LEFT) }}</span>
                </div>
                
                <div class="pt-0.5">
                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold tracking-wider uppercase border {{ $palette['badge'] }} truncate max-w-[85%] shadow-xs">
                        {{ $categoryTag }}
                    </span>
                </div>

                <!-- Dải viền mạ vàng / bạc -->
                <div class="w-12 h-0.5 bg-gradient-to-r {{ $palette['foil'] }} rounded-full mt-1.5 opacity-80"></div>
            </div>

            <!-- GIỮA BÌA: Tiêu đề cuốn sách in hoa đậm nét, tương phản hoàn hảo -->
            <div class="my-auto py-2 pl-2 text-left">
                <div class="text-[8px] uppercase tracking-widest text-white/40 font-mono mb-1">
                    OFFICIAL ARCHIVE EDITION
                </div>
                <h4 class="font-serif font-black text-white text-sm sm:text-base leading-snug line-clamp-3 uppercase tracking-wide drop-shadow-md">
                    {{ $title }}
                </h4>
                <div class="w-8 h-0.5 bg-white/20 rounded-full mt-2"></div>
            </div>

            <!-- CHÂN BÌA: Tác giả, Nhà xuất bản, Năm phát hành & Con dấu mộc thư viện -->
            <div class="pt-2 border-t border-white/15 pl-2 flex items-end justify-between gap-2">
                <div class="min-w-0 flex-1 truncate">
                    <div class="text-[8px] uppercase tracking-wider text-white/50 font-semibold font-mono">
                        TÁC GIẢ
                    </div>
                    <div class="text-xs sm:text-sm font-bold text-white/95 truncate">
                        {{ $author }}
                    </div>
                    <div class="text-[8px] sm:text-[9px] text-white/50 font-mono truncate mt-0.5">
                        {{ $publisher }} • {{ $publishYear }}
                    </div>
                </div>
                
                <!-- Dấu Mộc Học Thuật "TIỂU ĐỘI 2P" -->
                <div class="shrink-0 opacity-70 group-hover:opacity-100 transition-opacity">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full border border-white/30 flex items-center justify-center p-1 bg-white/5 {{ $palette['seal'] }}">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endif
