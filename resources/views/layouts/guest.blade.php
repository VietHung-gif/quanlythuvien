<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TIỂU ĐỘI 2P') }} - Cổng Xác Thực</title>

        <!-- Google Fonts: Montserrat (Heavy/Black), Inter & Be Vietnam Pro -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,700;0,800;0,900;1,700;1,800;1,900&family=Inter:wght@400;500;600;700;800&family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

        <!-- Tailwind CSS & Vite -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['"Be Vietnam Pro"', 'system-ui', '-apple-system', 'sans-serif'],
                            montserrat: ['"Montserrat"', 'system-ui', '-apple-system', 'sans-serif'],
                            inter: ['"Inter"', 'system-ui', '-apple-system', 'sans-serif'],
                            mono: ['JetBrains Mono', 'ui-monospace', 'SFMono-Regular', 'monospace'],
                        },
                        colors: {
                            canvas: {
                                DEFAULT: '#f8fafc',
                                surface: '#ffffff',
                                subtle: '#f1f5f9',
                            },
                            brand: {
                                50: '#eef2ff',
                                100: '#e0e7ff',
                                200: '#c7d2fe',
                                500: '#6366f1',
                                600: '#4f46e5',
                                700: '#4338ca',
                            },
                            cyber: {
                                950: '#f8fafc',
                                900: '#f1f5f9',
                                800: '#e2e8f0',
                                700: '#cbd5e1',
                                600: '#94a3b8',
                            },
                            neon: {
                                cyan: '#0284c7',
                                pink: '#e11d48',
                                green: '#059669',
                                amber: '#d97706',
                                purple: '#4f46e5',
                            },
                        },
                        boxShadow: {
                            'subtle': '0 1px 2px 0 rgba(0, 0, 0, 0.03)',
                            'card': '0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.04)',
                            'card-hover': '0 10px 25px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02)',
                        }
                    }
                }
            }
        </script>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        <style>
            body, button, input, select, textarea, h1, h2, h3, h4, h5, h6, a, p, span, div, table, th, td {
                font-family: 'Be Vietnam Pro', system-ui, -apple-system, sans-serif !important;
            }
            .font-mono {
                font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace !important;
            }

            body {
                background-color: #f8fafc;
                color: #475569;
            }

            .font-montserrat {
                font-family: 'Montserrat', system-ui, -apple-system, sans-serif !important;
            }
            .font-inter {
                font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
            }
        </style>
    </head>
    <body class="font-sans antialiased text-slate-800 bg-[#f8fafc]">
        <!-- Dải accent siêu mảnh trên cùng -->
        <div class="rgb-top-bar w-full fixed top-0 left-0 z-50"></div>

        <div class="min-h-screen cyber-grid-bg flex flex-col justify-center items-center p-4 sm:p-6 pt-10">
            <!-- Logo Modern Minimalist Light -->
            <div class="mb-6 text-center">
                <a href="{{ url('/') }}" class="inline-flex flex-col items-center gap-2 group">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-3xl bg-black border border-slate-800 p-1.5 flex items-center justify-center shadow-lg overflow-hidden shrink-0 group-hover:scale-108 group-hover:border-blue-500 group-hover:shadow-[0_0_30px_rgba(37,99,235,0.5)] transition-all duration-300 ring-1 ring-white/10">
                        <img src="{{ asset('images/logo-2p.png') }}" alt="Logo Tiểu Đội 2P" class="w-full h-full object-contain rounded-2xl select-none transform group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <div class="font-montserrat font-black uppercase tracking-normal text-2xl sm:text-3xl mt-1 flex items-center leading-snug py-0.5">
                        <span class="text-slate-900 group-hover:text-blue-600 transition-colors">TIỂU ĐỘI</span>
                        <span class="ml-1.5 text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-blue-500 to-cyan-500 font-black inline-block pr-1.5 drop-shadow-[0_0_12px_rgba(37,99,235,0.3)]">2P</span>
                    </div>
                    <span class="font-inter text-[10px] sm:text-[11px] font-bold tracking-[0.22em] text-slate-400 uppercase">
                        HỆ THỐNG QUẢN LÝ THƯ VIỆN SỐ
                    </span>
                </a>
            </div>

            <!-- Card chứa Form -->
            <div class="w-full sm:max-w-md bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8 relative shadow-card">
                {{ $slot }}
            </div>

            <!-- Bản quyền & Đội ngũ phát triển -->
            <div class="mt-8 text-center text-xs text-slate-400 font-mono">
                <p>&copy; 2026 <span class="text-slate-700 font-semibold">TIỂU ĐỘI 2P</span> - Hệ thống Quản lý Thư viện Số</p>
                <p class="text-[11px] text-slate-400 mt-1 leading-relaxed max-w-md mx-auto">Core Developers: <span class="text-indigo-600 font-medium whitespace-nowrap">Nguyễn Hữu Việt Hưng</span>, <span class="text-slate-600 font-medium whitespace-nowrap">Ngô Hào Đào Sang</span>, <span class="text-slate-600 font-medium whitespace-nowrap">Phạm Hồ Nguyên Khang</span> & <span class="text-slate-600 font-medium whitespace-nowrap">Phạm Nguyễn Minh Hiếu</span></p>
            </div>
        </div>
    </body>
</html>
