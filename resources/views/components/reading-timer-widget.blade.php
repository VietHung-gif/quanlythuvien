@php
    $currentUser = Auth::user();
    $canCheckIn = $currentUser ? $currentUser->canCheckInToday() : false;
@endphp

<div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-2xl relative overflow-hidden backdrop-blur-md"
     x-data="standaloneExpWidget({{ $canCheckIn ? 'true' : 'false' }}, {{ $currentUser ? $currentUser->exp : 0 }})">
    
    <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -left-10 -top-10 w-44 h-44 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
        
        <!-- Phần 1: Nút Điểm Danh Hằng Ngày (+20 EXP) -->
        <div class="flex items-center gap-3.5 flex-1">
            <div class="w-12 h-12 rounded-2xl bg-emerald-950/80 border border-emerald-500/40 flex items-center justify-center text-2xl shrink-0 shadow-[0_0_15px_rgba(16,185,129,0.3)]">
                📅
            </div>
            <div>
                <div class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <span>ĐIỂM DANH HẰNG NGÀY</span>
                    <span class="text-[10px] font-mono text-emerald-400 font-bold bg-emerald-950/60 border border-emerald-500/30 px-2 py-0.5 rounded">+20 EXP</span>
                </div>
                <div class="mt-1">
                    @auth
                        <template x-if="canCheckIn">
                            <button type="button" 
                                    @click="performCheckIn()" 
                                    :disabled="isCheckingIn"
                                    class="px-4 py-1.5 rounded-xl text-xs font-black uppercase tracking-wider bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow-[0_0_15px_rgba(16,185,129,0.5)] transition hover:scale-105 active:scale-95 cursor-pointer flex items-center gap-1.5">
                                <span x-show="!isCheckingIn">⚡ Điểm Danh (+20 EXP)</span>
                                <span x-show="isCheckingIn">Đang xử lý...</span>
                            </button>
                        </template>
                        <template x-if="!canCheckIn">
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-semibold bg-slate-800 text-slate-400 border border-slate-700/80">
                                <span>✓ Đã Điểm Danh Hôm Nay</span>
                            </span>
                        </template>
                    @else
                        <a href="{{ route('login') }}" class="text-xs text-cyan-400 underline font-semibold">
                            Đăng nhập để điểm danh
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Phần 2: Mượn Sách Nhận EXP (+50 EXP / Cuốn) -->
        <div class="flex items-center gap-4 sm:border-l sm:border-slate-800 sm:pl-6 flex-1">
            <div class="w-12 h-12 rounded-2xl bg-cyan-950/80 border border-cyan-500/40 flex items-center justify-center text-2xl shrink-0 shadow-[0_0_15px_rgba(0,240,255,0.3)]">
                📚
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between text-xs font-bold text-white uppercase tracking-wider mb-1">
                    <span class="flex items-center gap-1.5">
                        <span>MƯỢN SÁCH TÍCH LŨY</span>
                        <span class="text-[10px] font-mono text-cyan-400 font-bold bg-cyan-950/60 border border-cyan-500/30 px-2 py-0.5 rounded">+50 EXP</span>
                    </span>
                </div>
                <div class="text-xs text-slate-400 mb-2">
                    Mượn sách thành công nhận ngay <strong class="text-cyan-300">+50 EXP</strong> công lực và thăng cấp danh hiệu.
                </div>
                <div>
                    <a href="{{ route('books.index') }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-xl text-xs font-bold uppercase tracking-wider bg-cyan-500 hover:bg-cyan-400 text-slate-950 shadow-[0_0_15px_rgba(0,240,255,0.4)] transition hover:scale-105 active:scale-95">
                        <span>Tra Cứu & Mượn Sách</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function standaloneExpWidget(initialCanCheckIn, initialExp) {
    return {
        canCheckIn: initialCanCheckIn,
        isCheckingIn: false,
        userExp: initialExp,

        async performCheckIn() {
            if (!this.canCheckIn || this.isCheckingIn) return;
            this.isCheckingIn = true;

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch('{{ route('entertainment.checkin') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                });
                const data = await res.json();
                if (data.success) {
                    this.canCheckIn = false;
                    this.userExp = data.total_exp;
                    alert(data.message + (data.badge_upgraded ? '\n🎉 Chúc mừng bạn đã thăng cấp thành: ' + data.new_badge : ''));
                } else {
                    alert(data.message || 'Hôm nay bạn đã điểm danh rồi!');
                    if (data.already_checked_in) this.canCheckIn = false;
                }
            } catch(e) {
                console.error(e);
            } finally {
                this.isCheckingIn = false;
            }
        }
    };
}
</script>
