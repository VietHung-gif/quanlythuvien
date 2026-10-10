<x-guest-layout>
    <!-- TAB CHUYỂN ĐỔI: ĐĂNG NHẬP / ĐĂNG KÝ -->
    <div class="grid grid-cols-2 gap-1.5 p-1 mb-6 bg-slate-100 rounded-xl border border-slate-200/80">
        <a href="{{ route('login') }}"
           class="py-2.5 text-center font-bold text-xs tracking-wide rounded-lg bg-white text-indigo-600 shadow-xs transition">
            🔑 ĐĂNG NHẬP
        </a>
        <a href="{{ route('register') }}"
           class="py-2.5 text-center font-semibold text-xs tracking-wide rounded-lg text-slate-600 hover:text-slate-900 transition">
            ✨ ĐĂNG KÝ
        </a>
    </div>

    <!-- Thông báo trạng thái session -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div x-data="{
        fillAccount(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'tieudoi2p';
        }
    }">
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

        <!-- Hộp tiện ích: Điền nhanh 3 tài khoản mẫu để test -->
        <div class="mt-6 pt-5 border-t border-slate-100">
            <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider text-center mb-2.5">
                Tài khoản trải nghiệm nhanh
            </div>
            <div class="grid grid-cols-3 gap-2">
                <button type="button"
                        @click="fillAccount('admin@gmail.com')"
                        class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/50 text-center transition group">
                    <div class="text-[11px] font-bold text-slate-800 group-hover:text-indigo-600">ADMIN</div>
                    <div class="text-[10px] text-slate-400 truncate">admin@...</div>
                </button>

                <button type="button"
                        @click="fillAccount('thuthu@gmail.com')"
                        class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/50 text-center transition group">
                    <div class="text-[11px] font-bold text-slate-800 group-hover:text-indigo-600">THỦ THƯ</div>
                    <div class="text-[10px] text-slate-400 truncate">thuthu@...</div>
                </button>

                <button type="button"
                        @click="fillAccount('docgia@gmail.com')"
                        class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/50 text-center transition group">
                    <div class="text-[11px] font-bold text-slate-800 group-hover:text-indigo-600">ĐỘC GIẢ</div>
                    <div class="text-[10px] text-slate-400 truncate">docgia@...</div>
                </button>
            </div>
            <p class="text-[11px] text-center text-slate-400 mt-2.5 font-mono">Mật khẩu mặc định chung: tieudoi2p</p>
        </div>
    </div>
</x-guest-layout>
