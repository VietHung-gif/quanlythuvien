<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-black text-2xl sm:text-3xl text-slate-950 tracking-tight flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-slate-900 text-white shadow-xs">
                        📜
                    </span>
                    <span>NHẬT KÝ KIỂM TOÁN HỆ THỐNG</span>
                </h1>
                <p class="text-base font-semibold text-slate-800 mt-1 flex items-center gap-2">
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Giám sát kiểm toán toàn diện mọi hoạt động nghiệp vụ của Thủ Thư & Ban Quản Trị</span>
                </p>
            </div>
        </div>
    </x-slot>

    <div class="bg-slate-50 min-h-screen py-8" x-data="{
        diffModalOpen: false,
        activeLog: null,
        openDiffModal(log) {
            this.activeLog = log;
            this.diffModalOpen = true;
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- 1. TỔNG THỂ & KHỐI THỐNG KÊ TRÊN CÙNG (AUDIT STATS CARDS) -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                <!-- Thống kê 1: Tổng sự kiện -->
                <div class="bg-white border-2 border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-700">TỔNG SỰ KIỆN</div>
                        <div class="text-3xl font-black text-slate-950 mt-1 tracking-tight">{{ number_format($stats['total']) }}</div>
                    </div>
                    <div class="text-xs font-semibold text-slate-500 mt-2">Toàn bộ lịch sử ghi nhận</div>
                </div>

                <!-- Thống kê 2: Thu tiền phạt -->
                <div class="bg-white border-2 border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-700">THU TIỀN PHẠT</div>
                        <div class="text-3xl font-black text-slate-950 mt-1 tracking-tight">{{ number_format($stats['collect_fine']) }}</div>
                    </div>
                    <div class="text-xs font-semibold text-slate-500 mt-2">Lượt xác nhận thu ngân</div>
                </div>

                <!-- Thống kê 3: Duyệt mượn -->
                <div class="bg-white border-2 border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-700">DUYỆT MƯỢN</div>
                        <div class="text-3xl font-black text-slate-950 mt-1 tracking-tight">{{ number_format($stats['approve_borrow']) }}</div>
                    </div>
                    <div class="text-xs font-semibold text-slate-500 mt-2">Lượt phê duyệt sách</div>
                </div>

                <!-- Thống kê 4: Can thiệp kho -->
                <div class="bg-white border-2 border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-700">CAN THIỆP KHO</div>
                        <div class="text-3xl font-black text-slate-950 mt-1 tracking-tight">{{ number_format($stats['update_stock']) }}</div>
                    </div>
                    <div class="text-xs font-semibold text-slate-500 mt-2">Lượt sửa tồn kho sách</div>
                </div>

                <!-- Thống kê 5: Giao dịch hôm nay -->
                <div class="col-span-2 sm:col-span-1 bg-white border-2 border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-700">GIAO DỊCH HÔM NAY</div>
                        <div class="text-3xl font-black text-slate-950 mt-1 tracking-tight">{{ number_format($stats['today']) }}</div>
                    </div>
                    <div class="text-xs font-semibold text-slate-500 mt-2">Giao dịch trong ngày</div>
                </div>
            </div>

            <!-- 2. KHU VỰC BỘ LỌC TÌM KIẾM (SEARCH & FILTER SECTION) -->
            <div class="bg-white border-2 border-slate-200 rounded-2xl p-5 shadow-sm mt-6">
                <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Người thao tác -->
                        <div>
                            <label class="text-xs font-bold uppercase text-slate-700 mb-1.5 block">
                                👤 Người thao tác
                            </label>
                            <select name="user_id" 
                                    class="w-full bg-white border-2 border-slate-200 text-slate-900 font-semibold text-sm rounded-xl focus:border-blue-600 focus:ring-0 py-2.5 px-3">
                                <option value="">— Tất cả nhân sự —</option>
                                @foreach($staffUsers as $staff)
                                    <option value="{{ $staff->id }}" {{ $userId == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }} ({{ $staff->role_name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Loại hành động -->
                        <div>
                            <label class="text-xs font-bold uppercase text-slate-700 mb-1.5 block">
                                ⚡ Loại hành động
                            </label>
                            <select name="action" 
                                    class="w-full bg-white border-2 border-slate-200 text-slate-900 font-semibold text-sm rounded-xl focus:border-blue-600 focus:ring-0 py-2.5 px-3">
                                <option value="all">— Tất cả hành động —</option>
                                @foreach($actionTypes as $actKey => $actInfo)
                                    <option value="{{ $actKey }}" {{ $action == $actKey ? 'selected' : '' }}>
                                        {{ $actInfo['icon'] }} {{ $actInfo['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Từ ngày -->
                        <div>
                            <label class="text-xs font-bold uppercase text-slate-700 mb-1.5 block">
                                📅 Từ ngày
                            </label>
                            <input type="date" 
                                   name="from_date" 
                                   value="{{ $fromDate }}" 
                                   class="w-full bg-white border-2 border-slate-200 text-slate-900 font-semibold text-sm rounded-xl focus:border-blue-600 focus:ring-0 py-2.5 px-3">
                        </div>

                        <!-- Đến ngày -->
                        <div>
                            <label class="text-xs font-bold uppercase text-slate-700 mb-1.5 block">
                                📅 Đến ngày
                            </label>
                            <input type="date" 
                                   name="to_date" 
                                   value="{{ $toDate }}" 
                                   class="w-full bg-white border-2 border-slate-200 text-slate-900 font-semibold text-sm rounded-xl focus:border-blue-600 focus:ring-0 py-2.5 px-3">
                        </div>
                    </div>

                    <!-- Hàng thứ 2: Tìm kiếm từ khóa và các nút bấm -->
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-slate-100">
                        <div class="w-full sm:w-1/2 relative">
                            <input type="text" 
                                   name="search" 
                                   value="{{ $search }}" 
                                   placeholder="Tìm kiếm nội dung chi tiết, mã phiếu, tên sách, IP..." 
                                   class="w-full bg-white border-2 border-slate-200 text-slate-900 font-medium placeholder:text-slate-400 text-sm rounded-xl focus:border-blue-600 focus:ring-0 pl-10 pr-4 py-2.5 transition">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>

                        <div class="flex items-center gap-2.5 w-full sm:w-auto">
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow-sm transition flex-1 sm:flex-none flex items-center justify-center gap-1.5 cursor-pointer">
                                <span>🔍</span>
                                <span>Lọc Dữ Liệu</span>
                            </button>
                            <a href="{{ route('admin.audit-logs.index') }}" 
                               class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm px-4 py-2.5 rounded-xl transition text-center flex-1 sm:flex-none">
                                Đặt lại
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- 3. BẢNG DỮ LIỆU NHẬT KÝ (AUDIT LOGS DATA TABLE) -->
            <div class="bg-white border-2 border-slate-200 rounded-2xl shadow-sm overflow-hidden mt-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-100 border-b-2 border-slate-200">
                                <th class="text-slate-800 font-extrabold text-xs uppercase tracking-wider py-4 px-4 text-center w-16">MÃ ID</th>
                                <th class="text-slate-800 font-extrabold text-xs uppercase tracking-wider py-4 px-4 whitespace-nowrap">THỜI GIAN</th>
                                <th class="text-slate-800 font-extrabold text-xs uppercase tracking-wider py-4 px-4 whitespace-nowrap">NGƯỜI THAO TÁC</th>
                                <th class="text-slate-800 font-extrabold text-xs uppercase tracking-wider py-4 px-4 text-center whitespace-nowrap">HÀNH ĐỘNG</th>
                                <th class="text-slate-800 font-extrabold text-xs uppercase tracking-wider py-4 px-4 min-w-[280px]">MÔ TẢ CHI TIẾT</th>
                                <th class="text-slate-800 font-extrabold text-xs uppercase tracking-wider py-4 px-4 text-center whitespace-nowrap">ĐỊA CHỈ IP</th>
                                <th class="text-slate-800 font-extrabold text-xs uppercase tracking-wider py-4 px-4 text-center whitespace-nowrap">ĐỐI SOÁT</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-800">
                            @forelse($logs as $log)
                                <tr class="border-b border-slate-100 hover:bg-slate-50/80 transition">
                                    <!-- MÃ ID -->
                                    <td class="py-4 px-4 text-center font-bold text-slate-900 text-sm">
                                        #{{ $log->id }}
                                    </td>

                                    <!-- THỜI GIAN -->
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <div class="font-semibold text-slate-800 text-sm">{{ $log->created_at->format('d/m/Y H:i:s') }}</div>
                                        <div class="text-xs font-medium text-slate-500 mt-0.5">{{ $log->created_at->diffForHumans() }}</div>
                                    </td>

                                    <!-- NGƯỜI THAO TÁC -->
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        @if($log->user)
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-full border-2 border-slate-200 overflow-hidden shrink-0 bg-slate-100 flex items-center justify-center font-bold text-xs text-slate-800 shadow-2xs">
                                                    @if($log->user->avatar)
                                                        <img src="{{ asset('storage/' . $log->user->avatar) }}" alt="{{ $log->user->name }}" class="w-full h-full object-cover">
                                                    @else
                                                        {{ mb_strtoupper(mb_substr($log->user->name, 0, 2, 'UTF-8'), 'UTF-8') }}
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="font-bold text-slate-950 text-sm">{{ $log->user->name }}</div>
                                                    <div class="flex items-center gap-1.5 mt-0.5">
                                                        @if($log->user->role === 'admin')
                                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                                                👑 ADMIN
                                                            </span>
                                                        @elseif($log->user->role === 'librarian')
                                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-300">
                                                                📜 THỦ THƯ
                                                            </span>
                                                        @else
                                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                                ⚡ ĐỘC GIẢ
                                                            </span>
                                                        @endif
                                                        <span class="text-xs font-medium text-slate-600">{{ $log->user->email }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-slate-500 italic text-xs font-medium">[Hệ thống tự động / Đã xóa]</span>
                                        @endif
                                    </td>

                                    <!-- HÀNH ĐỘNG -->
                                    <td class="py-4 px-4 whitespace-nowrap text-center">
                                        @php
                                            $actionBadgeClass = match ($log->action) {
                                                'COLLECT_FINE' => 'bg-emerald-100 text-emerald-800 border border-emerald-300 font-bold text-xs px-2.5 py-1 rounded-lg',
                                                'DELETE_USER', 'DELETE_BOOK', 'DELETE_CATEGORY' => 'bg-rose-100 text-rose-800 border border-rose-300 font-bold text-xs px-2.5 py-1 rounded-lg',
                                                'UPDATE_USER_ROLE' => 'bg-amber-100 text-amber-800 border border-amber-300 font-bold text-xs px-2.5 py-1 rounded-lg',
                                                'UPDATE_STOCK' => 'bg-blue-100 text-blue-800 border border-blue-300 font-bold text-xs px-2.5 py-1 rounded-lg',
                                                'APPROVE_BORROW' => 'bg-teal-100 text-teal-800 border border-teal-300 font-bold text-xs px-2.5 py-1 rounded-lg',
                                                'REJECT_BORROW' => 'bg-rose-100 text-rose-800 border border-rose-300 font-bold text-xs px-2.5 py-1 rounded-lg',
                                                'RETURN_BOOK' => 'bg-indigo-100 text-indigo-800 border border-indigo-300 font-bold text-xs px-2.5 py-1 rounded-lg',
                                                default => 'bg-slate-100 text-slate-800 border border-slate-300 font-bold text-xs px-2.5 py-1 rounded-lg',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 {{ $actionBadgeClass }} shadow-2xs whitespace-nowrap">
                                            <span>{{ $log->action_icon }}</span>
                                            <span>{{ $log->action_label }}</span>
                                        </span>
                                    </td>

                                    <!-- MÔ TẢ CHI TIẾT -->
                                    <td class="py-4 px-4 text-slate-800 font-medium text-sm leading-relaxed max-w-md">
                                        {{ $log->description }}
                                    </td>

                                    <!-- ĐỊA CHỈ IP -->
                                    <td class="py-4 px-4 whitespace-nowrap text-center">
                                        <span class="font-mono text-xs font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded-md" title="{{ $log->user_agent }}">
                                            {{ $log->ip_address ?? '127.0.0.1' }}
                                        </span>
                                    </td>

                                    <!-- ĐỐI SOÁT -->
                                    <td class="py-4 px-4 whitespace-nowrap text-center">
                                        @if(!empty($log->old_values) || !empty($log->new_values))
                                            <button type="button" 
                                                    @click="openDiffModal({{ json_encode($log) }})"
                                                    class="text-blue-600 hover:text-blue-800 font-bold text-xs hover:underline inline-flex items-center gap-1 cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                <span>Xem Snapshot</span>
                                            </button>
                                        @else
                                            <span class="text-slate-400 font-mono text-xs">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                        <div class="text-4xl mb-3">🔍</div>
                                        <p class="font-bold text-slate-900 text-sm">KHÔNG TÌM THẤY BẢN GHI NHẬT KÝ KIỂM TOÁN</p>
                                        <p class="text-xs text-slate-500 mt-1">Chưa có sự kiện nào phù hợp với bộ lọc tìm kiếm được chọn.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- 4. PHÂN TRANG (PAGINATION) -->
                @if($logs->hasPages())
                    <div class="p-4 border-t border-slate-200 bg-slate-50">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>

        </div>

        <!-- MODAL XEM CHI TIẾT ĐỐI CHIẾU DỮ LIỆU (JSON SNAPSHOT DIFF - LIGHT THEME) -->
        <div x-show="diffModalOpen" 
             x-cloak 
             @keydown.escape.window="diffModalOpen = false"
             style="display: none;"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            
            <div class="relative w-full max-w-3xl rounded-2xl bg-white border-2 border-slate-200 shadow-2xl p-6 space-y-4"
                 @click.outside="diffModalOpen = false">
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-200 text-blue-600 flex items-center justify-center text-lg">
                            🔍
                        </span>
                        <div>
                            <h3 class="font-black text-slate-950 text-base">ĐỐI CHIẾU DỮ LIỆU // SNAPSHOT DIFF</h3>
                            <div class="text-xs font-semibold text-slate-500 mt-0.5">
                                Log #<span x-text="activeLog?.id" class="text-slate-900 font-mono font-bold"></span> | Hành động: <span x-text="activeLog?.action" class="text-blue-600 font-mono font-bold"></span>
                            </div>
                        </div>
                    </div>
                    <button type="button" 
                            @click="diffModalOpen = false" 
                            class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Nội dung mô tả -->
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700 font-medium">
                    <span class="font-bold text-slate-900">Mô tả chi tiết:</span> <span x-text="activeLog?.description"></span>
                </div>

                <!-- So sánh 2 cột Old Values vs New Values -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Dữ liệu trước khi sửa -->
                    <div class="rounded-xl bg-rose-50/60 border border-rose-200 p-3.5 space-y-2">
                        <div class="text-xs font-mono uppercase text-rose-800 font-bold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            <span>Dữ Liệu Trước (Old Values)</span>
                        </div>
                        <pre class="text-xs font-mono text-rose-950 bg-white p-3 rounded-lg overflow-x-auto max-h-56 leading-relaxed border border-rose-200 shadow-2xs" 
                             x-text="JSON.stringify(activeLog?.old_values, null, 2) || 'Không có dữ liệu cũ'"></pre>
                    </div>

                    <!-- Dữ liệu sau khi sửa -->
                    <div class="rounded-xl bg-emerald-50/60 border border-emerald-200 p-3.5 space-y-2">
                        <div class="text-xs font-mono uppercase text-emerald-800 font-bold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Dữ Liệu Mới (New Values)</span>
                        </div>
                        <pre class="text-xs font-mono text-emerald-950 bg-white p-3 rounded-lg overflow-x-auto max-h-56 leading-relaxed border border-emerald-200 shadow-2xs" 
                             x-text="JSON.stringify(activeLog?.new_values, null, 2) || 'Không có dữ liệu mới'"></pre>
                    </div>
                </div>

                <!-- Metadata bổ sung & nút đóng -->
                <div class="flex items-center justify-between text-xs font-medium text-slate-500 pt-3 border-t border-slate-100">
                    <div>IP: <span x-text="activeLog?.ip_address || '127.0.0.1'" class="text-slate-800 font-mono font-bold"></span></div>
                    <button type="button" 
                            @click="diffModalOpen = false" 
                            class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-4 py-2 rounded-xl transition cursor-pointer text-xs">
                        Đóng cửa sổ
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
