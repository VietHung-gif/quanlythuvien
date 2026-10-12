<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-black text-2xl sm:text-3xl text-slate-950 tracking-tight flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-slate-900 text-white shadow-xs">
                        <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                    </span>
                    <span>QUẢN LÝ THÀNH VIÊN & PHÂN QUYỀN (RBAC)</span>
                </h1>
                <p class="text-base font-semibold text-slate-800 mt-1">
                    Thiết lập và phân chia quyền hạn giữa Quản Trị Viên (Admin), Thủ Thư (Librarian) và Độc Giả (Reader).
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="bg-slate-900 text-white font-bold text-xs px-3 py-1.5 rounded-lg shadow-xs tracking-wider uppercase inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                    <span>KHU VỰC QUẢN TRỊ TỐI CAO</span>
                </span>
            </div>
        </div>
    </x-slot>

    <div class="bg-slate-50 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Thông Báo -->
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm font-semibold flex items-center gap-3 shadow-xs">
                    <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-sm font-semibold flex items-center gap-3 shadow-xs">
                    <span class="w-6 h-6 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- 4 Thẻ Thống Kê Đầu Trang (User Stats Cards) -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Thống kê 1: Tổng người dùng -->
                <div class="bg-white border-2 border-slate-200 rounded-2xl p-4 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-700">TỔNG NGƯỜI DÙNG</div>
                        <div class="text-3xl font-black text-slate-950 mt-1 tracking-tight">{{ number_format($totalUsers) }}</div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-300 flex items-center justify-center text-slate-800 shadow-2xs">
                        <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                </div>

                <!-- Thống kê 2: Quản trị viên -->
                <div class="bg-white border-2 border-slate-200 rounded-2xl p-4 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-700">QUẢN TRỊ VIÊN</div>
                        <div class="text-3xl font-black text-slate-950 mt-1 tracking-tight">{{ number_format($totalAdmins) }}</div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-300 flex items-center justify-center text-amber-700 shadow-2xs">
                        <svg class="w-6 h-6 text-amber-600" fill="currentColor" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                    </div>
                </div>

                <!-- Thống kê 3: Thủ thư -->
                <div class="bg-white border-2 border-slate-200 rounded-2xl p-4 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-700">THỦ THƯ</div>
                        <div class="text-3xl font-black text-slate-950 mt-1 tracking-tight">{{ number_format($totalLibrarians) }}</div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-300 flex items-center justify-center text-indigo-700 shadow-2xs">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>

                <!-- Thống kê 4: Độc giả -->
                <div class="bg-white border-2 border-slate-200 rounded-2xl p-4 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-700">ĐỘC GIẢ</div>
                        <div class="text-3xl font-black text-slate-950 mt-1 tracking-tight">{{ number_format($totalReaders) }}</div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-300 flex items-center justify-center text-emerald-700 shadow-2xs">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Bộ Lọc & Tìm Kiếm -->
            <div class="bg-white border-2 border-slate-200 rounded-2xl p-5 shadow-sm">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <!-- Ô Tìm Kiếm -->
                    <div class="w-full sm:w-80 relative">
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}"
                               placeholder="Tìm theo tên, email, SĐT..."
                               class="w-full text-sm font-medium text-slate-900 placeholder:text-slate-500 py-2.5 px-3.5 pr-10 rounded-xl border-2 border-slate-300 bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition">
                        <button type="submit" class="absolute right-3 top-3 text-slate-500 hover:text-slate-900 transition cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Pills Bộ Lọc Vai Trò -->
                    <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto">
                        <span class="text-slate-800 font-bold text-xs uppercase me-1 shrink-0">VAI TRÒ:</span>
                        @foreach([
                            'all' => 'Tất Cả',
                            'admin' => 'Admin',
                            'librarian' => 'Thủ Thư',
                            'reader' => 'Độc Giả'
                        ] as $rKey => $rLabel)
                            <a href="{{ route('admin.users.index', ['role' => $rKey, 'search' => $search]) }}"
                               class="px-3.5 py-2 rounded-lg text-xs tracking-wide transition whitespace-nowrap {{ $role === $rKey ? 'bg-slate-900 text-white font-bold shadow-sm' : 'bg-slate-100 text-slate-800 hover:bg-slate-200 font-semibold border border-slate-300' }}">
                                {{ $rLabel }}
                            </a>
                        @endforeach
                    </div>
                </form>
            </div>

            <!-- Bảng Dữ Liệu Người Dùng (Data Table) -->
            <div class="bg-white rounded-2xl border-2 border-slate-200 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                        <thead class="bg-slate-900 text-white uppercase tracking-wider">
                            <tr>
                                <th scope="col" class="py-3.5 px-4 w-14 text-center font-bold text-xs tracking-wider uppercase">STT</th>
                                <th scope="col" class="py-3.5 px-4 font-bold text-xs tracking-wider uppercase">THÀNH VIÊN</th>
                                <th scope="col" class="py-3.5 px-4 font-bold text-xs tracking-wider uppercase">DANH HIỆU</th>
                                <th scope="col" class="py-3.5 px-4 text-center font-bold text-xs tracking-wider uppercase">CÔNG LỰC (EXP)</th>
                                <th scope="col" class="py-3.5 px-4 text-center font-bold text-xs tracking-wider uppercase">VAI TRÒ HIỆN TẠI</th>
                                <th scope="col" class="py-3.5 px-4 text-center font-bold text-xs tracking-wider uppercase">THAY ĐỔI VAI TRÒ</th>
                                <th scope="col" class="py-3.5 px-4 text-center font-bold text-xs tracking-wider uppercase">THAO TÁC</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-slate-800">
                            @forelse($users as $index => $u)
                                <tr class="hover:bg-slate-50 transition-colors {{ $u->id === auth()->id() ? 'bg-blue-50/40' : '' }}">
                                    <!-- STT -->
                                    <td class="px-4 py-3.5 text-center font-bold text-slate-900 text-sm">
                                        #{{ str_pad($users->firstItem() + $index, 3, '0', STR_PAD_LEFT) }}
                                    </td>

                                    <!-- Thành viên (Tên & Email) -->
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full border-2 border-slate-300 bg-slate-100 flex items-center justify-center font-bold text-xs shrink-0 overflow-hidden text-slate-800 shadow-2xs">
                                                @if($u->avatar)
                                                    <img src="{{ asset('storage/' . $u->avatar) }}" alt="{{ $u->name }}" class="w-full h-full object-cover">
                                                @else
                                                    {{ mb_strtoupper(mb_substr($u->name, 0, 2, 'UTF-8'), 'UTF-8') }}
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-950 text-sm flex items-center gap-1.5">
                                                    <span>{{ $u->name }}</span>
                                                    @if($u->id === auth()->id())
                                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">BẠN</span>
                                                    @endif
                                                </div>
                                                <div class="text-slate-600 font-medium text-xs mt-0.5">{{ $u->email }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Danh hiệu -->
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold shadow-2xs {{ $u->badge_color }}">
                                            <span>{!! $u->badge_icon !!}</span>
                                            <span>{{ $u->title_badge ?? 'Tân Thủ Nhập Môn' }}</span>
                                        </span>
                                    </td>

                                    <!-- EXP / Công lực -->
                                    <td class="px-4 py-3.5 whitespace-nowrap text-center font-extrabold text-slate-900 text-sm">
                                        {{ number_format($u->exp ?? 0) }} EXP
                                    </td>

                                    <!-- Vai trò hiện tại -->
                                    <td class="px-4 py-3.5 whitespace-nowrap text-center">
                                        @if($u->role === 'admin')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs bg-rose-100 text-rose-800 font-bold border border-rose-300 shadow-2xs">
                                                <svg class="w-3.5 h-3.5 text-rose-600" fill="currentColor" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                                                <span>QUẢN TRỊ VIÊN</span>
                                            </span>
                                        @elseif($u->role === 'librarian')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs bg-indigo-100 text-indigo-800 font-bold border border-indigo-300 shadow-2xs">
                                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                <span>THỦ THƯ</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs bg-emerald-100 text-emerald-800 font-bold border border-emerald-300 shadow-2xs">
                                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                <span>ĐỘC GIẢ</span>
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Dropdown Thay đổi vai trò -->
                                    <td class="px-4 py-3.5 whitespace-nowrap text-center">
                                        <form method="POST" action="{{ route('admin.users.update-role', $u->id) }}" class="inline-flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="role" 
                                                    onchange="this.form.submit()" 
                                                    {{ $u->id === auth()->id() ? 'disabled' : '' }}
                                                    class="border-2 border-slate-300 text-slate-900 font-semibold rounded-lg text-xs py-1.5 px-2.5 bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                                <option value="reader" {{ $u->role === 'reader' ? 'selected' : '' }}>Độc Giả</option>
                                                <option value="librarian" {{ $u->role === 'librarian' ? 'selected' : '' }}>Thủ Thư</option>
                                                <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Quản Trị Viên</option>
                                            </select>
                                        </form>
                                    </td>

                                    <!-- Nút xóa (Thao tác) -->
                                    <td class="px-4 py-3.5 whitespace-nowrap text-center">
                                        @if($u->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản {{ addslashes($u->name) }} khỏi hệ thống?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-rose-600 hover:text-rose-800 hover:bg-rose-50 p-2 rounded-lg transition border border-transparent hover:border-rose-200 cursor-pointer" 
                                                        title="Xóa tài khoản này">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-slate-400 font-semibold text-xs">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                        <p class="text-base font-bold text-slate-800 tracking-wide">KHÔNG TÌM THẤY TÀI KHOẢN PHÙ HỢP</p>
                                        <p class="text-xs text-slate-500 mt-1">Vui lòng thử tìm kiếm với từ khóa hoặc bộ lọc vai trò khác.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="p-4 bg-slate-50 border-t border-slate-200">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
