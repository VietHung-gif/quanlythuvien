<!-- ========================================================================= -->
<!-- FOOTER VINH DANH THƯƠNG HIỆU TIỂU ĐỘI 2P                                  -->
<!-- ========================================================================= -->
<footer class="border-t border-slate-200/80 bg-white text-slate-600 text-sm mt-auto shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            
            <!-- Cột Thông Tin Thương Hiệu -->
            <div class="md:col-span-2">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-lg font-bold text-slate-900 tracking-tight">
                            TIỂU ĐỘI <span class="text-indigo-600 font-extrabold">2P</span>
                        </span>
                        <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
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
            <p class="font-mono text-[11px] text-slate-500">
                Designed & Engineered with <svg class="w-3.5 h-3.5 inline-block text-rose-500 fill-current mx-0.5 -mt-0.5" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg> by <span class="text-indigo-600 font-semibold">Nguyễn Hữu Việt Hưng</span> & <span class="text-sky-600 font-semibold">Ngô Hào Đào Sang</span>
            </p>
        </div>
    </div>
</footer>
