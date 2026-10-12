@props(['book'])

@php
    $copies = (int) ($book->available_copies ?? $book->quantity ?? 0);
    $isOutOfStock = $copies <= 0;
    $isLowStock = $copies > 0 && $copies <= 10;
    $canSeeStock = Auth::check() && in_array(Auth::user()->role, ['admin', 'librarian']);
@endphp

<div class="{{ $isOutOfStock ? 'bg-slate-50/90 border border-dashed border-slate-300 rounded-xl overflow-hidden shadow-none opacity-45 grayscale-[50%] select-none cursor-not-allowed' : 'bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md' }} transition-all duration-300 flex flex-col group h-full relative">
    
    <!-- 1. PHẦN HÌNH ẢNH BÌA SÁCH (TỈ LỆ DỌC CHUẨN PORTRAIT TRÊN NỀN SLATE-50) -->
    <a href="{{ $isOutOfStock ? 'javascript:void(0)' : route('books.show', $book->id) }}" 
       class="relative bg-slate-50 aspect-[3/4] p-4 flex items-center justify-center border-b border-slate-100 overflow-hidden block group/cover {{ $isOutOfStock ? 'cursor-not-allowed pointer-events-none' : 'cursor-pointer' }}" 
       title="{{ $book->title }}">
        
        <img src="{{ Str::startsWith($book->cover_image, ['http://', 'https://']) ? $book->cover_image : ($book->cover_image ? asset('storage/' . ltrim($book->cover_image, '/')) : asset('images/covers/default.jpg')) }}" 
             alt="{{ $book->title }}" 
             loading="lazy"
             onerror="this.onerror=null; this.src='{{ asset('images/covers/default.jpg') }}';"
             style="transform: translateZ(0); backface-visibility: hidden;"
             class="w-auto h-full max-h-full object-contain rounded-sm shadow-md {{ $isOutOfStock ? 'opacity-60' : 'group-hover/cover:scale-105' }} transition-transform duration-300" />

        <!-- Tag thể loại góc trên bên trái -->
        @if($book->category)
            <span class="absolute top-3 left-3 px-2 py-0.5 rounded text-[11px] font-semibold bg-white/95 backdrop-blur-xs text-slate-700 border border-slate-200/80 shadow-2xs truncate max-w-[75%]">
                {{ $book->category->name }}
            </span>
        @endif

        <!-- Tình trạng tồn kho góc trên bên phải (Chỉ Admin mới thấy số sách còn lại) -->
        @if($copies > 10)
            <span class="absolute top-3 right-3 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-2xs flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span>Còn sách{{ $canSeeStock ? " ({$copies})" : '' }}</span>
            </span>
        @elseif($copies > 0)
            <span class="absolute top-3 right-3 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200/80 shadow-2xs flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                <span>Sắp hết{{ $canSeeStock ? " ({$copies})" : '' }}</span>
            </span>
        @else
            <span class="absolute top-3 right-3 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-200/90 text-slate-500 border border-slate-300 shadow-2xs flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                <span>Hết sách{{ $canSeeStock ? ' (0)' : '' }}</span>
            </span>
        @endif
    </a>

    <!-- 2. PHẦN THÔNG TIN CHI TIẾT (EDITORIAL LIGHT CONTENT) -->
    <div class="p-4 flex-1 flex flex-col">
        <!-- Tiêu đề sách: Rõ nét, chữ đậm vừa phải, giới hạn 2 dòng (line-clamp-2) -->
        <h3 class="font-semibold {{ $isOutOfStock ? 'text-slate-400' : 'text-slate-800 group-hover:text-indigo-600' }} text-base leading-snug mb-1 line-clamp-2 min-h-[2.5rem] transition-colors" title="{{ $book->title }}">
            <a href="{{ $isOutOfStock ? 'javascript:void(0)' : route('books.show', $book->id) }}" class="{{ $isOutOfStock ? 'cursor-not-allowed' : '' }}">
                {{ $book->title }}
            </a>
        </h3>

        <!-- Tác giả: Chữ nhỏ, màu xám nhạt, có icon User -->
        <p class="text-sm text-slate-400 mb-2.5 flex items-center gap-1.5 line-clamp-1">
            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
            </svg>
            <span class="truncate">{{ $book->author }}</span>
        </p>

        <!-- Thông tin bổ trợ: Mã ấn bản & NXB -->
        <div class="mt-auto pt-2.5 flex items-center justify-between text-xs text-slate-400 border-t border-slate-100">
            <span class="font-mono">#{{ str_pad($book->id, 3, '0', STR_PAD_LEFT) }}</span>
            <span class="truncate max-w-[60%] text-right text-slate-400">{{ $book->publisher ?: 'NXB Tri Thức' }}</span>
        </div>
    </div>

    <!-- 3. PHẦN NÚT TƯƠNG TÁC (ACTION BUTTON Ở CHÂN THẺ) -->
    <div class="p-4 pt-0 mt-auto {{ $isOutOfStock ? 'bg-transparent' : 'bg-white' }}">
        @if(!$isOutOfStock)
            <button type="button" 
                    class="btn-add-to-cart w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-4 rounded-lg flex items-center justify-center gap-2 transition-colors text-sm shadow-xs active:scale-[0.98] cursor-pointer"
                    data-book-id="{{ $book->id }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span>Thêm vào giỏ mượn</span>
            </button>
        @else
            <div class="w-full py-2.5 px-4 rounded-lg bg-slate-200/70 border border-slate-300 text-slate-400 font-semibold flex items-center justify-center gap-2 text-xs select-none cursor-not-allowed pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
                <span>Hết hàng (Không thể chọn)</span>
            </div>
        @endif
    </div>
</div>
