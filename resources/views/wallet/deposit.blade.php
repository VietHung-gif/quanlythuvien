<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">
                    <a href="{{ url('/') }}" class="hover:text-indigo-600 transition">Trang chủ</a>
                    <span>/</span>
                    <a href="{{ route('membership.index') }}" class="hover:text-indigo-600 transition">Gói hội viên</a>
                    <span>/</span>
                    <span class="text-indigo-600 font-bold">Nạp ví điện tử</span>
                </div>
                <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>CỔNG NẠP TIỀN VÀO VÍ ĐIỆN TỬ</span>
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Hệ thống nạp tiền tự động qua VietQR Napas 24/7 • Miễn phí giao dịch • Nâng cấp Scholar VIP & cọc sách tiện lợi
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('membership.index') }}" 
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold text-xs shadow-2xs transition">
                    <svg class="w-3.5 h-3.5 text-amber-500 fill-current" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                    <span>Xem Gói VIP</span>
                </a>
                <a href="{{ route('borrows.tracking') }}" 
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-700 font-bold text-xs transition">
                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Theo dõi mượn trả</span>
                </a>
            </div>
        </div>
    </x-slot>

    <!-- GIAO DIỆN CHÍNH TAB NẠP TIỀN (MODERN MINIMALIST LIGHT THEME) -->
    <div class="bg-slate-50 min-h-screen py-8" x-data="depositPageHandler()">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- ================================================================= -->
                <!-- CỘT TRÁI - THAO TÁC NẠP & CHỌN MỆNH GIÁ (7 CỘT)                   -->
                <!-- ================================================================= -->
                <div class="lg:col-span-7 space-y-6">

                    <!-- THẺ 1: THÔNG TIN VÍ HIỆN TẠI -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Số dư khả dụng</span>
                                <div class="mt-2 flex items-baseline gap-2">
                                    <span class="text-3xl sm:text-4xl font-black text-slate-900 font-mono tracking-tight"
                                          x-text="formatMoney(currentBalance)">
                                        {{ number_format($wallet->balance ?? 0, 0, ',', '.') }}đ
                                    </span>
                                    <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                        ● Sẵn sàng chi tiêu
                                    </span>
                                </div>
                            </div>

                            <!-- Trạng thái gói thẻ -->
                            <div class="text-right">
                                @if(Auth::user()->isVip())
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-2xl bg-gradient-to-r from-amber-400 to-amber-500 text-slate-950 font-black text-xs shadow-xs border border-amber-300">
                                        <svg class="w-3.5 h-3.5 text-slate-950 fill-current" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                                        <span>SCHOLAR VIP</span>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-1 font-medium">Miễn cọc 0đ • Mượn tối đa 15 cuốn</div>
                                @else
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-2xl bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200">
                                        <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                        <span>THÀNH VIÊN THƯỜNG</span>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-1 font-medium">Cọc 50.000đ/cuốn • Mượn tối đa 3 cuốn</div>
                                @endif
                            </div>
                        </div>

                        <!-- Dải thông tin phụ -->
                        <div class="mt-5 pt-5 border-t border-slate-100 grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                            <div>
                                <span class="text-slate-400 block text-[11px]">Chủ tài khoản:</span>
                                <span class="font-bold text-slate-800 truncate block">{{ Auth::user()->name }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Mã độc giả:</span>
                                <span class="font-bold text-slate-800 font-mono">#{{ str_pad(Auth::id(), 5, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <span class="text-slate-400 block text-[11px]">Email liên kết:</span>
                                <span class="font-bold text-slate-800 truncate block">{{ Auth::user()->email }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- THẺ 2: CHỌN MỆNH GIÁ NẠP TIỀN -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-black">1</span>
                                <span>Chọn số tiền nạp</span>
                            </h3>
                            <span class="text-xs text-slate-400 font-medium">Đơn vị: VNĐ</span>
                        </div>

                        <!-- Lưới 6 nút chọn nhanh (Grid 3x2) -->
                        <div class="grid grid-cols-3 gap-3 mb-5">
                            <template x-for="val in presets" :key="val">
                                <button type="button"
                                        @click="selectPreset(val)"
                                        class="py-3 px-2 rounded-2xl text-center text-xs sm:text-sm font-bold transition-all duration-150 cursor-pointer"
                                        :class="(amount === val && !customActive) 
                                                ? 'border-2 border-indigo-600 bg-indigo-50/50 text-indigo-700 font-black shadow-xs ring-2 ring-indigo-500/20' 
                                                : 'border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold hover:border-slate-300'">
                                    <div x-text="formatMoney(val)"></div>
                                </button>
                            </template>
                        </div>

                        <!-- Ô nhập số tiền tùy ý -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-700 flex items-center justify-between">
                                <span>Hoặc nhập số tiền tùy ý khác:</span>
                                <span class="text-slate-400 font-normal">Tối thiểu 10.000đ • Tối đa 5.000.000đ</span>
                            </label>
                            <div class="relative">
                                <input type="text"
                                       x-model="customInputDisplay"
                                       @input="onCustomInput($event)"
                                       @focus="customActive = true"
                                       placeholder="Ví dụ: 350.000"
                                       class="w-full px-4 py-3.5 pr-12 rounded-2xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 text-slate-900 font-mono font-bold text-base transition outline-none">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">VNĐ</span>
                            </div>
                        </div>

                        <!-- Gợi ý nhu cầu nạp -->
                        <div class="mt-4 p-3 rounded-2xl bg-amber-50/60 border border-amber-200/60 flex items-center justify-between text-xs text-amber-900">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                                <span>Gợi ý: Cần <strong>49.000đ</strong> để đăng ký Gói Scholar VIP 1 tháng (Miễn cọc 0đ).</span>
                            </div>
                            <button type="button" @click="selectPreset(49000)" class="text-indigo-600 font-bold hover:underline shrink-0 ml-2">Chọn 49k</button>
                        </div>
                    </div>

                    <!-- THẺ 3: PHƯƠNG THỨC THANH TOÁN -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-black">2</span>
                                <span>Phương thức thanh toán</span>
                            </h3>
                            <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Đã chọn mặc định</span>
                            </span>
                        </div>

                        <!-- Phương thức VietQR Napas 24/7 -->
                        <div class="p-4 rounded-2xl border-2 border-indigo-600 bg-indigo-50/20 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3.5">
                                <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 flex items-center justify-center p-1.5 shadow-2xs shrink-0">
                                    <img src="https://img.vietqr.io/image/MB-0987654321-compact2.png" alt="VietQR" class="w-full h-full object-contain">
                                </div>
                                <div>
                                    <div class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                                        <span>Chuyển khoản liên ngân hàng 24/7 qua VietQR Napas</span>
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-black bg-indigo-100 text-indigo-700">TỰ ĐỘNG</span>
                                    </div>
                                    <div class="text-xs text-slate-500 mt-0.5">
                                        Hỗ trợ tất cả ngân hàng Việt Nam & Ví MoMo, ZaloPay, VNPay
                                    </div>
                                </div>
                            </div>
                            <div class="w-5 h-5 rounded-full bg-indigo-600 text-white flex items-center justify-center shrink-0">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Logo các ngân hàng đối tác -->
                        <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-[11px] text-slate-400 font-medium">
                            <span>Đối tác thanh toán: MB Bank • Vietcombank • BIDV • Techcombank • ACB • VPBank</span>
                            <span class="text-emerald-600 font-bold">Bảo mật mã hóa SSL 256-bit</span>
                        </div>
                    </div>

                    <!-- THẺ 4: BẢNG LỊCH SỬ 5 GIAO DỊCH NẠP GẦN NHẤT -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Lịch sử nạp tiền gần nhất</span>
                            </h3>
                            <span class="text-xs text-slate-400">5 giao dịch đối soát</span>
                        </div>

                        @if(isset($transactions) && $transactions->count() > 0)
                            <div class="divide-y divide-slate-100 overflow-hidden">
                                @foreach($transactions as $tx)
                                    <div class="py-3 flex items-center justify-between gap-4 hover:bg-slate-50/50 rounded-xl px-2 transition">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </div>
                                            <div>
                                                <div class="text-xs font-bold text-slate-800">
                                                    {{ $tx->description ?? 'Nạp tiền vào ví điện tử' }}
                                                </div>
                                                <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                                                    {{ $tx->created_at ? $tx->created_at->format('d/m/Y H:i') : '' }} • Mã: {{ $tx->reference_id ?? ('#TX' . $tx->id) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-black text-emerald-600 font-mono">
                                                +{{ number_format($tx->amount, 0, ',', '.') }}đ
                                            </div>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                Thành công
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></div>
                                <div class="text-xs font-medium">Chưa có giao dịch nạp nào trước đây.</div>
                                <div class="text-[11px] text-slate-400 mt-1">Giao dịch nạp thành công sẽ được lưu trữ tự động tại đây.</div>
                            </div>
                        @endif
                    </div>

                </div>

                <!-- ================================================================= -->
                <!-- CỘT PHẢI - KHUNG VIETQR THANH TOÁN TỰ ĐỘNG (5 CỘT)                 -->
                <!-- ================================================================= -->
                <div class="lg:col-span-5">
                    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-sm sticky top-24 text-center space-y-5">
                        
                        <!-- Tiêu đề quét mã -->
                        <div>
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-bold uppercase tracking-wider mb-2">
                                <span>3. Quét mã thanh toán</span>
                            </div>
                            <h3 class="text-lg font-black text-slate-900">MÃ THANH TOÁN VIETQR ĐỘNG</h3>
                            <p class="text-xs text-slate-500 mt-1">
                                Mở ứng dụng ngân hàng hoặc ví điện tử bất kỳ để quét mã
                            </p>
                        </div>

                        <!-- Khung hình ảnh VietQR động -->
                        <div class="relative mx-auto w-64 max-w-full bg-slate-50 p-3 rounded-2xl border-2 border-indigo-100 shadow-inner group">
                            <img :src="qrUrl" 
                                 alt="VietQR Napas 24/7" 
                                 class="w-full h-auto rounded-xl shadow-xs transition-transform duration-300 group-hover:scale-102">
                            
                            <!-- Nhãn đính góc -->
                            <div class="absolute bottom-4 right-4 bg-white/90 backdrop-blur-xs px-2 py-0.5 rounded-md text-[10px] font-black text-indigo-700 shadow-xs border border-indigo-100">
                                NAPAS 24/7
                            </div>
                        </div>

                        <!-- Thông tin chuyển khoản chi tiết (có nút copy 1-chạm) -->
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 text-left space-y-2.5 text-xs">
                            
                            <!-- Ngân hàng -->
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400 font-medium">Ngân hàng:</span>
                                <div class="flex items-center gap-1.5">
                                    <span class="font-bold text-slate-900">Quân Đội (MB Bank)</span>
                                </div>
                            </div>

                            <!-- Số tài khoản -->
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400 font-medium">Số tài khoản:</span>
                                <div class="flex items-center gap-1.5">
                                    <span class="font-black text-slate-900 font-mono text-sm">0987654321</span>
                                    <button type="button" 
                                            @click="copyText('0987654321', 'Số tài khoản')" 
                                            title="Sao chép số tài khoản"
                                            class="p-1 rounded-md bg-white border border-slate-200 hover:bg-slate-100 text-slate-600 transition cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Chủ tài khoản -->
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400 font-medium">Chủ tài khoản:</span>
                                <span class="font-bold text-slate-900">THU VIEN TIEU DOI 2P</span>
                            </div>

                            <!-- Số tiền -->
                            <div class="flex items-center justify-between pt-1 border-t border-slate-200/60">
                                <span class="text-slate-400 font-medium">Số tiền nạp:</span>
                                <div class="flex items-center gap-1.5">
                                    <span class="font-black text-indigo-700 font-mono text-base" x-text="formatMoney(amount)">50.000đ</span>
                                    <button type="button" 
                                            @click="copyText(amount.toString(), 'Số tiền')" 
                                            title="Sao chép số tiền"
                                            class="p-1 rounded-md bg-white border border-slate-200 hover:bg-slate-100 text-slate-600 transition cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Nội dung chuyển khoản -->
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400 font-medium">Nội dung CK:</span>
                                <div class="flex items-center gap-1.5">
                                    <span class="font-black text-amber-700 font-mono text-sm bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                        NAPVI {{ Auth::id() }}
                                    </span>
                                    <button type="button" 
                                            @click="copyText('NAPVI {{ Auth::id() }}', 'Nội dung chuyển khoản')" 
                                            title="Sao chép nội dung"
                                            class="p-1 rounded-md bg-white border border-slate-200 hover:bg-slate-100 text-slate-600 transition cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Hướng dẫn ngắn -->
                        <div class="text-[11px] text-slate-500 leading-relaxed bg-indigo-50/40 p-3 rounded-2xl border border-indigo-100 text-left flex items-start gap-2">
                            <svg class="w-4 h-4 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Mở ứng dụng Mobile Banking bất kỳ để quét mã. Tiền sẽ được cộng tự động vào ví số dư trong vòng <strong>30 giây</strong>.</span>
                        </div>

                        <!-- ========================================================= -->
                        <!-- NÚT XÁC NHẬN THANH TOÁN CHUYÊN NGHIỆP                    -->
                        <!-- ========================================================= -->
                        <div class="pt-2 border-t border-slate-100">
                            <button type="button"
                                    @click="simulateDeposit()"
                                    :disabled="isSimulating"
                                    class="w-full py-3.5 px-6 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm tracking-wide shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-98 disabled:opacity-75">
                                <template x-if="!isSimulating">
                                    <div class="flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span>XÁC NHẬN ĐÃ CHUYỂN KHOẢN</span>
                                    </div>
                                </template>
                                <template x-if="isSimulating">
                                    <div class="flex items-center justify-center gap-2">
                                        <svg class="animate-spin w-5 h-5 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                        </svg>
                                        <span>ĐANG KIỂM TRA GIAO DỊCH...</span>
                                    </div>
                                </template>
                            </button>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <!-- TOAST THÔNG BÁO NỔI -->
        <div x-show="showToast"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             x-cloak
             style="display: none;"
             class="fixed bottom-6 right-6 z-50 max-w-md bg-slate-900 text-white p-4 rounded-2xl shadow-2xl border border-slate-800 flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <div class="text-xs font-semibold" x-text="toastMessage"></div>
        </div>
    </div>

    <!-- SCRIPT XỬ LÝ TRANG NẠP TIỀN -->
    <script>
        function depositPageHandler() {
            return {
                amount: 50000,
                customInputDisplay: '',
                customActive: false,
                currentBalance: {{ (float) ($wallet->balance ?? 0) }},
                userId: {{ Auth::id() ?? 1 }},
                presets: [20000, 50000, 100000, 200000, 500000, 1000000],
                isSimulating: false,
                toastMessage: '',
                showToast: false,

                get qrUrl() {
                    const addInfo = encodeURIComponent('NAPVI_' + this.userId);
                    const accountName = encodeURIComponent('THU VIEN TIEU DOI 2P');
                    return `https://img.vietqr.io/image/MB-0987654321-compact2.png?amount=${this.amount}&addInfo=${addInfo}&accountName=${accountName}`;
                },

                selectPreset(val) {
                    this.amount = val;
                    this.customActive = false;
                    this.customInputDisplay = '';
                },

                onCustomInput(e) {
                    let raw = e.target.value.replace(/\D/g, '');
                    if (raw === '') {
                        this.amount = 50000;
                        this.customInputDisplay = '';
                        return;
                    }
                    let num = parseInt(raw, 10);
                    if (num > 5000000) num = 5000000;
                    this.amount = num;
                    this.customActive = true;
                    this.customInputDisplay = new Intl.NumberFormat('vi-VN').format(num);
                },

                formatMoney(num) {
                    return new Intl.NumberFormat('vi-VN').format(num) + 'đ';
                },

                copyText(text, label) {
                    if (navigator.clipboard && window.isSecureContext) {
                        navigator.clipboard.writeText(text);
                    } else {
                        const textArea = document.createElement("textarea");
                        textArea.value = text;
                        textArea.style.position = "fixed";
                        textArea.style.left = "-999999px";
                        document.body.appendChild(textArea);
                        textArea.focus();
                        textArea.select();
                        document.execCommand('copy');
                        textArea.remove();
                    }
                    this.notify(`Đã sao chép ${label}: ${text}`);
                },

                notify(msg) {
                    this.toastMessage = msg;
                    this.showToast = true;
                    setTimeout(() => {
                        this.showToast = false;
                    }, 3500);
                },

                async simulateDeposit() {
                    if (this.isSimulating) return;
                    this.isSimulating = true;

                    try {
                        const response = await fetch('{{ route('wallet.deposit.process') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ amount: this.amount })
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.currentBalance = data.balance;

                            // Bắn event toàn cục đồng bộ số dư trên Header
                            window.dispatchEvent(new CustomEvent('balance-updated', {
                                detail: {
                                    balance: data.balance,
                                    formatted_balance: data.formatted_balance
                                }
                            }));
                            window.dispatchEvent(new CustomEvent('wallet-updated', {
                                detail: {
                                    balance: data.balance,
                                    formatted_balance: data.formatted_balance
                                }
                            }));

                            this.notify(data.message);

                            // Tự động tải lại trang sau 1.2s để cập nhật bảng lịch sử nạp mới
                            setTimeout(() => {
                                window.location.reload();
                            }, 1200);
                        } else {
                            alert(data.message || 'Có lỗi xảy ra trong quá trình xác thực giao dịch.');
                        }
                    } catch (err) {
                        console.error('Lỗi giao dịch:', err);
                        alert('Lỗi kết nối khi xác thực giao dịch.');
                    } finally {
                        this.isSimulating = false;
                    }
                }
            };
        }
    </script>
</x-app-layout>
