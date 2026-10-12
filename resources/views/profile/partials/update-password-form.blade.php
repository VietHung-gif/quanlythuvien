<section>
    <header class="border-b border-slate-200 pb-5 mb-6">
        <h2 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2.5">
            <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
            {{ __('THAY ĐỔI MẬT KHẨU BẢO MẬT') }}
        </h2>

        <p class="mt-1.5 text-sm text-slate-600 font-normal">
            {{ __('Đảm bảo tài khoản sử dụng mật khẩu mạnh với tối thiểu 8 ký tự để bảo vệ an toàn phiên làm việc.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <!-- Mật khẩu hiện tại -->
        <div>
            <label for="update_password_current_password" class="block text-slate-700 font-semibold text-sm uppercase tracking-wide mb-2 flex items-center gap-1.5">
                <span class="text-red-500">*</span> {{ __('MẬT KHẨU HIỆN TẠI') }}
            </label>
            <input id="update_password_current_password"
                   name="current_password"
                   type="password"
                   class="w-full bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all placeholder:text-slate-400"
                   autocomplete="current-password"
                   placeholder="Nhập mật khẩu hiện tại..." />
            @if ($errors->updatePassword->get('current_password'))
                <p class="text-xs text-red-500 font-semibold mt-1.5 tracking-wide">{{ $errors->updatePassword->get('current_password')[0] }}</p>
            @endif
        </div>

        <!-- Mật khẩu mới -->
        <div>
            <label for="update_password_password" class="block text-slate-700 font-semibold text-sm uppercase tracking-wide mb-2 flex items-center gap-1.5">
                <span class="text-red-500">*</span> {{ __('MẬT KHẨU MỚI') }}
            </label>
            <input id="update_password_password"
                   name="password"
                   type="password"
                   class="w-full bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all placeholder:text-slate-400"
                   autocomplete="new-password"
                   placeholder="Tối thiểu 8 ký tự..." />
            @if ($errors->updatePassword->get('password'))
                <p class="text-xs text-red-500 font-semibold mt-1.5 tracking-wide">{{ $errors->updatePassword->get('password')[0] }}</p>
            @endif
        </div>

        <!-- Xác nhận mật khẩu mới -->
        <div>
            <label for="update_password_password_confirmation" class="block text-slate-700 font-semibold text-sm uppercase tracking-wide mb-2 flex items-center gap-1.5">
                <span class="text-red-500">*</span> {{ __('XÁC NHẬN MẬT KHẨU MỚI') }}
            </label>
            <input id="update_password_password_confirmation"
                   name="password_confirmation"
                   type="password"
                   class="w-full bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all placeholder:text-slate-400"
                   autocomplete="new-password"
                   placeholder="Nhập lại mật khẩu mới..." />
            @if ($errors->updatePassword->get('password_confirmation'))
                <p class="text-xs text-red-500 font-semibold mt-1.5 tracking-wide">{{ $errors->updatePassword->get('password_confirmation')[0] }}</p>
            @endif
        </div>

        <!-- Nút lưu mật khẩu -->
        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-sm flex items-center gap-2 cursor-pointer">
                <svg class="w-5 h-5 text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                </svg>
                <span>{{ __('CẬP NHẬT MẬT KHẨU') }}</span>
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm font-medium text-emerald-600 flex items-center gap-1.5"
                >
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ __('Đã thay đổi mật khẩu thành công!') }}</span>
                </p>
            @endif
        </div>
    </form>
</section>
