<x-guest-layout>
    <!-- TAB CHUYỂN ĐỔI: ĐĂNG NHẬP / ĐĂNG KÝ -->
    <div class="grid grid-cols-2 gap-1.5 p-1 mb-6 bg-slate-100 rounded-xl border border-slate-200/80">
        <a href="{{ route('login') }}"
           class="inline-flex items-center justify-center gap-1.5 py-2.5 text-center font-bold text-xs tracking-wide rounded-lg bg-white text-indigo-600 shadow-xs transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
            <span>ĐĂNG NHẬP</span>
        </a>
        <a href="{{ route('register') }}"
           class="inline-flex items-center justify-center gap-1.5 py-2.5 text-center font-semibold text-xs tracking-wide rounded-lg text-slate-600 hover:text-slate-900 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            <span>ĐĂNG KÝ</span>
        </a>
    </div>

    <!-- Thông báo trạng thái session -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Địa chỉ Email -->
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                <span class="text-rose-500">*</span> Địa chỉ Email
            </label>
            <input id="email"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   required
                   autofocus
                   autocomplete="username"
                   placeholder="admin@gmail.com"
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-800 placeholder-slate-400 py-2.5 px-3.5 transition" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-600 font-medium" />
        </div>

        <!-- Mật khẩu -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="text-rose-500">*</span> Mật khẩu
                </label>
                @if (Route::has('password.request'))
                    <a class="text-xs text-indigo-600 hover:text-indigo-700 font-medium transition" href="{{ route('password.request') }}">
                        {{ __('Quên mật khẩu?') }}
                    </a>
                @endif
            </div>

            <input id="password"
                   type="password"
                   name="password"
                   required
                   autocomplete="current-password"
                   placeholder="••••••••"
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-800 placeholder-slate-400 py-2.5 px-3.5 transition" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-600 font-medium" />
        </div>

        <!-- Ghi nhớ đăng nhập (Remember Me) -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me"
                       type="checkbox"
                       class="rounded border-slate-300 text-indigo-600 shadow-xs focus:ring-indigo-500"
                       name="remember">
                <span class="ms-2 text-xs text-slate-600 font-medium">{{ __('Ghi nhớ phiên đăng nhập') }}</span>
            </label>
        </div>

        <!-- Nút Đăng nhập Indigo -->
        <div class="pt-2">
            <button type="submit" class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm tracking-wide transition shadow-xs hover:shadow flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                {{ __('Đăng nhập hệ thống') }}
            </button>
        </div>
    </form>

</x-guest-layout>
