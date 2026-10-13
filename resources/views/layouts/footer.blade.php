<!-- ========================================================================= -->
<!-- FOOTER VINH DANH THƯƠNG HIỆU TIỂU ĐỘI 2P                                  -->
<!-- ========================================================================= -->
<footer class="border-t border-slate-200/80 bg-white text-slate-600 text-sm mt-auto shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            
            <!-- Cột Thông Tin Thương Hiệu -->
            <div class="md:col-span-2">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-black border border-slate-800 p-1 flex items-center justify-center shadow-sm overflow-hidden shrink-0 ring-1 ring-white/10">
                        <img src="{{ asset('images/logo-2p.png') }}" alt="Logo Tiểu Đội 2P" class="w-full h-full object-contain rounded-xl select-none hover:scale-105 transition duration-300">
                    </div>
                    <div>
                        <span class="font-montserrat font-black uppercase text-lg tracking-normal leading-snug py-0.5 block">
                            <span class="text-slate-900">TIỂU ĐỘI</span>
                            <span class="ml-1 text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500 inline-block pr-1.5 font-black">2P</span>
                        </span>
                        <span class="font-inter block text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-1">
                            Hệ Thống Quản Lý Thư Viện Số
                        </span>
                    </div>
                </div>

                <p class="mt-3 text-xs sm:text-sm text-slate-500 leading-relaxed max-w-sm">
                    Nền tảng số hóa thư viện toàn diện, tự động hóa quy trình tra cứu, mượn trả tài liệu chuyên sâu và kết nối độc giả với kho tàng tri thức nhân loại.
                </p>

                <div class="mt-4 flex items-center gap-2 text-xs text-slate-500">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Hệ thống vận hành trực tuyến • Sẵn sàng phục vụ độc giả 24/7</span>
                </div>
            </div>

            <!-- Cột Điều Hướng Nhanh -->
            <div>
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                    Liên Kết Nhanh
                </h4>
                <ul class="space-y-2 text-xs sm:text-sm">
                    <li><a href="{{ url('/') }}#hero" class="text-slate-600 hover:text-indigo-600 transition">Trang Chủ</a></li>
                    <li><a href="{{ route('books.index') }}" class="text-slate-600 hover:text-indigo-600 transition">Kho Sách & Tra Cứu</a></li>
                    <li><a href="{{ url('/') }}#categories-section" class="text-slate-600 hover:text-indigo-600 transition">Chuyên Ngành Thể Loại</a></li>
                    <li><a href="{{ url('/') }}#workflow-section" class="text-slate-600 hover:text-indigo-600 transition">Quy Trình Mượn Sách</a></li>
                </ul>
            </div>

            <!-- Cột Tài Khoản & Hỗ Trợ -->
            <div>
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                    Tài Khoản & Trợ Giúp
                </h4>
                <ul class="space-y-2 text-xs sm:text-sm">
                    @auth
                        <li><a href="{{ route('dashboard') }}" class="text-slate-600 hover:text-indigo-600 transition">Control Panel</a></li>
                        <li><a href="{{ route('borrows.my') }}" class="text-slate-600 hover:text-indigo-600 transition">Sách Đang Mượn Của Tôi</a></li>
                        <li><a href="{{ route('profile.edit') }}" class="text-slate-600 hover:text-indigo-600 transition">Hồ Sơ Cá Nhân</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="text-slate-600 hover:text-indigo-600 transition">Cổng Đăng Nhập</a></li>
                        <li><a href="{{ route('register') }}" class="text-slate-600 hover:text-indigo-600 transition">Đăng Ký Thẻ Độc Giả</a></li>
                        <li><a href="{{ route('books.index') }}" class="text-slate-600 hover:text-indigo-600 transition">Tra Cứu Danh Mục</a></li>
                    @endauth
                    <li class="pt-2 text-[11px] text-slate-500 font-mono">Hỗ trợ: contact@tieudoi2p.vn</li>
                </ul>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- BẢN QUYỀN CHÍNH THỨC CUỐI TRANG                                           -->
        <!-- ========================================================================= -->
        <div class="mt-10 pt-6 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-3">
            <p>&copy; 2026 TIỂU ĐỘI 2P - Hệ thống Quản lý Thư viện Số. Toàn bộ bản quyền được bảo lưu.</p>
            <p class="font-mono text-[11px] text-slate-500 flex flex-wrap items-center justify-center sm:justify-end gap-1">
                <span>Designed & Engineered with</span>
                <svg class="w-3.5 h-3.5 inline-block text-rose-500 fill-current mx-0.5 -mt-0.5" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                <span>by</span>
                <span class="text-indigo-600 font-semibold whitespace-nowrap">Nguyễn Hữu Việt Hưng</span>,
                <span class="text-sky-600 font-semibold whitespace-nowrap">Ngô Hào Đào Sang</span>,
                <span class="text-emerald-600 font-semibold whitespace-nowrap">Phạm Hồ Nguyên Khang</span>
                <span>&</span>
                <span class="text-violet-600 font-semibold whitespace-nowrap">Phạm Nguyễn Minh Hiếu</span>
            </p>
        </div>
    </div>
</footer>
