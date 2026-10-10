<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TIỂU ĐỘI 2P') }} - Cổng Xác Thực</title>

        <!-- Google Fonts: Be Vietnam Pro (Chuẩn tiếng Việt 100% không lỗi dấu) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

        <!-- Tailwind CSS & Vite -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['"Be Vietnam Pro"', 'system-ui', '-apple-system', 'sans-serif'],
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
        </style>
    </head>
    <body class="font-sans antialiased text-slate-800 bg-[#f8fafc]">
        <!-- Dải accent siêu mảnh trên cùng -->
        <div class="rgb-top-bar w-full fixed top-0 left-0 z-50"></div>

        <div class="min-h-screen cyber-grid-bg flex flex-col justify-center items-center p-4 sm:p-6 pt-10">
            <!-- Logo Modern Minimalist Light -->
            <div class="mb-6 text-center">
                <a href="{{ url('/') }}" class="inline-flex flex-col items-center gap-2 group">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shadow-subtle group-hover:bg-indigo-600 group-hover:text-white transition duration-200">
                        <svg class="w-6 h-6 transition-transform duration-200 group-hover:scale-105" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <span class="font-bold tracking-tight text-2xl text-slate-900 mt-1">
                        TIỂU ĐỘI <span class="text-indigo-600 font-extrabold">2P</span>
                    </span>
                    <span class="text-[11px] font-mono tracking-widest text-slate-400 uppercase">
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
                <p class="text-[11px] text-slate-400 mt-1">Core Developers: <span class="text-indigo-600 font-medium">Nguyễn Hữu Việt Hưng</span> & <span class="text-slate-600 font-medium">Ngô Hào Đào Sang</span></p>
            </div>
        </div>
    </body>
</html>
