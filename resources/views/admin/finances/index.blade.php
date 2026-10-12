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
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
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
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
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
                        <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
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
                        <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
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
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
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
                                <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
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
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            <span>CƠ CẤU PHÂN BỔ QUỸ</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Tỷ lệ chi tiêu theo mục đích chiến lược</p>
                    </div>

                    <div class="space-y-4 text-xs font-bold">
                        <!-- Server & Cloud AI -->
                        <div class="space-y-1">
                            <div class="flex justify-between text-slate-700">
                                <span class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15z"/></svg> Server Cloud & AI Engine</span>
                                <span class="font-mono font-black">{{ number_format($allocations['server_cloud'] ?? 0, 0, ',', '.') }} đ</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $totalSponsorship > 0 ? round((($allocations['server_cloud'] ?? 0) / $totalSponsorship) * 100) : 0 }}%"></div>
                            </div>
                        </div>

                        <!-- Mua sách mới -->
                        <div class="space-y-1">
                            <div class="flex justify-between text-slate-700">
                                <span class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg> Mua sắm sách mới (CapEx)</span>
                                <span class="font-mono font-black">{{ number_format($allocations['book_procurement'] ?? 0, 0, ',', '.') }} đ</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-emerald-600 h-2.5 rounded-full" style="width: {{ $totalSponsorship > 0 ? round((($allocations['book_procurement'] ?? 0) / $totalSponsorship) * 100) : 0 }}%"></div>
                            </div>
                        </div>

                        <!-- Thù lao thủ thư -->
                        <div class="space-y-1">
                            <div class="flex justify-between text-slate-700">
                                <span class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg> Thù lao Thủ thư & Kỹ thuật</span>
                                <span class="font-mono font-black">{{ number_format($allocations['staff_salary'] ?? 0, 0, ',', '.') }} đ</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-amber-500 h-2.5 rounded-full" style="width: {{ $totalSponsorship > 0 ? round((($allocations['staff_salary'] ?? 0) / $totalSponsorship) * 100) : 0 }}%"></div>
                            </div>
                        </div>

                        <!-- Tu bổ & Bảo trì -->
                        <div class="space-y-1">
                            <div class="flex justify-between text-slate-700">
                                <span class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Tu bổ tài liệu & Thiết bị</span>
                                <span class="font-mono font-black">{{ number_format($allocations['facility_maintenance'] ?? 0, 0, ',', '.') }} đ</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-purple-600 h-2.5 rounded-full" style="width: {{ $totalSponsorship > 0 ? round((($allocations['facility_maintenance'] ?? 0) / $totalSponsorship) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-[11px] text-slate-600 font-medium leading-relaxed mt-4 flex items-start gap-2">
                        <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        <div><strong>Nguyên lý quản trị:</strong> Dòng tiền phạt quá hạn và phí VIP tự động đổ về Quỹ tu bổ và Quỹ Server để hệ thống tự cân đối thu chi mà không phụ thuộc hoàn toàn vào ngân sách trường.</div>
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
                            <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
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
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
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
                                <option value="server_cloud">Cơ sở hạ tầng, Server Cloud & AI</option>
                                <option value="book_procurement">Mua sắm & Nhập sách mới (CapEx)</option>
                                <option value="staff_salary">Thù lao Thủ thư & Trợ giảng sinh viên</option>
                                <option value="facility_maintenance">Bảo trì trang thiết bị & Tu bổ sách</option>
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
