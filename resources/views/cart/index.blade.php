<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-black text-2xl sm:text-3xl text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-600 shadow-xs">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25c-.669 0-1.188-.578-1.119-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                    </span>
                    <span>GIỎ SÁCH MƯỢN</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 font-medium mt-1">
                    Quản lý danh sách các cuốn sách đã chọn và đăng ký tạo phiếu mượn tài liệu trực tuyến
                </p>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('books.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs tracking-wider uppercase text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 shadow-xs transition hover:border-slate-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>TIẾP TỤC TÌM SÁCH</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="bg-slate-50 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- THÔNG BÁO FLASH MESSAGE -->
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border-2 border-emerald-200 rounded-2xl shadow-xs flex items-center justify-between text-emerald-900">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0 text-emerald-700">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold">{{ session('success') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('borrows.tracking') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 underline ml-4 whitespace-nowrap">
                        Xem phiếu mượn &rarr;
                    </a>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-rose-50 border-2 border-rose-200 rounded-2xl shadow-xs flex items-center justify-between text-rose-900">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-rose-100 flex items-center justify-center shrink-0 text-rose-700">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <span class="text-sm font-bold">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if (session('info'))
                <div class="p-4 bg-blue-50 border-2 border-blue-200 rounded-2xl shadow-xs flex items-center justify-between text-blue-900">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center shrink-0 text-blue-700">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                            </svg>
                        </div>
                        <span class="text-sm font-bold">{{ session('info') }}</span>
                    </div>
                </div>
            @endif

            <!-- ĐIỀU HƯỚNG BREADCRUMB -->
            <nav class="flex items-center text-xs font-semibold text-slate-500 flex-wrap gap-2">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition">Trang chủ</a>
                <span>/</span>
                <a href="{{ route('books.index') }}" class="hover:text-indigo-600 transition">Kho sách</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">Giỏ sách mượn</span>
            </nav>

            @if (empty($cart) || count($cart) === 0)
                <!-- TRẠNG THÁI GIỎ TRỐNG -->
                <div class="bg-white rounded-3xl border-2 border-slate-200 p-8 sm:p-14 text-center max-w-2xl mx-auto shadow-xs">
                    <div class="w-24 h-24 mx-auto mb-6 rounded-3xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shadow-sm">
                        <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-2">Giỏ sách trống</h2>
                    <p class="text-slate-600 text-sm max-w-md mx-auto leading-relaxed mb-8">
                        Bạn chưa chọn cuốn sách nào vào giỏ mượn. Hãy dạo quanh kho lưu trữ để khám phá hàng ngàn đầu sách hấp dẫn và thêm vào giỏ nhé!
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                        <a href="{{ route('books.index') }}" 
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-white font-bold text-sm bg-indigo-600 hover:bg-indigo-700 shadow-md hover:shadow-lg transition cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <span>Khám phá kho sách ngay</span>
                        </a>

                        <a href="{{ route('borrows.tracking') }}" 
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-slate-700 font-bold text-sm bg-slate-100 hover:bg-slate-200 transition">
                            <span>Tra cứu phiếu đã mượn</span>
                        </a>
                    </div>
                </div>
            @else
                <!-- GIAO DIỆN KHI CÓ SÁCH TRONG GIỎ -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                    <!-- CỘT TRÁI: DANH SÁCH CÁC CUỐN SÁCH (8 COLS) -->
                    <div class="lg:col-span-7 space-y-4">
                        <div class="bg-white rounded-2xl border-2 border-slate-200 p-5 shadow-xs flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                                <h2 class="font-black text-slate-900 text-base">
                                    DANH SÁCH SÁCH ĐÃ CHỌN ({{ $totalBooks }} cuốn)
                                </h2>
                            </div>

                            <form method="POST" action="{{ route('cart.clear') }}" onsubmit="return confirm('Bạn có chắc chắn muốn dọn dẹp sạch giỏ sách này?');">
                                @csrf
                                <button type="submit" 
                                        class="text-xs font-bold text-rose-600 hover:text-rose-800 hover:underline flex items-center gap-1 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span>Xóa tất cả</span>
                                </button>
                            </form>
                        </div>

                        <!-- DANH SÁCH THẺ SÁCH -->
                        <div class="space-y-3">
                            @foreach ($cart as $item)
                                <div class="bg-white rounded-2xl border-2 border-slate-200 p-4 shadow-xs hover:border-slate-300 transition flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                                    <div class="flex items-center gap-4 min-w-0 flex-1">
                                        <!-- Ảnh bìa sách -->
                                        <div class="w-16 h-22 sm:w-20 sm:h-26 rounded-xl bg-slate-100 overflow-hidden border border-slate-200 shrink-0 relative">
                                            @if (!empty($item['cover_url']))
                                                <img src="{{ $item['cover_url'] }}" 
                                                     alt="{{ $item['title'] }}" 
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-indigo-50 text-indigo-400">
                                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Thông tin sách -->
                                        <div class="min-w-0 flex-1">
                                            <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-100 mb-1">
                                                {{ $item['category'] ?? 'Tổng hợp' }}
                                            </span>
                                            <h3 class="font-black text-slate-900 text-sm sm:text-base leading-snug line-clamp-2 hover:text-indigo-600 transition">
                                                <a href="{{ route('books.show', $item['id']) }}">
                                                    {{ $item['title'] }}
                                                </a>
                                            </h3>
                                            <p class="text-xs text-slate-500 font-medium mt-1">
                                                Tác giả: <span class="text-slate-800 font-semibold">{{ $item['author'] ?? 'Chưa rõ' }}</span>
                                            </p>

                                            <div class="mt-2 flex items-center gap-3 text-xs">
                                                @php
                                                    $canSeeStock = Auth::check() && in_array(Auth::user()->role, ['admin', 'librarian']);
                                                    $itemCopies = (int) ($item['available_copies'] ?? 0);
                                                @endphp
                                                @if ($itemCopies > 10)
                                                    <span class="text-emerald-700 font-bold flex items-center gap-1">
                                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                                        <span>Còn sách{{ $canSeeStock ? " ({$itemCopies} cuốn)" : '' }}</span>
                                                    </span>
                                                @elseif ($itemCopies > 0)
                                                    <span class="text-amber-700 font-bold flex items-center gap-1">
                                                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                                        <span>Sắp hết{{ $canSeeStock ? " ({$itemCopies} cuốn)" : '' }}</span>
                                                    </span>
                                                @else
                                                    <span class="text-rose-600 font-bold flex items-center gap-1">
                                                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                                        <span>Hết sách{{ $canSeeStock ? ' (0 cuốn)' : ' (Không thể mượn)' }}</span>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Cụm Số lượng & Nút Xóa -->
                                    <div class="flex items-center gap-3 sm:gap-4 shrink-0 self-end sm:self-center w-full sm:w-auto justify-between sm:justify-end pt-3 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                                        <!-- Cụm điều chỉnh số lượng (Quantity Controls) -->
                                        <div class="flex items-center gap-1 bg-slate-50 border-2 border-slate-200 rounded-xl p-1 shadow-2xs">
                                            <form method="POST" action="{{ route('cart.update-quantity') }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="book_id" value="{{ $item['id'] }}">
                                                <input type="hidden" name="action" value="decrement">
                                                <button type="submit" 
                                                        title="Giảm số lượng"
                                                        {{ ($item['quantity'] ?? 1) <= 1 ? 'disabled' : '' }}
                                                        class="w-7 h-7 flex items-center justify-center rounded-lg font-bold text-slate-600 hover:text-indigo-600 hover:bg-white transition disabled:opacity-25 disabled:hover:bg-transparent disabled:cursor-not-allowed cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4" />
                                                    </svg>
                                                </button>
                                            </form>

                                            <span class="w-8 text-center text-xs font-black text-slate-900 select-none">
                                                {{ $item['quantity'] ?? 1 }}
                                            </span>

                                            <form method="POST" action="{{ route('cart.update-quantity') }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="book_id" value="{{ $item['id'] }}">
                                                <input type="hidden" name="action" value="increment">
                                                <button type="submit" 
                                                        title="Tăng số lượng"
                                                        {{ ($item['quantity'] ?? 1) >= ($item['available_copies'] ?? 99) ? 'disabled' : '' }}
                                                        class="w-7 h-7 flex items-center justify-center rounded-lg font-bold text-slate-600 hover:text-indigo-600 hover:bg-white transition disabled:opacity-25 disabled:hover:bg-transparent disabled:cursor-not-allowed cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>

                                        <!-- Nút xóa 1 sách khỏi giỏ -->
                                        <form method="POST" action="{{ route('cart.remove') }}" class="shrink-0">
                                            @csrf
                                            <input type="hidden" name="book_id" value="{{ $item['id'] }}">
                                            <button type="submit" 
                                                    title="Xóa cuốn này khỏi giỏ"
                                                    class="px-3 py-2 rounded-xl text-xs font-bold text-rose-600 hover:text-white bg-rose-50 hover:bg-rose-600 border border-rose-200 transition flex items-center gap-1.5 cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                <span class="hidden sm:inline">Xóa</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- CỘT PHẢI: BẢNG TỔNG KẾT VÀ FORM ĐĂNG KÝ MƯỢN (5 COLS) -->
                    <div class="lg:col-span-5 space-y-4">
                        <div class="bg-white rounded-3xl border-2 border-slate-200 p-6 sm:p-7 shadow-xs space-y-6">
                            <div>
                                <h2 class="font-black text-xl text-slate-900 tracking-tight flex items-center gap-2">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                    </svg>
                                    <span>XÁC NHẬN ĐĂNG KÝ MƯỢN</span>
                                </h2>
                                <p class="text-xs text-slate-500 font-medium mt-1">
                                    Kiểm tra thông tin tài khoản và hoàn tất thủ tục lập phiếu
                                </p>
                            </div>

                            <!-- CHI TIẾT ĐỘC GIẢ -->
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2.5 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 font-medium">Độc giả:</span>
                                    <span class="font-bold text-slate-900">{{ auth()->user()->name }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 font-medium">Email:</span>
                                    <span class="font-bold text-slate-900">{{ auth()->user()->email }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 font-medium">Hạng tài khoản:</span>
                                    <span class="font-bold flex items-center gap-1.5 {{ $isVip ? 'text-amber-600' : 'text-slate-700' }}">
                                        @if($isVip)
                                            <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M5 16L3 5L8.5 10L12 4L15.5 10L21 5L19 16H5M19 19C19 19.6 18.6 20 18 20H6C5.4 20 5 19.6 5 19V17H19V19Z" />
                                            </svg>
                                            <span>Scholar VIP</span>
                                        @else
                                            <span>Standard Member</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 font-medium">Hạn mức tối đa:</span>
                                    <span class="font-bold text-slate-900">{{ $maxLimit }} cuốn / lần</span>
                                </div>
                            </div>

                            <!-- BẢNG TÍNH TOÁN ĐĂNG KÝ -->
                            <div class="space-y-3 pt-2 border-t border-slate-100 text-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-600 font-medium">Tổng số lượng sách:</span>
                                    <span class="font-black text-slate-900 text-base">{{ $totalBooks }} cuốn</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-600 font-medium">Thời hạn mượn:</span>
                                    <span class="font-bold text-indigo-600">{{ $borrowDays }} ngày</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-600 font-medium">Hạn trả dự kiến:</span>
                                    <span class="font-bold text-slate-900">{{ \Carbon\Carbon::now()->addDays($borrowDays)->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex items-center justify-between pt-2 border-t border-dashed border-slate-200">
                                    <div>
                                        <span class="text-slate-800 font-bold block">Tiền cọc sách:</span>
                                        <span class="text-[11px] text-slate-400 font-normal">
                                            {{ $isVip ? 'Miễn cọc cho hội viên VIP' : '50.000 VNĐ / cuốn' }}
                                        </span>
                                    </div>
                                    <span class="font-black text-base {{ $isVip ? 'text-emerald-600' : 'text-slate-900' }}">
                                        {{ $isVip ? '0đ (VIP)' : number_format($depositFee, 0, ',', '.') . ' VNĐ' }}
                                    </span>
                                </div>
                            </div>

                            <!-- LƯU Ý KHI MƯỢN -->
                            <div class="p-3.5 rounded-xl bg-amber-50/70 border border-amber-200 text-amber-900 text-xs leading-relaxed flex items-start gap-2.5">
                                <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.516 0c.85.493 1.508 1.333 1.508 2.316V18" />
                                </svg>
                                <span>
                                    Sau khi bấm đăng ký, yêu cầu sẽ được chuyển đến Thủ thư. Vui lòng mang thẻ độc giả hoặc CCCD đến quầy thư viện để nhận sách trực tiếp.
                                </span>
                            </div>

                            <!-- FORM ĐĂNG KÝ MƯỢN TỔNG (ĐÃ BỎ TRƯỜNG SỐ ĐIỆN THOẠI THEO YÊU CẦU) -->
                            <form method="POST" action="{{ route('borrow.create') }}">
                                @csrf

                                <div class="mb-2">
                                    <label for="borrow_notes" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                        Ghi chú thêm (Tùy chọn):
                                    </label>
                                    <textarea id="borrow_notes" 
                                              name="notes" 
                                              rows="2" 
                                              placeholder="Ví dụ: Giờ đến nhận sách, yêu cầu sách bìa đẹp..."
                                              class="w-full text-sm rounded-xl border border-slate-200 px-3.5 py-2 focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                                </div>

                                <!-- NÚT SUBMIT THEO YÊU CẦU -->
                                <button type="submit" 
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 px-6 rounded-xl w-full mt-4 flex items-center justify-center gap-2 shadow-md hover:shadow-lg transition cursor-pointer active:scale-[0.98]">
                                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>[ĐĂNG KÝ MƯỢN]</span>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            @endif

        </div>
    </div>
</x-app-layout>
