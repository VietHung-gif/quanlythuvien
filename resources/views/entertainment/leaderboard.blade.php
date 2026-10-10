<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="text-3xl">🏆</span>
                    <span>BẢNG PHONG THẦN: "MỌT SÁCH CHIẾN TƯỚNG"</span>
                </h1>
            </div>

            <!-- Phím tắt liên kết -->
            <div class="flex items-center gap-2.5">
                <a href="{{ route('books.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-300 hover:border-blue-500 shadow-2xs transition flex items-center gap-1.5">
                    <span>📚</span> Mượn Sách (+50 EXP)
                </a>
                <a href="{{ route('entertainment.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider text-white bg-blue-600 hover:bg-blue-700 shadow-xs transition flex items-center gap-1.5">
                    <span>🎯</span> Nhiệm Vụ & Điểm Danh
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- BANNER THÔNG BÁO RESET MÙA GIẢI & TRAO QUÀ TOP 1 TUẦN -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200/60 rounded-2xl p-4 sm:p-5 flex flex-col md:flex-row items-center justify-between gap-4 shadow-sm">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-white border border-blue-200 flex items-center justify-center text-2xl shadow-xs shrink-0">
                        🎁
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-blue-600 text-white">MÙA GIẢI HÀNG TUẦN</span>
                            <span class="text-xs text-blue-700 font-bold">Tự động đặt lại vào 00:00 Chủ Nhật</span>
                        </div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900 mt-0.5">
                            Top 1 tuần nhận ngay <span class="text-emerald-700 font-black underline decoration-2">100 Điểm Đổi Quà</span> cùng đặc quyền mượn sách không giới hạn!
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Điểm đổi quà dùng để quy đổi hiện vật, đặc quyền mượn sách không giới hạn và kỷ niệm chương danh giá của Thư viện.
                        </p>
                    </div>
                </div>

                <!-- Đồng hồ đếm ngược đến 00:00 Chủ Nhật -->
                <div class="bg-white border border-blue-200 px-4 py-2.5 rounded-xl flex items-center gap-3 shrink-0 shadow-xs self-start md:self-auto"
                     x-data="weeklyCountdownTimer()">
                    <div class="text-right">
                        <span class="text-[10px] font-bold text-slate-500 uppercase block leading-none">Thời gian còn lại</span>
                        <div class="font-mono text-sm sm:text-base font-bold text-blue-700 mt-0.5" x-text="timeLeftString">
                            Đang tính...
                        </div>
                    </div>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                </div>
            </div>

            <!-- THẺ VỊ THẾ HIỆP KHÁCH HIỆN TẠI & SỐ DƯ ĐIỂM ĐỔI QUÀ -->
            @auth
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm space-y-3">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                        <!-- Thông tin cá nhân -->
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full overflow-hidden bg-slate-100 border border-slate-200 flex items-center justify-center text-xs font-bold text-slate-700 shadow-xs shrink-0">
                                @if($currentUser->avatar)
                                    <img src="{{ asset('storage/' . $currentUser->avatar) }}" alt="{{ $currentUser->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ mb_substr($currentUser->name, 0, 1, 'UTF-8') }}
                                @endif
                            </div>
                            <div>
                                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">VỊ THẾ HIỆN TẠI CỦA BẠN:</div>
                                <h3 class="text-lg font-extrabold text-slate-900 mt-0.5">{{ $currentUser->name }}</h3>
                                <div class="mt-1 flex flex-wrap items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-md text-xs font-bold {{ $currentUser->badge_color }}">
                                        {{ $currentUser->title_badge }}
                                    </span>
                                    <span class="text-xs font-mono font-bold text-blue-700 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-md">
                                        ⚡ Tuần này: <strong>{{ number_format($currentUser->weekly_exp ?? 0) }}</strong> EXP
                                    </span>
                                    <span class="text-xs font-mono font-bold text-slate-700 bg-slate-50 border border-slate-200 px-2 py-0.5 rounded-md">
                                        🔥 Tổng tích lũy: <strong>{{ number_format($currentUser->exp ?? 0) }}</strong> EXP
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- 2 Ô chỉ số: Thứ hạng & Số dư Điểm Đổi Quà + Nút -->
                        <div class="flex flex-wrap items-center gap-3">
                            <!-- Hạng tuần -->
                            <div class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-center min-w-[110px]">
                                <span class="text-[10px] text-slate-500 block font-bold uppercase">HẠNG TUẦN NÀY</span>
                                <div class="text-lg font-black text-blue-700 font-mono">
                                    @if(in_array($currentUser->role, ['admin', 'librarian']))
                                        👑 ADMIN
                                    @else
                                        #{{ $currentUserWeeklyRank ?? '—' }}
                                    @endif
                                </div>
                            </div>

                            <!-- Điểm Đổi Quà Hiện Có -->
                            <div class="bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-2 text-center min-w-[120px]">
                                <span class="text-[10px] text-emerald-800 block font-bold uppercase">ĐIỂM ĐỔI QUÀ</span>
                                <div class="text-lg font-black text-emerald-700 font-mono flex items-center justify-center gap-1">
                                    <span>🎁</span>
                                    <span>{{ number_format($currentUser->reward_points ?? 0) }}</span>
                                </div>
                            </div>

                            <a href="{{ route('books.index') }}" class="h-10 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition flex items-center justify-center gap-1.5 shadow-2xs">
                                <span>📚</span>
                                <span>Mượn Sách (+50)</span>
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white border border-slate-200 rounded-2xl p-5 text-center text-slate-600 shadow-sm space-y-2">
                    <p class="text-xs font-medium">
                        Đăng nhập tài khoản thư viện của bạn để tích lũy EXP, đua top tuần nhận 100 Điểm Đổi Quà và ghi danh trên Bảng Phong Thần!
                    </p>
                    <a href="{{ route('login') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase px-5 py-2.5 rounded-xl shadow-2xs transition">
                        Đăng Nhập Ngay
                    </a>
                </div>
            @endauth

            <!-- ========================================================================= -->
            <!-- KHUNG VINH DANH RIÊNG: BAN ĐIỀU HÀNH / QUẢN TRỊ VIÊN TỐI CAO               -->
            <!-- ========================================================================= -->
            @if(isset($adminUsers) && $adminUsers->isNotEmpty())
                <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-sm space-y-3">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">
                        <span class="flex items-center gap-1.5 text-slate-700 font-extrabold">🛡️ BAN QUẢN TRỊ TỐI CAO</span>
                        <span class="text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full font-semibold">Đặc quyền hệ thống • Không đua rank</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($adminUsers as $adm)
                            <div class="p-3 rounded-xl border border-slate-200 bg-slate-50/60 hover:bg-slate-100/80 transition flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <div class="w-10 h-10 rounded-full overflow-hidden bg-white border border-slate-300 shrink-0 flex items-center justify-center font-bold text-slate-700 text-xs shadow-xs">
                                        @if($adm->avatar)
                                            <img src="{{ asset('storage/' . $adm->avatar) }}" alt="{{ $adm->name }}" class="w-full h-full object-cover">
                                        @else
                                            {{ mb_substr($adm->name, 0, 1, 'UTF-8') }}
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-sm font-extrabold text-slate-900 truncate">{{ $adm->name }}</span>
                                            @if($adm->role === 'admin')
                                                <span class="bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-bold px-2 py-0.5 rounded-md shrink-0">ADMIN</span>
                                            @else
                                                <span class="bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-bold px-2 py-0.5 rounded-md shrink-0">THỦ THƯ</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-slate-500 font-semibold truncate mt-0.5 whitespace-nowrap">
                                            Độc Cô Cầu Bại • 9,999,999 EXP
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right shrink-0">
                                    <span class="text-xs font-bold text-amber-600 font-mono">👑 TỐI THƯỢNG</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- BẢNG PHONG THẦN TOP 10 HIỆP KHÁCH CAO CƯỜNG -->
            <div class="bg-white border-2 border-slate-200 rounded-3xl overflow-hidden shadow-xs">
                
                <!-- Header bảng kèm Tab chuyển đổi Tuần & Toàn mùa -->
                <div class="px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/70">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                        <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">
                            {{ $tab === 'all' ? 'BẢNG TỔNG SẮP: TOÀN THỜI GIAN' : 'ĐUA TOP HÀNG TUẦN: TRANH ĐOẠT 100 ĐIỂM QUÀ' }}
                        </h3>
                    </div>

                    <!-- TAB SWITCHER -->
                    <div class="flex items-center bg-slate-100 border border-slate-200 rounded-xl p-1 gap-1">
                        <a href="{{ route('entertainment.leaderboard', ['tab' => 'weekly']) }}" 
                           class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ $tab !== 'all' ? 'bg-white text-blue-600 font-black shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                            <span>⚡</span> Top Tuần Này
                        </a>
                        <a href="{{ route('entertainment.leaderboard', ['tab' => 'all']) }}" 
                           class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ $tab === 'all' ? 'bg-white text-blue-600 font-black shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                            <span>👑</span> Toàn Mùa Giải
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 uppercase text-xs font-bold tracking-wider">
                            <tr>
                                <th scope="col" class="px-6 py-4 w-24 text-center">THỨ HẠNG</th>
                                <th scope="col" class="px-6 py-4">ĐỘC GIẢ TRI THỨC</th>
                                <th scope="col" class="px-6 py-4">DANH HIỆU</th>
                                <th scope="col" class="px-6 py-4 text-center">
                                    {{ $tab === 'all' ? 'TỔNG EXP' : 'EXP TUẦN NÀY' }}
                                </th>
                                <th scope="col" class="px-6 py-4 text-center">PHẦN THƯỞNG / VỊ THẾ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse ($topUsers as $index => $user)
                                @php
                                    $rank = $index + 1;
                                    $isMe = Auth::check() && Auth::id() === $user->id;
                                    $displayExp = ($tab === 'all') ? $user->exp : $user->weekly_exp;
                                    $rowStyle = match ($rank) {
                                        1 => 'bg-amber-50/50 hover:bg-amber-50/80 border-l-4 border-amber-400',
                                        2 => 'bg-slate-50/80 hover:bg-slate-100/90 border-l-4 border-slate-300',
                                        3 => 'bg-orange-50/40 hover:bg-orange-50/70 border-l-4 border-amber-200',
                                        default => $isMe ? 'bg-blue-50/60 border-l-4 border-blue-500 font-bold' : 'hover:bg-slate-50/60',
                                    };
                                @endphp
                                <tr class="transition duration-150 {{ $rowStyle }}">
                                    <!-- Cột Thứ hạng -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if ($rank === 1)
                                            <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-amber-100 border border-amber-300 text-xl font-bold shadow-2xs">
                                                🥇
                                            </div>
                                        @elseif ($rank === 2)
                                            <div class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-slate-100 border border-slate-300 text-lg font-bold shadow-2xs">
                                                🥈
                                            </div>
                                        @elseif ($rank === 3)
                                            <div class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-orange-100 border border-orange-300 text-lg font-bold shadow-2xs">
                                                🥉
                                            </div>
                                        @else
                                            <span class="font-mono text-sm text-slate-500 font-bold">
                                                #{{ $rank }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Cột Tên Hiệp Khách -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full overflow-hidden bg-slate-100 border border-slate-200 flex items-center justify-center text-xs font-bold text-slate-700 shadow-2xs shrink-0">
                                                @if($user->avatar)
                                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                                @else
                                                    {{ mb_substr($user->name, 0, 1, 'UTF-8') }}
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-extrabold text-slate-900 flex items-center gap-2">
                                                    <span>{{ $user->name }}</span>
                                                    @if ($isMe)
                                                        <span class="px-2 py-0.5 rounded text-[10px] bg-blue-100 text-blue-700 font-black">BẠN</span>
                                                    @endif
                                                </div>
                                                <div class="text-xs text-slate-500">
                                                    {{ $user->role_name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Cột Danh Hiệu Võ Lâm -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $user->badge_color }}">
                                            <span>{{ $user->badge_icon }}</span>
                                            <span>{{ $user->title_badge }}</span>
                                        </span>
                                    </td>

                                    <!-- Cột Công Lực (EXP) -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center font-mono">
                                        <span class="text-base font-black text-slate-900 tracking-wider">
                                            {{ number_format($displayExp ?? 0) }}
                                        </span>
                                        <span class="text-xs text-slate-400 font-semibold block">EXP</span>
                                    </td>

                                    <!-- Cột Vị Thế & Phần Thưởng -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-xs">
                                        @if ($rank === 1)
                                            <div class="inline-flex flex-col items-center">
                                                <span class="text-amber-700 font-extrabold tracking-wide">👑 TOP 1 TUẦN</span>
                                                @if ($tab !== 'all')
                                                    <span class="text-[11px] font-mono text-emerald-700 font-black mt-0.5">🎁 +100 ĐIỂM QUÀ</span>
                                                @endif
                                            </div>
                                        @elseif ($rank <= 3)
                                            <span class="text-blue-700 font-bold">⚔️ ĐẠI CAO THỦ</span>
                                        @else
                                            <span class="text-slate-500 font-medium">HỘI VIÊN ĐUA TOP</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                        Chưa có độc giả nào phát sinh EXP trong bảng xếp hạng này. Hãy mượn sách hoặc điểm danh ngay để ghi danh!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer bảng xếp hạng -->
                <div class="p-5 bg-slate-50 border-t border-slate-200 text-center flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-600">
                    <div>
                        Điểm danh hàng ngày nhận <strong class="text-blue-700 font-black">+15 EXP</strong> • Mượn sách thư viện nhận ngay <strong class="text-emerald-700 font-black">+50 EXP</strong>.
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('books.index') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold uppercase tracking-wider text-xs shadow-2xs transition">
                            📚 Mượn Sách (+50 EXP)
                        </a>
                        <a href="{{ route('entertainment.index') }}" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold uppercase tracking-wider text-xs shadow-2xs transition">
                            🎯 Nhiệm Vụ & Điểm Danh
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Script đếm ngược đến 00:00 Chủ Nhật kế tiếp -->
    <script>
    function weeklyCountdownTimer() {
        return {
            timeLeftString: 'Đang tải...',
            timer: null,

            init() {
                this.updateCountdown();
                this.timer = setInterval(() => this.updateCountdown(), 1000);
            },

            updateCountdown() {
                const now = new Date();
                // Tính ngày Chủ Nhật tiếp theo lúc 00:00
                const nextSunday = new Date();
                const day = now.getDay();
                // Nếu hôm nay là Chủ Nhật (day = 0), mốc tiếp theo là 7 ngày nữa
                const daysUntilSunday = (day === 0) ? 7 : (7 - day);
                nextSunday.setDate(now.getDate() + daysUntilSunday);
                nextSunday.setHours(0, 0, 0, 0);

                const diff = nextSunday - now;
                if (diff <= 0) {
                    this.timeLeftString = '00 ngày 00:00:00';
                    return;
                }

                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                const pad = (n) => String(n).padStart(2, '0');
                this.timeLeftString = `${days} ngày ${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
            }
        };
    }
    </script>
</x-app-layout>
