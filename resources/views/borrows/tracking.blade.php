<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-bold text-xl sm:text-2xl text-slate-900 tracking-tight flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                    <span>THEO DÕI PHIẾU ĐĂNG KÝ MƯỢN</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Giám sát tiến độ phê duyệt, trạng thái nhận sách và hạn trả theo từng phiếu mượn hoặc đầu sách</p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('books.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-xs tracking-wide uppercase bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs hover:shadow transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Đăng ký mượn thêm
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        detailModalOpen: false,
        detailLoading: false,
        slipData: null,
        async openSlipDetail(borrowId) {
            this.detailLoading = true;
            this.detailModalOpen = true;
            this.slipData = null;
            try {
                const res = await fetch(`/borrows-detail/${borrowId}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    this.slipData = data;
                } else {
                    alert('Không thể tải chi tiết phiếu mượn.');
                    this.detailModalOpen = false;
                }
            } catch (err) {
                console.error(err);
                alert('Lỗi kết nối máy chủ khi tải chi tiết.');
                this.detailModalOpen = false;
            } finally {
                this.detailLoading = false;
            }
        },
        async requestSlipRenewal(borrowId, slipTitle) {
            if (!confirm(`XÁC NHẬN GIA HẠN:\nBạn có chắc chắn muốn gửi yêu cầu gia hạn thêm 7 ngày cho "${slipTitle}" đến Ban Quản Trị?\n(Lưu ý: Mỗi phiếu mượn chỉ được gửi yêu cầu gia hạn tối đa 1 lần trước khi hết hạn)`)) {
                return;
            }
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch(`/borrows/${borrowId}/request-renewal`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                const data = await res.json();
                if (data.success) {
                    alert(data.message || 'Đã gửi yêu cầu gia hạn (+7 ngày) thành công!');
                    window.location.reload();
                } else {
                    alert(data.message || 'Không thể gửi yêu cầu gia hạn.');
                }
            } catch (err) {
                console.error(err);
                alert('Lỗi kết nối máy chủ khi xin gia hạn.');
            }
        },
        printSlip() {
            window.print();
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- THỐNG KÊ TỔNG QUAN -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3.5">
                <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs relative overflow-hidden group hover:border-indigo-300 transition">
                    <div class="text-[11px] font-semibold uppercase text-slate-500 tracking-wider">TỔNG PHIẾU</div>
                    <div class="text-2xl font-bold font-mono text-slate-900 mt-1">{{ $totalAll }}</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Tất cả lượt đăng ký</div>
                </div>

                <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs relative overflow-hidden group hover:border-amber-300 transition">
                    <div class="text-[11px] font-semibold uppercase text-amber-700 tracking-wider">CHỜ DUYỆT</div>
                    <div class="text-2xl font-bold font-mono text-amber-600 mt-1">{{ $totalPending }}</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Thủ thư đang thẩm định</div>
                </div>

                <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs relative overflow-hidden group hover:border-emerald-300 transition">
                    <div class="text-[11px] font-semibold uppercase text-emerald-700 tracking-wider">ĐÃ DUYỆT</div>
                    <div class="text-2xl font-bold font-mono text-emerald-600 mt-1">{{ $totalApproved }}</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Đã được phê duyệt</div>
                </div>

                <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs relative overflow-hidden group hover:border-indigo-300 transition">
                    <div class="text-[11px] font-semibold uppercase text-indigo-700 tracking-wider">CHƯA MƯỢN</div>
                    <div class="text-2xl font-bold font-mono text-indigo-600 mt-1">{{ $totalNotBorrowed }}</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Chờ bạn đến nhận sách</div>
                </div>

                <div class="col-span-2 sm:col-span-1 p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs relative overflow-hidden group hover:border-rose-300 transition">
                    <div class="text-[11px] font-semibold uppercase text-rose-700 tracking-wider">QUÁ HẠN</div>
                    <div class="text-2xl font-bold font-mono text-rose-600 mt-1">{{ $totalOverdue }}</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Cần hoàn trả gấp</div>
                </div>
            </div>

            <!-- THANH ĐIỀU HƯỚNG CHẾ ĐỘ XEM & BỘ LỌC TRẠNG THÁI -->
            <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <!-- HAI NÚT CHUYỂN ĐỔI CHẾ ĐỘ XEM -->
                    <div class="inline-flex p-1 rounded-xl bg-slate-100 border border-slate-200 text-xs font-semibold">
                        <a href="{{ route('borrows.tracking', ['mode' => 'slip', 'status' => $status]) }}"
                           class="flex items-center gap-2 px-4 py-2 rounded-lg transition {{ $mode === 'slip' ? 'bg-white text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Theo phiếu mượn
                        </a>
                        <a href="{{ route('borrows.tracking', ['mode' => 'book', 'status' => $status]) }}"
                           class="flex items-center gap-2 px-4 py-2 rounded-lg transition {{ $mode === 'book' ? 'bg-white text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            Theo đầu sách
                        </a>
                        <a href="{{ route('borrows.my') }}"
                           class="flex items-center gap-1.5 px-3 py-2 rounded-lg transition text-slate-600 hover:text-indigo-600 hover:bg-slate-200/50"
                           title="Xem danh sách sách đang mượn và xin gia hạn chi tiết">
                            <span>⚡</span>
                            <span>Sách cá nhân & Gia hạn</span>
                        </a>
                    </div>

                    <!-- BỘ LỌC TRẠNG THÁI -->
                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 text-xs font-medium">
                        <span class="text-slate-400 font-semibold text-[11px] uppercase me-1 shrink-0">Lọc:</span>

                        @php
                            $filterOptions = [
                                'all' => 'Tất cả',
                                'approved' => 'Đã duyệt',
                                'pending' => 'Chưa duyệt',
                                'borrowing' => 'Đã mượn',
                                'not_borrowed' => 'Chưa mượn',
                                'overdue' => 'Quá hạn',
                            ];
                        @endphp

                        @foreach($filterOptions as $key => $label)
                            <a href="{{ route('borrows.tracking', ['mode' => $mode, 'status' => $key]) }}"
                               class="px-3 py-1.5 rounded-lg border transition whitespace-nowrap {{ $status === $key ? 'bg-indigo-50 text-indigo-700 border-indigo-300 font-semibold shadow-2xs' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100 hover:text-slate-900' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- CHẾ ĐỘ 1: THEO PHIẾU MƯỢN -->
            @if($mode === 'slip')
                <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                            <thead class="bg-slate-50/80 text-slate-500 uppercase text-[11px] font-semibold tracking-wider border-b border-slate-200/80">
                                <tr>
                                    <th scope="col" class="px-5 py-3.5">Số phiếu</th>
                                    <th scope="col" class="px-5 py-3.5">Ngày lập phiếu</th>
                                    <th scope="col" class="px-5 py-3.5">Ngày duyệt</th>
                                    <th scope="col" class="px-5 py-3.5">Ngày hết hạn phiếu</th>
                                    <th scope="col" class="px-5 py-3.5 text-center">Trạng thái duyệt</th>
                                    <th scope="col" class="px-5 py-3.5 text-center">SL sách duyệt</th>
                                    <th scope="col" class="px-5 py-3.5 text-center">Tình hình mượn</th>
                                    <th scope="col" class="px-5 py-3.5 text-center">Ngày hẹn trả</th>
                                    <th scope="col" class="px-5 py-3.5 text-center">Gia hạn / Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-600">
                                @forelse ($slips as $slip)
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <!-- Số phiếu (Clickable link mở modal chi tiết) -->
                                        <td class="px-5 py-4 whitespace-nowrap">
                                            <button type="button"
                                                    @click="openSlipDetail({{ $slip->id }})"
                                                    class="inline-flex items-center gap-1.5 font-mono font-bold text-sm text-indigo-600 hover:text-indigo-800 underline decoration-indigo-300 underline-offset-4 transition cursor-pointer">
                                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                {{ $slip->code }}
                                            </button>
                                        </td>

                                        <!-- Ngày lập phiếu -->
                                        <td class="px-5 py-4 whitespace-nowrap text-xs font-mono text-slate-500">
                                            {{ $slip->borrow_date ? $slip->borrow_date->format('d/m/Y') : '—' }}
                                        </td>

                                        <!-- Ngày duyệt -->
                                        <td class="px-5 py-4 whitespace-nowrap text-xs font-mono">
                                            @if($slip->approved_date)
                                                <span class="text-emerald-700 font-semibold">{{ $slip->approved_date->format('d/m/Y') }}</span>
                                            @else
                                                <span class="text-slate-400 italic">Chưa duyệt</span>
                                            @endif
                                        </td>

                                        <!-- Ngày hết hạn phiếu (hoặc hạn nhận sách) -->
                                        <td class="px-5 py-4 whitespace-nowrap text-xs font-mono text-slate-500">
                                            @if($slip->due_date)
                                                {{ $slip->due_date->format('d/m/Y') }}
                                            @else
                                                <span class="text-slate-400">—</span>
                                            @endif
                                        </td>

                                        <!-- Trạng thái duyệt -->
                                        <td class="px-5 py-4 whitespace-nowrap text-center">
                                            @if($slip->status === 'approved' || $slip->status === 'borrowing')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                    Đã duyệt
                                                </span>
                                            @elseif($slip->status === 'pending')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    Chưa duyệt
                                                </span>
                                            @elseif($slip->status === 'returned')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                                                    Đã hoàn tất
                                                </span>
                                            @elseif($slip->status === 'rejected')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                                    Từ chối
                                                </span>
                                            @endif
                                        </td>

                                        <!-- SL sách duyệt -->
                                        <td class="px-5 py-4 whitespace-nowrap text-center font-mono font-bold text-sm text-slate-900">
                                            {{ $slip->total_quantity ?? $slip->details->sum('quantity') }}
                                        </td>

                                        <!-- Tình hình mượn (Đã mượn / Chưa mượn) -->
                                        <td class="px-5 py-4 whitespace-nowrap text-center">
                                            @if($slip->borrow_status === 'Đã mượn' || $slip->status === 'borrowing')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                    📖 Đã mượn
                                                </span>
                                            @elseif($slip->status === 'returned')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    ✓ Đã trả sách
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                                    ⏳ Chưa mượn
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Ngày hẹn trả -->
                                        <td class="px-5 py-4 whitespace-nowrap text-center text-xs font-mono">
                                            @if($slip->due_date)
                                                @php
                                                    $isOverdue = $slip->status === 'borrowing' && $slip->due_date->isPast();
                                                @endphp
                                                <span class="{{ $isOverdue ? 'text-rose-600 font-bold bg-rose-50 px-2 py-0.5 rounded border border-rose-200' : 'text-slate-700' }}">
                                                    {{ $slip->due_date->format('d/m/Y') }}
                                                </span>
                                            @else
                                                <span class="text-slate-400">—</span>
                                            @endif
                                        </td>

                                        <!-- CỘT GIA HẠN / THAO TÁC CHO ĐỘC GIẢ -->
                                        <td class="px-5 py-4 whitespace-nowrap text-center">
                                            @php
                                                $isOverdue = ($slip->status === 'borrowing' || $slip->borrow_status === 'Đã mượn') && $slip->due_date && $slip->due_date->isPast();
                                                $isBorrowing = ($slip->status === 'borrowing' || $slip->status === 'approved' || $slip->borrow_status === 'Đã mượn') && $slip->status !== 'returned' && $slip->status !== 'rejected';
                                                $renewStatus = $slip->renewal_status ?? 'none';
                                                $canRenew = $isBorrowing && $slip->borrow_status !== 'Chưa mượn' && !$isOverdue && $renewStatus === 'none' && ($slip->renewed_times ?? 0) < 1;
                                            @endphp

                                            @if($renewStatus === 'pending')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-300 shadow-2xs animate-pulse" title="Yêu cầu xin gia hạn của bạn đang chờ Admin / Thủ thư duyệt">
                                                    <span>🔄</span>
                                                    <span>Đang chờ duyệt</span>
                                                </span>
                                            @elseif($renewStatus === 'approved' || ($slip->renewed_times ?? 0) >= 1)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-300 shadow-2xs" title="Đã được gia hạn (+7 ngày)">
                                                    <span>✓</span>
                                                    <span>Đã gia hạn (+7 ngày)</span>
                                                </span>
                                            @elseif($renewStatus === 'rejected')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200 shadow-2xs" title="Yêu cầu xin gia hạn bị từ chối">
                                                    <span>✕</span>
                                                    <span>Từ chối gia hạn</span>
                                                </span>
                                            @elseif($isOverdue)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200 shadow-2xs" title="Sách đã quá hạn, vui lòng mang đến quầy thư viện trả và nộp phạt">
                                                    <span>⚠️</span>
                                                    <span>Quá hạn trả</span>
                                                </span>
                                            @elseif($canRenew)
                                                <button type="button"
                                                        @click="requestSlipRenewal({{ $slip->id }}, '{{ $slip->code }}')"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-black text-indigo-700 bg-indigo-50 hover:bg-indigo-100 hover:text-indigo-800 border border-indigo-200 hover:border-indigo-300 transition shadow-2xs active:scale-95 cursor-pointer whitespace-nowrap">
                                                    <span>⚡</span>
                                                    <span>Gia hạn (+7 ngày)</span>
                                                </button>
                                            @elseif($slip->status === 'returned')
                                                <span class="text-xs text-slate-400 font-mono">Đã hoàn tất</span>
                                            @else
                                                <span class="text-xs text-slate-400 font-mono">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-16 text-center text-slate-400">
                                            <div class="w-14 h-14 mx-auto rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 mb-3 shadow-2xs">
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <p class="text-sm font-semibold text-slate-700">Chưa có phiếu đăng ký nào</p>
                                            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                                Không có dữ liệu phiếu mượn tương ứng với bộ lọc đã chọn. Hãy thêm sách vào giỏ để đăng ký nhé!
                                            </p>
                                            <div class="mt-4">
                                                <a href="{{ route('books.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs">
                                                    Khám phá kho sách ngay
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($slips && $slips->hasPages())
                        <div class="p-4 bg-slate-50/60 border-t border-slate-200/80">
                            {{ $slips->links() }}
                        </div>
                    @endif
                </div>
            @endif

            <!-- CHẾ ĐỘ 2: THEO ĐẦU SÁCH -->
            @if($mode === 'book')
                <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                            <thead class="bg-slate-50/80 text-slate-500 uppercase text-[11px] font-semibold tracking-wider border-b border-slate-200/80">
                                <tr>
                                    <th scope="col" class="px-5 py-3.5">Nhan đề & Đầu sách</th>
                                    <th scope="col" class="px-5 py-3.5">Số phiếu</th>
                                    <th scope="col" class="px-5 py-3.5">Tác giả</th>
                                    <th scope="col" class="px-5 py-3.5">Ngày lập phiếu</th>
                                    <th scope="col" class="px-5 py-3.5">Ngày duyệt</th>
                                    <th scope="col" class="px-5 py-3.5 text-center">Trạng thái</th>
                                    <th scope="col" class="px-5 py-3.5 text-center">Tình hình mượn</th>
                                    <th scope="col" class="px-5 py-3.5 text-center">Ngày hẹn trả</th>
                                    <th scope="col" class="px-5 py-3.5 text-center">Gia hạn / Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-600">
                                @forelse ($bookItems as $item)
                                    @php
                                        $slip = $item->borrow;
                                        $book = $item->book;
                                    @endphp
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <!-- Nhan đề & Đầu sách -->
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-14 rounded-lg overflow-hidden bg-slate-100 border border-slate-200 shrink-0 shadow-2xs">
                                                    <img src="{{ $book->cover_url }}"
                                                         alt="{{ $book->title }}"
                                                         class="w-full h-full object-cover"
                                                         onerror="this.onerror=null; this.src='https://placehold.co/400x600/1e293b/00f0ff?text=2P';">
                                                </div>
                                                <div class="min-w-0 max-w-xs">
                                                    <div class="font-semibold text-slate-900 text-sm truncate hover:text-indigo-600 transition" title="{{ $book->title }}">
                                                        {{ $book->title }}
                                                    </div>
                                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                                        Số lượng mượn: <strong class="text-slate-800">{{ $item->quantity }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Số phiếu -->
                                        <td class="px-5 py-4 whitespace-nowrap">
                                            <button type="button"
                                                    @click="openSlipDetail({{ $slip->id }})"
                                                    class="font-mono font-bold text-sm text-indigo-600 hover:text-indigo-800 underline decoration-indigo-300 underline-offset-4 transition cursor-pointer">
                                                {{ $slip->code }}
                                            </button>
                                        </td>

                                        <!-- Tác giả -->
                                        <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-500 font-medium">
                                            {{ $book->author }}
                                        </td>

                                        <!-- Ngày lập phiếu -->
                                        <td class="px-5 py-4 whitespace-nowrap text-xs font-mono text-slate-500">
                                            {{ $slip->borrow_date ? $slip->borrow_date->format('d/m/Y') : '—' }}
                                        </td>

                                        <!-- Ngày duyệt -->
                                        <td class="px-5 py-4 whitespace-nowrap text-xs font-mono">
                                            @if($slip->approved_date)
                                                <span class="text-emerald-700 font-semibold">{{ $slip->approved_date->format('d/m/Y') }}</span>
                                            @else
                                                <span class="text-slate-400 italic">Chưa duyệt</span>
                                            @endif
                                        </td>

                                        <!-- Trạng thái -->
                                        <td class="px-5 py-4 whitespace-nowrap text-center">
                                            @if($slip->status === 'approved' || $slip->status === 'borrowing')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    Đã duyệt
                                                </span>
                                            @elseif($slip->status === 'pending')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                    Chưa duyệt
                                                </span>
                                            @elseif($slip->status === 'returned')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                    Đã trả
                                                </span>
                                            @elseif($slip->status === 'rejected')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                    Từ chối
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Tình hình mượn -->
                                        <td class="px-5 py-4 whitespace-nowrap text-center">
                                            @if($slip->borrow_status === 'Đã mượn' || $slip->status === 'borrowing')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                    Đã mượn
                                                </span>
                                            @elseif($slip->status === 'returned')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    Đã trả
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                                    Chưa mượn
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Ngày hẹn trả -->
                                        <td class="px-5 py-4 whitespace-nowrap text-center text-xs font-mono">
                                            @if($slip->due_date)
                                                @php
                                                    $isOverdue = $slip->status === 'borrowing' && $slip->due_date->isPast();
                                                @endphp
                                                <span class="{{ $isOverdue ? 'text-rose-600 font-bold bg-rose-50 px-2 py-0.5 rounded border border-rose-200' : 'text-slate-700' }}">
                                                    {{ $slip->due_date->format('d/m/Y') }}
                                                </span>
                                            @else
                                                <span class="text-slate-400">—</span>
                                            @endif
                                        </td>

                                        <!-- CỘT GIA HẠN / THAO TÁC CHO ĐỘC GIẢ (THEO ĐẦU SÁCH) -->
                                        <td class="px-5 py-4 whitespace-nowrap text-center">
                                            @php
                                                $isOverdue = ($slip->status === 'borrowing' || $slip->borrow_status === 'Đã mượn') && $slip->due_date && $slip->due_date->isPast();
                                                $isBorrowing = ($slip->status === 'borrowing' || $slip->status === 'approved' || $slip->borrow_status === 'Đã mượn') && $slip->status !== 'returned' && $slip->status !== 'rejected';
                                                $renewStatus = $slip->renewal_status ?? 'none';
                                                $canRenew = $isBorrowing && $slip->borrow_status !== 'Chưa mượn' && !$isOverdue && $renewStatus === 'none' && ($slip->renewed_times ?? 0) < 1;
                                            @endphp

                                            @if($renewStatus === 'pending')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-300 shadow-2xs animate-pulse" title="Đang chờ Admin duyệt gia hạn">
                                                    <span>🔄</span>
                                                    <span>Đang chờ duyệt</span>
                                                </span>
                                            @elseif($renewStatus === 'approved' || ($slip->renewed_times ?? 0) >= 1)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-300 shadow-2xs" title="Đã được duyệt gia hạn">
                                                    <span>✓</span>
                                                    <span>Đã gia hạn</span>
                                                </span>
                                            @elseif($renewStatus === 'rejected')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200 shadow-2xs" title="Bị từ chối gia hạn">
                                                    <span>✕</span>
                                                    <span>Từ chối</span>
                                                </span>
                                            @elseif($isOverdue)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200 shadow-2xs" title="Quá hạn trả sách">
                                                    <span>⚠️</span>
                                                    <span>Quá hạn</span>
                                                </span>
                                            @elseif($canRenew)
                                                <button type="button"
                                                        @click="requestSlipRenewal({{ $slip->id }}, '{{ $book->title }}')"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-black text-indigo-700 bg-indigo-50 hover:bg-indigo-100 hover:text-indigo-800 border border-indigo-200 hover:border-indigo-300 transition shadow-2xs active:scale-95 cursor-pointer whitespace-nowrap">
                                                    <span>⚡</span>
                                                    <span>Gia hạn (+7 ngày)</span>
                                                </button>
                                            @elseif($slip->status === 'returned')
                                                <span class="text-xs text-slate-400 font-mono">Đã hoàn tất</span>
                                            @else
                                                <span class="text-xs text-slate-400 font-mono">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-16 text-center text-slate-400">
                                            <div class="w-14 h-14 mx-auto rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 mb-3 shadow-2xs">
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                            </div>
                                            <p class="text-sm font-semibold text-slate-700">Chưa có đầu sách nào trong phiếu</p>
                                            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                                Không có đầu sách nào tương ứng với bộ lọc đã chọn.
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($bookItems && $bookItems->hasPages())
                        <div class="p-4 bg-slate-50/60 border-t border-slate-200/80">
                            {{ $bookItems->links() }}
                        </div>
                    @endif
                </div>
            @endif

        </div>

        <!-- ========================================================================= -->
        <!-- MODAL XEM CHI TIẾT PHIẾU MƯỢN (POPUP BẢNG DANH SÁCH ĐẦU SÁCH TRONG PHIẾU) -->
        <!-- ========================================================================= -->
        <div x-show="detailModalOpen"
             x-cloak
             style="display: none;"
             class="fixed inset-0 z-50 overflow-y-auto"
             role="dialog"
             aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="detailModalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="detailModalOpen = false"
                     class="fixed inset-0 transition-opacity bg-slate-900/40 backdrop-blur-xs"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div x-show="detailModalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border border-slate-200 p-6 relative">

                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full bg-indigo-600"></span>
                            <div>
                                <h3 class="font-bold text-lg text-slate-900 flex items-center gap-2">
                                    <span>Chi tiết phiếu mượn:</span>
                                    <span class="text-indigo-600 font-mono" x-text="slipData ? slipData.code : '...'"></span>
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Độc giả: <strong class="text-slate-800" x-text="slipData ? slipData.borrower_name : ''"></strong>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="printSlip()" title="In phiếu mượn" class="text-slate-500 hover:text-indigo-600 hover:bg-slate-50 p-2 rounded-xl border border-slate-200 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                            </button>
                            <button type="button" @click="detailModalOpen = false" class="text-slate-400 hover:text-slate-700 transition p-2 rounded-xl hover:bg-slate-50">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div x-show="detailLoading" class="py-16 text-center space-y-3">
                        <div class="inline-block w-8 h-8 border-2 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                        <p class="text-xs text-slate-400">Đang nạp dữ liệu chi tiết phiếu mượn...</p>
                    </div>

                    <!-- Modal Body (Data Loaded) -->
                    <div x-show="!detailLoading && slipData" class="space-y-5">
                        <!-- Thông tin metadata của phiếu -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 text-xs">
                            <div>
                                <div class="text-slate-400 font-semibold text-[10px] uppercase">Ngày lập phiếu</div>
                                <div class="font-bold text-slate-800 font-mono mt-0.5" x-text="slipData ? slipData.borrow_date : ''"></div>
                            </div>
                            <div>
                                <div class="text-slate-400 font-semibold text-[10px] uppercase">Ngày duyệt</div>
                                <div class="font-bold text-emerald-700 font-mono mt-0.5" x-text="slipData ? slipData.approved_date : ''"></div>
                            </div>
                            <div>
                                <div class="text-slate-400 font-semibold text-[10px] uppercase">Ngày hẹn trả</div>
                                <div class="font-bold text-indigo-600 font-mono mt-0.5" x-text="slipData ? slipData.due_date : ''"></div>
                            </div>
                            <div>
                                <div class="text-slate-400 font-semibold text-[10px] uppercase">Tình hình mượn</div>
                                <div class="font-bold text-slate-800 mt-0.5" x-text="slipData ? slipData.borrow_status : ''"></div>
                            </div>
                        </div>

                        <!-- Bảng chi tiết các đầu sách trong phiếu -->
                        <div class="border border-slate-200/80 rounded-xl overflow-hidden shadow-2xs">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-100 text-left text-xs">
                                    <thead class="bg-slate-50 text-slate-500 uppercase font-semibold">
                                        <tr>
                                            <th class="px-4 py-3">STT</th>
                                            <th class="px-4 py-3">Nhan đề sách</th>
                                            <th class="px-4 py-3 text-center">SL mượn</th>
                                            <th class="px-4 py-3">Tác giả</th>
                                            <th class="px-4 py-3">Loại sách</th>
                                            <th class="px-4 py-3">Nhà xuất bản</th>
                                            <th class="px-4 py-3 text-center">Năm XB</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-slate-600">
                                        <template x-if="slipData && slipData.items">
                                            <template x-for="(item, idx) in slipData.items" :key="item.id">
                                                <tr class="hover:bg-slate-50/70">
                                                    <td class="px-4 py-3 font-mono text-slate-400 text-center" x-text="idx + 1"></td>
                                                    <td class="px-4 py-3">
                                                        <div class="flex items-center gap-2.5">
                                                            <img :src="item.cover_url"
                                                                 :alt="item.title"
                                                                 class="w-7 h-10 rounded object-cover border border-slate-200 bg-slate-50 shrink-0"
                                                                 onerror="this.onerror=null; this.src='https://placehold.co/400x600/1e293b/00f0ff?text=2P';">
                                                            <span class="font-semibold text-slate-900 text-xs" x-text="item.title"></span>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-3 text-center font-bold font-mono text-indigo-600" x-text="item.quantity"></td>
                                                    <td class="px-4 py-3 text-slate-500" x-text="item.author"></td>
                                                    <td class="px-4 py-3">
                                                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200" x-text="item.category"></span>
                                                    </td>
                                                    <td class="px-4 py-3 text-slate-500" x-text="item.publisher"></td>
                                                    <td class="px-4 py-3 text-center font-mono text-slate-500" x-text="item.publish_year"></td>
                                                </tr>
                                            </template>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Ghi chú nếu có -->
                        <template x-if="slipData && slipData.notes">
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                                <span class="text-slate-700 font-semibold uppercase">Ghi chú phiếu:</span>
                                <p class="text-slate-600 mt-1 whitespace-pre-line" x-text="slipData.notes"></p>
                            </div>
                        </template>

                        <!-- Footer -->
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 border-t border-slate-100">
                            <div class="text-xs text-slate-500">
                                Tổng số sách đăng ký: <strong class="text-indigo-600 font-mono text-sm" x-text="slipData ? slipData.total_quantity : 0"></strong> cuốn
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <template x-if="slipData && slipData.can_renew">
                                    <button type="button"
                                            @click="requestSlipRenewal(slipData.id, slipData.code)"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-black text-white bg-indigo-600 hover:bg-indigo-700 shadow-2xs transition cursor-pointer active:scale-95">
                                        <span>⚡</span>
                                        <span>Yêu cầu gia hạn (+7 ngày)</span>
                                    </button>
                                </template>
                                <template x-if="slipData && slipData.renewal_status === 'pending'">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-50 text-amber-800 border border-amber-300">
                                        <span>🔄</span>
                                        <span>Đang chờ duyệt gia hạn</span>
                                    </span>
                                </template>
                                <template x-if="slipData && slipData.renewal_status === 'approved'">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                                        <span>✓</span>
                                        <span>Đã được duyệt gia hạn (+7 ngày)</span>
                                    </span>
                                </template>

                                <button type="button"
                                        @click="printSlip()"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 shadow-2xs transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                    In phiếu mượn
                                </button>
                                <button type="button"
                                        @click="detailModalOpen = false"
                                        class="px-5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                                    Đóng
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <style>
        @media print {
            nav, header, footer, .py-8 > div:first-child {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
            }
            .fixed[role="dialog"] {
                position: static !important;
                display: block !important;
                overflow: visible !important;
                padding: 0 !important;
            }
            .fixed[role="dialog"] > div > div:first-child {
                display: none !important;
            }
            .fixed[role="dialog"] .relative {
                border: 1px solid #cbd5e1 !important;
                box-shadow: none !important;
                max-width: 100% !important;
                width: 100% !important;
                padding: 24px !important;
            }
            .fixed[role="dialog"] button {
                display: none !important;
            }
        }
    </style>
</x-app-layout>
