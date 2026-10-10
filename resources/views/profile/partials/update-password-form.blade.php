<section>
    <header class="border-b border-cyan-500/20 pb-4 mb-6">
        <h2 class="font-['Orbitron'] text-lg font-bold text-white tracking-wide flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-[#ffb800] shadow-[0_0_8px_#ffb800]"></span>
            {{ __('THAY ĐỔI MẬT KHẨU BẢO MẬT') }}
        </h2>

        <p class="mt-1 text-xs text-slate-400 font-mono">
            {{ __('Đảm bảo tài khoản sử dụng mật khẩu mạnh với tối thiểu 8 ký tự để bảo vệ an toàn phiên làm việc.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <!-- Mật khẩu hiện tại -->
        <div>
            <label for="update_password_current_password" class="block text-xs font-['Orbitron'] font-bold uppercase tracking-wider text-amber-300 mb-1.5 flex items-center gap-1.5">
                <span class="text-[#ff007f]">*</span> {{ __('MẬT KHẨU HIỆN TẠI') }}
            </label>
            <input id="update_password_current_password"
                   name="current_password"
                   type="password"
                   class="cyber-input w-full rounded-lg text-sm py-2.5 px-3.5"
                   autocomplete="current-password"
                   placeholder="••••••••" />
            @if ($errors->updatePassword->get('current_password'))
                <p class="text-xs text-[#ff007f] font-semibold mt-1.5 tracking-wide">{{ $errors->updatePassword->get('current_password')[0] }}</p>
            @endif
        </div>

        <!-- Mật khẩu mới -->
        <div>
            <label for="update_password_password" class="block text-xs font-['Orbitron'] font-bold uppercase tracking-wider text-amber-300 mb-1.5 flex items-center gap-1.5">
                <span class="text-[#ff007f]">*</span> {{ __('MẬT KHẨU MỚI') }}
            </label>
            <input id="update_password_password"
                   name="password"
                   type="password"
                   class="cyber-input w-full rounded-lg text-sm py-2.5 px-3.5"
                   autocomplete="new-password"
                   placeholder="Tối thiểu 8 ký tự..." />
            @if ($errors->updatePassword->get('password'))
                <p class="text-xs text-[#ff007f] font-semibold mt-1.5 tracking-wide">{{ $errors->updatePassword->get('password')[0] }}</p>
            @endif
        </div>

        <!-- Xác nhận mật khẩu mới -->
        <div>
            <label for="update_password_password_confirmation" class="block text-xs font-['Orbitron'] font-bold uppercase tracking-wider text-amber-300 mb-1.5 flex items-center gap-1.5">
                <span class="text-[#ff007f]">*</span> {{ __('XÁC NHẬN MẬT KHẨU MỚI') }}
            </label>
            <input id="update_password_password_confirmation"
                   name="password_confirmation"
                   type="password"
                   class="cyber-input w-full rounded-lg text-sm py-2.5 px-3.5"
                   autocomplete="new-password"
                   placeholder="Nhập lại mật khẩu mới..." />
            @if ($errors->updatePassword->get('password_confirmation'))
                <p class="text-xs text-[#ff007f] font-semibold mt-1.5 tracking-wide">{{ $errors->updatePassword->get('password_confirmation')[0] }}</p>
            @endif
        </div>

        <!-- Nút lưu mật khẩu -->
        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="cyber-btn-amber px-5 py-2.5 rounded-lg font-['Orbitron'] font-bold text-xs tracking-wider uppercase flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                {{ __('CẬP NHẬT MẬT KHẨU') }}
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-xs font-mono text-[#00ff66] flex items-center gap-1"
                >
                    <span class="w-2 h-2 rounded-full bg-[#00ff66] animate-pulse"></span>
                    {{ __('Đã thay đổi mật khẩu thành công!') }}
                </p>
            @endif
        </div>
    </form>
</section>
