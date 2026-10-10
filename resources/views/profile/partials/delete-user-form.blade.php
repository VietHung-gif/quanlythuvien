<section class="space-y-6">
    <header class="border-b border-[#ff007f]/30 pb-4">
        <h2 class="font-['Orbitron'] text-lg font-bold text-[#ff007f] tracking-wide flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-[#ff007f] shadow-[0_0_8px_#ff007f] animate-pulse"></span>
            {{ __('KHU VỰC NGUY HIỂM // XÓA TÀI KHOẢN VĨNH VIỄN') }}
        </h2>

        <p class="mt-1 text-xs text-rose-300/80 font-mono">
            {{ __('Khi tài khoản bị xóa, toàn bộ dữ liệu định danh và phiên làm việc sẽ bị hủy vĩnh viễn khỏi cơ sở dữ liệu. Thao tác này không thể hoàn tác!') }}
        </p>
    </header>

    <div>
        <button
            type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="cyber-btn-pink px-5 py-2.5 rounded-lg font-['Orbitron'] font-bold text-xs tracking-wider uppercase flex items-center gap-2"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            {{ __('TIẾN HÀNH XÓA TÀI KHOẢN') }}
        </button>
    </div>

    <!-- Modal Xác Nhận Xóa Tài Khoản Cyberpunk -->
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 sm:p-8 bg-[#0d1322] border border-[#ff007f]/50 rounded-2xl shadow-[0_0_35px_rgba(255,0,127,0.3)] text-left">
            @csrf
            @method('delete')

            <div class="text-center pb-4 border-b border-cyan-500/20">
                <div class="w-12 h-12 mx-auto rounded-full bg-rose-950/60 border border-[#ff007f] flex items-center justify-center text-[#ff007f] shadow-[0_0_15px_rgba(255,0,127,0.4)] mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="font-['Orbitron'] text-xl font-bold text-white tracking-wide">
                    XÁC NHẬN HỦY TÀI KHOẢN
                </h3>
                <p class="text-xs text-rose-300 font-mono mt-1">
                    Hành động này sẽ xóa vĩnh viễn quyền truy cập và dữ liệu cá nhân của bạn.
                </p>
            </div>

            <div class="mt-6 space-y-4">
                <p class="text-xs text-slate-300">
                    Vui lòng nhập mật khẩu hiện tại của bạn để xác thực yêu cầu hủy bỏ tài khoản:
                </p>

                <div>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="cyber-input w-full rounded-lg text-sm py-2.5 px-3.5"
                        placeholder="Nhập mật khẩu của bạn..."
                        required
                    />
                    @if ($errors->userDeletion->get('password'))
                        <p class="text-xs text-[#ff007f] font-semibold mt-1.5 tracking-wide">{{ $errors->userDeletion->get('password')[0] }}</p>
                    @endif
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-cyan-500/20">
                <button
                    type="button"
                    x-on:click="$dispatch('close')"
                    class="cyber-btn-cyan px-4 py-2.5 rounded-lg font-['Orbitron'] font-semibold text-xs tracking-wider uppercase"
                >
                    HỦY THAO TÁC
                </button>

                <button
                    type="submit"
                    class="cyber-btn-pink px-5 py-2.5 rounded-lg font-['Orbitron'] font-bold text-xs tracking-wider uppercase flex items-center gap-1.5"
                >
                    XÁC NHẬN XÓA NGAY
                </button>
            </div>
        </form>
    </x-modal>
</section>
