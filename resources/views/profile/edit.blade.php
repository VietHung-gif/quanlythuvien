<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="inline-block w-2.5 h-6 rounded-full bg-indigo-600"></span>
                    {{ __('HỒ SƠ CÁ NHÂN & BẢO MẬT') }}
                </h2>
                <p class="text-xs text-slate-500 mt-1">Quản lý thông tin tài khoản, cập nhật mật khẩu và bảo mật hệ thống TIỂU ĐỘI 2P.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-3 py-1 rounded-lg bg-slate-100 border border-slate-200 text-slate-700 font-mono text-xs font-semibold">
                    ĐỊNH DANH: {{ strtoupper(Auth::user()->role_name ?? Auth::user()->role) }}
                </span>
                @if(Auth::user()->title_badge)
                    <span class="px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1.5 {{ Auth::user()->badge_color }}">
                        <span>{!! Auth::user()->badge_icon_svg !!}</span>
                        <span>{{ Auth::user()->title_badge }}</span>
                    </span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 font-mono text-xs font-semibold">
                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span>{{ number_format(Auth::user()->exp ?? 0) }} EXP</span>
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Card 1: Cập nhật thông tin tài khoản -->
            <div class="bg-white rounded-2xl border border-slate-300 shadow-sm p-6 sm:p-8">
                @include('profile.partials.update-profile-information-form')
            </div>

            <!-- Card 2: Đổi mật khẩu đăng nhập -->
            <div class="bg-white rounded-2xl border border-slate-300 shadow-sm p-6 sm:p-8">
                @include('profile.partials.update-password-form')
            </div>

            <!-- Card 3: Xóa tài khoản vĩnh viễn (Khu vực nguy hiểm - Danger Zone) -->
            <div class="bg-white rounded-2xl border border-rose-300 shadow-sm p-6 sm:p-8 border-t-4 border-t-rose-500">
                @include('profile.partials.delete-user-form')
            </div>

        </div>
    </div>
</x-app-layout>
