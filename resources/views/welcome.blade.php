<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TIỂU ĐỘI 2P - Hệ Thống Quản Lý Thư Viện Số Hiện Đại</title>

    <!-- Google Fonts: Montserrat (Heavy/Black), Inter & Be Vietnam Pro -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,700;0,800;0,900;1,700;1,800;1,900&family=Inter:wght@400;500;600;700;800&family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

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
                        montserrat: ['"Montserrat"', 'system-ui', '-apple-system', 'sans-serif'],
                        inter: ['"Inter"', 'system-ui', '-apple-system', 'sans-serif'],
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

        .font-montserrat {
            font-family: 'Montserrat', system-ui, -apple-system, sans-serif !important;
        }
        .font-inter {
            font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
        }

        /* 2P Cosmic Glow & Float Animation */
        @keyframes cosmicLogoPulse {
            0%, 100% {
                transform: translateY(0px) scale(1);
                box-shadow: 0 0 25px rgba(37, 99, 235, 0.4), 0 0 50px rgba(6, 182, 212, 0.25);
            }
            50% {
                transform: translateY(-8px) scale(1.02);
                box-shadow: 0 0 45px rgba(37, 99, 235, 0.65), 0 0 80px rgba(6, 182, 212, 0.45);
            }
        }
        .anim-logo-cosmic {
            animation: cosmicLogoPulse 4.2s ease-in-out infinite;
        }

        /* Dải màu Galactic Cyber: Xanh Hoàng Gia (#2563EB) chuyển dần sang Xanh Ngọc/Cyan sáng (#06B6D4) */
        @keyframes galacticCyberFlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .hero-brand-galactic {
            font-family: 'Montserrat', system-ui, -apple-system, sans-serif !important;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0;
            background: linear-gradient(
                115deg,
                #2563eb 0%,
                #3b82f6 25%,
                #0ea5e9 50%,
                #06b6d4 75%,
                #22d3ee 100%
            );
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: galacticCyberFlow 5s ease-in-out infinite;
            filter: drop-shadow(0 0 20px rgba(37, 99, 235, 0.45)) drop-shadow(0 0 40px rgba(6, 182, 212, 0.35));
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1.45;
            padding-top: 0.2em;
            padding-bottom: 0.2em;
            padding-left: 0.15em;
            padding-right: 0.15em;
            overflow: visible;
            text-align: center;
            transition: filter 0.3s ease, transform 0.3s ease;
        }

        .hero-brand-galactic:hover {
            filter: drop-shadow(0 0 30px rgba(37, 99, 235, 0.7)) drop-shadow(0 0 55px rgba(6, 182, 212, 0.6));
            transform: scale(1.02);
        }
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
            <div class="relative rounded-3xl sm:rounded-[2.5rem] overflow-hidden bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 border-2 border-slate-800/90 shadow-2xl p-5 sm:p-7 lg:p-9 text-center">
                
                <!-- Hiệu ứng quầng sáng nền (Ambient Glow Backdrops) -->
                <div class="absolute -top-32 -left-32 w-80 sm:w-96 h-80 sm:h-96 bg-indigo-600/25 rounded-full blur-[90px] sm:blur-[110px] pointer-events-none"></div>
                <div class="absolute -bottom-32 -right-32 w-80 sm:w-96 h-80 sm:h-96 bg-amber-500/20 rounded-full blur-[90px] sm:blur-[110px] pointer-events-none"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl h-64 bg-violet-600/15 rounded-full blur-[80px] pointer-events-none"></div>

                <!-- Lưới hạt sao li ti (Micro Star Grid) -->
                <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#818cf8_1px,transparent_1px)] [background-size:24px_24px] pointer-events-none"></div>

                <!-- Badge Tiêu Đề Phụ Đầu Banner -->
                <div class="relative z-10 inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-slate-900/90 border border-slate-700/80 text-[11px] font-bold text-slate-300 mb-2 sm:mb-3 backdrop-blur-md shadow-xs">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                    <span class="text-emerald-400 font-mono tracking-widest uppercase">NÂNG TẦM TRI THỨC</span>
                </div>

                <!-- ========================================================================= -->
                <!-- ĐIỂM NHẤN TRUNG TÂM: TYPOGRAPHY CHỮ LỚN "TIỂU ĐỘI 2P" PHONG CÁCH STARBOY / SPIKES -->
                <!-- ========================================================================= -->
                <div class="relative z-10 max-w-4xl mx-auto flex flex-col items-center justify-center text-center">
                    
                    <!-- Khung Logo Chrome 3D & Bàn Tay Kim Cương (Cân chỉnh kích cỡ tỉ lệ vàng gọn gàng w-28 h-28 sm:w-32 sm:h-32) -->
                    <div class="relative group mb-2">
                        <!-- Quầng sáng Ambient Glow phía sau Logo (Galactic Cyber: Royal Blue & Cyan) -->
                        <div class="absolute -inset-1.5 bg-gradient-to-r from-blue-600/30 via-cyan-400/40 to-blue-500/30 rounded-2xl blur-xl opacity-80 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>

                        <!-- Khung viền tinh tế bo góc quanh Logo 2P nền đen huyền bí -->
                        <div class="relative w-28 h-28 sm:w-32 sm:h-32 rounded-2xl bg-black border-2 border-slate-700/80 p-2 flex items-center justify-center anim-logo-cosmic overflow-hidden ring-1 ring-white/20 transform group-hover:scale-108 transition-all duration-500">
                            <img src="{{ asset('images/logo-2p.png') }}" 
                                 alt="Logo Tiểu Đội 2P" 
                                 class="w-full h-full object-contain rounded-xl select-none transform group-hover:scale-110 transition-transform duration-700">
                        </div>
                    </div>

                    <!-- Tiêu Đề TIỂU ĐỘI 2P (Montserrat Black / Căn giữa hoàn hảo, đầy đủ dấu chữ) -->
                    <div class="w-full flex items-center justify-center text-center my-1 sm:my-1.5 overflow-visible">
                        <h1 class="hero-brand-galactic text-2xl sm:text-3xl md:text-4xl font-black uppercase select-none text-center">
                            <span>TIỂU ĐỘI</span><span class="ml-2.5 sm:ml-3 inline-block">2P</span>
                        </h1>
                    </div>
                </div>

                <!-- ========================================================================= -->
                <!-- KHẨU HIỆU (SLOGAN): BỨC PHÁ TRI THỨC - DẪN LỐI TƯƠNG LAI                  -->
                <!-- ========================================================================= -->
                <div class="relative z-10 mt-1 sm:mt-1.5 mb-1 w-full text-center">
                    <div class="font-inter inline-flex items-center justify-center gap-2 font-bold text-xs sm:text-sm tracking-[0.2em] uppercase pl-[0.2em]">
                        <svg class="w-3.5 h-3.5 text-cyan-400 anim-star-twinkle shrink-0" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 0L14.59 9.41L24 12L14.59 14.59L12 24L9.41 14.59L0 12L9.41 9.41L12 0Z"/>
                        </svg>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-slate-100 via-white to-cyan-200 drop-shadow-[0_0_15px_rgba(6,182,212,0.4)]">
                            BỨC PHÁ TRI THỨC — DẪN LỐI TƯƠNG LAI
                        </span>
                        <svg class="w-3.5 h-3.5 text-cyan-400 anim-star-twinkle-delay shrink-0" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 0L14.59 9.41L24 12L14.59 14.59L12 24L9.41 14.59L0 12L9.41 9.41L12 0Z"/>
                        </svg>
                    </div>
                    <p class="mt-1 text-slate-300 text-xs sm:text-sm max-w-xl mx-auto font-normal leading-relaxed">
                        Hệ thống thư viện số hóa thế hệ mới. Khám phá kho sách chuyên ngành tinh hoa, mượn trả minh bạch và trải nghiệm đặc quyền Scholar VIP miễn cọc 0đ.
                    </p>
                </div>

                <!-- ========================================================================= -->
                <!-- CÁC NÚT KÊU GỌI HÀNH ĐỘNG (CTA BUTTONS)                                   -->
                <!-- ========================================================================= -->
                <div class="relative z-10 mt-3.5 sm:mt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <!-- NÚT 1: [KHÁM PHÁ KHO SÁCH] (Nền đen/indigo bo góc lớn, đổ bóng) -->
                    <a href="{{ route('books.index') }}" 
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-6 py-2.5 sm:py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-black text-xs sm:text-sm uppercase tracking-wider shadow-lg shadow-indigo-600/35 hover:shadow-indigo-500/50 hover:scale-[1.03] active:scale-95 transition-all duration-200 border border-indigo-400/40 cursor-pointer">
                        <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span>KHÁM PHÁ KHO SÁCH</span>
                        <svg class="w-3.5 h-3.5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>

                    <!-- NÚT 2: [ĐĂNG KÝ HỘI VIÊN VIP] (Viền mảnh, chữ đậm kèm icon vương miện) -->
                    <a href="{{ route('membership.index') }}" 
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 sm:py-3 rounded-xl bg-slate-900/90 hover:bg-amber-950/40 text-amber-300 hover:text-amber-200 font-black text-xs sm:text-sm uppercase tracking-wider backdrop-blur-md border-2 border-amber-400/70 hover:border-amber-400 shadow-md shadow-amber-500/10 hover:shadow-amber-500/25 hover:scale-[1.03] active:scale-95 transition-all duration-200 cursor-pointer">
                        <svg class="w-4 h-4 text-amber-400 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/>
                        </svg>
                        <span>ĐĂNG KÝ HỘI VIÊN VIP</span>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-mono font-black bg-amber-400 text-slate-950 uppercase">0đ CỌC</span>
                    </a>
                </div>

                <!-- ========================================================================= -->
                <!-- Ô TÌM KIẾM SÁCH TRỰC TIẾP (QUICK SEARCH BAR)                               -->
                <!-- ========================================================================= -->
                <div class="relative z-30 mt-4 sm:mt-5 max-w-3xl mx-auto"
                     x-data="{
                        searchQuery: '{{ request('search') }}',
                        placeholderIdx: 0,
                        placeholderPrefix: 'Thử tìm: ',
                        placeholders: [
                            'Ứng dụng AI',
                            'Khởi nghiệp tinh gọn',
                            'Phân tích kỹ thuật',
                            'Manga chuyển sinh',
                            'Lập trình Python',
                            'Tài chính cá nhân',
                            'Đầu tư giá trị',
                            'Marketing thực chiến',
                            'Cấu trúc dữ liệu',
                            'Light Novel hot'
                        ],
                        timer: null,
                        init() {
                            this.timer = setInterval(() => {
                                if (!this.searchQuery) {
                                    this.placeholderIdx = (this.placeholderIdx + 1) % this.placeholders.length;
                                }
                            }, 3000);
                        },
                        currentPlaceholder() {
                            return this.placeholderPrefix + this.placeholders[this.placeholderIdx] + '...';
                        }
                     }">

                    <!-- Form Search Input chính -->
                    <form action="{{ route('books.index') }}" method="GET" 
                          class="relative flex items-center bg-slate-900/90 rounded-2xl border-2 border-slate-700 focus-within:border-indigo-400 focus-within:ring-4 focus-within:ring-indigo-500/25 shadow-2xl transition-all duration-200 p-1.5 sm:p-2 backdrop-blur-md">
                        <div class="pl-3 sm:pl-4 text-slate-400 pointer-events-none shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" 
                               name="search"
                               x-model="searchQuery"
                               :placeholder="currentPlaceholder()"
                               autocomplete="off"
                               class="w-full pl-3 pr-3 py-2.5 sm:py-3 bg-transparent border-0 text-white placeholder:text-slate-400 text-sm sm:text-base font-medium focus:outline-none focus:ring-0">
                        
                        <!-- Nút Xóa nhanh khi người dùng đã gõ -->
                        <button type="button" 
                                x-show="searchQuery && searchQuery.length > 0"
                                x-on:click="searchQuery = ''; $el.previousElementSibling.focus()"
                                class="p-1.5 text-slate-400 hover:text-white rounded-lg transition mr-1"
                                title="Xóa từ khóa">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>

                        <!-- Nút Submit Tìm kiếm -->
                        <button type="submit" 
                                class="px-5 sm:px-7 py-2.5 sm:py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs sm:text-sm tracking-wide uppercase transition active:scale-[0.98] shadow-md shadow-indigo-600/30 shrink-0 flex items-center gap-1.5 cursor-pointer">
                            <span>Tìm kiếm</span>
                        </button>
                    </form>
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
                    <div class="col-span-3 p-8 rounded-xl bg-slate-50 border border-slate-200 text-center space-y-3">
                        <div class="w-12 h-12 mx-auto rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shadow-2xs">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                            </svg>
                        </div>
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
                        <span>Xem toàn bộ {{ $totalBooks }} đầu sách tinh hoa</span>
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
    <!-- 5. CHUYÊN NGÀNH & THỂ LOẠI CHỌN LỌC (REDESIGNED)                          -->
    <!-- ========================================================================= -->
    @php
        $categoryThemeMap = [
            'CNTT' => [
                'tag' => 'Công nghệ & AI',
                'gradient' => 'from-indigo-600 via-blue-600 to-cyan-500',
                'shadow' => 'shadow-indigo-500/25',
                'border_hover' => 'hover:border-indigo-400/80 hover:shadow-indigo-500/10',
                'badge' => 'bg-indigo-50 text-indigo-700 border-indigo-200/80',
                'accent_text' => 'text-indigo-600',
                'desc' => 'Khoa học máy tính, lập trình hệ thống, kiến trúc phần mềm, AI & an toàn thông tin.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />'
            ],
            'Kinh doanh' => [
                'tag' => 'Chiến lược & Quản trị',
                'gradient' => 'from-emerald-600 via-teal-600 to-emerald-500',
                'shadow' => 'shadow-emerald-500/25',
                'border_hover' => 'hover:border-emerald-400/80 hover:shadow-emerald-500/10',
                'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                'accent_text' => 'text-emerald-600',
                'desc' => 'Quản trị chiến lược, khởi nghiệp tinh gọn, vận hành doanh nghiệp & marketing số.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />'
            ],
            'Chứng khoán' => [
                'tag' => 'Tài chính & Đầu tư',
                'gradient' => 'from-amber-500 via-orange-500 to-amber-600',
                'shadow' => 'shadow-amber-500/25',
                'border_hover' => 'hover:border-amber-400/80 hover:shadow-amber-500/10',
                'badge' => 'bg-amber-50 text-amber-800 border-amber-200/80',
                'accent_text' => 'text-amber-600',
                'desc' => 'Phân tích kỹ thuật, đầu tư giá trị, thị trường vốn & quản trị danh mục rủi ro.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />'
            ],
            'Truyện Anime' => [
                'tag' => 'Manga & Novel',
                'gradient' => 'from-rose-500 via-pink-600 to-purple-600',
                'shadow' => 'shadow-rose-500/25',
                'border_hover' => 'hover:border-rose-400/80 hover:shadow-rose-500/10',
                'badge' => 'bg-rose-50 text-rose-700 border-rose-200/80',
                'accent_text' => 'text-rose-600',
                'desc' => 'Tuyển tập Light Novel, Manga chuyển sinh, huyền huyễn và tiểu thuyết đồ họa đặc sắc.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />'
            ],
            'Học ngoại ngữ' => [
                'tag' => 'Ngôn ngữ & Quốc tế',
                'gradient' => 'from-sky-500 via-blue-600 to-indigo-600',
                'shadow' => 'shadow-sky-500/25',
                'border_hover' => 'hover:border-sky-400/80 hover:shadow-sky-500/10',
                'badge' => 'bg-sky-50 text-sky-700 border-sky-200/80',
                'accent_text' => 'text-sky-600',
                'desc' => 'Giáo trình luyện thi chứng chỉ quốc tế IELTS, TOEIC, HSK, JLPT & phản xạ đa ngôn ngữ.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />'
            ],
            'Tâm lý học' => [
                'tag' => 'Hành vi & Nhận thức',
                'gradient' => 'from-purple-600 via-violet-600 to-fuchsia-600',
                'shadow' => 'shadow-purple-500/25',
                'border_hover' => 'hover:border-purple-400/80 hover:shadow-purple-500/10',
                'badge' => 'bg-purple-50 text-purple-700 border-purple-200/80',
                'accent_text' => 'text-purple-600',
                'desc' => 'Công trình nghiên cứu kinh điển về hành vi con người, tư duy nhận thức & trí tuệ cảm xúc.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />'
            ],
            'Kỹ năng sống' => [
                'tag' => 'Phát triển cá nhân',
                'gradient' => 'from-teal-600 via-emerald-600 to-cyan-600',
                'shadow' => 'shadow-teal-500/25',
                'border_hover' => 'hover:border-teal-400/80 hover:shadow-teal-500/10',
                'badge' => 'bg-teal-50 text-teal-700 border-teal-200/80',
                'accent_text' => 'text-teal-600',
                'desc' => 'Cẩm nang rèn luyện nhân cách, đối nhân xử thế, quản trị bản thân & tư duy thành công.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />'
            ],
            'Văn học' => [
                'tag' => 'Văn học & Tinh hoa',
                'gradient' => 'from-rose-600 via-red-600 to-amber-600',
                'shadow' => 'shadow-rose-500/25',
                'border_hover' => 'hover:border-rose-400/80 hover:shadow-rose-500/10',
                'badge' => 'bg-rose-50 text-rose-700 border-rose-200/80',
                'accent_text' => 'text-rose-600',
                'desc' => 'Kiệt tác văn học kinh điển Việt Nam & thế giới, nuôi dưỡng tâm hồn và chiều sâu tư tưởng.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />'
            ],
        ];

        $fallbackPalettes = [
            [
                'tag' => 'Chuyên ngành',
                'gradient' => 'from-indigo-600 via-blue-600 to-sky-600',
                'shadow' => 'shadow-indigo-500/25',
                'border_hover' => 'hover:border-indigo-400/80 hover:shadow-indigo-500/10',
                'badge' => 'bg-indigo-50 text-indigo-700 border-indigo-200/80',
                'accent_text' => 'text-indigo-600',
                'desc' => 'Tài liệu và sách chuyên khảo chất lượng cao phục vụ học tập và nghiên cứu.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />'
            ],
            [
                'tag' => 'Khoa học',
                'gradient' => 'from-violet-600 via-purple-600 to-indigo-600',
                'shadow' => 'shadow-violet-500/25',
                'border_hover' => 'hover:border-violet-400/80 hover:shadow-violet-500/10',
                'badge' => 'bg-violet-50 text-violet-700 border-violet-200/80',
                'accent_text' => 'text-violet-600',
                'desc' => 'Nguồn tri thức đa dạng được tuyển chọn và cập nhật liên tục.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />'
            ],
        ];
    @endphp

    <section id="categories-section" class="relative py-20 bg-gradient-to-b from-slate-50 via-indigo-50/20 to-slate-50 border-t border-slate-200/80 overflow-hidden">
        <!-- Ambient decorative blurs -->
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Section Header -->
            <div class="text-center max-w-2xl mx-auto mb-14">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-50 border border-indigo-200/80 text-indigo-700 text-xs font-bold uppercase tracking-wider mb-3 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
                    <span>CHỦ ĐỀ NGHIÊN CỨU & HỌC THUẬT</span>
                </div>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Khám Phá Các Chuyên Ngành Khoa Học
                </h2>
                <p class="mt-3 text-sm sm:text-base text-slate-500 leading-relaxed">
                    Phân loại chuẩn mực giúp bạn dễ dàng tiếp cận nguồn tri thức đúng theo nhu cầu học tập, tra cứu và nghiên cứu chuyên sâu.
                </p>
                <div class="mt-4 flex flex-wrap items-center justify-center gap-3 text-xs font-medium text-slate-500">
                    <span class="inline-flex items-center gap-1.5"><span class="text-indigo-600 font-bold">8</span> Chuyên ngành trọng điểm</span>
                    <span class="text-slate-300">•</span>
                    <span class="inline-flex items-center gap-1.5"><span class="text-emerald-600 font-bold">400+</span> Đầu sách chuẩn hóa</span>
                    <span class="text-slate-300">•</span>
                    <span class="inline-flex items-center gap-1.5">⚡ Tra cứu & Mượn tức thì</span>
                </div>
            </div>

            <!-- Grid 8 cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($categories as $category)
                    @php
                        $theme = $categoryThemeMap[$category->name] ?? $fallbackPalettes[$loop->index % count($fallbackPalettes)];
                        $bookCount = $category->books_count ?? $category->books()->count();
                        $cardDesc = $category->description ?: $theme['desc'];
                    @endphp

                    <a href="{{ route('books.index', ['category_id' => $category->id]) }}" 
                       class="group relative flex flex-col justify-between bg-white rounded-2xl p-6 border border-slate-200/90 shadow-xs hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 overflow-hidden cursor-pointer {{ $theme['border_hover'] }}">
                        
                        <!-- Top accent gradient line on hover -->
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r {{ $theme['gradient'] }} opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                        <!-- Ambient decorative corner tint on hover -->
                        <div class="absolute -top-10 -right-10 w-24 h-24 bg-gradient-to-br {{ $theme['gradient'] }} opacity-0 group-hover:opacity-10 rounded-full blur-xl transition-opacity duration-300 pointer-events-none"></div>

                        <div>
                            <!-- Top Row: Gradient Icon + Count Badge -->
                            <div class="flex items-center justify-between gap-3 mb-5">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $theme['gradient'] }} flex items-center justify-center text-white shadow-md {{ $theme['shadow'] }} group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300 shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        {!! $theme['icon_svg'] !!}
                                    </svg>
                                </div>
                                
                                <div class="flex flex-col items-end gap-1">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $theme['badge'] }} border shadow-2xs">
                                        {{ $theme['tag'] }}
                                    </span>
                                    <span class="text-[11px] font-mono font-semibold text-slate-500 bg-slate-100/90 px-2 py-0.5 rounded-md border border-slate-200/70">
                                        {{ $bookCount }} đầu sách
                                    </span>
                                </div>
                            </div>

                            <!-- Title -->
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 group-hover:text-indigo-600 transition-colors line-clamp-1 flex items-center justify-between">
                                <span>{{ $category->name }}</span>
                                <svg class="w-4 h-4 text-slate-300 group-hover:text-indigo-600 group-hover:translate-x-1 transition-all duration-200 shrink-0 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7" />
                                </svg>
                            </h3>

                            <!-- Description -->
                            <p class="mt-2 text-xs sm:text-sm text-slate-500 line-clamp-2 leading-relaxed">
                                {{ $cardDesc }}
                            </p>
                        </div>

                        <!-- Bottom Action CTA -->
                        <div class="mt-5 pt-3.5 border-t border-slate-100 flex items-center justify-between text-xs font-semibold {{ $theme['accent_text'] }}">
                            <span class="group-hover:underline">Khám phá kho sách</span>
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-50 group-hover:bg-indigo-50 group-hover:translate-x-0.5 transition-all text-slate-400 group-hover:text-indigo-600">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-4 text-center py-12 text-slate-400 bg-white rounded-2xl border border-slate-200">
                        Chưa có thể loại nào được khởi tạo trong hệ thống.
                    </div>
                @endforelse
            </div>

            <!-- Action Button below grid -->
            <div class="mt-12 text-center">
                <a href="{{ route('books.index') }}" 
                   class="inline-flex items-center gap-2.5 px-6 py-3 rounded-xl bg-white hover:bg-slate-50 border border-slate-200/90 hover:border-indigo-300 text-slate-700 hover:text-indigo-600 text-xs sm:text-sm font-bold shadow-xs hover:shadow-md transition-all duration-200 group">
                    <svg class="w-4 h-4 text-indigo-600 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span>Truy Cập Toàn Bộ Danh Mục & Bộ Lọc Nâng Cao</span>
                    <span class="px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-600 font-mono text-xs font-semibold">
                        400+ cuốn
                    </span>
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-indigo-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
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

    <!-- Giỏ sách mượn đã chuyển sang trang chuyên biệt /cart chuẩn Laravel Blade -->
    {{-- <x-cart-drawer /> --}}

    <!-- Cổng Nạp Tiền VietQR Napas 247 (Fintech Recharge Modal) -->
    <x-deposit-modal />
</body>
</html>
