<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-indigo-500"></span>
                    <span>GÓI THÀNH VIÊN & ĐẶC QUYỀN ĐỌC SÁCH</span>
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Hệ thống hội viên phân cấp tối ưu học đường: Tăng hạn mức mượn, miễn cọc 0đ & giao sách tận nơi
                </p>
            </div>

            <!-- SỐ DƯ VÍ NHANH -->
            @auth
                <div class="flex items-center gap-3">
                    <a href="{{ route('wallet.deposit') }}"
                       title="Bấm để vào trang nạp ví VietQR"
                       class="px-4 py-2 bg-white hover:bg-emerald-50/50 border border-slate-200 hover:border-emerald-300 rounded-2xl shadow-2xs text-xs flex items-center gap-2 cursor-pointer transition">
                        <span class="text-slate-400 font-bold uppercase text-[10px]">Ví số dư:</span>
                        <span class="font-mono font-black text-emerald-600 text-sm user-wallet-balance">
                            {{ number_format(Auth::user()->getOrCreateWallet()->balance, 0, ',', '.') }} đ
                        </span>
                    </a>
                    <a href="{{ route('wallet.deposit') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-black text-xs uppercase tracking-wider transition shadow-sm cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>+ NẠP VÍ</span>
                    </a>
                </div>
            @endauth
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50 min-h-screen" x-data="membershipHandler()">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

            <!-- THÔNG BÁO FLASH -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-2 border-emerald-300 rounded-2xl flex items-center gap-3 text-emerald-900 font-bold text-sm shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-rose-50 border-2 border-rose-300 rounded-2xl flex items-center gap-3 text-rose-900 font-bold text-sm shadow-xs">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- BANNER TRẠNG THÁI HIỆN TẠI NẾU LÀ VIP -->
            @auth
                @if(Auth::user()->isVip())
                    <div class="p-6 bg-gradient-to-r from-indigo-900 via-indigo-800 to-purple-900 text-white rounded-3xl shadow-lg border border-indigo-700/60 flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
                        <div class="space-y-1.5 relative z-10">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-400 text-amber-950 text-xs font-black uppercase tracking-wider shadow-2xs">
                                <svg class="w-3.5 h-3.5 text-amber-950 fill-current" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                                <span>TÀI KHOẢN ĐANG KÍCH HOẠT SCHOLAR VIP</span>
                            </div>
                            <h3 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                                Xin chào, {{ Auth::user()->name }}! Bạn đang sở hữu đặc quyền tối đa.
                            </h3>
                            <p class="text-xs sm:text-sm text-indigo-200">
                                Thời hạn đặc quyền đến: <strong class="text-amber-300 font-mono">{{ Auth::user()->vip_expires_at ? Auth::user()->vip_expires_at->format('d/m/Y') : (Auth::user()->membership_expires_at ? Auth::user()->membership_expires_at->format('d/m/Y') : 'Vô thời hạn') }}</strong>
                                • Hạn mức: <strong class="text-white">15 cuốn</strong> • Thời hạn: <strong class="text-white">30 ngày</strong> • Cọc: <strong class="text-white">0đ</strong>
                            </p>
                        </div>
                        <div class="flex items-center gap-3 relative z-10">
                            <button type="button" 
                                    @click="openUpgradeModal('monthly')"
                                    class="px-5 py-3 rounded-2xl bg-amber-400 hover:bg-amber-300 text-amber-950 font-black text-xs uppercase tracking-wider shadow-sm transition active:scale-95 cursor-pointer whitespace-nowrap">
                                + Gia Hạn Tháng (49k)
                            </button>
                            <button type="button" 
                                    @click="openUpgradeModal('term')"
                                    class="px-5 py-3 rounded-2xl bg-white/10 hover:bg-white/20 text-white border border-white/20 font-black text-xs uppercase tracking-wider transition active:scale-95 cursor-pointer whitespace-nowrap">
                                + Gia Hạn Kỳ (129k)
                            </button>
                        </div>
                    </div>
                @endif
            @endauth

            <!-- TIÊU ĐỀ SECTION SO SÁNH -->
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <h3 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    BẢNG ĐẶC QUYỀN HỘI VIÊN THƯ VIỆN
                </h3>
                <p class="text-sm text-slate-500">
                    Lựa chọn gói dịch vụ phù hợp với nhu cầu nghiên cứu, học tập và đọc sách của bạn
                </p>
            </div>

            <!-- ========================================================================= -->
            <!-- BỐ CỤC 2 THẺ SO SÁNH SANG TRỌNG (MODERN MINIMALIST LIGHT THEME)            -->
            <!-- ========================================================================= -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch">
                
                <!-- ===================================================================== -->
                <!-- THẺ 1: GÓI TIÊU CHUẨN (STANDARD MEMBER - MẶC ĐỊNH)                     -->
                <!-- Nền trắng, viền xám nhạt border border-slate-200                      -->
                <!-- ===================================================================== -->
                <div class="bg-white border border-slate-200 rounded-3xl p-7 sm:p-9 shadow-sm flex flex-col justify-between hover:shadow-md transition relative">
                    <div class="space-y-6">
                        
                        <!-- Header Thẻ -->
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-[11px] font-black uppercase tracking-wider">
                                    Gói Mặc Định
                                </span>
                                <h4 class="text-2xl font-black text-slate-900 mt-2 tracking-tight">
                                    Standard Member
                                </h4>
                                <p class="text-xs text-slate-500 mt-1">
                                    Dành cho bạn đọc có nhu cầu mượn sách cơ bản tại trường
                                </p>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                        </div>

                        <!-- Mức phí -->
                        <div class="pt-4 border-t border-slate-100">
                            <div class="flex items-baseline gap-2">
                                <span class="text-4xl font-black font-mono text-slate-900">0 đ</span>
                                <span class="text-xs font-bold text-slate-400">/ Vĩnh viễn (Miễn phí)</span>
                            </div>
                        </div>

                        <!-- Danh sách quyền lợi chi tiết -->
                        <div class="space-y-3.5 pt-4 border-t border-slate-100 text-xs">
                            <div class="text-[11px] font-black uppercase tracking-wider text-slate-400">
                                Quy định quyền lợi:
                            </div>

                            <div class="flex items-start gap-3 text-slate-700">
                                <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center shrink-0"><svg class="w-3 h-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                                <div>
                                    Số lượng mượn tối đa: <strong class="text-slate-900 font-bold">3 cuốn / lần</strong>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 text-slate-700">
                                <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center shrink-0"><svg class="w-3 h-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                                <div>
                                    Thời hạn mượn: <strong class="text-slate-900 font-bold">14 ngày</strong> (gia hạn tối đa 1 lần)
                                </div>
                            </div>

                            <div class="flex items-start gap-3 text-slate-700">
                                <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 font-bold">!</span>
                                <div>
                                    Đặt cọc sách: <strong class="text-amber-800 font-bold">Bắt buộc 50.000đ / cuốn</strong>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 text-slate-700">
                                <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center shrink-0"><svg class="w-3 h-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                                <div>
                                    Phương thức nhận sách: <strong class="text-slate-900 font-bold">Quầy tiếp nhận thông thường</strong>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 text-slate-700">
                                <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center shrink-0"><svg class="w-3 h-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                                <div>
                                    Tích lũy công lực: <strong class="text-slate-900 font-bold">Cơ bản (x1 EXP)</strong>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 text-slate-700">
                                <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center shrink-0"><svg class="w-3 h-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                                <div>
                                    Trợ lý AI Chatbot: <strong class="text-slate-900 font-bold">10 tin nhắn / ngày</strong>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Footer Nút trạng thái -->
                    <div class="pt-8">
                        @auth
                            @if(!Auth::user()->isVip())
                                <button type="button" 
                                        disabled
                                        class="w-full py-3.5 px-4 rounded-2xl bg-slate-100 text-slate-500 font-black text-xs uppercase tracking-wider text-center cursor-not-allowed">
                                    Gói Hiện Tại Của Bạn
                                </button>
                            @else
                                <div class="text-center text-xs text-slate-400 font-medium">
                                    Đang được nâng cấp lên Scholar VIP
                                </div>
                            @endif
                        @else
                            <a href="{{ route('register') }}" 
                               class="block w-full py-3.5 px-4 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-black text-xs uppercase tracking-wider text-center transition">
                                Đăng Ký Tài Khoản Miễn Phí
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- ===================================================================== -->
                <!-- THẺ 2: GÓI SCHOLAR VIP (49.000đ/tháng hoặc 129.000đ/kỳ)               -->
                <!-- Nền trắng sáng viền xanh/tím thanh lịch border-2 border-indigo-500     -->
                <!-- shadow-md relative, có huy hiệu [GÓI KHUYÊN DÙNG]                     -->
                <!-- ===================================================================== -->
                <div class="bg-white border-2 border-indigo-500 rounded-3xl p-7 sm:p-9 shadow-md relative flex flex-col justify-between hover:shadow-xl transition ring-4 ring-indigo-50">
                    
                    <!-- HUY HIỆU NỔI BẬT GÓI KHUYÊN DÙNG -->
                    <div class="absolute -top-3.5 right-8">
                        <span class="px-4 py-1.5 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-xs font-black uppercase tracking-wider shadow-sm flex items-center gap-1.5">
                            <span>⭐</span>
                            <span>GÓI KHUYÊN DÙNG</span>
                        </span>
                    </div>

                    <div class="space-y-6">
                        
                        <!-- Header Thẻ -->
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="px-3 py-1 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 text-[11px] font-black uppercase tracking-wider">
                                    Đặc Quyền Toàn Diện
                                </span>
                                <h4 class="text-2xl font-black text-indigo-950 mt-2 tracking-tight flex items-center gap-2">
                                    <span>Scholar VIP Membership</span>
                                    <svg class="w-5 h-5 text-amber-500 fill-current" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                                </h4>
                                <p class="text-xs text-slate-500 mt-1">
                                    Giải pháp mượn sách số hóa cao cấp cho giảng viên, sinh viên & mọt sách
                                 </p>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 border border-indigo-100">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                            </div>
                        </div>

                        <!-- 2 Mức giá linh hoạt -->
                        <div class="pt-4 border-t border-slate-100 grid grid-cols-2 gap-3">
                            <div class="p-3 bg-indigo-50/60 border border-indigo-100 rounded-2xl">
                                <div class="text-[10px] font-black uppercase text-indigo-600 tracking-wider">Gói Tháng</div>
                                <div class="text-xl sm:text-2xl font-black font-mono text-slate-900 mt-0.5">49.000 đ</div>
                                <div class="text-[10px] text-slate-500 font-medium">/ 30 ngày đặc quyền</div>
                            </div>
                            <div class="p-3 bg-purple-50/60 border border-purple-200 rounded-2xl relative overflow-hidden">
                                <span class="absolute top-1 right-2 text-[9px] font-black text-purple-700 bg-purple-200/80 px-1.5 py-0.5 rounded">Tiết kiệm 18k</span>
                                <div class="text-[10px] font-black uppercase text-purple-700 tracking-wider">Gói Kỳ (3 Tháng)</div>
                                <div class="text-xl sm:text-2xl font-black font-mono text-slate-900 mt-0.5">129.000 đ</div>
                                <div class="text-[10px] text-slate-500 font-medium">/ 90 ngày (1 học kỳ)</div>
                            </div>
                        </div>

                        <!-- Danh sách tick xanh đặc quyền VIP -->
                        <div class="space-y-3.5 pt-4 border-t border-slate-100 text-xs">
                            <div class="text-[11px] font-black uppercase tracking-wider text-indigo-600">
                                Đặc quyền ưu đãi độc quyền:
                            </div>

                            <div class="flex items-start gap-3 text-slate-800">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0"><svg class="w-3 h-3 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                                <div>
                                    Hạn mức mượn mở rộng: <strong class="text-indigo-900 font-black">Tối đa 15 cuốn cùng lúc</strong> (gấp 5 lần thường)
                                </div>
                            </div>

                            <div class="flex items-start gap-3 text-slate-800">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0"><svg class="w-3 h-3 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                                <div>
                                    Thời hạn giữ sách: <strong class="text-indigo-900 font-black">30 ngày</strong> (cho phép gia hạn trực tuyến 2 lần)
                                </div>
                            </div>

                            <div class="flex items-start gap-3 text-slate-800">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0"><svg class="w-3 h-3 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                                <div>
                                    Miễn phí đặt cọc: <strong class="text-emerald-700 font-black">0đ tiền cọc sách</strong> (Không bị giữ vốn)
                                </div>
                            </div>

                            <div class="flex items-start gap-3 text-slate-800">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0"><svg class="w-3 h-3 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                                <div>
                                    Hình thức nhận sách: Lựa chọn <strong class="text-indigo-900 font-black">"Làn ưu tiên tại Quầy"</strong> HOẶC <strong class="text-indigo-900 font-black">"Giao sách tận nhà / KTX"</strong>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 text-slate-800">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0"><svg class="w-3 h-3 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                                <div>
                                    Buff chỉ số EXP: <strong class="text-amber-600 font-black">Nhân đôi EXP (x2 EXP)</strong> khi trả đúng hạn & làm nhiệm vụ
                                </div>
                            </div>

                            <div class="flex items-start gap-3 text-slate-800">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0"><svg class="w-3 h-3 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                                <div>
                                    Trợ lý AI Chatbot: <strong class="text-indigo-900 font-black">Không giới hạn lượt hỏi</strong> 24/7 không lo hết quota
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Footer Nút nâng cấp -->
                    <div class="pt-8 space-y-2.5">
                        @auth
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <button type="button" 
                                        @click="openUpgradeModal('monthly')"
                                        class="py-3.5 px-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 active:scale-98 text-white font-black text-xs uppercase tracking-wider text-center transition shadow-sm cursor-pointer">
                                    Nâng Cấp Gói Tháng (49k)
                                </button>
                                <button type="button" 
                                        @click="openUpgradeModal('term')"
                                        class="py-3.5 px-3 rounded-2xl bg-purple-600 hover:bg-purple-700 active:scale-98 text-white font-black text-xs uppercase tracking-wider text-center transition shadow-sm cursor-pointer">
                                    Nâng Cấp Gói Kỳ (129k)
                                </button>
                            </div>
                            <p class="text-center text-[11px] text-slate-400">
                                Trừ trực tiếp số dư ví điện tử hoặc nạp nhanh qua VietQR
                            </p>
                        @else
                            <a href="{{ route('login') }}" 
                               class="block w-full py-4 px-4 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs uppercase tracking-wider text-center transition shadow-sm">
                                Đăng Nhập Để Nâng Cấp Scholar VIP
                            </a>
                        @endauth
                    </div>
                </div>

            </div>

            <!-- ========================================================================= -->
            <!-- BẢNG HỎI ĐÁP QUY ĐỊNH (FAQ SECTION)                                       -->
            <!-- ========================================================================= -->
            <div class="bg-white border border-slate-200 rounded-3xl p-7 sm:p-9 shadow-xs space-y-6">
                <h4 class="text-lg font-black text-slate-900 uppercase tracking-tight flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    <span>CÁC CÂU HỎI THƯỜNG GẶP VỀ GÓI SCHOLAR VIP</span>
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs text-slate-600">
                    <div class="space-y-1">
                        <strong class="text-slate-900 text-sm block">1. Tôi nhận sách tại nhà / KTX như thế nào?</strong>
                        <p>Khi thêm sách vào giỏ và tiến hành đăng ký mượn, tài khoản Scholar VIP sẽ xuất hiện tùy chọn "Giao sách tận nhà / KTX". Bạn chỉ cần nhập địa chỉ phòng/nhà và số điện thoại, thủ thư sẽ đóng gói và liên hệ chuyển sách tận nơi cho bạn.</p>
                    </div>
                    <div class="space-y-1">
                        <strong class="text-slate-900 text-sm block">2. Đặc quyền miễn phí đặt cọc 0đ hoạt động ra sao?</strong>
                        <p>Thông thường bạn đọc phải đặt cọc 50.000đ/cuốn (giữ cọc cho tới khi trả sách). Đối với Scholar VIP, hệ thống hoàn toàn miễn khoản cọc này, giúp bạn có thể mượn tối đa 15 cuốn sách mà không cần giữ vốn.</p>
                    </div>
                    <div class="space-y-1">
                        <strong class="text-slate-900 text-sm block">3. Nếu tôi nâng cấp khi gói VIP cũ chưa hết hạn?</strong>
                        <p>Thời hạn mới sẽ được cộng dồn tiếp tục vào ngày hết hạn hiện tại của bạn. Bạn không bao giờ bị mất bất kỳ ngày VIP nào đã thanh toán trước đó.</p>
                    </div>
                    <div class="space-y-1">
                        <strong class="text-slate-900 text-sm block">4. Doanh thu từ các gói VIP được sử dụng vào việc gì?</strong>
                        <p>100% doanh thu gói thành viên được chuyển thẳng vào Quỹ hạ tầng Server Cloud & AI để thanh toán chi phí máy chủ, đường truyền và bổ sung đầu sách mới định kỳ phục vụ cộng đồng.</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- TOAST NOTIFICATION THÀNH CÔNG & LỖI (TAILWIND CSS)                       -->
        <!-- ========================================================================= -->
        <div id="toast-success" class="fixed top-5 right-5 hidden z-50 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl shadow-xl flex items-center gap-3 transition-all duration-300 max-w-md pointer-events-auto">
            <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0 shadow-xs">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="flex-1">
                <p class="font-bold text-sm text-emerald-950">Nâng cấp VIP thành công!</p>
                <p id="toast-success-message" class="text-xs text-emerald-700 mt-0.5 leading-relaxed"></p>
            </div>
        </div>

        <div id="toast-error" class="fixed top-5 right-5 hidden z-50 bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl shadow-xl flex items-center gap-3 transition-all duration-300 max-w-md pointer-events-auto">
            <div class="w-9 h-9 rounded-xl bg-rose-100 flex items-center justify-center shrink-0 shadow-xs">
                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div class="flex-1">
                <p class="font-bold text-sm text-rose-950">Thông báo</p>
                <p id="toast-error-message" class="text-xs text-rose-700 mt-0.5 leading-relaxed"></p>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL XÁC NHẬN NÂNG CẤP SCHOLAR VIP                                       -->
        <!-- ========================================================================= -->
        <div x-show="modalOpen" 
             x-cloak
             style="display: none;"
             class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 text-center">
                <div x-show="modalOpen"
                     @click="modalOpen = false"
                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

                <div x-show="modalOpen"
                     class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full text-left relative z-10 border border-slate-200 shadow-2xl my-8">
                    
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                <svg class="w-5 h-5 text-amber-500 fill-current" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-black text-slate-900 uppercase">XÁC NHẬN NÂNG CẤP VIP</h3>
                                <p class="text-[11px] text-slate-500 font-medium" x-text="selectedPlanTitle"></p>
                            </div>
                        </div>
                        <button type="button" @click="modalOpen = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition cursor-pointer">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="mt-4 space-y-3">
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-2 text-xs">
                            <div class="flex justify-between text-slate-600">
                                <span>Gói đăng ký:</span>
                                <strong class="text-slate-900" x-text="selectedPlanTitle"></strong>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>Mức phí:</span>
                                <strong class="text-indigo-600 font-mono text-sm" x-text="formatCurrency(selectedFee) + ' VNĐ'"></strong>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>Số dư ví hiện tại:</span>
                                <strong class="font-mono" :class="currentBalance >= selectedFee ? 'text-emerald-600' : 'text-rose-600'" x-text="formatCurrency(currentBalance) + ' VNĐ'"></strong>
                            </div>
                        </div>

                        <!-- CẢNH BÁO THIẾU TIỀN VÍ KÈM NẠP NHANH -->
                        <template x-if="currentBalance < selectedFee">
                            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900 space-y-2">
                                <div class="font-bold flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <span>Số dư ví không đủ thanh toán</span>
                                </div>
                                <p class="text-[11px] text-slate-600">
                                    Bạn cần nạp thêm tối thiểu <strong class="font-mono text-amber-950" x-text="formatCurrency(selectedFee - currentBalance) + ' VNĐ'"></strong> vào ví để tiếp tục.
                                </p>
                                <a href="{{ route('wallet.deposit') }}"
                                   class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-xl text-xs uppercase tracking-wider transition cursor-pointer shadow-sm flex items-center justify-center gap-1.5 active:scale-95">
                                    <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    <span>Nạp Ví Qua VietQR Ngay</span>
                                </a>
                            </div>
                        </template>

                    </div>

                    <div class="mt-6 flex items-center gap-3">
                        <button type="button" 
                                @click="modalOpen = false"
                                class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase rounded-xl transition cursor-pointer">
                            Hủy bỏ
                        </button>
                        <button type="button" 
                                @click="confirmUpgrade()"
                                :disabled="currentBalance < selectedFee || isLoading"
                                class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white font-black text-xs uppercase rounded-xl transition cursor-pointer shadow-sm">
                            <span x-text="isLoading ? 'Đang xử lý...' : 'Xác Nhận Trừ Ví'"></span>
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- ALPINE LOGIC CHO TRANG MEMBERSHIP -->
    <script>
        function showSuccessToast(message) {
            const toast = document.getElementById('toast-success');
            const msgEl = document.getElementById('toast-success-message');
            if (toast && msgEl) {
                msgEl.textContent = message;
                toast.classList.remove('hidden');
                toast.classList.remove('opacity-0');
                toast.classList.add('opacity-100');
                setTimeout(() => {
                    toast.classList.remove('opacity-100');
                    toast.classList.add('opacity-0');
                    setTimeout(() => toast.classList.add('hidden'), 300);
                }, 4000);
            }
            if (typeof window.showToastNotification === 'function') {
                window.showToastNotification('Nâng cấp VIP thành công!', message, true);
            }
        }

        function showErrorToast(message) {
            const toast = document.getElementById('toast-error');
            const msgEl = document.getElementById('toast-error-message');
            if (toast && msgEl) {
                msgEl.textContent = message;
                toast.classList.remove('hidden');
                toast.classList.remove('opacity-0');
                toast.classList.add('opacity-100');
                setTimeout(() => {
                    toast.classList.remove('opacity-100');
                    toast.classList.add('opacity-0');
                    setTimeout(() => toast.classList.add('hidden'), 300);
                }, 4000);
            }
            if (typeof window.showToastNotification === 'function') {
                window.showToastNotification('Thông báo', message, false);
            }
        }

        function membershipHandler() {
            return {
                modalOpen: false,
                selectedPlan: 'monthly',
                selectedPlanTitle: 'Gói Tháng (49.000đ / 30 ngày)',
                selectedFee: 49000,
                currentBalance: {{ Auth::check() ? Auth::user()->getOrCreateWallet()->balance : 0 }},
                isLoading: false,

                formatCurrency(num) {
                    return new Intl.NumberFormat('vi-VN').format(num || 0);
                },

                openUpgradeModal(plan) {
                    this.selectedPlan = plan;
                    if (plan === 'term') {
                        this.selectedPlanTitle = 'Gói Kỳ (129.000đ / 3 tháng)';
                        this.selectedFee = 129000;
                    } else {
                        this.selectedPlanTitle = 'Gói Tháng (49.000đ / 30 ngày)';
                        this.selectedFee = 49000;
                    }
                    this.modalOpen = true;
                },

                async confirmUpgrade() {
                    if (this.currentBalance < this.selectedFee) {
                        showErrorToast('Số dư ví không đủ. Vui lòng nạp thêm tiền!');
                        return;
                    }

                    this.isLoading = true;
                    try {
                        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        const res = await fetch('{{ route('membership.upgrade') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                            },
                            body: JSON.stringify({
                                plan: this.selectedPlan,
                            })
                        });

                        const data = await res.json();
                        if (data.success) {
                            // 1. Đóng Modal ngay lập tức và reset trạng thái nút
                            this.modalOpen = false;
                            this.isLoading = false;

                            // 2. Cập nhật số dư ví mới nếu có
                            if (data.balance !== undefined) {
                                this.currentBalance = data.balance;
                            }

                            // 3. Hiển thị Toast thông báo thành công
                            showSuccessToast(data.message || 'Chúc mừng bạn đã nâng cấp thành công lên SCHOLAR VIP!');

                            // 4. Tự động reload sau 2 giây để giao diện cập nhật đặc quyền VIP mượt mà
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            this.isLoading = false;
                            showErrorToast(data.message || 'Lỗi nâng cấp gói hội viên');
                        }
                    } catch (e) {
                        this.isLoading = false;
                        showErrorToast('Lỗi kết nối máy chủ. Vui lòng thử lại!');
                    } finally {
                        this.isLoading = false;
                    }
                }
            };
        }
    </script>
</x-app-layout>
