<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-black text-2xl sm:text-3xl text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-blue-50 border border-blue-200 text-blue-600 shadow-xs">
                        📖
                    </span>
                    <span>CHI TIẾT ẤN PHẨM SÁCH</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 font-medium mt-1">Thông tin học thuật, tình trạng lưu trữ và đăng ký mượn tài liệu trực tuyến</p>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('books.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs tracking-wider uppercase text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 shadow-xs transition hover:border-slate-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>VỀ KHO SÁCH</span>
                </a>

                @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'librarian']))
                    <a href="{{ route('books.edit', $book->id) }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl font-bold text-xs tracking-wider uppercase text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-200 shadow-xs transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>SỬA ĐẦU SÁCH</span>
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="bg-slate-50 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- THÔNG BÁO FLASH -->
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl shadow-xs flex items-center justify-between text-emerald-800">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0 text-emerald-600 font-bold">✓</div>
                        <span class="text-sm font-semibold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl shadow-xs flex items-center justify-between text-rose-800">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-rose-100 flex items-center justify-center shrink-0 text-rose-600 font-bold">✕</div>
                        <span class="text-sm font-semibold">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- 1. BREADCRUMB ĐIỀU HƯỚNG -->
            <nav class="flex items-center text-xs font-semibold text-slate-500 flex-wrap gap-2">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition flex items-center gap-1">
                    <span>🏠</span> Trang Chủ
                </a>
                <span>/</span>
                <a href="{{ route('books.index') }}" class="hover:text-blue-600 transition">
                    Kho Sách
                </a>
                @if($book->category)
                    <span>/</span>
                    <a href="{{ route('books.index', ['category_id' => $book->category_id]) }}" class="hover:text-blue-600 transition">
                        {{ $book->category->name }}
                    </a>
                @endif
                <span>/</span>
                <span class="text-slate-900 font-bold truncate max-w-xs sm:max-w-md">{{ $book->title }}</span>
            </nav>

            <!-- 2. KHỐI NỘI DUNG CHÍNH (CHUẨN E-COMMERCE / HỌC THUẬT 2 CỘT) -->
            <div class="bg-white rounded-3xl border-2 border-slate-200 shadow-sm p-6 sm:p-8 lg:p-10">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-12 items-start">
                    
                    <!-- CỘT TRÁI (BÌA SÁCH & TỒN KHO) -->
                    <div class="md:col-span-5 lg:col-span-4 flex flex-col items-center space-y-6">
                        <!-- Bìa sách Vector Động chất lượng cao (100% Dynamic Vector Book Cover) -->
                        <div class="w-full max-w-[320px] aspect-[3/4] rounded-2xl shadow-xl overflow-hidden border-2 border-slate-200 group relative">
                            <x-book-cover :book="$book" class="w-full h-full rounded-none shadow-none" />
                        </div>

                        <!-- Khối trạng thái kho & mã sách -->
                        <div class="w-full max-w-[320px] bg-slate-50 border-2 border-slate-200 rounded-2xl p-4 space-y-3">
                            <div class="flex items-center justify-between text-xs font-semibold text-slate-600">
                                <span class="uppercase tracking-wider">Mã định danh:</span>
                                <span class="font-mono font-bold text-slate-900 bg-white px-2.5 py-1 rounded-md border border-slate-200">
                                    #{{ str_pad($book->id, 3, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-xs font-semibold text-slate-600">
                                <span class="uppercase tracking-wider">Tổng số lượng nhập:</span>
                                <span class="font-bold text-slate-900">{{ number_format($book->quantity ?? 1) }} cuốn</span>
                            </div>

                            <div class="pt-2 border-t border-slate-200 text-center">
                                @if($book->available_copies > 0)
                                    <span class="inline-flex items-center justify-center gap-2 w-full px-4 py-2 rounded-xl text-xs sm:text-sm font-bold bg-emerald-50 text-emerald-800 border border-emerald-300 shadow-2xs">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>Còn {{ $book->available_copies }} cuốn sẵn sàng cho mượn</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center gap-2 w-full px-4 py-2 rounded-xl text-xs sm:text-sm font-bold bg-rose-50 text-rose-800 border border-rose-300 shadow-2xs">
                                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                        <span>Hiện tại đã mượn hết trong kho</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- CỘT PHẢI (THÔNG TIN CHI TIẾT & HÀNH ĐỘNG) -->
                    <div class="md:col-span-7 lg:col-span-8 flex flex-col justify-between space-y-6">
                        
                        <div>
                            <!-- Tag thể loại & Viện nghiên cứu -->
                            <div class="flex items-center gap-2 flex-wrap mb-2">
                                <span class="px-3 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-800 border border-blue-200 tracking-wide uppercase">
                                    {{ $book->category ? $book->category->name : 'SÁCH CHỌN LỌC' }}
                                </span>
                                <span class="text-xs font-semibold text-slate-500">
                                    Ấn bản Thư viện TIỂU ĐỘI 2P
                                </span>
                            </div>

                            <!-- Tên sách: Font to bản, đen nhánh -->
                            <h1 class="text-3xl md:text-4xl font-black text-slate-950 tracking-tight leading-snug">
                                {{ $book->title }}
                            </h1>

                            <!-- Hàng thông tin học thuật (Metadata Grid) -->
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 rounded-2xl bg-slate-50 border-2 border-slate-200 mt-6">
                                <div>
                                    <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Tác giả</span>
                                    <span class="font-bold text-slate-900 text-sm mt-0.5 block truncate" title="{{ $book->author }}">
                                        {{ $book->author ?: 'Chưa cập nhật' }}
                                    </span>
                                </div>

                                <div>
                                    <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Năm xuất bản</span>
                                    <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                                        {{ $book->publish_year ?: 'N/A' }}
                                    </span>
                                </div>

                                <div>
                                    <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Nhà xuất bản</span>
                                    <span class="font-bold text-slate-900 text-sm mt-0.5 block truncate" title="{{ $book->publisher }}">
                                        {{ $book->publisher ?: 'Đang cập nhật' }}
                                    </span>
                                </div>

                                <div>
                                    <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Định dạng</span>
                                    <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                                        Bản in bìa cứng
                                    </span>
                                </div>
                            </div>

                            <!-- Khối tóm tắt nội dung sách -->
                            <div class="mt-6">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 mb-3 flex items-center gap-2">
                                    <span>📖</span>
                                    <span>TÓM TẮT & NỘI DUNG TÁC PHẨM</span>
                                </h3>
                                <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200 text-slate-800 leading-relaxed text-base font-normal">
                                    <p class="whitespace-pre-line">
                                        {{ $book->description ?: 'Tác phẩm hiện đang được bảo quản và phân loại trong kho lưu trữ của thư viện TIỂU ĐỘI 2P. Độc giả có thể đăng ký mượn về nghiên cứu hoặc đọc tại phòng đọc.' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Nhóm nút hành động ở đáy trang chi tiết -->
                        <div class="pt-6 border-t-2 border-slate-100 flex flex-col sm:flex-row items-center gap-3">
                            @if($book->available_copies > 0)
                                <button type="button"
                                        onclick="if(window.addToCart){ window.addToCart({{ $book->id }}); } else { alert('Giỏ sách đang khởi tạo!'); }"
                                        class="w-full sm:w-auto flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-8 rounded-xl shadow-sm transition flex items-center justify-center gap-2.5 text-base active:scale-[0.98] cursor-pointer">
                                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span>Đăng Ký Mượn Cuốn Sách Này</span>
                                </button>
                            @else
                                <button type="button" 
                                        disabled
                                        class="w-full sm:w-auto flex-1 bg-slate-200 text-slate-400 font-bold py-3.5 px-8 rounded-xl cursor-not-allowed text-base">
                                    Tạm hết sách trong kho
                                </button>
                            @endif

                            <a href="{{ route('books.index') }}" 
                               class="w-full sm:w-auto bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-3.5 px-6 rounded-xl transition text-center text-sm">
                                Quay lại kho sách
                            </a>
                        </div>

                    </div>

                </div>
            </div>

            <!-- 3. KHỐI GỢI Ý: SÁCH CÙNG CHUYÊN NGÀNH / THỂ LOẠI (RELATED BOOKS) -->
            @if(isset($relatedBooks) && $relatedBooks->count() > 0)
                <div class="space-y-4 pt-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="font-black text-xl text-slate-950 tracking-tight flex items-center gap-2">
                                <span>📚</span>
                                <span>SÁCH CÙNG CHUYÊN NGÀNH / THỂ LOẠI</span>
                            </h2>
                            <p class="text-xs text-slate-600 font-medium mt-0.5">Những tác phẩm chọn lọc liên quan bạn có thể quan tâm</p>
                        </div>
                        <a href="{{ route('books.index', ['category_id' => $book->category_id]) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline">
                            Xem tất cả cùng thể loại &rarr;
                        </a>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                        @foreach($relatedBooks as $rel)
                            <div class="bg-white rounded-2xl border-2 border-slate-200 shadow-xs hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col h-full overflow-hidden group">
                                <!-- Bìa sách -->
                                <div class="relative aspect-[3/4] bg-slate-100 overflow-hidden">
                                    <a href="{{ route('books.show', $rel->id) }}" class="block w-full h-full">
                                        <x-book-cover :book="$rel" class="w-full h-full rounded-none shadow-none group-hover:scale-105 transition-transform duration-300" />
                                    </a>
                                    <div class="absolute top-2.5 right-2.5 z-10">
                                        @if($rel->available_copies > 0)
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500 text-white shadow-xs">
                                                Còn {{ $rel->available_copies }}
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-500 text-white shadow-xs">
                                                Hết
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Thân thẻ -->
                                <div class="p-4 flex-1 flex flex-col justify-between">
                                    <div>
                                        <span class="text-[10px] font-bold uppercase text-blue-600 tracking-wider block">
                                            {{ $rel->category ? $rel->category->name : 'Tổng hợp' }}
                                        </span>
                                        <h3 class="font-bold text-slate-900 text-sm line-clamp-1 hover:text-blue-600 transition mt-1 leading-snug">
                                            <a href="{{ route('books.show', $rel->id) }}">
                                                {{ $rel->title }}
                                            </a>
                                        </h3>
                                        <p class="text-xs text-slate-500 truncate mt-0.5">{{ $rel->author }}</p>
                                    </div>

                                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2">
                                        <a href="{{ route('books.show', $rel->id) }}" class="text-xs font-semibold text-slate-600 hover:text-blue-600 hover:underline">
                                            Chi tiết &rarr;
                                        </a>

                                        @if($rel->available_copies > 0)
                                            <button type="button" 
                                                    onclick="if(window.addToCart){ window.addToCart({{ $rel->id }}); } else { alert('Giỏ sách đang khởi tạo!'); }"
                                                    class="p-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition cursor-pointer"
                                                    title="Thêm vào giỏ">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
