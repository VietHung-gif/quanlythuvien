<!-- ========================================================================= -->
<!-- FOOTER VINH DANH THƯƠNG HIỆU & ĐỘI NGŨ PHÁT TRIỂN TIỂU ĐỘI 2P            -->
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
        <!-- KHU VỰC VINH DANH ĐỘI NGŨ PHÁT TRIỂN / CORE DEVELOPERS                    -->
        <!-- ========================================================================= -->
        <div class="mt-12 pt-8 border-t border-slate-200/80">
            <div class="flex flex-col items-center mb-8 text-center">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-50 border border-indigo-200/80 text-indigo-700 text-xs font-mono font-semibold uppercase tracking-wider mb-2.5 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
                    <span>ĐỘI NGŨ PHÁT TRIỂN / CORE DEVELOPERS</span>
                </div>
                <h3 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">
                    Kỹ Sư Kiến Trúc & Thiết Kế Nền Tảng TIỂU ĐỘI 2P
                </h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-xl">
                    Được phát triển với niềm đam mê công nghệ số, chuẩn mực kiến trúc Laravel hiện đại và trải nghiệm người dùng tối ưu.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 max-w-4xl mx-auto">
                
                <!-- Developer 1: Nguyễn Hữu Việt Hưng -->
                <div class="relative group rounded-2xl p-5 bg-slate-50/80 hover:bg-white border border-slate-200/80 hover:border-indigo-300 transition-all duration-300 shadow-sm hover:shadow-md flex items-center gap-4">
                    <!-- Avatar Badge -->
                    <div class="w-14 h-14 rounded-2xl bg-indigo-100 border border-indigo-200 flex items-center justify-center text-indigo-700 font-extrabold text-lg shrink-0 shadow-inner group-hover:scale-105 transition-all">
                        VH
                    </div>

                    <!-- Details -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h4 class="text-base font-bold text-slate-900 tracking-tight group-hover:text-indigo-600 transition">
                                Nguyễn Hữu Việt Hưng
                            </h4>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                Lead Developer
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5 font-medium">
                            Lead Developer / Full-Stack Specialist
                        </p>

                        <!-- Tech tags -->
                        <div class="mt-2.5 flex items-center gap-2 flex-wrap text-xs text-slate-500">
                            <span class="inline-flex items-center gap-1 text-[11px] font-mono text-slate-700 bg-white px-2 py-0.5 rounded border border-slate-200 shadow-2xs">
                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                </svg>
                                Laravel 11 • REST API • DB Architect
                            </span>
                            <span class="inline-flex items-center gap-1 text-[11px] font-mono text-slate-600 hover:text-indigo-600 bg-white px-2 py-0.5 rounded border border-slate-200 transition shadow-2xs">
                                <svg class="w-3 h-3 text-slate-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                                </svg>
                                GitHub
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Developer 2: Ngô Hào Đào Sang -->
                <div class="relative group rounded-2xl p-5 bg-slate-50/80 hover:bg-white border border-slate-200/80 hover:border-indigo-300 transition-all duration-300 shadow-sm hover:shadow-md flex items-center gap-4">
                    <!-- Avatar Badge -->
                    <div class="w-14 h-14 rounded-2xl bg-sky-100 border border-sky-200 flex items-center justify-center text-sky-700 font-extrabold text-lg shrink-0 shadow-inner group-hover:scale-105 transition-all">
                        DS
                    </div>

                    <!-- Details -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h4 class="text-base font-bold text-slate-900 tracking-tight group-hover:text-indigo-600 transition">
                                Ngô Hào Đào Sang
                            </h4>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                UI-UX Specialist
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5 font-medium">
                            Developer / UI-UX Specialist
                        </p>

                        <!-- Tech tags -->
                        <div class="mt-2.5 flex items-center gap-2 flex-wrap text-xs text-slate-500">
                            <span class="inline-flex items-center gap-1 text-[11px] font-mono text-slate-700 bg-white px-2 py-0.5 rounded border border-slate-200 shadow-2xs">
                                <svg class="w-3.5 h-3.5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                                </svg>
                                Tailwind CSS • UI/UX • Micro-interactions
                            </span>
                            <span class="inline-flex items-center gap-1 text-[11px] font-mono text-slate-600 hover:text-indigo-600 bg-white px-2 py-0.5 rounded border border-slate-200 transition shadow-2xs">
                                <svg class="w-3 h-3 text-slate-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                                </svg>
                                GitHub
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- BẢN QUYỀN CHÍNH THỨC CUỐI TRANG                                           -->
        <!-- ========================================================================= -->
        <div class="mt-10 pt-6 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-3">
            <p>&copy; 2026 TIỂU ĐỘI 2P - Hệ thống Quản lý Thư viện Số. Toàn bộ bản quyền được bảo lưu.</p>
            <p class="font-mono text-[11px] text-slate-500">
                Designed & Engineered with <span class="text-rose-500">♥</span> by <span class="text-indigo-600 font-semibold">Nguyễn Hữu Việt Hưng</span> & <span class="text-sky-600 font-semibold">Ngô Hào Đào Sang</span>
            </p>
        </div>
    </div>
</footer>
