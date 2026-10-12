<x-guest-layout>
    <!-- TAB CHUYỂN ĐỔI: ĐĂNG NHẬP / ĐĂNG KÝ -->
    <div class="grid grid-cols-2 gap-1.5 p-1 mb-6 bg-slate-100 rounded-xl border border-slate-200/80">
        <a href="{{ route('login') }}"
           class="inline-flex items-center justify-center gap-1.5 py-2.5 text-center font-semibold text-xs tracking-wide rounded-lg text-slate-600 hover:text-slate-900 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
            <span>ĐĂNG NHẬP</span>
        </a>
        <a href="{{ route('register') }}"
           class="inline-flex items-center justify-center gap-1.5 py-2.5 text-center font-bold text-xs tracking-wide rounded-lg bg-white text-indigo-600 shadow-xs transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            <span>ĐĂNG KÝ</span>
        </a>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Họ và Tên -->
        <div>
            <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                <span class="text-rose-500">*</span> Họ và tên
            </label>
            <input id="name"
                   type="text"
                   name="name"
                   value="{{ old('name') }}"
                   required
                   autofocus
                   autocomplete="name"
                   placeholder="Nhập họ và tên của bạn..."
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-800 placeholder-slate-400 py-2.5 px-3.5 transition" />
            <x-input-error :messages="$errors->get('name')" class="mt-1.5 text-xs text-rose-600 font-medium" />
        </div>

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
                   autocomplete="username"
                   placeholder="email@example.com"
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-800 placeholder-slate-400 py-2.5 px-3.5 transition" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-600 font-medium" />
        </div>

        <!-- Mật khẩu -->
        <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                <span class="text-rose-500">*</span> Mật khẩu
            </label>
            <input id="password"
                   type="password"
                   name="password"
                   required
                   autocomplete="new-password"
                   placeholder="Tối thiểu 8 ký tự..."
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-800 placeholder-slate-400 py-2.5 px-3.5 transition" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-600 font-medium" />
        </div>

        <!-- Xác nhận Mật khẩu -->
        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                <span class="text-rose-500">*</span> Nhập lại mật khẩu
            </label>
            <input id="password_confirmation"
                   type="password"
                   name="password_confirmation"
                   required
                   autocomplete="new-password"
                   placeholder="Nhập lại mật khẩu để xác nhận..."
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-800 placeholder-slate-400 py-2.5 px-3.5 transition" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs text-rose-600 font-medium" />
        </div>

        <!-- Nút Đăng ký Indigo -->
        <div class="pt-3">
            <button type="submit" class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm tracking-wide uppercase transition shadow-xs hover:shadow flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                {{ __('Đăng ký tài khoản') }}
            </button>
        </div>

        <div class="text-center pt-2">
            <span class="text-xs text-slate-500">Đã có tài khoản?</span>
            <a class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 ms-1 transition" href="{{ route('login') }}">
                {{ __('Đăng nhập ngay') }} &rarr;
            </a>
        </div>
    </form>
</x-guest-layout>
