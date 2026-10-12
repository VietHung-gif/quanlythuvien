@auth
<script>
    // Khởi tạo hàm mở modal toàn cục để gọi được từ bất kỳ đâu (Native JS / Alpine / Inline HTML)
    window.openDepositModal = window.openDepositModal || function(amount) {
        window.dispatchEvent(new CustomEvent('open-deposit', { detail: { amount: amount } }));
    };

    function depositModalComponent() {
        return {
            openDeposit: false,
            amount: 50000,
            customAmount: '',
            customActive: false,
            presets: [20000, 50000, 100000, 200000, 500000],
            isGenerating: false,
            isLoading: false,
            isPaid: false,
            copied: false,
            userId: {{ Auth::id() ?? 1 }},
            randomCode: Math.random().toString(36).substring(2, 7).toUpperCase(),
            currentBalance: {{ (float) (Auth::user()->wallet->balance ?? (Auth::user()->getOrCreateWallet()->balance ?? 0)) }},
            showToast: false,
            successMessage: '',

            init() {
                // Đăng ký trực tiếp listener lên window
                window.addEventListener('open-deposit', (e) => {
                    this.openModal(e?.detail?.amount);
                });
                window.addEventListener('open-wallet', (e) => {
                    this.openModal(e?.detail?.amount);
                });
                window.addEventListener('balance-updated', (e) => {
                    if (e.detail && typeof e.detail.balance !== 'undefined') {
                        this.currentBalance = e.detail.balance;
                    }
                });

                // Gán hàm direct trigger vào window
                window.openDepositModal = (amount) => {
                    this.openModal(amount);
                };
            },

            openModal(prefAmount) {
                if (prefAmount && typeof prefAmount === 'number' && prefAmount >= 10000) {
                    this.amount = prefAmount;
                    this.customAmount = '';
                    this.customActive = false;
                }
                this.openDeposit = true;
                this.isPaid = false;
                this.showToast = false;
                this.regenerateCode();
            },

            closeModal() {
                if (!this.isLoading) {
                    this.openDeposit = false;
                }
            },

            get qrImageUrl() {
                const amt = this.amount && this.amount >= 10000 ? this.amount : 50000;
                const addInfo = encodeURIComponent('NAPVI_' + this.userId + '_' + this.randomCode);
                const accountName = encodeURIComponent('THU VIEN TIEU DOI 2P');
                return `https://img.vietqr.io/image/MB-0987654321-compact2.png?amount=${amt}&addInfo=${addInfo}&accountName=${accountName}`;
            },

            get transferCode() {
                return `NAPVI ${this.userId} ${this.randomCode}`;
            },

            selectPreset(val) {
                this.amount = val;
                this.customAmount = '';
                this.customActive = false;
                this.triggerQrRefresh();
            },

            onCustomInput() {
                if (this.customAmount && !isNaN(this.customAmount)) {
                    this.amount = parseInt(this.customAmount, 10);
                }
                this.triggerQrRefresh();
            },

            regenerateCode() {
                this.randomCode = Math.random().toString(36).substring(2, 7).toUpperCase();
            },

            triggerQrRefresh() {
                this.isGenerating = true;
                setTimeout(() => {
                    this.isGenerating = false;
                }, 200);
            },

            copyTransferCode() {
                const text = this.transferCode;
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(() => {
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2000);
                    });
                } else {
                    const textArea = document.createElement("textarea");
                    textArea.value = text;
                    textArea.style.position = "fixed";
                    textArea.style.left = "-999999px";
                    document.body.appendChild(textArea);
                    textArea.focus();
                    textArea.select();
                    try {
                        document.execCommand('copy');
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2000);
                    } catch (err) {
                        console.error('Không thể sao chép', err);
                    }
                    textArea.remove();
                }
            },


            formatCurrency(val) {
                return new Intl.NumberFormat('vi-VN').format(val || 0);
            }
        };
    }
</script>

<div x-data="depositModalComponent()"
     @open-deposit.window="openModal($event.detail?.amount)"
     @open-wallet.window="openModal($event.detail?.amount)"
     @keydown.escape.window="closeModal()"
     class="relative z-50">

    <!-- MODAL BACKDROP & DIALOG WRAPPER -->
    <div x-show="openDeposit"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog"
         aria-modal="true"
         aria-labelledby="deposit-modal-title">

        <div class="flex items-center justify-center min-h-screen px-4 py-6 text-center sm:p-0">

            <!-- BACKDROP NỀN TỐI BLUR NHẸ NHÀNG -->
            <div x-show="openDeposit"
                 @click="closeModal()"
                 x-transition:enter="transition-opacity ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

            <!-- MODAL CARD FINTECH HIỆN ĐẠI -->
            <div x-show="openDeposit"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-3"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-3"
                 class="relative z-10 bg-white rounded-3xl border border-slate-200 shadow-2xl p-6 md:p-8 max-w-2xl w-full text-left my-8 transform transition-all overflow-hidden">

                <!-- SUCCESS TOAST OVERLAY / BANNER THÔNG BÁO THÀNH CÔNG -->
                <div x-show="showToast"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-4"
                     class="absolute top-4 left-4 right-4 z-50 p-4 bg-emerald-600 text-white rounded-2xl shadow-xl flex items-center justify-between gap-3 border border-emerald-400">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wider font-black text-emerald-100">NẠP TIỀN THÀNH CÔNG</div>
                            <div class="text-sm font-bold text-white" x-text="successMessage"></div>
                        </div>
                    </div>
                    <div class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center animate-spin text-xs">
                        ⟳
                    </div>
                </div>

                <!-- 1. HEADER MODAL -->
                <div class="flex items-start justify-between pb-5 border-b border-slate-100 gap-4">
                    <div class="flex items-center gap-3.5">
                        <!-- Biểu tượng ngân hàng / ví số -->
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center shadow-md shrink-0 border border-emerald-400/40">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 id="deposit-modal-title" class="text-base sm:text-lg font-black text-slate-900 uppercase tracking-tight">
                                    CỔNG NẠP TIỀN HỘI VIÊN TIỂU ĐỘI 2P
                                </h3>
                                <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 uppercase tracking-wider">
                                    LIVE 24/7
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 font-medium mt-0.5 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Hệ thống nạp tự động 24/7 qua cổng VietQR Napas 247</span>
                            </p>
                        </div>
                    </div>

                    <!-- Nút đóng "X" góc phải -->
                    <button type="button"
                            @click="closeModal()"
                            class="w-9 h-9 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 flex items-center justify-center font-bold text-sm transition cursor-pointer shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- 2. NỘI DUNG 2 CỘT CÂN ĐỐI (RESPONSIVE GRID) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 items-start">

                    <!-- ================= CỘT TRÁI: CHỌN MỆNH GIÁ NẠP ================= -->
                    <div class="space-y-4">
                        
                        <!-- THẺ SỐ DƯ HIỆN TẠI CỦA ĐỘC GIẢ -->
                        <div class="p-4 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800 text-white rounded-2xl border border-slate-800 shadow-md relative overflow-hidden">
                            <!-- Background glow effect -->
                            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-emerald-500/10 rounded-full blur-xl pointer-events-none"></div>

                            <div class="flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                <span>Số dư khả dụng</span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black tracking-wider {{ (Auth::user()->isVip() ?? false) ? 'bg-amber-400 text-amber-950' : 'bg-slate-700 text-slate-200' }}">
                                    @if(Auth::user()->isVip() ?? false)
                                        <svg class="w-3 h-3 text-amber-950 fill-current" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <span>SCHOLAR VIP</span>
                                    @else
                                        <span>STANDARD</span>
                                    @endif
                                </span>
                            </div>

                            <div class="mt-2 flex items-baseline justify-between">
                                <div class="text-2xl font-black font-mono text-emerald-400 tracking-tight"
                                     x-text="formatCurrency(currentBalance) + ' đ'">
                                    {{ number_format(Auth::user()->wallet->balance ?? (Auth::user()->getOrCreateWallet()->balance ?? 0), 0, ',', '.') }} đ
                                </div>
                                <div class="text-[11px] text-slate-400 font-mono">
                                    ID: <span class="text-slate-200 font-bold">#{{ Auth::id() }}</span>
                                </div>
                            </div>

                            <div class="mt-2 pt-2 border-t border-slate-800 text-[11px] text-slate-400 flex items-center justify-between">
                                <span class="truncate">Độc giả: <strong class="text-slate-200">{{ Auth::user()->name }}</strong></span>
                                <span class="text-emerald-400 font-mono text-[10px] font-bold">Ví hoạt động</span>
                            </div>
                        </div>

                        <!-- CÁC NÚT MỆNH GIÁ CHỌN NHANH (PRESETS) -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-black uppercase tracking-wider text-slate-700">
                                    Mệnh giá nạp nhanh:
                                </label>
                                <span class="text-[11px] text-slate-400 font-medium">Bấm để chọn</span>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                <template x-for="p in presets" :key="p">
                                    <button type="button"
                                            @click="selectPreset(p)"
                                            class="relative py-2.5 px-3 rounded-2xl text-xs font-mono font-bold transition flex items-center justify-between cursor-pointer border-2 active:scale-95 text-left"
                                            :class="amount === p && !customActive
                                                ? 'border-emerald-600 bg-emerald-50 text-emerald-950 font-black shadow-xs ring-2 ring-emerald-500/20'
                                                : 'border-slate-200 bg-slate-50/70 hover:bg-slate-100 hover:border-slate-300 text-slate-700'">
                                        <span x-text="formatCurrency(p) + 'đ'"></span>
                                        <template x-if="amount === p && !customActive">
                                            <span class="w-4 h-4 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0">
                                                <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </span>
                                        </template>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Ô INPUT NHẬP SỐ TIỀN TÙY Ý (TỐI THIỂU 10.000Đ) -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                                    Hoặc nhập số tiền tùy chọn:
                                </label>
                                <span class="text-[10px] text-slate-400">Tối thiểu 10.000đ</span>
                            </div>

                            <div class="relative">
                                <input type="number"
                                       x-model="customAmount"
                                       @focus="customActive = true"
                                       @input="onCustomInput()"
                                       min="10000"
                                       step="10000"
                                       placeholder="Ví dụ: 150000"
                                       class="w-full h-11 bg-slate-50 border-2 rounded-xl px-3.5 pr-14 text-sm font-mono font-black text-slate-900 outline-none transition"
                                       :class="customActive ? 'border-emerald-600 bg-white ring-2 ring-emerald-500/20' : 'border-slate-200 focus:border-emerald-600'">
                                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold font-mono text-slate-400">
                                    VNĐ
                                </span>
                            </div>

                            <template x-if="customActive && amount < 10000">
                                <p class="text-[10px] font-bold text-rose-500 mt-1 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <span>Số tiền nạp tối thiểu là 10.000 VNĐ</span>
                                </p>
                            </template>
                        </div>

                        <!-- HƯỚNG DẪN 3 BƯỚC NGẮN GỌN -->
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-2xl text-[11px] space-y-1.5">
                            <div class="font-extrabold flex items-center gap-1.5 text-slate-800 uppercase tracking-wider text-[10px]">
                                <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>HƯỚNG DẪN 3 BƯỚC NẠP NHANH:</span>
                            </div>
                            <div class="grid grid-cols-3 gap-1 pt-0.5 text-center text-[10px] sm:text-[11px] font-bold text-slate-700">
                                <div class="p-1.5 bg-white rounded-xl border border-slate-200 shadow-2xs">
                                    <div class="text-blue-600 font-black">1. Chọn tiền</div>
                                    <div class="text-[9px] text-slate-400 font-medium mt-0.5">Mệnh giá cần</div>
                                </div>
                                <div class="p-1.5 bg-white rounded-xl border border-slate-200 shadow-2xs">
                                    <div class="text-indigo-600 font-black">2. Quét QR</div>
                                    <div class="text-[9px] text-slate-400 font-medium mt-0.5">App ngân hàng</div>
                                </div>
                                <div class="p-1.5 bg-white rounded-xl border border-slate-200 shadow-2xs">
                                    <div class="text-emerald-600 font-black">3. Vào ví</div>
                                    <div class="text-[9px] text-slate-400 font-medium mt-0.5">Cộng tức thì</div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- ================= CỘT PHẢI: KHUNG HIỂN THỊ VIETQR ================= -->
                    <div class="space-y-4">
                        
                        <!-- KHUNG HIỂN THỊ MÃ QR NỀN TRẮNG PHẲNG, BO VIỀN ĐÔI TINH TẾ -->
                        <div class="rounded-2xl border-2 border-slate-100 p-4 shadow-sm text-center relative bg-slate-50/50">
                            
                            <!-- BADGE HEADER VIETQR & NAPAS 247 -->
                            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200/80 px-1">
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded-md bg-blue-600 text-white font-black text-[10px] tracking-wider">
                                        VietQR
                                    </span>
                                    <span class="text-[10px] font-bold text-slate-500 uppercase">
                                        Napas 247
                                    </span>
                                </div>
                                <div class="flex items-center gap-1 text-[10px] font-mono text-emerald-600 font-bold">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                    <span>TỰ ĐỘNG KHỚP LỆNH</span>
                                </div>
                            </div>

                            <!-- KHUNG QR IMAGE CONTAINER -->
                            <div class="relative inline-block mx-auto bg-white p-2.5 rounded-2xl border-2 border-slate-200/80 shadow-xs">
                                <img :src="qrImageUrl"
                                     alt="Mã VietQR Nạp Ví Độc Giả"
                                     class="w-44 h-44 sm:w-48 sm:h-48 object-contain mx-auto transition-all duration-300"
                                     :class="isGenerating ? 'opacity-30 blur-xs' : 'opacity-100'">

                                <!-- Overlay loading khi đang đổi mệnh giá -->
                                <div x-show="isGenerating"
                                     class="absolute inset-0 flex items-center justify-center bg-white/70 rounded-2xl">
                                    <div class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                        <span class="animate-spin text-emerald-600">⟳</span>
                                        <span>Đang cập nhật QR...</span>
                                    </div>
                                </div>
                            </div>

                            <p class="text-[11px] text-slate-500 mt-2 font-medium">
                                Mở ứng dụng ngân hàng hoặc ví điện tử bất kỳ để quét mã
                            </p>
                        </div>

                        <!-- THÔNG TIN ĐỐI SOÁT CHUYỂN KHOẢN -->
                        <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 space-y-2 text-xs">
                            <div class="flex items-center justify-between py-1 border-b border-slate-200/60">
                                <span class="text-slate-500 font-medium">Ngân hàng thụ hưởng:</span>
                                <span class="font-bold text-slate-900 flex items-center gap-1">
                                    <span class="text-blue-600 font-black">MB Bank</span> (Quân Đội)
                                </span>
                            </div>

                            <div class="flex items-center justify-between py-1 border-b border-slate-200/60">
                                <span class="text-slate-500 font-medium">Chủ tài khoản:</span>
                                <span class="font-extrabold text-slate-900 tracking-tight uppercase">
                                    THƯ VIỆN TIỂU ĐỘI 2P
                                </span>
                            </div>

                            <div class="flex items-center justify-between py-1 border-b border-slate-200/60">
                                <span class="text-slate-500 font-medium">Số tiền nạp:</span>
                                <span class="font-mono font-black text-emerald-600 text-sm"
                                      x-text="formatCurrency(amount) + ' đ'">
                                    50.000 đ
                                </span>
                            </div>

                            <!-- NỘI DUNG CHUYỂN KHOẢN KÈM NÚT COPY 1 CHẠM -->
                            <div class="pt-1 flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="text-[10px] text-slate-500 font-medium uppercase tracking-wider">
                                        Nội dung chuyển khoản:
                                    </div>
                                    <div class="font-mono font-black text-slate-900 text-xs sm:text-sm tracking-tight truncate select-all"
                                         x-text="transferCode"></div>
                                </div>
                                <button type="button"
                                        @click="copyTransferCode()"
                                        title="Bấm để sao chép nội dung"
                                        class="px-2.5 py-1.5 rounded-xl border transition flex items-center gap-1 text-[11px] font-bold cursor-pointer shrink-0 shadow-2xs active:scale-95"
                                        :class="copied ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white hover:bg-slate-100 text-slate-700 border-slate-300'">
                                    <template x-if="copied">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span>Đã chép</span>
                                        </span>
                                    </template>
                                    <template x-if="!copied">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            <span>Sao chép</span>
                                        </span>
                                    </template>
                                </button>
                            </div>
                        </div>

                        <!-- THÔNG BÁO TỰ ĐỘNG XÁC NHẬN -->
                        <div class="pt-1">
                            <div class="p-3 bg-emerald-50 rounded-2xl border border-emerald-200/80 text-center">
                                <div class="flex items-center justify-center gap-1.5 text-emerald-800 text-xs font-bold">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Hệ thống VietQR Napas 24/7 tự động</span>
                                </div>
                                <p class="text-[11px] text-emerald-700 mt-1 leading-relaxed">
                                    Tiền sẽ tự động cộng vào ví độc giả trong vòng 1-3 phút sau khi chuyển khoản thành công.
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
@else
<!-- Hộp thoại nhắc đăng nhập nếu chưa đăng nhập mà ấn Nạp ví -->
<script>
    window.openDepositModal = window.openDepositModal || function() {
        window.dispatchEvent(new CustomEvent('open-deposit'));
    };
</script>
<div x-data="{ openGuestModal: false }"
     @open-deposit.window="openGuestModal = true"
     @open-wallet.window="openGuestModal = true"
     class="relative z-50">
    <div x-show="openGuestModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 py-6 text-center">
            <div @click="openGuestModal = false" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>
            <div class="relative z-10 bg-white rounded-3xl border border-slate-200 shadow-2xl p-6 sm:p-8 max-w-md w-full text-center">
                <div class="w-14 h-14 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-amber-200">
                    <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-black text-slate-900 uppercase">YÊU CẦU ĐĂNG NHẬP</h3>
                <p class="text-xs text-slate-600 mt-2">Vui lòng đăng nhập vào tài khoản độc giả để sử dụng ví điện tử và nạp tiền qua cổng VietQR Napas 247.</p>
                <div class="mt-6 flex items-center gap-3">
                    <button type="button" @click="openGuestModal = false" class="flex-1 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition cursor-pointer">Hủy bỏ</button>
                    <a href="{{ route('login') }}" class="flex-1 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-xs">Đăng nhập ngay</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endauth
