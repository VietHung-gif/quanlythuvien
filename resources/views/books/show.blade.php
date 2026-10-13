<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-black text-2xl sm:text-3xl text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-blue-50 border border-blue-200 text-blue-600 shadow-xs">
                        <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
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
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0 text-emerald-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </div>
                        <span class="text-sm font-semibold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl shadow-xs flex items-center justify-between text-rose-800">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-rose-100 flex items-center justify-center shrink-0 text-rose-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <span class="text-sm font-semibold">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- 1. BREADCRUMB ĐIỀU HƯỚNG -->
            <nav class="flex items-center text-xs font-semibold text-slate-500 flex-wrap gap-2">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    <span>Trang Chủ</span>
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
                                <span class="uppercase tracking-wider">Tình trạng kho:</span>
                                @if($book->available_copies > 10)
                                    <span class="font-bold text-emerald-600">Còn sách</span>
                                @elseif($book->available_copies > 0)
                                    <span class="font-bold text-amber-600">Sắp hết</span>
                                @else
                                    <span class="font-bold text-slate-500">Hết sách</span>
                                @endif
                            </div>

                            @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'librarian']))
                                <div class="flex items-center justify-between text-xs font-semibold text-slate-600 pt-2 border-t border-slate-200">
                                    <span class="uppercase tracking-wider text-indigo-700 font-bold flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        Số lượng còn lại:
                                    </span>
                                    <span class="font-mono font-bold text-indigo-900 bg-white px-2.5 py-1 rounded-md border border-indigo-200">
                                        {{ $book->available_copies }} / {{ $book->quantity }} cuốn
                                    </span>
                                </div>
                            @endif

                            <div class="pt-2 border-t border-slate-200 text-center">
                                @php
                                    $canSeeStock = Auth::check() && in_array(Auth::user()->role, ['admin', 'librarian']);
                                @endphp
                                @if($book->available_copies > 10)
                                    <span class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold bg-emerald-50 text-emerald-800 border border-emerald-300 shadow-2xs">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        <span>Còn sách {{ $canSeeStock ? "({$book->available_copies} cuốn khả dụng)" : '(Sẵn sàng cho mượn)' }}</span>
                                    </span>
                                @elseif($book->available_copies > 0)
                                    <span class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold bg-amber-50 text-amber-800 border border-amber-300 shadow-2xs">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                        <span>Sắp hết {{ $canSeeStock ? "({$book->available_copies} cuốn khả dụng)" : '(Hãy đăng ký sớm)' }}</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold bg-slate-100 text-slate-500 border border-dashed border-slate-300 shadow-none">
                                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                        <span>Hết hàng {{ $canSeeStock ? '(0 cuốn trong kho)' : '(Không thể chọn mượn)' }}</span>
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
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                    </svg>
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
                                <form method="POST" action="{{ route('borrows.request', $book->id) }}" class="w-full sm:w-auto flex-1">
                                    @csrf
                                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit"
                                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 px-6 rounded-xl shadow-sm hover:shadow transition flex items-center justify-center gap-2.5 text-base active:scale-[0.98] cursor-pointer">
                                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                        <span>Đăng Ký Mượn Cuốn Sách Này</span>
                                    </button>
                                </form>

                                <button type="button"
                                        class="btn-add-to-cart w-full sm:w-auto bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 font-semibold py-3.5 px-6 rounded-xl transition flex items-center justify-center gap-2 text-sm cursor-pointer shadow-2xs active:scale-[0.98]"
                                        data-book-id="{{ $book->id }}">
                                    <svg class="w-4 h-4 shrink-0 text-blue-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                                    </svg>
                                    <span>+ Thêm Vào Giỏ Mượn</span>
                                </button>
                            @else
                                @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'librarian']))
                                    <a href="{{ route('books.edit', $book->id) }}" 
                                       class="w-full sm:w-auto flex-1 bg-amber-500 hover:bg-amber-600 text-white font-bold py-3.5 px-6 rounded-xl shadow-sm hover:shadow transition flex items-center justify-center gap-2 text-sm cursor-pointer"
                                       title="Bổ sung số lượng sách vào kho để độc giả có thể mượn">
                                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>+ Bổ sung số lượng tồn kho để mở mượn</span>
                                    </a>
                                @else
                                    <div class="w-full sm:w-auto flex-1 bg-slate-100/90 border-2 border-dashed border-slate-300 text-slate-400 font-bold py-3.5 px-6 rounded-xl flex items-center justify-center gap-2.5 text-sm select-none cursor-not-allowed pointer-events-none opacity-60">
                                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                        <span>Hết hàng (Không thể chọn)</span>
                                    </div>
                                @endif
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
                                <svg class="w-5 h-5 text-indigo-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
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
                                        @if($rel->available_copies > 10)
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500 text-white shadow-xs">
                                                Còn sách
                                            </span>
                                        @elseif($rel->available_copies > 0)
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-500 text-white shadow-xs">
                                                Sắp hết
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-500 text-white shadow-xs">
                                                Hết sách
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
                                                    class="btn-add-to-cart p-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition cursor-pointer active:scale-95"
                                                    data-book-id="{{ $rel->id }}"
                                                    title="Thêm vào giỏ mượn">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
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
