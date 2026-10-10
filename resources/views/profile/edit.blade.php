<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-['Orbitron'] font-bold text-2xl text-white tracking-wide flex items-center gap-3">
                    <span class="inline-block w-3 h-3 rounded-sm bg-[#00f0ff] shadow-[0_0_12px_#00f0ff] animate-pulse"></span>
                    {{ __('HỒ SƠ CÁ NHÂN & BẢO MẬT') }}
                </h2>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-3 py-1 rounded bg-cyan-950/60 border border-cyan-500/40 text-cyan-300 font-mono text-xs shadow-[0_0_10px_rgba(0,240,255,0.2)]">
                    ĐỊNH DANH: {{ strtoupper(Auth::user()->role_name ?? Auth::user()->role) }}
                </span>
                @if(Auth::user()->title_badge)
                    <span class="px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1.5 {{ Auth::user()->badge_color }}">
                        <span>{{ Auth::user()->badge_icon }}</span>
                        <span>{{ Auth::user()->title_badge }}</span>
                    </span>
                    <span class="px-2.5 py-1 rounded bg-amber-950/60 border border-amber-500/40 text-amber-300 font-mono text-xs">
                        ⚡ {{ number_format(Auth::user()->exp ?? 0) }} EXP
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Card 0: Gói hội viên Thư viện & Hạn mức mượn sách -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm relative overflow-hidden">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-100">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl {{ $user->isVip() ? 'bg-amber-50 text-amber-600 border border-amber-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }} flex items-center justify-center text-2xl font-bold shadow-xs shrink-0">
                            {{ $user->isVip() ? '👑' : '📖' }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">GÓI HỘI VIÊN THƯ VIỆN</h3>
                                @if($user->isVip())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-black bg-gradient-to-r from-amber-500 to-amber-600 text-white shadow-xs">
                                        ⭐ HỌC GIẢ VIP
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        ĐỘC GIẢ TIÊU CHUẨN
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 font-medium mt-1">
                                @if($user->isVip())
                                    Thời hạn thẻ VIP đến: <strong class="text-amber-800 font-bold font-mono">{{ $user->membership_expires_at?->format('H:i - d/m/Y') }}</strong>
                                    @if($user->membership_expires_at)
                                        <span class="text-slate-400 font-normal">({{ now()->diffInDays($user->membership_expires_at, false) > 0 ? 'còn ' . now()->diffInDays($user->membership_expires_at) . ' ngày' : 'sắp hết hạn' }})</span>
                                    @endif
                                @else
                                    Tài khoản tiêu chuẩn miễn phí trọn đời. Nâng cấp VIP để nhân đôi hạn mức sách và miễn cọc ký quỹ.
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" 
                                @click="$dispatch('open-wallet')"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition shadow-2xs cursor-pointer">
                            <span>💳</span>
                            <span>Mở Ví & Nạp tiền</span>
                        </button>
                        @if($user->isVip())
                            <button type="button" 
                                    @click="$dispatch('open-wallet')"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-black text-amber-950 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-600 transition shadow-sm cursor-pointer">
                                <span>⚡</span>
                                <span>Gia Hạn VIP (+6 tháng)</span>
                            </button>
                        @else
                            <button type="button" 
                                    @click="$dispatch('open-wallet')"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-black text-amber-950 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-600 transition shadow-sm cursor-pointer">
                                <span>👑</span>
                                <span>Nâng Cấp VIP (99k)</span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- ĐẶC QUYỀN HỘI VIÊN & HẠN MỨC -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-6">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                        <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Hạn mức mượn tối đa</div>
                        <div class="text-xl font-black text-slate-800 mt-1 flex items-baseline gap-1.5 font-mono">
                            <span>{{ $user->getMaxBorrowBooksCount() }}</span>
                            <span class="text-xs font-medium text-slate-500">cuốn cùng lúc</span>
                        </div>
                        <div class="text-[11px] text-slate-400 mt-1">
                            {{ $user->isVip() ? 'Đã kích hoạt hạn mức tối đa VIP' : 'Nâng cấp VIP để mượn tối đa 5 cuốn' }}
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                        <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Ký quỹ đặt cọc mượn sách</div>
                        <div class="text-xl font-black {{ $user->isVip() ? 'text-emerald-700' : 'text-slate-800' }} mt-1 flex items-baseline gap-1.5 font-mono">
                            <span>{{ $user->isVip() ? '0 đ' : '50.000 đ' }}</span>
                            <span class="text-xs font-medium text-slate-500">/ cuốn</span>
                        </div>
                        <div class="text-[11px] text-slate-400 mt-1">
                            {{ $user->isVip() ? 'Miễn đặt cọc 100% cho Scholar VIP' : 'Hoàn trả 100% ngay khi trả sách đúng hạn' }}
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-emerald-50/70 border border-emerald-200/80">
                        <div class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider">Số dư ví khả dụng</div>
                        <div class="text-xl font-black text-emerald-900 mt-1 font-mono">
                            {{ number_format($user->getOrCreateWallet()->balance, 0, ',', '.') }} đ
                        </div>
                        <div class="text-[11px] text-emerald-700/80 mt-1">
                            Sử dụng tự động cọc sách, nộp phạt & nâng cấp
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 1: Cập nhật thông tin tài khoản -->
            <div class="cyber-card rounded-2xl p-6 sm:p-8 relative overflow-hidden border-t-2 border-t-[#00f0ff]">
                <div class="absolute -top-12 -right-12 w-36 h-36 bg-[#00f0ff]/10 rounded-full blur-2xl pointer-events-none"></div>
                <div class="relative z-10">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Card 2: Đổi mật khẩu đăng nhập -->
            <div class="cyber-card rounded-2xl p-6 sm:p-8 relative overflow-hidden border-t-2 border-t-[#ffb800]">
                <div class="absolute -top-12 -right-12 w-36 h-36 bg-[#ffb800]/10 rounded-full blur-2xl pointer-events-none"></div>
                <div class="relative z-10">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Card 3: Xóa tài khoản vĩnh viễn (Khu vực nguy hiểm) -->
            <div class="cyber-card rounded-2xl p-6 sm:p-8 relative overflow-hidden border border-[#ff007f]/40 border-t-2 border-t-[#ff007f] shadow-[0_0_20px_rgba(255,0,127,0.15)]">
                <div class="absolute -top-12 -right-12 w-36 h-36 bg-[#ff007f]/10 rounded-full blur-2xl pointer-events-none"></div>
                <div class="relative z-10">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
