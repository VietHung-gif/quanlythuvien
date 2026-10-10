<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <span>QUẢN TRỊ TÀI CHÍNH & NGÂN SÁCH THƯ VIỆN</span>
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Mô hình Bền vững Hybrid: Quỹ tài trợ nền tảng kết hợp Dòng tiền vận hành mượn/trả nội bộ
                </p>
            </div>
            
            <div class="flex items-center gap-2.5">
                <a href="{{ route('borrows.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl font-bold text-xs uppercase bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                    <span>← Bàn lưu thông</span>
                </a>
                <button type="button"
                        onclick="document.getElementById('sponsorshipModal').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>GHI NHẬN TÀI TRỢ MỚI</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- THÔNG BÁO FLASH NẾU CÓ -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-2 border-emerald-300 rounded-2xl flex items-center gap-3 text-emerald-900 font-bold text-sm shadow-xs">
                    <span>✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- ========================================================================= -->
            <!-- 1. BỐN THẺ KPI SỐ LIỆU TÀI CHÍNH PHẲNG (FINANCIAL METRICS CARDS)          -->
            <!-- ========================================================================= -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                
                <!-- THẺ 1: QUỸ CƠ SỞ HẠ TẦNG & SERVER AI -->
                <div class="border border-slate-200 bg-white p-5 rounded-2xl shadow-sm relative overflow-hidden transition hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black uppercase tracking-wider text-slate-500">Quỹ Server Cloud & AI</span>
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg">
                            ☁️
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-black font-mono text-slate-900 tracking-tight">
                            {{ number_format($totalServerFund, 0, ',', '.') }} đ
                        </div>
                        <div class="text-[11px] font-bold text-emerald-600 flex items-center gap-1 mt-1">
                            <span>●</span>
                            <span>Đảm bảo 100% chi phí Hosting & AI</span>
                        </div>
                    </div>
                </div>

                <!-- THẺ 2: TỔNG DOANH THU PHẠT & HỘI VIÊN -->
                <div class="border border-slate-200 bg-white p-5 rounded-2xl shadow-sm relative overflow-hidden transition hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black uppercase tracking-wider text-slate-500">Doanh thu Phạt & Hội viên</span>
                        <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg">
                            💳
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-black font-mono text-slate-900 tracking-tight">
                            {{ number_format($totalRevenue, 0, ',', '.') }} đ
                        </div>
                        <div class="text-[11px] font-bold text-slate-500 mt-1 flex items-center gap-2">
                            <span>Phạt: {{ number_format($totalFineCollected, 0, ',', '.') }} đ</span>
                            <span>•</span>
                            <span>VIP: {{ number_format($totalMembershipFees, 0, ',', '.') }} đ</span>
                        </div>
                    </div>
                </div>

                <!-- THẺ 3: TIỀN CỌC SÁCH ĐANG LƯU GIỮ (ESCROW / FLOAT) -->
                <div class="border border-slate-200 bg-white p-5 rounded-2xl shadow-sm relative overflow-hidden transition hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black uppercase tracking-wider text-slate-500">Tiền cọc sách lưu giữ</span>
                        <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-lg">
                            🛡️
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-black font-mono text-slate-900 tracking-tight">
                            {{ number_format($totalEscrowDeposit, 0, ',', '.') }} đ
                        </div>
                        <div class="text-[11px] font-bold text-purple-700 flex items-center gap-1 mt-1">
                            <span>●</span>
                            <span>Ký quỹ an toàn bảo hiểm sách</span>
                        </div>
                    </div>
                </div>

                <!-- THẺ 4: TỔNG QUỸ TÀI TRỢ & HIẾN TẶNG -->
                <div class="border border-slate-200 bg-white p-5 rounded-2xl shadow-sm relative overflow-hidden transition hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black uppercase tracking-wider text-slate-500">Quỹ tài trợ & Hiến tặng</span>
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">
                            🤝
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-black font-mono text-slate-900 tracking-tight">
                            {{ number_format($totalSponsorship, 0, ',', '.') }} đ
                        </div>
                        <div class="text-[11px] font-bold text-emerald-700 flex items-center gap-1 mt-1">
                            <span>●</span>
                            <span>Từ Nhà trường, Cựu SV & NXB</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ========================================================================= -->
            <!-- 2. PHÂN BỔ NGUỒN QUỸ & BẢNG TRI ÂN TÀI TRỢ                                -->
            <!-- ========================================================================= -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- CỘT TRÁI: DANH SÁCH CÁC KHOẢN TÀI TRỢ & QUỸ NỀN TẢNG (2 CỘT) -->
                <div class="lg:col-span-2 bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="text-base font-black text-slate-900 uppercase tracking-tight flex items-center gap-2">
                                <span>🏛️</span>
                                <span>DANH SÁCH NGUỒN QUỸ & ĐƠN VỊ TÀI TRỢ</span>
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Minh bạch hóa các nguồn tài trợ phục vụ vận hành và số hóa thư viện</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-black bg-slate-100 text-slate-700">
                            {{ $funds->count() }} Khoản quỹ
                        </span>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse($funds as $fund)
                            <div class="py-3.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 hover:bg-slate-50/60 rounded-xl px-2 transition">
                                <div class="space-y-1">
                                    <div class="text-sm font-black text-slate-900 flex items-center gap-2">
                                        <span>{{ $fund->source_name }}</span>
                                    </div>
                                    <div class="text-xs text-slate-500 font-medium">
                                        {{ $fund->notes ?? 'Khoản tài trợ định kỳ phục vụ bạn đọc' }}
                                    </div>
                                    <div class="flex items-center gap-2 text-[11px] text-slate-400">
                                        <span>Mục đích: <strong class="text-slate-700 font-bold">{{ $fund->allocated_name }}</strong></span>
                                        <span>•</span>
                                        <span>Ngày nhận: {{ $fund->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <div class="text-base font-black font-mono text-emerald-700">
                                        +{{ number_format($fund->amount, 0, ',', '.') }} đ
                                    </div>
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-slate-100 text-slate-600">
                                        {{ $fund->allocated_for }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="py-10 text-center text-slate-400 text-xs">
                                Chưa có khoản tài trợ nào được ghi nhận.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- CỘT PHẢI: CƠ CẤU PHÂN BỔ NGÂN SÁCH (1 CỘT) -->
                <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
                    <div class="pb-3 border-b border-slate-100">
                        <h3 class="text-base font-black text-slate-900 uppercase tracking-tight flex items-center gap-2">
                            <span>📊</span>
                            <span>CƠ CẤU PHÂN BỔ QUỸ</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Tỷ lệ chi tiêu theo mục đích chiến lược</p>
                    </div>

                    <div class="space-y-4 text-xs font-bold">
                        <!-- Server & Cloud AI -->
                        <div class="space-y-1">
                            <div class="flex justify-between text-slate-700">
                                <span>☁️ Server Cloud & AI Engine</span>
                                <span class="font-mono font-black">{{ number_format($allocations['server_cloud'] ?? 0, 0, ',', '.') }} đ</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $totalSponsorship > 0 ? round((($allocations['server_cloud'] ?? 0) / $totalSponsorship) * 100) : 0 }}%"></div>
                            </div>
                        </div>

                        <!-- Mua sách mới -->
                        <div class="space-y-1">
                            <div class="flex justify-between text-slate-700">
                                <span>📚 Mua sắm sách mới (CapEx)</span>
                                <span class="font-mono font-black">{{ number_format($allocations['book_procurement'] ?? 0, 0, ',', '.') }} đ</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-emerald-600 h-2.5 rounded-full" style="width: {{ $totalSponsorship > 0 ? round((($allocations['book_procurement'] ?? 0) / $totalSponsorship) * 100) : 0 }}%"></div>
                            </div>
                        </div>

                        <!-- Thù lao thủ thư -->
                        <div class="space-y-1">
                            <div class="flex justify-between text-slate-700">
                                <span>👥 Thù lao Thủ thư & Kỹ thuật</span>
                                <span class="font-mono font-black">{{ number_format($allocations['staff_salary'] ?? 0, 0, ',', '.') }} đ</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-amber-500 h-2.5 rounded-full" style="width: {{ $totalSponsorship > 0 ? round((($allocations['staff_salary'] ?? 0) / $totalSponsorship) * 100) : 0 }}%"></div>
                            </div>
                        </div>

                        <!-- Tu bổ & Bảo trì -->
                        <div class="space-y-1">
                            <div class="flex justify-between text-slate-700">
                                <span>🛠️ Tu bổ tài liệu & Thiết bị</span>
                                <span class="font-mono font-black">{{ number_format($allocations['facility_maintenance'] ?? 0, 0, ',', '.') }} đ</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-purple-600 h-2.5 rounded-full" style="width: {{ $totalSponsorship > 0 ? round((($allocations['facility_maintenance'] ?? 0) / $totalSponsorship) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-[11px] text-slate-600 font-medium leading-relaxed mt-4">
                        💡 <strong>Nguyên lý quản trị:</strong> Dòng tiền phạt quá hạn và phí VIP tự động đổ về Quỹ tu bổ và Quỹ Server để hệ thống tự cân đối thu chi mà không phụ thuộc hoàn toàn vào ngân sách trường.
                    </div>
                </div>

            </div>

            <!-- ========================================================================= -->
            <!-- 3. NHẬT KÝ GIAO DỊCH TÀI CHÍNH TOÀN HỆ THỐNG (TRANSACTIONS LOG)            -->
            <!-- ========================================================================= -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-black text-slate-900 uppercase tracking-tight flex items-center gap-2">
                            <span>🧾</span>
                            <span>NHẬT KÝ GIAO DỊCH NỘI BỘ (TRANSACTIONS AUDIT)</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Theo dõi luồng tiền: Nạp ví, Thu tiền phạt, Tiền cọc sách và Phí nâng cấp VIP</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold tracking-wider">
                            <tr>
                                <th class="px-4 py-3">MÃ GD</th>
                                <th class="px-4 py-3">ĐỘC GIẢ</th>
                                <th class="px-4 py-3">LOẠI GIAO DỊCH</th>
                                <th class="px-4 py-3">NỘI DUNG</th>
                                <th class="px-4 py-3 text-right">SỐ TIỀN</th>
                                <th class="px-4 py-3 text-center">THỜI GIAN</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 text-xs">
                            @forelse($recentTransactions as $tx)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-4 py-3 font-mono font-bold text-slate-500">
                                        #{{ $tx->reference_id ?? 'TX' . str_pad($tx->id, 5, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-4 py-3 font-bold text-slate-900">
                                        {{ $tx->user ? $tx->user->name : 'Hệ thống' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($tx->type === 'deposit')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-emerald-100 text-emerald-800">Nạp tiền ví</span>
                                        @elseif($tx->type === 'fine_payment')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-rose-100 text-rose-800">Nộp phạt trễ</span>
                                        @elseif(in_array($tx->type, ['membership_fee', 'membership_upgrade']))
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-amber-100 text-amber-800">Phí VIP Scholar</span>
                                        @elseif($tx->type === 'book_deposit_hold')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-purple-100 text-purple-800">Tạm giữ cọc</span>
                                        @elseif($tx->type === 'book_deposit_refund')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-blue-100 text-blue-800">Hoàn cọc</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-slate-100 text-slate-800">{{ $tx->type_name }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 font-medium">
                                        {{ $tx->description }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-bold {{ $tx->is_positive ? 'text-emerald-700' : 'text-slate-900' }}">
                                        {{ $tx->is_positive ? '+' : '-' }}{{ number_format($tx->amount, 0, ',', '.') }} đ
                                    </td>
                                    <td class="px-4 py-3 text-center text-slate-400 font-mono text-[11px]">
                                        {{ $tx->created_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-400">Chưa có giao dịch tài chính nào phát sinh.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($recentTransactions->hasPages())
                    <div class="pt-4 border-t border-slate-100">
                        {{ $recentTransactions->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL POPUP: GHI NHẬN TÀI TRỢ MỚI                                         -->
    <!-- ========================================================================= -->
    <div id="sponsorshipModal" class="fixed inset-0 z-50 overflow-y-auto hidden" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                 onclick="document.getElementById('sponsorshipModal').classList.add('hidden')"></div>

            <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full text-left relative z-10 border-2 border-slate-300 shadow-2xl my-8">
                <form action="{{ route('admin.finances.store-sponsorship') }}" method="POST">
                    @csrf
                    <div class="text-center pb-4 border-b border-slate-200">
                        <h3 class="text-xl font-black text-slate-950 uppercase tracking-tight flex items-center justify-center gap-2">
                            <span>🤝</span>
                            <span>GHI NHẬN KHOẢN TÀI TRỢ / HIẾN TẶNG</span>
                        </h3>
                        <p class="text-xs text-slate-500 font-bold mt-1">
                            Tiếp nhận nguồn lực từ Nhà trường, Cựu sinh viên & Doanh nghiệp
                        </p>
                    </div>

                    <div class="mt-5 space-y-4 text-sm">
                        <div>
                            <label class="block text-xs font-black uppercase text-slate-800 mb-1">
                                TÊN ĐƠN VỊ / MẠNH THƯỜNG QUÂN:
                            </label>
                            <input type="text"
                                   name="source_name"
                                   placeholder="VD: Hội Cựu SV Khóa 16, NXB Trẻ..."
                                   required
                                   class="h-12 bg-white border-2 border-slate-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 w-full rounded-2xl text-sm font-bold py-2 px-4 text-slate-900">
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase text-slate-800 mb-1">
                                SỐ TIỀN TÀI TRỢ (VNĐ):
                            </label>
                            <input type="number"
                                   name="amount"
                                   placeholder="10000000"
                                   min="100000"
                                   step="100000"
                                   required
                                   class="h-12 bg-white border-2 border-slate-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 w-full rounded-2xl text-sm font-mono font-bold py-2 px-4 text-slate-900">
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase text-slate-800 mb-1">
                                DANH MỤC PHÂN BỔ QUỸ:
                            </label>
                            <select name="allocated_for"
                                    required
                                    class="h-12 bg-white border-2 border-slate-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 w-full rounded-2xl text-xs font-bold py-2 px-4 text-slate-900">
                                <option value="server_cloud">☁️ Cơ sở hạ tầng, Server Cloud & AI</option>
                                <option value="book_procurement">📚 Mua sắm & Nhập sách mới (CapEx)</option>
                                <option value="staff_salary">👥 Thù lao Thủ thư & Trợ giảng sinh viên</option>
                                <option value="facility_maintenance">🛠️ Bảo trì trang thiết bị & Tu bổ sách</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase text-slate-800 mb-1">
                                GHI CHÚ / MỤC ĐÍCH:
                            </label>
                            <textarea name="notes"
                                      rows="2"
                                      placeholder="Mô tả mục đích tài trợ hoặc điều khoản cam kết..."
                                      class="bg-white border-2 border-slate-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 w-full rounded-2xl text-xs font-normal py-2.5 px-4 text-slate-900"></textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                        <button type="button"
                                onclick="document.getElementById('sponsorshipModal').classList.add('hidden')"
                                class="h-12 px-5 rounded-2xl font-bold text-xs uppercase text-slate-700 bg-slate-100 hover:bg-slate-200 border-2 border-slate-300 transition cursor-pointer">
                            HỦY BỎ
                        </button>
                        <button type="submit"
                                class="h-12 px-6 rounded-2xl font-black text-xs uppercase tracking-wider bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition cursor-pointer">
                            XÁC NHẬN GHI NHẬN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
