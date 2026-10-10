<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TIỂU ĐỘI 2P') }} - Hệ Thống Quản Lý Thư Viện</title>

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
                            accent: {
                                sky: '#0ea5e9',
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

        <!-- Custom Stylesheet -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        <!-- Alpine.js CDN -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>
            [x-cloak] { display: none !important; }
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

        <div class="min-h-screen cyber-grid-bg pt-[2px]">
            @include('layouts.navigation')

            <!-- Tiêu đề trang (Page Heading) -->
            @isset($header)
                <header class="bg-white/80 backdrop-blur-md border-b border-slate-200/80 shadow-subtle">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Nội dung chính (Page Content) -->
            <main>
                {{ $slot }}
            </main>

            <!-- Footer chung vinh danh đội ngũ phát triển TIỂU ĐỘI 2P -->
            @include('layouts.footer')
        </div>

        <!-- Widget Chatbot AI (Trợ lý Thủ thư ảo) -->
        <x-chatbot-widget />

        <!-- Ngăn kéo Giỏ sách & Đăng ký mượn sách -->
        <x-cart-drawer />

        <!-- Cổng Nạp Tiền VietQR Napas 247 (Fintech Recharge Modal) -->
        <x-deposit-modal />

        @stack('scripts')
    </body>
</html>
