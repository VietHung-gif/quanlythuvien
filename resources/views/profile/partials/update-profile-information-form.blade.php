<section x-data="{
    avatarPreview: '{{ $user->avatar ? asset('storage/' . $user->avatar) : '' }}',
    isSubmitting: false,
    handleFileSelect(event) {
        const file = event.target.files[0];
        if (file) {
            this.avatarPreview = URL.createObjectURL(file);
        }
    }
}">
    <header class="border-b border-cyan-500/20 pb-4 mb-6">
        <h2 class="font-['Orbitron'] text-lg font-bold text-white tracking-wide flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-[#00f0ff] shadow-[0_0_8px_#00f0ff]"></span>
            {{ __('THÔNG TIN ĐỊNH DANH CÁ NHÂN') }}
        </h2>

        <p class="mt-1 text-xs text-slate-400 font-mono">
            {{ __('Cập nhật ảnh đại diện, họ tên, địa chỉ email và quản lý định danh trong hệ thống TIỂU ĐỘI 2P.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" 
          action="{{ route('profile.update') }}" 
          enctype="multipart/form-data" 
          @submit="isSubmitting = true"
          class="space-y-6">
        @csrf
        @method('patch')

        <!-- KHU VỰC TẢI ẢNH ĐẠI DIỆN (AVATAR UPLOAD SECTION) -->
        <div class="p-5 rounded-2xl bg-[#080b14]/90 border border-cyan-500/30 flex flex-col sm:flex-row items-center sm:items-start gap-6">
            <!-- Khung tròn Avatar xem trước kích thước lớn -->
            <div class="relative group shrink-0">
                <div class="w-24 h-24 rounded-full border-2 border-cyan-400 p-1 relative shadow-[0_0_15px_rgba(0,240,255,0.35)] group-hover:shadow-[0_0_25px_rgba(0,240,255,0.7)] group-hover:border-cyan-300 transition-all duration-300">
                    <!-- Ảnh xem trước nếu đã có hoặc vừa chọn file -->
                    <template x-if="avatarPreview">
                        <img :src="avatarPreview"
                             alt="Avatar Preview"
                             class="w-full h-full rounded-full object-cover">
                    </template>
                    
                    <!-- Khung mặc định nếu chưa có ảnh -->
                    <template x-if="!avatarPreview">
                        <div class="w-full h-full rounded-full bg-gradient-to-br from-cyan-900/60 via-slate-900 to-pink-900/50 flex items-center justify-center font-['Orbitron'] font-bold text-2xl text-cyan-300 select-none">
                            {{ mb_strtoupper(mb_substr($user->name, 0, 2, 'UTF-8'), 'UTF-8') }}
                        </div>
                    </template>

                    <!-- Lớp phủ icon máy ảnh khi hover -->
                    <label for="avatar" 
                           title="Bấm để tải ảnh mới từ thiết bị"
                           class="absolute inset-0 rounded-full bg-black/65 backdrop-blur-[2px] opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center text-cyan-300 cursor-pointer transition-all duration-200">
                        <svg class="w-6 h-6 mb-1 text-cyan-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="text-[10px] font-bold font-mono tracking-wider uppercase text-cyan-300">ĐỔI ẢNH</span>
                    </label>
                </div>

                <!-- Input file ẩn -->
                <input type="file" 
                       name="avatar" 
                       id="avatar" 
                       class="hidden" 
                       accept="image/jpeg,image/png,image/jpg,image/webp"
                       @change="handleFileSelect($event)">
            </div>

            <!-- Mô tả & Nút chọn ảnh từ máy -->
            <div class="flex-1 text-center sm:text-left space-y-2">
                <div class="flex items-center justify-center sm:justify-start gap-2">
                    <span class="font-['Orbitron'] text-sm font-bold text-white uppercase tracking-wider">
                        ẢNH ĐẠI DIỆN HỒ SƠ
                    </span>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-cyan-950 text-cyan-300 border border-cyan-500/40 uppercase">
                        AVATAR
                    </span>
                </div>
                <p class="text-xs text-slate-400">
                    Hỗ trợ tệp <span class="text-cyan-300 font-mono">JPG, PNG, WEBP</span> tối đa <span class="text-pink-400 font-mono">2MB</span>. Ảnh sau khi lưu sẽ đồng bộ tự động lên thanh Header Navbar và toàn bộ hệ thống.
                </p>
                <div class="pt-1 flex flex-wrap items-center justify-center sm:justify-start gap-3">
                    <label for="avatar" 
                           class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-cyan-300 bg-cyan-950/70 border border-cyan-400/60 hover:bg-cyan-900/60 hover:border-cyan-300 hover:text-white transition cursor-pointer shadow-[0_0_10px_rgba(0,240,255,0.2)]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Chọn ảnh mới</span>
                    </label>
                    <template x-if="avatarPreview && avatarPreview !== '{{ $user->avatar ? asset('storage/' . $user->avatar) : '' }}'">
                        <span class="text-xs font-mono text-emerald-400 flex items-center gap-1">
                            <span>✓</span> Đã chọn ảnh mới (Bấm "Lưu Thay Đổi" để cập nhật)
                        </span>
                    </template>
                </div>
                @if ($errors->get('avatar'))
                    <p class="text-xs text-[#ff007f] font-semibold tracking-wide mt-1">{{ $errors->get('avatar')[0] }}</p>
                @endif
            </div>
        </div>

        <!-- Vai trò hệ thống & Cấp bậc Danh hiệu -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-['Orbitron'] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                    VAI TRÒ TRUY CẬP (HỆ THỐNG GÁN)
                </label>
                <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-[#080b14] border border-cyan-500/30 text-slate-200 w-full">
                    <span class="w-2 h-2 rounded-full bg-[#00ff66] shadow-[0_0_6px_#00ff66]"></span>
                    <span class="font-['Orbitron'] text-xs font-bold text-[#00f0ff] uppercase tracking-wider">
                        {{ $user->role_name ?? strtoupper($user->role) }}
                    </span>
                    <span class="text-[11px] text-slate-500 font-mono">({{ $user->role }})</span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-['Orbitron'] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                    DANH HIỆU BẢNG PHONG THẦN
                </label>
                <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-[#080b14] border border-cyan-500/30 text-slate-200 w-full">
                    <span class="text-base">{{ $user->badge_icon }}</span>
                    <span class="font-['Orbitron'] text-xs font-bold uppercase tracking-wider truncate">
                        {{ $user->title_badge ?: 'Tân Thủ Nhập Môn' }}
                    </span>
                    <span class="text-[11px] text-amber-400 font-mono ml-auto">({{ number_format($user->exp ?? 0) }} EXP)</span>
                </div>
            </div>
        </div>

        <!-- Họ và Tên -->
        <div>
            <label for="name" class="block text-xs font-['Orbitron'] font-bold uppercase tracking-wider text-cyan-300 mb-1.5 flex items-center gap-1.5">
                <span class="text-[#ff007f]">*</span> {{ __('HỌ VÀ TÊN') }}
            </label>
            <input id="name"
                   name="name"
                   type="text"
                   class="cyber-input w-full rounded-lg text-sm py-2.5 px-3.5"
                   value="{{ old('name', $user->name) }}"
                   required
                   autofocus
                   autocomplete="name"
                   placeholder="Nhập họ và tên..." />
            @if ($errors->get('name'))
                <p class="text-xs text-[#ff007f] font-semibold mt-1.5 tracking-wide">{{ $errors->get('name')[0] }}</p>
            @endif
        </div>

        <!-- Địa chỉ Email -->
        <div>
            <label for="email" class="block text-xs font-['Orbitron'] font-bold uppercase tracking-wider text-cyan-300 mb-1.5 flex items-center gap-1.5">
                <span class="text-[#ff007f]">*</span> {{ __('ĐỊA CHỈ EMAIL') }}
            </label>
            <input id="email"
                   name="email"
                   type="email"
                   class="cyber-input w-full rounded-lg text-sm py-2.5 px-3.5"
                   value="{{ old('email', $user->email) }}"
                   required
                   autocomplete="username"
                   placeholder="email@example.com" />
            @if ($errors->get('email'))
                <p class="text-xs text-[#ff007f] font-semibold mt-1.5 tracking-wide">{{ $errors->get('email')[0] }}</p>
            @endif

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3 bg-amber-950/60 border border-[#ffb800]/40 rounded-lg text-xs text-amber-300">
                    <p class="flex items-center gap-2">
                        <span>⚠️ Địa chỉ email của bạn chưa được xác minh.</span>
                        <button form="send-verification" class="underline font-bold text-white hover:text-cyan-300 transition">
                            Bấm vào đây để gửi lại email xác minh.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-semibold text-emerald-400">
                            ✓ Liên kết xác minh mới đã được gửi tới hòm thư của bạn!
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Nút lưu thay đổi -->
        <div class="flex items-center gap-4 pt-2">
            <button type="submit" 
                    :disabled="isSubmitting"
                    class="cyber-btn-cyan px-6 py-2.5 rounded-xl font-['Orbitron'] font-bold text-xs tracking-wider uppercase flex items-center gap-2 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
                <!-- Icon loading spinner khi submit -->
                <svg x-show="isSubmitting" x-cloak class="w-4 h-4 animate-spin text-slate-950" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <!-- Icon check khi bình thường -->
                <svg x-show="!isSubmitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span x-text="isSubmitting ? 'ĐANG LƯU DỮ LIỆU...' : 'LƯU THAY ĐỔI'">{{ __('LƯU THAY ĐỔI') }}</span>
            </button>

            @if (session('status') === 'profile-updated' || session('success'))
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 4000)"
                    class="text-xs font-mono text-[#00ff66] flex items-center gap-1.5"
                >
                    <span class="w-2 h-2 rounded-full bg-[#00ff66] animate-pulse"></span>
                    <span>{{ session('success') ?? __('Cập nhật thông tin hồ sơ và ảnh đại diện thành công!') }}</span>
                </p>
            @endif
        </div>
    </form>
</section>
