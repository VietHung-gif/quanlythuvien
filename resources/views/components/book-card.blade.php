@props(['book'])

<div class="group relative flex flex-col h-full rounded-2xl bg-white hover:bg-white border border-slate-200/80 hover:border-slate-300/90 transition-all duration-300 shadow-sm hover:shadow-lg hover:-translate-y-1 p-3.5">
    
    <!-- 1. Bìa sách với tỉ lệ chuẩn 3:4 & Badge trạng thái tồn kho -->
    <div class="relative w-full aspect-[3/4] overflow-hidden rounded-xl bg-slate-100 shrink-0">
        <x-book-cover :book="$book" class="w-full h-full rounded-none shadow-none group-hover:scale-105 transition-transform duration-300" />
        
        <!-- Gradient mờ nhẹ tăng chiều sâu chân ảnh -->
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 via-transparent to-transparent opacity-40 group-hover:opacity-20 transition pointer-events-none"></div>

        <!-- Badge Tồn kho: Nền trắng mờ nhạt, text màu ngữ nghĩa thanh lịch -->
        <div class="absolute top-2.5 right-2.5 z-10">
            @if($book->quantity > 0)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium bg-white/95 backdrop-blur-md text-emerald-700 border border-emerald-200/60 shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Còn {{ $book->quantity }}
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium bg-white/95 backdrop-blur-md text-rose-700 border border-rose-200/60 shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                    Tạm hết
                </span>
            @endif
        </div>
    </div>

    <!-- 2. Phần nội dung giữa: Thể loại, Tiêu đề, Tác giả -->
    <div class="pt-3 flex-1 flex flex-col">
        <!-- Tag thể loại bo góc thanh thoát -->
        @if($book->category)
            <div class="mb-2">
                <span class="inline-block px-2.5 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-600 border border-slate-200/60 truncate max-w-full">
                    {{ $book->category->name }}
                </span>
            </div>
        @endif

        <!-- Tiêu đề sách: slate-900 đậm đà tương phản cao, hover sang indigo -->
        <h3 class="text-sm sm:text-base font-semibold text-slate-900 group-hover:text-indigo-600 transition-colors line-clamp-2 min-h-[2.75rem] leading-snug" title="{{ $book->title }}">
            {{ $book->title }}
        </h3>

        <!-- Tác giả: slate-500 dịu mắt -->
        <p class="mt-1 text-xs text-slate-500 flex items-center gap-1.5 line-clamp-1">
            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span>{{ $book->author }}</span>
        </p>
    </div>

    <!-- 3. Nút Thao Tác (Action Button): Nút Thêm vào giỏ Deep Indigo chuẩn Stripe/Linear -->
    <div class="pt-3 mt-auto border-t border-slate-100 flex items-center justify-between gap-2">
        <span class="text-xs text-slate-400 font-mono">
            #{{ str_pad($book->id, 3, '0', STR_PAD_LEFT) }}
        </span>

        @if($book->quantity > 0)
            <button type="button" 
                    onclick="window.addToCart && window.addToCart({{ $book->id }})"
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] border border-indigo-600 shadow-sm hover:shadow transition-all duration-150 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Thêm vào giỏ</span>
            </button>
        @else
            <button type="button" 
                    disabled
                    class="inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-xl text-xs font-medium text-slate-400 bg-slate-100 border border-slate-200 cursor-not-allowed">
                <span>Hết sách</span>
            </button>
        @endif
    </div>
</div>
