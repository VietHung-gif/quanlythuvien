@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
        <!-- Giao diện Mobile: Nút Trước & Sau rút gọn -->
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-xs font-semibold text-slate-600 bg-[#080b14]/80 border border-cyan-950 rounded-lg cursor-not-allowed select-none">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="cyber-btn-cyan relative inline-flex items-center px-4 py-2 text-xs font-semibold rounded-lg transition ease-in-out duration-150">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="cyber-btn-cyan relative inline-flex items-center px-4 py-2 text-xs font-semibold rounded-lg transition ease-in-out duration-150">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 text-xs font-semibold text-slate-600 bg-[#080b14]/80 border border-cyan-950 rounded-lg cursor-not-allowed select-none">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        <!-- Giao diện Desktop / Máy tính bảng -->
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-mono text-slate-400">
                    HIỂN THỊ TỪ
                    <span class="font-bold text-[#00f0ff] font-semibold">{{ $paginator->firstItem() }}</span>
                    ĐẾN
                    <span class="font-bold text-[#00f0ff] font-semibold">{{ $paginator->lastItem() }}</span>
                    TRÊN TỔNG SỐ
                    <span class="font-bold text-[#00f0ff] font-semibold">{{ $paginator->total() }}</span>
                    BẢN GHI
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex rounded-lg shadow-sm space-x-1">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="relative inline-flex items-center px-3 py-2 text-xs font-medium text-slate-600 bg-[#080b14] border border-cyan-950 rounded-lg cursor-not-allowed select-none" aria-hidden="true">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-3 py-2 text-xs font-medium text-slate-300 bg-[#080b14] border border-cyan-500/25 rounded-lg hover:border-cyan-400 hover:text-[#00f0ff] hover:shadow-[0_0_10px_rgba(0,240,255,0.4)] transition ease-in-out duration-150" aria-label="{{ __('pagination.previous') }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center px-3.5 py-2 text-xs font-mono font-medium text-slate-500 bg-[#080b14] border border-cyan-950 rounded-lg select-none">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center px-3.5 py-2 text-xs font-semibold font-bold text-[#00f0ff] bg-cyan-950/60 border border-cyan-400 rounded-lg shadow-[0_0_12px_rgba(0,240,255,0.5)] select-none">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center px-3.5 py-2 text-xs font-semibold font-medium text-slate-300 bg-[#080b14] border border-cyan-500/25 rounded-lg hover:border-cyan-400 hover:text-[#00f0ff] hover:shadow-[0_0_10px_rgba(0,240,255,0.4)] transition ease-in-out duration-150" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-3 py-2 text-xs font-medium text-slate-300 bg-[#080b14] border border-cyan-500/25 rounded-lg hover:border-cyan-400 hover:text-[#00f0ff] hover:shadow-[0_0_10px_rgba(0,240,255,0.4)] transition ease-in-out duration-150" aria-label="{{ __('pagination.next') }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="relative inline-flex items-center px-3 py-2 text-xs font-medium text-slate-600 bg-[#080b14] border border-cyan-950 rounded-lg cursor-not-allowed select-none" aria-hidden="true">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
