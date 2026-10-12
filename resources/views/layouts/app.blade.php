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

        <!-- Giỏ sách mượn đã chuyển sang trang chuyên biệt /cart chuẩn Laravel Blade -->
        {{-- <x-cart-drawer /> --}}

        <!-- Cổng Nạp Tiền VietQR Napas 247 (Fintech Recharge Modal) -->
        <x-deposit-modal />

        <!-- SCRIPT XỬ LÝ AJAX THÊM VÀO GIỎ & TOAST NOTIFICATION TOÀN CỤC (KHÔNG RELOAD / KHÔNG NHẢY SCROLL) -->
        <script>
        (function() {
            if (window._cartAjaxLoaded) return;
            window._cartAjaxLoaded = true;

            function getToastContainer() {
                let container = document.getElementById('ajax-toast-container');
                if (!container) {
                    container = document.createElement('div');
                    container.id = 'ajax-toast-container';
                    container.className = 'fixed top-20 right-4 sm:right-6 z-[99999] flex flex-col gap-3 pointer-events-none max-w-sm w-full';
                    document.body.appendChild(container);
                }
                return container;
            }

            function showToastNotification(title, message, isSuccess = true) {
                const container = getToastContainer();
                // Đảm bảo chỉ hiển thị DUY NHẤT 1 toast trên màn hình
                container.innerHTML = '';
                const toast = document.createElement('div');
                
                const borderBg = isSuccess
                    ? 'bg-white/95 border-emerald-300 text-emerald-950 shadow-xl shadow-emerald-500/10'
                    : 'bg-white/95 border-rose-300 text-rose-950 shadow-xl shadow-rose-500/10';
                const iconBg = isSuccess ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600';
                const iconSvg = isSuccess 
                    ? '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>' 
                    : '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>';

                toast.className = `pointer-events-auto p-4 rounded-2xl border-2 ${borderBg} flex items-start gap-3 backdrop-blur-md transition-all duration-300 transform translate-x-8 opacity-0 shadow-lg`;
                toast.innerHTML = `
                    <div class="w-8 h-8 rounded-xl ${iconBg} flex items-center justify-center font-black text-sm shrink-0">
                        ${iconSvg}
                    </div>
                    <div class="flex-1 min-w-0 text-xs">
                        <p class="font-bold text-sm text-slate-900">${title}</p>
                        <p class="mt-0.5 leading-relaxed text-slate-600">${message}</p>
                    </div>
                    <button type="button" class="text-slate-400 hover:text-slate-700 transition shrink-0 p-1 rounded-lg cursor-pointer" onclick="this.closest('div.pointer-events-auto').remove()">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                `;

                container.appendChild(toast);

                requestAnimationFrame(() => {
                    toast.classList.remove('translate-x-8', 'opacity-0');
                    toast.classList.add('translate-x-0', 'opacity-100');
                });

                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.classList.remove('translate-x-0', 'opacity-100');
                        toast.classList.add('translate-x-8', 'opacity-0');
                        setTimeout(() => toast.remove(), 300);
                    }
                }, 3500);
            }

            function updateCartBadges(count) {
                const badges = document.querySelectorAll('[data-cart-badge], #cart-counter-badge');
                badges.forEach(badge => {
                    badge.innerText = count;
                    if (count > 0) {
                        badge.classList.remove('hidden');
                        if (badge.style.display === 'none') {
                            badge.style.display = '';
                        }
                    } else {
                        badge.classList.add('hidden');
                    }
                });
            }

            let isAddingToCart = false;

            async function sendAddToCartRequest(bookId, buttonEl = null) {
                if (isAddingToCart) {
                    return;
                }
                isAddingToCart = true;

                const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

                let originalContent = '';
                if (buttonEl) {
                    originalContent = buttonEl.innerHTML;
                    buttonEl.disabled = true;
                    buttonEl.classList.add('opacity-75', 'cursor-not-allowed');
                }

                try {
                    const response = await fetch('{{ route('cart.add') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ book_id: bookId })
                    });

                    if (response.status === 401) {
                        window.location.href = '{{ route('login') }}';
                        return;
                    }

                    const data = await response.json();

                    if (data.success) {
                        if (typeof data.cart_count !== 'undefined') {
                            updateCartBadges(data.cart_count);
                        }
                        showToastNotification('Thêm sách thành công!', data.message || 'Đã thêm vào giỏ mượn', true);
                    } else {
                        showToastNotification('Thông báo', data.message || 'Không thể thêm sách vào giỏ.', false);
                    }
                } catch (err) {
                    console.error('Lỗi khi thêm sách vào giỏ:', err);
                    showToastNotification('Lỗi kết nối', 'Không thể kết nối đến máy chủ. Vui lòng thử lại!', false);
                } finally {
                    isAddingToCart = false;
                    if (buttonEl) {
                        buttonEl.disabled = false;
                        buttonEl.classList.remove('opacity-75', 'cursor-not-allowed');
                        buttonEl.innerHTML = originalContent;
                    }
                }
            }

            window.addToCart = function(bookId) {
                sendAddToCartRequest(bookId);
            };
            window.handleAddToCart = window.addToCart;

            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-add-to-cart');
                if (!btn) return;

                e.preventDefault();
                e.stopPropagation();

                const bookId = btn.dataset.bookId || btn.getAttribute('data-book-id');
                if (bookId) {
                    sendAddToCartRequest(bookId, btn);
                }
            });
        })();
        </script>

        @stack('scripts')
    </body>
</html>
