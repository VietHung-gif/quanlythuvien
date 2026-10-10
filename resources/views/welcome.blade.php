<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TIỂU ĐỘI 2P - Hệ Thống Quản Lý Thư Viện Số Hiện Đại</title>

    <!-- Google Fonts: Be Vietnam Pro (Chuẩn hiển thị tiếng Việt 100%, không lỗi dấu, cân đối) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500&display=swap" rel="stylesheet">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Be Vietnam Pro"', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                    colors: {
                        canvas: '#f8fafc',
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        },
                        accent: {
                            500: '#0ea5e9',
                            600: '#0284c7',
                        }
                    },
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Be Vietnam Pro', system-ui, -apple-system, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow-x: hidden;
        }

        /* Nền canvas dịu mắt chuẩn công thái học với hiệu ứng radial mềm */
        .deep-dark-bg,
        .light-canvas-bg {
            background-color: #f8fafc;
            background-image: 
                radial-gradient(circle at 15% 10%, rgba(99, 102, 241, 0.05) 0px, transparent 40%),
                radial-gradient(circle at 85% 25%, rgba(14, 165, 233, 0.04) 0px, transparent 45%),
                radial-gradient(circle at 50% 75%, rgba(99, 102, 241, 0.03) 0px, transparent 50%);
            background-attachment: fixed;
        }

        /* Card tiêu chuẩn Modern Minimalist */
        .dark-card {
            background-color: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.25s ease-in-out;
        }

        .dark-card:hover {
            border-color: rgba(99, 102, 241, 0.35);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.07), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
            transform: translateY(-3px);
        }

        /* 3D Flip Book Logo Animation - Modern Royal Indigo & Sky */
        .cyber-book {
            position: relative;
            width: 26px;
            height: 18px;
            perspective: 120px;
            transform-style: preserve-3d;
        }
        .cyber-book-cover-left {
            position: absolute;
            left: 0;
            top: 0;
            width: 13px;
            height: 18px;
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.2), rgba(255, 255, 255, 0.95));
            border: 1.5px solid #4f46e5;
            border-right: none;
            border-radius: 3px 1px 1px 3px;
            box-shadow: -2px 2px 6px rgba(79, 70, 229, 0.25);
        }
        .cyber-book-cover-left::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 2px;
            right: 2px;
            bottom: 3px;
            background: repeating-linear-gradient(0deg, transparent, transparent 2.5px, rgba(79, 70, 229, 0.35) 2.5px, rgba(79, 70, 229, 0.35) 3.5px);
        }
        .cyber-book-cover-right {
            position: absolute;
            left: 13px;
            top: 0;
            width: 13px;
            height: 18px;
            background: linear-gradient(225deg, rgba(79, 70, 229, 0.2), rgba(255, 255, 255, 0.95));
            border: 1.5px solid #4f46e5;
            border-left: none;
            border-radius: 1px 3px 3px 1px;
            box-shadow: 2px 2px 6px rgba(79, 70, 229, 0.25);
        }
        .cyber-book-cover-right::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 2px;
            right: 2px;
            bottom: 3px;
            background: repeating-linear-gradient(0deg, transparent, transparent 2.5px, rgba(79, 70, 229, 0.35) 2.5px, rgba(79, 70, 229, 0.35) 3.5px);
        }
        .cyber-book-spine {
            position: absolute;
            left: 12px;
            top: -1px;
            width: 2px;
            height: 20px;
            background: #4f46e5;
            border-radius: 1px;
            box-shadow: 0 0 6px rgba(79, 70, 229, 0.5);
            z-index: 20;
        }
        .cyber-flip-page {
            position: absolute;
            left: 13px;
            top: 0;
            width: 12.5px;
            height: 17.5px;
            background: linear-gradient(90deg, #ffffff, rgba(99, 102, 241, 0.25));
            border: 1px solid rgba(79, 70, 229, 0.7);
            border-left: none;
            border-radius: 1px 2px 2px 1px;
            transform-origin: left center;
            box-shadow: 0 0 4px rgba(79, 70, 229, 0.3);
            backface-visibility: visible;
            animation: cyberBookPageFlip 2.4s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }
        .cyber-flip-page::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 1.5px;
            right: 1.5px;
            bottom: 2px;
            background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(79, 70, 229, 0.4) 2px, rgba(79, 70, 229, 0.4) 3px);
        }
        .cyber-flip-page.page-1 { animation-delay: 0s; z-index: 5; }
        .cyber-flip-page.page-2 { animation-delay: 0.8s; z-index: 4; }
        .cyber-flip-page.page-3 { animation-delay: 1.6s; z-index: 3; }
        @keyframes cyberBookPageFlip {
            0% { transform: rotateY(0deg); opacity: 0; }
            12% { opacity: 1; }
            75% { opacity: 1; }
            85% { transform: rotateY(-180deg); opacity: 0.85; }
            100% { transform: rotateY(-180deg); opacity: 0; }
        }

        /* Viền mỏng mượt mà không gây chói */
        .subtle-border {
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        /* Tùy chỉnh thanh cuộn sáng */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Starboy Spikes & Twinkle Animations */
        @keyframes starTwinkle {
            0%, 100% { opacity: 0.35; transform: scale(0.85); }
            50% { opacity: 1; transform: scale(1.25); }
        }
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-6px); }
        }
        .anim-star-twinkle { animation: starTwinkle 2.8s ease-in-out infinite; }
        .anim-star-twinkle-delay { animation: starTwinkle 3.2s ease-in-out infinite 1.4s; }
        .anim-float-slow { animation: floatSlow 4s ease-in-out infinite; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col antialiased selection:bg-blue-500/20 selection:text-blue-700">

@php
    $featuredBooks = $featuredBooks ?? \App\Models\Book::with('category')->latest()->take(8)->get();
    $categories = $categories ?? \App\Models\Category::withCount('books')->get();
    $totalBooks = $totalBooks ?? \App\Models\Book::count();
    $totalBorrowRecords = $totalBorrowRecords ?? \App\Models\BorrowRecord::count();
    $totalUsers = $totalUsers ?? \App\Models\User::count();
    $announcements = $announcements ?? \App\Models\ForumPost::with('user')
        ->whereHas('category', function ($q) {
            $q->where('slug', 'thong-bao-thu-vien');
        })
        ->latest()
        ->take(3)
        ->get();
@endphp

    <!-- ========================================================================= -->
    <!-- 1. HEADER / NAVBAR ĐỊNH DANH "TIỂU ĐỘI 2P" (DÙNG CHUNG TOÀN HỆ THỐNG)    -->
    <!-- ========================================================================= -->
    @include('layouts.navigation')

    <!-- ========================================================================= -->
    <!-- 2. HERO BANNER: GRAFFITI / STARBOY SHARP SPIKES & SPARKLE STARS           -->
    <!-- ========================================================================= -->
    <section id="hero" class="relative pt-6 pb-12 sm:pt-8 sm:pb-16 bg-slate-50 overflow-hidden">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- THẺ BANNER NỀN DARK-ACCENT CAO CẤP VỚI HIỆU ỨNG ÁNH SÁNG VÀ VIỀN TỐI -->
            <div class="relative rounded-3xl sm:rounded-[2.5rem] overflow-hidden bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 border-2 border-slate-800/90 shadow-2xl p-6 sm:p-10 lg:p-14 text-center">
                
                <!-- Hiệu ứng quầng sáng nền (Ambient Glow Backdrops) -->
                <div class="absolute -top-32 -left-32 w-80 sm:w-96 h-80 sm:h-96 bg-indigo-600/25 rounded-full blur-[90px] sm:blur-[110px] pointer-events-none"></div>
                <div class="absolute -bottom-32 -right-32 w-80 sm:w-96 h-80 sm:h-96 bg-amber-500/20 rounded-full blur-[90px] sm:blur-[110px] pointer-events-none"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl h-64 bg-violet-600/15 rounded-full blur-[80px] pointer-events-none"></div>

                <!-- Lưới hạt sao li ti (Micro Star Grid) -->
                <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#818cf8_1px,transparent_1px)] [background-size:24px_24px] pointer-events-none"></div>

                <!-- Badge Tiêu Đề Phụ Đầu Banner -->
                <div class="relative z-10 inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-900/90 border border-slate-700/80 text-xs font-bold text-slate-300 mb-4 sm:mb-6 backdrop-blur-md shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span class="text-emerald-400 font-mono tracking-widest uppercase">NÂNG TẦM TRI THỨC</span>
                </div>

                <!-- ========================================================================= -->
                <!-- ĐIỂM NHẤN TRUNG TÂM: TYPOGRAPHY CHỮ LỚN "TIỂU ĐỘI 2P" PHONG CÁCH STARBOY / SPIKES -->
                <!-- ========================================================================= -->
                <div class="relative z-10 max-w-4xl mx-auto my-1 sm:my-2">
                    <svg class="w-full h-auto max-h-[140px] sm:max-h-[190px] md:max-h-[220px] mx-auto filter drop-shadow-[0_0_35px_rgba(99,102,241,0.5)] select-none" 
                         viewBox="0 0 960 210" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <!-- Metallic Silver / Chrome Gradient -->
                            <linearGradient id="chromeHeroFill" x1="0%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stop-color="#FFFFFF"/>
                                <stop offset="25%" stop-color="#F1F5F9"/>
                                <stop offset="50%" stop-color="#CBD5E1"/>
                                <stop offset="75%" stop-color="#94A3B8"/>
                                <stop offset="100%" stop-color="#E2E8F0"/>
                            </linearGradient>

                            <!-- Neon Stroke Outline Gradient -->
                            <linearGradient id="strokeHeroGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#818CF8"/>
                                <stop offset="50%" stop-color="#C084FC"/>
                                <stop offset="100%" stop-color="#FBBF24"/>
                            </linearGradient>

                            <!-- Gold Star Gradient -->
                            <linearGradient id="starHeroGold" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#FFFBEB"/>
                                <stop offset="40%" stop-color="#FDE68A"/>
                                <stop offset="100%" stop-color="#F59E0B"/>
                            </linearGradient>

                            <!-- Glow filter for sparkles -->
                            <filter id="glowHeroSparkle" x="-50%" y="-50%" width="200%" height="200%">
                                <feGaussianBlur in="SourceGraphic" stdDeviation="2.5" result="blur"/>
                                <feMerge>
                                    <feMergeNode in="blur"/>
                                    <feMergeNode in="SourceGraphic"/>
                                </feMerge>
                            </filter>
                        </defs>

                        <!-- LỚP 1: VIỀN KÉP NGOÀI (DOUBLE OUTLINE DEEP SHADOW EFFECT) -->
                        <text x="480" y="145" text-anchor="middle"
                              font-family="'Be Vietnam Pro', system-ui, sans-serif"
                              font-weight="900" font-style="italic" font-size="116" letter-spacing="-0.04em"
                              stroke="#0f172a" stroke-width="18" stroke-linejoin="miter" stroke-miterlimit="4"
                              fill="#0f172a">
                            TIỂU ĐỘI 2P
                        </text>

                        <!-- LỚP 2: ĐƯỜNG VIỀN NEON KÉP (NEON DUAL OUTLINE) -->
                        <text x="480" y="145" text-anchor="middle"
                              font-family="'Be Vietnam Pro', system-ui, sans-serif"
                              font-weight="900" font-style="italic" font-size="116" letter-spacing="-0.04em"
                              stroke="url(#strokeHeroGrad)" stroke-width="8" stroke-linejoin="miter" stroke-miterlimit="4"
                              fill="none">
                            TIỂU ĐỘI 2P
                        </text>

                        <!-- LỚP 3: CHROME METALLIC CHÍNH DIỆN (CHROME FILL) -->
                        <text x="480" y="145" text-anchor="middle"
                              font-family="'Be Vietnam Pro', system-ui, sans-serif"
                              font-weight="900" font-style="italic" font-size="116" letter-spacing="-0.04em"
                              fill="url(#chromeHeroFill)">
                            TIỂU ĐỘI 2P
                        </text>

                        <!-- LỚP 4: HỌA TIẾT NÉT VUỐT GAI NHỌN (SHARP SPIKES ORNAMENTS) -->
                        <!-- Spikes trên chữ T -->
                        <polygon points="120,40 135,70 110,65" fill="#FFFFFF" stroke="#818CF8" stroke-width="1.5"/>
                        <polygon points="85,62 105,75 80,82" fill="#CBD5E1" stroke="#818CF8" stroke-width="1.5"/>

                        <!-- Spikes trên chữ Đ -->
                        <polygon points="360,50 375,30 380,55" fill="#FFFFFF" stroke="#818CF8" stroke-width="1.5"/>
                        <polygon points="415,85 435,78 420,95" fill="#F1F5F9" stroke="#818CF8" stroke-width="1.5"/>

                        <!-- Spikes trên số 2 -->
                        <polygon points="690,45 710,25 715,50" fill="#FFFFFF" stroke="#818CF8" stroke-width="1.5"/>
                        <polygon points="650,150 635,175 660,165" fill="#CBD5E1" stroke="#818CF8" stroke-width="1.5"/>
                        <polygon points="765,150 785,140 765,160" fill="#818CF8" stroke="#FBBF24" stroke-width="1.5"/>

                        <!-- Spikes trên chữ P -->
                        <polygon points="850,45 875,35 860,65" fill="#FFFFFF" stroke="#FBBF24" stroke-width="1.5"/>
                        <polygon points="785,165 770,195 800,175" fill="#C084FC" stroke="#818CF8" stroke-width="1.5"/>
                        <polygon points="885,105 910,95 890,120" fill="#FDE68A" stroke="#F59E0B" stroke-width="1.5"/>

                        <!-- LỚP 5: CÁC NGÔI SAO 4 CÁNH LẤP LÁNH (SPARKLES TỎA SÁNG XUNG QUANH) -->
                        <!-- Ngôi sao 1: Góc trên bên phải chữ '2P' (Ngôi sao chủ đạo lớn) -->
                        <g transform="translate(870, 25)" filter="url(#glowHeroSparkle)" class="anim-star-twinkle">
                            <path d="M 0 -22 C 0 -6 6 0 22 0 C 6 0 0 6 0 22 C 0 6 -6 0 -22 0 C -6 0 0 -6 0 -22 Z" fill="url(#starHeroGold)"/>
                            <circle cx="0" cy="0" r="3" fill="#FFFFFF"/>
                        </g>

                        <!-- Ngôi sao 2: Góc trên bên trái chữ 'TIỂU' -->
                        <g transform="translate(90, 45)" filter="url(#glowHeroSparkle)" class="anim-star-twinkle-delay">
                            <path d="M 0 -16 C 0 -4.5 4.5 0 16 0 C 4.5 0 0 4.5 0 16 C 0 4.5 -4.5 0 -16 0 C -4.5 0 0 -4.5 0 -16 Z" fill="url(#starHeroGold)"/>
                            <circle cx="0" cy="0" r="2" fill="#FFFFFF"/>
                        </g>

                        <!-- Ngôi sao 3: Ở giữa ĐỘI & 2P -->
                        <g transform="translate(635, 60)" filter="url(#glowHeroSparkle)" class="anim-star-twinkle">
                            <path d="M 0 -14 C 0 -4 4 0 14 0 C 4 0 0 4 0 14 C 0 4 -4 0 -14 0 C -4 0 0 -4 0 -14 Z" fill="#FBBF24"/>
                            <circle cx="0" cy="0" r="2" fill="#FFFFFF"/>
                        </g>

                        <!-- Ngôi sao 4: Phía dưới chữ P -->
                        <g transform="translate(895, 175)" filter="url(#glowHeroSparkle)" class="anim-star-twinkle-delay">
                            <path d="M 0 -11 C 0 -3 3 0 11 0 C 3 0 0 3 0 11 C 0 3 -3 0 -11 0 C -3 0 0 -3 0 -11 Z" fill="url(#starHeroGold)"/>
                            <circle cx="0" cy="0" r="1.5" fill="#FFFFFF"/>
                        </g>

                        <!-- Ngôi sao 5: Phía dưới chữ T -->
                        <g transform="translate(160, 165)" filter="url(#glowHeroSparkle)" class="anim-star-twinkle">
                            <path d="M 0 -9 C 0 -2.5 2.5 0 9 0 C 2.5 0 0 2.5 0 9 C 0 2.5 -2.5 0 -9 0 C -2.5 0 0 -2.5 0 -9 Z" fill="#FDE68A"/>
                        </g>
                    </svg>
                </div>

                <!-- ========================================================================= -->
                <!-- KHẨU HIỆU (SLOGAN): BỨC PHÁ TRI THỨC - DẪN LỐI TƯƠNG LAI                  -->
                <!-- ========================================================================= -->
                <div class="relative z-10 mt-3 sm:mt-4 mb-2">
                    <div class="inline-flex items-center gap-2 sm:gap-3 font-black text-xs sm:text-sm md:text-base tracking-[0.2em] sm:tracking-[0.3em] uppercase">
                        <span class="text-amber-400 text-sm sm:text-lg anim-star-twinkle">✦</span>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-300 via-amber-400 to-amber-200 drop-shadow-[0_0_12px_rgba(251,191,36,0.4)]">
                            BỨC PHÁ TRI THỨC — DẪN LỐI TƯƠNG LAI
                        </span>
                        <span class="text-amber-400 text-sm sm:text-lg anim-star-twinkle-delay">✦</span>
                    </div>
                    <p class="mt-2 text-slate-300 text-xs sm:text-sm md:text-base max-w-2xl mx-auto font-normal leading-relaxed">
                        Hệ thống thư viện số hóa thế hệ mới. Khám phá kho sách chuyên ngành tinh hoa, mượn trả minh bạch và trải nghiệm đặc quyền Scholar VIP miễn cọc 0đ.
                    </p>
                </div>

                <!-- ========================================================================= -->
                <!-- CÁC NÚT KÊU GỌI HÀNH ĐỘNG (CTA BUTTONS)                                   -->
                <!-- ========================================================================= -->
                <div class="relative z-10 mt-6 sm:mt-8 flex flex-col sm:flex-row items-center justify-center gap-3.5 sm:gap-5">
                    <!-- NÚT 1: [KHÁM PHÁ KHO SÁCH] (Nền đen/indigo bo góc lớn, đổ bóng) -->
                    <a href="{{ route('books.index') }}" 
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-3.5 sm:py-4 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-black text-xs sm:text-sm uppercase tracking-wider shadow-xl shadow-indigo-600/35 hover:shadow-indigo-500/50 hover:scale-[1.03] active:scale-95 transition-all duration-200 border border-indigo-400/40 cursor-pointer">
                        <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span>KHÁM PHÁ KHO SÁCH</span>
                        <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>

                    <!-- NÚT 2: [ĐĂNG KÝ HỘI VIÊN VIP] (Viền mảnh, chữ đậm kèm icon vương miện) -->
                    <a href="{{ route('membership.index') }}" 
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-3.5 sm:py-4 rounded-2xl bg-slate-900/90 hover:bg-amber-950/40 text-amber-300 hover:text-amber-200 font-black text-xs sm:text-sm uppercase tracking-wider backdrop-blur-md border-2 border-amber-400/70 hover:border-amber-400 shadow-lg shadow-amber-500/10 hover:shadow-amber-500/25 hover:scale-[1.03] active:scale-95 transition-all duration-200 cursor-pointer">
                        <span class="text-lg">👑</span>
                        <span>ĐĂNG KÝ HỘI VIÊN VIP</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-black bg-amber-400 text-slate-950 uppercase">0đ CỌC</span>
                    </a>
                </div>

                <!-- ========================================================================= -->
                <!-- Ô TÌM KIẾM SÁCH TẬP TRUNG NGAY TRONG BANNER                               -->
                <!-- ========================================================================= -->
                <div class="relative z-10 mt-8 sm:mt-10 max-w-2xl mx-auto">
                    <form action="{{ route('books.index') }}" method="GET" class="relative flex items-center bg-slate-900/90 rounded-2xl border-2 border-slate-700 focus-within:border-indigo-400 focus-within:ring-4 focus-within:ring-indigo-500/20 shadow-xl transition-all duration-200 p-1.5 sm:p-2 backdrop-blur-md">
                        <div class="pl-3 sm:pl-4 text-slate-400 pointer-events-none shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               placeholder="Nhập tên sách, tác giả hoặc chuyên ngành cần tìm..." 
                               class="w-full pl-3 pr-4 py-2.5 sm:py-3 bg-transparent border-0 text-white placeholder:text-slate-500 text-sm sm:text-base font-medium focus:outline-none focus:ring-0">
                        <button type="submit" 
                                class="px-5 sm:px-7 py-2.5 sm:py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs sm:text-sm tracking-wide uppercase transition active:scale-[0.98] shadow-sm shrink-0">
                            Tìm kiếm
                        </button>
                    </form>

                    <!-- Gợi Ý Từ Khóa Dạng Chip Bo Góc Nổi Bật -->
                    <div class="mt-4 flex flex-wrap items-center justify-center gap-2 text-xs">
                        <span class="font-bold text-slate-400">Gợi ý tìm kiếm:</span>
                        <a href="{{ route('books.index', ['search' => 'Clean Code']) }}" class="px-2.5 py-1 rounded-lg bg-slate-800/80 hover:bg-slate-700 border border-slate-700 text-slate-300 font-medium transition">
                            Clean Code
                        </a>
                        <a href="{{ route('books.index', ['search' => 'Trí tuệ nhân tạo']) }}" class="px-2.5 py-1 rounded-lg bg-slate-800/80 hover:bg-slate-700 border border-slate-700 text-slate-300 font-medium transition">
                            Trí tuệ nhân tạo
                        </a>
                        <a href="{{ route('books.index', ['search' => 'Kinh tế']) }}" class="px-2.5 py-1 rounded-lg bg-slate-800/80 hover:bg-slate-700 border border-slate-700 text-slate-300 font-medium transition">
                            Kinh tế
                        </a>
                        <a href="{{ route('books.index', ['search' => 'Thiết kế']) }}" class="px-2.5 py-1 rounded-lg bg-slate-800/80 hover:bg-slate-700 border border-slate-700 text-slate-300 font-medium transition">
                            Thiết kế
                        </a>
                        <a href="{{ route('books.index', ['search' => 'Y học']) }}" class="px-2.5 py-1 rounded-lg bg-slate-800/80 hover:bg-slate-700 border border-slate-700 text-slate-300 font-medium transition">
                            Y học
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 3. KHỐI 4 THỐNG KÊ SỐ LIỆU: CHỮ SỐ ĐẬM RÕ (TEXT-SLATE-950)                 -->
    <!-- ========================================================================= -->
    <section class="py-10 border-y border-slate-200 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 text-center">
                
                <!-- Thống kê 1: Đầu Sách -->
                <div class="bg-white border border-slate-200 rounded-xl p-5 sm:p-6 shadow-sm">
                    <div class="text-4xl md:text-5xl font-black text-slate-950 tracking-tight">
                        {{ number_format(max($totalBooks, 1874), 0, ',', '.') }}+
                    </div>
                    <div class="text-sm font-semibold text-slate-600 mt-1">
                        Đầu Sách Trong Kho
                    </div>
                </div>

                <!-- Thống kê 2: Lượt Mượn -->
                <div class="bg-white border border-slate-200 rounded-xl p-5 sm:p-6 shadow-sm">
                    <div class="text-4xl md:text-5xl font-black text-slate-950 tracking-tight">
                        {{ number_format(max($totalBorrowRecords, 250), 0, ',', '.') }}+
                    </div>
                    <div class="text-sm font-semibold text-slate-600 mt-1">
                        Lượt Độc Giả Mượn Sách
                    </div>
                </div>

                <!-- Thống kê 3: Độc Giả -->
                <div class="bg-white border border-slate-200 rounded-xl p-5 sm:p-6 shadow-sm">
                    <div class="text-4xl md:text-5xl font-black text-slate-950 tracking-tight">
                        {{ number_format(max($totalUsers, 120), 0, ',', '.') }}+
                    </div>
                    <div class="text-sm font-semibold text-slate-600 mt-1">
                        Độc Giả Hoạt Động
                    </div>
                </div>

                <!-- Thống kê 4: Phê Duyệt Trực Tuyến -->
                <div class="bg-white border border-slate-200 rounded-xl p-5 sm:p-6 shadow-sm">
                    <div class="text-4xl md:text-5xl font-black text-slate-950 tracking-tight">
                        100%
                    </div>
                    <div class="text-sm font-semibold text-slate-600 mt-1">
                        Phê Duyệt Trực Tuyến
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 3.5 BẢNG TIN & THÔNG BÁO THƯ VIỆN (TỪ HỘI QUÁN TRI THỨC)                  -->
    <!-- ========================================================================= -->
    <section id="announcements-section" class="py-12 relative bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-8 border-b border-slate-200 gap-3">
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full bg-blue-600"></span>
                    <div>
                        <!-- Section H2: text-2xl md:text-3xl tracking-tight text-slate-900 font-extrabold -->
                        <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                            <span>Bảng Tin & Thông Báo Mới</span>
                        </h2>
                    </div>
                </div>

                <a href="{{ route('forum.index', ['category' => 'thong-bao-thu-vien']) }}" 
                   class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:text-blue-700 transition duration-150 group">
                    <span>Xem tất cả bài viết</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($announcements as $post)
                    <div class="rounded-xl p-6 bg-slate-50 border border-slate-200 hover:border-slate-300 hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-blue-100 text-blue-800">
                                    THÔNG BÁO
                                </span>
                                <!-- Meta Info: text-slate-500 font-medium text-xs md:text-sm -->
                                <span class="text-xs md:text-sm font-medium text-slate-500">
                                    {{ $post->created_at->format('d/m/Y') }}
                                </span>
                            </div>

                            <!-- Card H3: text-lg md:text-xl line-clamp-1 hover:text-blue-600 transition text-slate-900 font-bold -->
                            <h3 class="text-lg md:text-xl font-bold text-slate-900 group-hover:text-blue-600 transition-colors line-clamp-2 leading-snug">
                                <a href="{{ route('forum.show', $post->id) }}">
                                    {{ $post->title }}
                                </a>
                            </h3>

                            <!-- Body Text / Snippet: text-slate-700 text-sm md:text-base leading-relaxed -->
                            <p class="text-sm md:text-base text-slate-700 line-clamp-3 leading-relaxed">
                                {{ $post->snippet }}
                            </p>
                        </div>

                        <div class="mt-5 pt-4 border-t border-slate-200 flex items-center justify-between text-xs md:text-sm">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full overflow-hidden bg-slate-200 border border-slate-300 flex items-center justify-center text-xs text-slate-700 font-bold">
                                    @if($post->user->avatar)
                                        <img src="{{ asset('storage/' . $post->user->avatar) }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($post->user->name, 0, 1)) }}
                                    @endif
                                </div>
                                <span class="text-slate-700 font-medium text-xs truncate max-w-[130px]">
                                    {{ $post->user->name }}
                                </span>
                            </div>

                            <a href="{{ route('forum.show', $post->id) }}" 
                               class="text-blue-600 hover:text-blue-700 text-xs md:text-sm font-semibold inline-flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                                Chi tiết &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 p-8 rounded-xl bg-slate-50 border border-slate-200 text-center space-y-2">
                        <span class="text-3xl">📢</span>
                        <div class="font-bold text-base text-slate-900">CHƯA CÓ THÔNG BÁO MỚI</div>
                        <p class="text-sm text-slate-600">Ban quản trị thư viện sẽ sớm cập nhật các thông báo hoạt động tại đây.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 4. KHO SÁCH NỔI BẬT & ẢNH BÌA THẬT CHẤT LƯỢNG CAO                        -->
    <!-- ========================================================================= -->
    <section id="books-section" class="py-16 sm:py-20 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Tiêu Đề Section Chuẩn Hóa -->
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 pb-4 border-b border-slate-200/80">
                <div>
                    <div class="inline-flex items-center gap-2 text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                        DANH MỤC TUYỂN CHỌN
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
                        Kho Sách Nổi Bật & Mới Cập Nhật
                    </h2>
                    <p class="mt-1.5 text-sm text-slate-500">
                        Tuyển tập kiệt tác văn học kinh điển, tâm lý phát triển, khoa học vũ trụ, lịch sử triết học, kinh tế khởi nghiệp và công nghệ AI.
                    </p>
                </div>

                <div class="mt-4 md:mt-0">
                    <a href="{{ route('books.index') }}" 
                       class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition">
                        <span>Xem toàn bộ {{ $totalBooks }} cuốn sách</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Lưới Sách 4 Cột Chuẩn Tỉ Lệ Bìa Sách, Độ Cao Đồng Nhất -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 items-stretch">
                @forelse($featuredBooks as $book)
                    <x-book-card :book="$book" />
                @empty
                    <div class="col-span-4 py-12 text-center text-slate-400">
                        Chưa có dữ liệu sách trong hệ thống. Vui lòng chạy seeder để khởi tạo!
                    </div>
                @endforelse
            </div>

            <!-- Banner Điều Hướng Vào Trang Tra Cứu Đầy Đủ -->
            <div class="mt-12 text-center">
                <a href="{{ route('books.index') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition shadow-xs">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span>Truy cập danh mục đầy đủ và bộ lọc nâng cao</span>
                </a>
            </div>

        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 5. CHUYÊN NGÀNH & THỂ LOẠI CHỌN LỌC                                       -->
    <!-- ========================================================================= -->
    <section id="categories-section" class="py-16 bg-slate-50/70 border-t border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-2xl mx-auto mb-12">
                <div class="inline-flex items-center gap-2 text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                    CHỦ ĐỀ NGHIÊN CỨU
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
                    Khám Phá Các Chuyên Ngành Khoa Học
                </h2>
                <p class="mt-2 text-sm text-slate-500">
                    Phân loại chuẩn mực giúp bạn dễ dàng tiếp cận nguồn tri thức đúng theo nhu cầu học tập và nghiên cứu.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @forelse($categories as $category)
                    <a href="{{ route('books.index', ['category_id' => $category->id]) }}" 
                       class="dark-card p-5 rounded-xl block group">
                        
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 rounded-lg bg-indigo-50 border border-indigo-200/80 flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            </div>
                            <span class="text-xs font-mono font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                {{ $category->books_count ?? $category->books()->count() }} đầu sách
                            </span>
                        </div>

                        <h3 class="text-base font-semibold text-slate-900 group-hover:text-indigo-600 transition-colors line-clamp-1">
                            {{ $category->name }}
                        </h3>

                        <p class="mt-1.5 text-xs text-slate-500 line-clamp-2 leading-relaxed">
                            {{ $category->description ?? 'Tuyển tập tài liệu và sách chuyên khảo chất lượng cao phục vụ nghiên cứu.' }}
                        </p>
                    </a>
                @empty
                    <div class="col-span-4 text-center py-8 text-slate-400">
                        Chưa có thể loại nào được khởi tạo.
                    </div>
                @endforelse
            </div>

        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 6. QUY TRÌNH MƯỢN SÁCH 4 BƯỚC DỄ DÀNG                                     -->
    <!-- ========================================================================= -->
    <section id="workflow-section" class="py-16 sm:py-20 border-t border-slate-200/80 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-2xl mx-auto mb-14">
                <div class="inline-flex items-center gap-2 text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                    TIỆN ÍCH DÀNH CHO ĐỘC GIẢ
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
                    Quy Trình Mượn Sách 4 Bước Chuẩn
                </h2>
                <p class="mt-2 text-sm text-slate-500">
                    Trực tuyến hóa 100% quy trình từ khâu tra cứu, gửi yêu cầu đến khi cầm trên tay cuốn sách yêu thích.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 relative">
                
                <!-- Bước 1 -->
                <div class="dark-card p-6 rounded-xl relative">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-600 font-bold text-sm flex items-center justify-center mb-4">
                        01
                    </div>
                    <h3 class="text-base font-semibold text-slate-900">
                        Đăng Ký & Đăng Nhập
                    </h3>
                    <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                        Tạo tài khoản độc giả hoàn toàn miễn phí chỉ trong 30 giây để được cấp quyền mượn sách.
                    </p>
                </div>

                <!-- Bước 2 -->
                <div class="dark-card p-6 rounded-xl relative">
                    <div class="w-8 h-8 rounded-lg bg-sky-50 border border-sky-200 text-sky-600 font-bold text-sm flex items-center justify-center mb-4">
                        02
                    </div>
                    <h3 class="text-base font-semibold text-slate-900">
                        Tìm Sách & Gửi Yêu Cầu
                    </h3>
                    <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                        Tra cứu theo tên sách hoặc chuyên ngành, bấm "Yêu cầu mượn" với 1 cú click chuột đơn giản.
                    </p>
                </div>

                <!-- Bước 3 -->
                <div class="dark-card p-6 rounded-xl relative">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 border border-amber-200 text-amber-600 font-bold text-sm flex items-center justify-center mb-4">
                        03
                    </div>
                    <h3 class="text-base font-semibold text-slate-900">
                        Thủ Thư Phê Duyệt
                    </h3>
                    <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                        Thủ thư kiểm tra tình trạng sách thực tế và duyệt phiếu mượn tự động trên hệ thống số.
                    </p>
                </div>

                <!-- Bước 4 -->
                <div class="dark-card p-6 rounded-xl relative">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-600 font-bold text-sm flex items-center justify-center mb-4">
                        04
                    </div>
                    <h3 class="text-base font-semibold text-slate-900">
                        Nhận Sách & Đọc
                    </h3>
                    <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                        Đến quầy nhận sách hoặc đọc tại chỗ. Hệ thống tự động nhắc nhở ngày hẹn trả chu đáo.
                    </p>
                </div>

            </div>

        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 7. ƯU ĐIỂM NỀN TẢNG TIỂU ĐỘI 2P                                            -->
    <!-- ========================================================================= -->
    <section id="features-section" class="py-16 bg-slate-50/70 border-t border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-2xl mx-auto mb-12">
                <div class="inline-flex items-center gap-2 text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                    TÍNH NĂNG VƯỢT TRỘI
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
                    Tại Sao Chọn Nền Tảng TIỂU ĐỘI 2P?
                </h2>
                <p class="mt-2 text-sm text-slate-500">
                    Ứng dụng công nghệ quản trị dữ liệu hiện đại mang lại trải nghiệm tối ưu nhất cho cả độc giả và quản lý thư viện.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="dark-card p-6 rounded-xl">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-200 flex items-center justify-center text-indigo-600 mb-5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">
                        Tốc Độ Tra Cứu Tức Thì
                    </h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Tìm kiếm tức thì theo tiêu đề, tác giả, danh mục với thuật toán tối ưu chỉ dưới 0.1 giây.
                    </p>
                </div>

                <div class="dark-card p-6 rounded-xl">
                    <div class="w-12 h-12 rounded-xl bg-sky-50 border border-sky-200 flex items-center justify-center text-sky-600 mb-5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">
                        Minh Bạch & Phân Quyền Chặt Chẽ
                    </h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Tách biệt rành mạch quyền hạn giữa Admin quản trị, Thủ thư xét duyệt và Độc giả mượn sách.
                    </p>
                </div>

                <div class="dark-card p-6 rounded-xl">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 mb-5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">
                        Lưu Vết Thời Gian Thực
                    </h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Theo dõi lịch sử mượn, hẹn trả, cảnh báo trễ hạn và tính toán phí phạt tự động chuẩn xác.
                    </p>
                </div>

            </div>

        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 8. FOOTER VINH DANH THƯƠNG HIỆU & ĐỘI NGŨ PHÁT TRIỂN                      -->
    <!-- ========================================================================= -->
    @include('layouts.footer')



    <!-- Widget Chatbot AI (Trợ lý Thủ thư ảo) -->
    <x-chatbot-widget />

    <!-- Ngăn kéo Giỏ sách & Đăng ký mượn sách -->
    <x-cart-drawer />

    <!-- Cổng Nạp Tiền VietQR Napas 247 (Fintech Recharge Modal) -->
    <x-deposit-modal />
</body>
</html>
