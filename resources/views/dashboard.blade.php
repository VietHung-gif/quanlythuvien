<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 tracking-tight flex items-center gap-2.5">
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    {{ __('BẢNG ĐIỀU KHIỂN TRUNG TÂM') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Tổng quan quản trị và trung tâm điều phối dữ liệu thư viện</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 rounded-full bg-indigo-50 border border-indigo-200/80 text-indigo-700 text-xs font-semibold shadow-2xs">
                    VẬN HÀNH TRỰC TUYẾN • {{ strtoupper(Auth::user()->role_name ?? Auth::user()->role) }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Thẻ chào mừng Modern Light Card -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 relative overflow-hidden border border-slate-200/80 shadow-sm">
                <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-gradient-to-br from-indigo-500/5 to-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10">
                    <div class="text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1.5">
                        @if(Auth::user()->role === 'admin')
                            [ QUẢN TRỊ VIÊN HỆ THỐNG // {{ strtoupper(Auth::user()->name) }} ]
                        @elseif(Auth::user()->role === 'librarian')
                            [ THỦ THƯ VẬN HÀNH // {{ strtoupper(Auth::user()->name) }} ]
                        @else
                            [ ĐỘC GIẢ THÀNH VIÊN // {{ strtoupper(Auth::user()->name) }} ]
                        @endif
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
                        HỆ THỐNG QUẢN LÝ THƯ VIỆN SỐ <span class="text-indigo-600">TIỂU ĐỘI 2P</span>
                    </h1>
                    <p class="mt-2 text-sm sm:text-base text-slate-600 max-w-2xl leading-relaxed">
                        @if(in_array(Auth::user()->role, ['admin', 'librarian']))
                            Nền tảng kiểm soát và quản lý danh mục sách, thể loại và lưu vết mượn/trả sách theo thời gian thực với chuẩn mực công thái học tối ưu.
                        @else
                            Chào mừng bạn đến với thư viện điện tử TIỂU ĐỘI 2P. Tra cứu kho sách trực tuyến, theo dõi tiến độ mượn trả và quản lý tài khoản cá nhân.
                        @endif
                    </p>

                    <!-- Phím tắt nhanh (Quick Actions) -->
                    <div class="mt-6 flex flex-wrap gap-2.5">
                        @if(in_array(Auth::user()->role, ['admin', 'librarian']))
                            <a href="{{ route('books.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-xs tracking-wider uppercase text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                KHO SÁCH
                            </a>

                            <a href="{{ route('borrows.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-xs tracking-wider uppercase text-white bg-slate-900 hover:bg-slate-800 shadow-sm transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                + LẬP PHIẾU MƯỢN
                            </a>

                            <a href="{{ route('categories.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-xs tracking-wider uppercase text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                THỂ LOẠI
                            </a>

                            <a href="{{ route('borrows.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-xs tracking-wider uppercase text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                TIẾN TRÌNH MƯỢN TRẢ
                            </a>

                            @if(Auth::user()->role === 'admin')
                                <a href="{{ route('admin.audit-logs.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-xs tracking-wider uppercase text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition">
                                    <span>📜</span>
                                    NHẬT KÝ HỆ THỐNG
                                </a>
                                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-xs tracking-wider uppercase text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition">
                                    <span>👑</span>
                                    QUẢN LÝ TÀI KHOẢN
                                </a>
                            @endif
                        @else
                            <a href="{{ route('books.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-xs tracking-wider uppercase text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                TRA CỨU SÁCH
                            </a>

                            <a href="{{ route('borrows.my') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-xs tracking-wider uppercase text-white bg-slate-900 hover:bg-slate-800 shadow-sm transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                SÁCH TÔI ĐANG MƯỢN
                            </a>

                            <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-xs tracking-wider uppercase text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                HỒ SƠ CÁ NHÂN
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Widget Điểm danh hàng ngày & Đồng hồ tích lũy 30 phút đọc sách nhận EXP -->
            <x-reading-timer-widget />

            <!-- Lưới Card tính năng chi tiết -->
            <div class="grid grid-cols-1 md:grid-cols-2 {{ Auth::user()->role === 'admin' ? 'lg:grid-cols-4' : 'lg:grid-cols-3' }} gap-6">
                @if(in_array(Auth::user()->role, ['admin', 'librarian']))
                    <!-- Card 1: Quản lý Sách -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 mb-4 shadow-2xs">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-1.5">QUẢN LÝ KHO SÁCH</h3>
                        <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                            Theo dõi tổng số lượng đầu sách, tác giả, kiểm tra tình trạng tồn kho, thêm mới và hiệu chỉnh dữ liệu sách.
                        </p>
                        <a href="{{ route('books.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                            Truy cập kho sách &rarr;
                        </a>
                    </div>

                    <!-- Card 2: Quản lý Mượn / Trả -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 mb-4 shadow-2xs">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-1.5">MƯỢN & TRẢ SÁCH</h3>
                        <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                            Tự động trừ tồn kho khi mượn, cộng lại kho khi trả, cảnh báo quá hạn và tự động tính tiền phạt chuẩn xác.
                        </p>
                        <a href="{{ route('borrows.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-800 flex items-center gap-1">
                            Quản lý mượn trả &rarr;
                        </a>
                    </div>

                    <!-- Card 3: Danh Mục Thể Loại -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 mb-4 shadow-2xs">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-1.5">DANH MỤC THỂ LOẠI</h3>
                        <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                            Phân loại tài liệu khoa học, kỹ thuật, văn học theo danh mục; đếm số lượng sách thuộc từng thể loại tự động.
                        </p>
                        <a href="{{ route('categories.index') }}" class="text-xs font-semibold text-amber-600 hover:text-amber-800 flex items-center gap-1">
                            Quản lý thể loại &rarr;
                        </a>
                    </div>

                    @if(Auth::user()->role === 'admin')
                        <!-- Card 4: Nhật ký hệ thống Audit Logs (Độc quyền Admin) -->
                        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition">
                            <div class="w-12 h-12 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600 mb-4 shadow-2xs">
                                <span class="text-xl">📜</span>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 mb-1.5">NHẬT KÝ HỆ THỐNG</h3>
                            <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                                Kiểm toán và giám sát mọi hành vi nhạy cảm: duyệt mượn, từ chối, thu tiền phạt và can thiệp số lượng kho sách.
                            </p>
                            <a href="{{ route('admin.audit-logs.index') }}" class="text-xs font-semibold text-purple-600 hover:text-purple-800 flex items-center gap-1">
                                Xem Audit Logs &rarr;
                            </a>
                        </div>
                    @endif
                @else
                    <!-- Card 1 Độc giả: Tra cứu kho sách -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 mb-4 shadow-2xs">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-1.5">TRA CỨU THƯ VIỆN</h3>
                        <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                            Khám phá hàng trăm đầu sách số hóa, tìm theo tên sách, tác giả, phân loại thể loại và kiểm tra số lượng sách còn trong kho.
                        </p>
                        <a href="{{ route('books.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                            Tìm kiếm sách ngay &rarr;
                        </a>
                    </div>

                    <!-- Card 2 Độc giả: Sách tôi đang mượn -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 mb-4 shadow-2xs">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-1.5">SÁCH TÔI ĐANG MƯỢN</h3>
                        <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                            Theo dõi danh sách những cuốn sách bạn đã đăng ký mượn, hạn trả dự kiến và trạng thái kiểm duyệt từ thủ thư.
                        </p>
                        <a href="{{ route('borrows.my') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-800 flex items-center gap-1">
                            Xem danh sách mượn &rarr;
                        </a>
                    </div>

                    <!-- Card 3 Độc giả: Hồ sơ cá nhân -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600 mb-4 shadow-2xs">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-1.5">HỒ SƠ CỦA TÔI</h3>
                        <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                            Cập nhật thông tin tài khoản, địa chỉ email, thay đổi mật khẩu và quản lý bảo mật tài khoản cá nhân.
                        </p>
                        <a href="{{ route('profile.edit') }}" class="text-xs font-semibold text-purple-600 hover:text-purple-800 flex items-center gap-1">
                            Cập nhật hồ sơ &rarr;
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
