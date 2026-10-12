<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl sm:text-2xl text-slate-900 tracking-tight flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                    {{ __('DANH SÁCH SÁCH TÔI MƯỢN') }}
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Theo dõi các đầu sách bạn đã đăng ký mượn, hạn trả và xin gia hạn trực tuyến</p>
            </div>
            <a href="{{ route('books.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-xs tracking-wide uppercase bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs hover:shadow transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Tra cứu thêm sách
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="readerBorrowManager()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- CỤM TOAST NOTIFICATIONS TRỰC QUAN -->
            <div class="fixed top-20 right-5 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none">
                <template x-for="toast in toasts" :key="toast.id">
                    <div class="pointer-events-auto p-4 rounded-2xl shadow-xl flex items-center justify-between gap-3 text-xs font-bold border transition transform duration-300"
                         :class="{
                             'bg-emerald-600 text-white border-emerald-500': toast.type === 'success',
                             'bg-amber-500 text-white border-amber-400': toast.type === 'warning',
                             'bg-rose-600 text-white border-rose-500': toast.type === 'error',
                             'bg-indigo-600 text-white border-indigo-500': toast.type === 'info' || toast.type === 'purple'
                         }"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-2 scale-95">
                        <div class="flex items-center gap-2.5">
                            <template x-if="toast.type === 'success'">
                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </template>
                            <template x-if="toast.type === 'error'">
                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </template>
                            <template x-if="toast.type !== 'success' && toast.type !== 'error'">
                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </template>
                            <span x-text="toast.message"></span>
                        </div>
                        <button type="button" @click="removeToast(toast.id)" class="text-white/80 hover:text-white cursor-pointer ml-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </template>
            </div>

            <!-- Thông báo thành công từ Session -->
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200/80 rounded-2xl shadow-xs flex items-center justify-between text-emerald-800">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-semibold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Thông báo lỗi từ Session -->
            @if (session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200/80 rounded-2xl shadow-xs flex items-center justify-between text-rose-800">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-semibold">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Bảng dữ liệu sách mượn -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between text-xs text-slate-500 bg-slate-50/50">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                        <span class="font-medium">Tổng số lượt đăng ký của bạn:</span>
                        <span class="font-bold text-indigo-600 text-sm">{{ $borrows->total() }}</span>
                    </div>
                    <div class="text-[11px] text-slate-400 font-medium">
                        Quy định: Mỗi phiếu mượn chỉ được gửi yêu cầu gia hạn (+7 ngày) tối đa 1 lần trước ngày hết hạn.
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                        <thead class="bg-slate-50/80 text-slate-500 uppercase text-[11px] font-semibold tracking-wider border-b border-slate-200">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 w-16 text-center">STT</th>
                                <th scope="col" class="px-6 py-3.5">TÊN ĐẦU SÁCH</th>
                                <th scope="col" class="px-6 py-3.5">TÁC GIẢ</th>
                                <th scope="col" class="px-6 py-3.5">NGÀY GỬI / MƯỢN</th>
                                <th scope="col" class="px-6 py-3.5">HẠN TRẢ SÁCH</th>
                                <th scope="col" class="px-6 py-3.5">NGÀY THỰC TRẢ</th>
                                <th scope="col" class="px-6 py-3.5 text-center">TRẠNG THÁI</th>
                                <th scope="col" class="px-6 py-3.5 text-center">THAO TÁC</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-600">
                            @forelse ($borrows as $index => $borrow)
                                @php
                                    $isOverdue = $borrow->isOverdue();
                                    $overdueDays = $borrow->getOverdueDays();
                                    $renewalStatus = $borrow->renewal_status ?? 'none';
                                    $renewedTimes = $borrow->renewed_times ?? 0;
                                    $hasRenewed = $renewedTimes >= 1 || $renewalStatus === 'approved' || str_contains($borrow->notes ?? '', '[Gia hạn]') || str_contains($borrow->notes ?? '', '[Đồng ý gia hạn]');
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition-colors {{ $isOverdue ? 'bg-rose-50/30' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-center font-mono text-xs text-slate-400">
                                        #{{ str_pad($borrows->firstItem() + $index, 3, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-semibold text-slate-900 text-sm">{{ $borrow->book->title }}</div>
                                        @if($borrow->notes)
                                            <div class="text-[11px] text-slate-400 mt-0.5 line-clamp-1 italic max-w-xs" title="{{ $borrow->notes }}">
                                                {{ $borrow->notes }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-500 text-xs">
                                        {{ $borrow->book->author }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-slate-500">
                                        {{ $borrow->borrow_date ? $borrow->borrow_date->format('d/m/Y') : '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-mono text-xs">
                                        @if($borrow->status === 'pending')
                                            <span class="text-amber-600 italic">Dự kiến +14 ngày sau duyệt</span>
                                        @elseif($borrow->due_date)
                                            <div class="flex flex-col items-start">
                                                <span class="{{ $isOverdue ? 'text-rose-600 font-bold' : 'text-slate-700 font-medium' }}">
                                                    {{ $borrow->due_date->format('d/m/Y') }}
                                                </span>
                                                @if($isOverdue)
                                                    <span class="text-[10px] text-rose-600 font-bold">Quá hạn {{ $overdueDays }} ngày</span>
                                                @elseif($renewalStatus === 'pending')
                                                    <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 mt-0.5">Xin +7 ngày</span>
                                                @elseif($renewalStatus === 'approved')
                                                    <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 mt-0.5">Đã +7 ngày</span>
                                                @endif
                                            </div>
                                        @else
                                            <span>—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-mono text-xs">
                                        @if($borrow->return_date)
                                            <span class="text-emerald-700 font-semibold">{{ $borrow->return_date->format('d/m/Y') }}</span>
                                        @elseif($borrow->status === 'pending')
                                            <span class="text-slate-400 italic">Chờ duyệt mượn</span>
                                        @elseif($borrow->status === 'rejected')
                                            <span class="text-slate-400 italic">Đã đóng</span>
                                        @else
                                            <span class="text-slate-400 italic">Đang cầm giữ</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($borrow->status === 'pending')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                                <span>Chờ duyệt</span>
                                            </span>
                                        @elseif($borrow->status === 'rejected')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                <svg class="w-3 h-3 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                <span>Bị từ chối</span>
                                            </span>
                                        @elseif($borrow->status === 'returned')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <svg class="w-3 h-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                <span>Đã trả sách</span>
                                            </span>
                                        @elseif($isOverdue)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                <svg class="w-3 h-3 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                </svg>
                                                <span>Quá hạn</span>
                                            </span>
                                        @elseif($renewalStatus === 'pending')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-300">
                                                <svg class="w-3 h-3 text-amber-700 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                                <span>Chờ duyệt gia hạn</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                                                <span>Đang mượn</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center space-x-1.5">
                                        @if($borrow->status === 'pending')
                                            <form action="{{ route('borrows.cancel', $borrow->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn hủy yêu cầu mượn cuốn sách này?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition cursor-pointer active:scale-95 shadow-2xs">
                                                    Hủy yêu cầu
                                                </button>
                                            </form>
                                        @elseif($borrow->status === 'borrowing')
                                            @if($isOverdue)
                                                {{-- ĐÃ QUÁ HẠN: Ẩn nút gia hạn, hiện badge "ĐÃ QUÁ HẠN - KHÔNG THỂ GIA HẠN" --}}
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 shadow-2xs" title="Sách đã quá hạn, vui lòng mang đến quầy để hoàn trả">
                                                    <svg class="w-3.5 h-3.5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                    </svg>
                                                    <span>ĐÃ QUÁ HẠN - KHÔNG THỂ GIA HẠN</span>
                                                </span>
                                            @elseif($renewalStatus === 'pending')
                                                {{-- ĐANG CHỜ DUYỆT: Hiện badge "ĐANG CHỜ ADMIN DUYỆT GIA HẠN" (màu vàng hổ phách, disable nút) --}}
                                                <button type="button" 
                                                        disabled 
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-50 text-amber-800 border border-amber-300 shadow-2xs opacity-85 cursor-not-allowed"
                                                        title="Yêu cầu xin gia hạn của bạn đang chờ Admin / Thủ thư phê duyệt">
                                                    <span class="inline-block w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                                                    <span>ĐANG CHỜ ADMIN DUYỆT GIA HẠN</span>
                                                </button>
                                            @elseif($renewalStatus === 'approved' || $hasRenewed)
                                                {{-- ĐÃ ĐƯỢC GIA HẠN: Hiện badge "ĐÃ ĐƯỢC GIA HẠN (+7 NGÀY)" (màu xanh lá, không cho gia hạn thêm lần 2) --}}
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-300 shadow-2xs" title="Đã được gia hạn 1 lần (+7 ngày), không thể gia hạn thêm">
                                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <span>ĐÃ ĐƯỢC GIA HẠN (+7 NGÀY)</span>
                                                </span>
                                            @elseif($renewalStatus === 'rejected')
                                                {{-- BỊ TỪ CHỐI GIA HẠN --}}
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 shadow-2xs" title="{{ $borrow->notes ?? 'Ban Quản Trị đã từ chối yêu cầu gia hạn' }}">
                                                    <svg class="w-3.5 h-3.5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                    <span>YÊU CẦU GIA HẠN BỊ TỪ CHỐI</span>
                                                </span>
                                            @else
                                                {{-- renewal_status == 'none': Hiển thị nút "YÊU CẦU GIA HẠN (+7 NGÀY)" (màu tím/xanh dịu, bo góc mềm) --}}
                                                <form action="{{ route('borrows.request-renewal', $borrow->id) }}" 
                                                      method="POST" 
                                                      class="inline-block"
                                                      @submit.prevent="handleRequestRenewal($event, {{ $borrow->id }}, '{{ addslashes($borrow->book->title) }}')">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-black uppercase tracking-wider text-white bg-indigo-600 hover:bg-indigo-700 active:scale-95 shadow-xs hover:shadow transition cursor-pointer"
                                                            title="Gửi yêu cầu xin gia hạn thêm 7 ngày đến Ban Quản Trị (Mỗi phiếu chỉ được gửi 1 lần)">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        <span>YÊU CẦU GIA HẠN (+7 NGÀY)</span>
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            <span class="text-xs text-slate-400 font-mono">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-14 text-center text-slate-400">
                                        <div class="w-12 h-12 mx-auto rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 mb-3 shadow-2xs">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-semibold text-slate-700">Bạn chưa mượn hoặc gửi yêu cầu nào</p>
                                        <p class="text-xs text-slate-400 mt-1">Hãy khám phá kho tài liệu và gửi yêu cầu mượn trực tuyến nhé.</p>
                                        <div class="mt-4">
                                            <a href="{{ route('books.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs">
                                                Khám phá kho sách
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($borrows->hasPages())
                    <div class="p-4 bg-slate-50/60 border-t border-slate-200">
                        {{ $borrows->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        function readerBorrowManager() {
            return {
                toasts: [],

                showToast(message, type = 'info') {
                    const id = Date.now() + Math.random();
                    this.toasts.push({ id, message, type });
                    setTimeout(() => {
                        this.removeToast(id);
                    }, 4000);
                },

                removeToast(id) {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                },

                async handleRequestRenewal(event, borrowId, bookTitle) {
                    if (!confirm(`XÁC NHẬN: Bạn có chắc chắn muốn gửi yêu cầu gia hạn thêm 7 ngày cho cuốn sách "${bookTitle}" đến Ban Quản Trị?\n(Lưu ý: Mỗi phiếu mượn chỉ được gửi yêu cầu gia hạn 1 lần)`)) {
                        return;
                    }

                    const form = event.target;
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalHtml = submitBtn ? submitBtn.innerHTML : '';
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<svg class="w-4 h-4 mr-1.5 animate-spin inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Đang gửi...';
                    }

                    try {
                        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: new FormData(form)
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            this.showToast(data.message || 'Đã gửi yêu cầu gia hạn đến Ban Quản Trị', 'success');
                            setTimeout(() => {
                                window.location.reload();
                            }, 1200);
                        } else {
                            this.showToast(data.message || 'Có lỗi xảy ra khi gửi yêu cầu gia hạn', 'error');
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalHtml;
                            }
                        }
                    } catch (err) {
                        console.error(err);
                        this.showToast('Đang chuyển tiếp yêu cầu đến máy chủ...', 'info');
                        form.submit();
                    }
                }
            };
        }

        if (window.Alpine) {
            window.Alpine.data('readerBorrowManager', readerBorrowManager);
        } else {
            document.addEventListener('alpine:init', () => {
                window.Alpine.data('readerBorrowManager', readerBorrowManager);
            });
        }
    </script>
    @endpush
</x-app-layout>
