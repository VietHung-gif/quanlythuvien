<section x-data="profileAvatarCropper('{{ $user->avatar ? asset('storage/' . $user->avatar) : '' }}')">
    <header class="border-b border-slate-200 pb-5 mb-6">
        <h2 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2.5">
            <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
            {{ __('THÔNG TIN ĐỊNH DANH CÁ NHÂN') }}
        </h2>

        <p class="mt-1.5 text-sm text-slate-600 font-normal">
            {{ __('Cập nhật ảnh đại diện, họ tên, địa chỉ email và thông tin liên hệ của bạn trong hệ thống TIỂU ĐỘI 2P.') }}
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

        <!-- KHU VỰC TẢI ẢNH ĐẠI DIỆN (AVATAR UPLOAD SECTION CÓ TÍCH HỢP CẮT ẢNH DISCORD) -->
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-300 flex flex-col sm:flex-row items-center sm:items-start gap-6">
            <!-- Khung tròn Avatar xem trước kích thước lớn -->
            <div class="relative group shrink-0">
                <div class="w-24 h-24 rounded-full border-2 border-indigo-500 p-1 relative shadow-sm group-hover:border-indigo-600 transition-all duration-300 bg-white">
                    <!-- Ảnh xem trước nếu đã có hoặc vừa cắt ảnh mới -->
                    <template x-if="avatarPreview">
                        <img :src="avatarPreview"
                             alt="Avatar Preview"
                             class="w-full h-full rounded-full object-cover">
                    </template>
                    
                    <!-- Khung mặc định nếu chưa có ảnh -->
                    <template x-if="!avatarPreview">
                        <div class="w-full h-full rounded-full bg-indigo-50 border border-indigo-200 flex items-center justify-center font-bold text-2xl text-indigo-700 select-none">
                            {{ mb_strtoupper(mb_substr($user->name, 0, 2, 'UTF-8'), 'UTF-8') }}
                        </div>
                    </template>

                    <!-- Lớp phủ icon máy ảnh khi hover -->
                    <label for="avatar_file_picker" 
                           title="Bấm để tải ảnh và cắt ảnh đại diện"
                           class="absolute inset-0 rounded-full bg-slate-900/60 backdrop-blur-[2px] opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center text-white cursor-pointer transition-all duration-200">
                        <svg class="w-6 h-6 mb-1 text-white group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                        </svg>
                        <span class="text-[10px] font-bold tracking-wider uppercase text-white">ĐỔI ẢNH</span>
                    </label>
                </div>

                <!-- Input chọn ảnh từ máy để đưa vào bộ cắt ảnh Cropper -->
                <input type="file" 
                       id="avatar_file_picker" 
                       class="hidden" 
                       accept="image/jpeg,image/png,image/jpg,image/webp"
                       @change="openCropperWithFile($event)">

                <!-- Input file thực tế chứa ảnh đã cắt để gửi form lên server -->
                <input type="file" 
                       name="avatar" 
                       id="avatar_final_input" 
                       class="hidden">

                <!-- Input ẩn Base64 fallback đảm bảo gửi ảnh thành công 100% -->
                <input type="hidden" 
                       name="avatar_base64" 
                       :value="avatarBase64">
            </div>

            <!-- Mô tả & Nút chọn ảnh từ máy -->
            <div class="flex-1 text-center sm:text-left space-y-2">
                <div class="flex items-center justify-center sm:justify-start gap-2">
                    <span class="text-sm font-bold text-slate-900 uppercase tracking-wide">
                        ẢNH ĐẠI DIỆN HỒ SƠ
                    </span>
                    <span class="text-[11px] font-mono font-semibold px-2 py-0.5 rounded bg-slate-200 text-slate-700 uppercase">
                        AVATAR
                    </span>
                </div>
                <p class="text-xs text-slate-500">
                    Hỗ trợ tệp <span class="text-slate-700 font-semibold font-mono">JPG, PNG, WEBP</span> tối đa <span class="text-indigo-600 font-semibold font-mono">5MB</span>. Tích hợp bộ công cụ căn chỉnh, phóng to thu nhỏ và cắt ảnh đại diện tròn theo chuẩn.
                </p>
                <div class="pt-1 flex flex-wrap items-center justify-center sm:justify-start gap-3">
                    <label for="avatar_file_picker" 
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-300 transition cursor-pointer shadow-xs">
                        <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                        <span>Chọn & Cắt ảnh mới</span>
                    </label>
                    <template x-if="avatarBase64">
                        <span class="text-xs font-semibold text-emerald-600 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Đã cắt ảnh đại diện (Bấm "LƯU THAY ĐỔI" bên dưới để hoàn tất)</span>
                        </span>
                    </template>
                </div>
                @if ($errors->get('avatar'))
                    <p class="text-xs text-red-500 font-semibold tracking-wide mt-1">{{ $errors->get('avatar')[0] }}</p>
                @endif
                @if ($errors->get('avatar_base64'))
                    <p class="text-xs text-red-500 font-semibold tracking-wide mt-1">{{ $errors->get('avatar_base64')[0] }}</p>
                @endif
            </div>
        </div>

        <!-- Vai trò hệ thống & Cấp bậc Danh hiệu -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-slate-700 font-semibold text-sm uppercase tracking-wide mb-2">
                    VAI TRÒ TRUY CẬP (HỆ THỐNG GÁN)
                </label>
                <div class="inline-flex items-center gap-2.5 px-4 py-3 rounded-xl bg-slate-50 border border-slate-300 text-slate-800 w-full shadow-xs">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span class="text-sm font-bold text-slate-900 uppercase tracking-wide">
                        {{ $user->role_name ?? strtoupper($user->role) }}
                    </span>
                    <span class="text-xs text-slate-500 font-mono">({{ $user->role }})</span>
                </div>
            </div>

            <div>
                <label class="block text-slate-700 font-semibold text-sm uppercase tracking-wide mb-2">
                    DANH HIỆU BẢNG PHONG THẦN
                </label>
                <div class="inline-flex items-center gap-2.5 px-4 py-3 rounded-xl bg-slate-50 border border-slate-300 text-slate-800 w-full shadow-xs">
                    <span class="shrink-0">{!! $user->badge_icon_svg !!}</span>
                    <span class="text-sm font-bold text-slate-900 uppercase tracking-wide truncate">
                        {{ $user->title_badge ?: 'Tân Thủ Nhập Môn' }}
                    </span>
                    <span class="inline-flex items-center gap-1 text-xs text-amber-700 font-mono font-bold ml-auto">
                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span>{{ number_format($user->exp ?? 0) }} EXP</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Họ và Tên -->
        <div>
            <label for="name" class="block text-slate-700 font-semibold text-sm uppercase tracking-wide mb-2 flex items-center gap-1.5">
                <span class="text-red-500">*</span> {{ __('HỌ VÀ TÊN') }}
            </label>
            <input id="name"
                   name="name"
                   type="text"
                   class="w-full bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all placeholder:text-slate-400"
                   value="{{ old('name', $user->name) }}"
                   required
                   autofocus
                   autocomplete="name"
                   placeholder="Nhập họ và tên..." />
            @if ($errors->get('name'))
                <p class="text-xs text-red-500 font-semibold mt-1.5 tracking-wide">{{ $errors->get('name')[0] }}</p>
            @endif
        </div>

        <!-- Địa chỉ Email -->
        <div>
            <label for="email" class="block text-slate-700 font-semibold text-sm uppercase tracking-wide mb-2 flex items-center gap-1.5">
                <span class="text-red-500">*</span> {{ __('ĐỊA CHỈ EMAIL') }}
            </label>
            <input id="email"
                   name="email"
                   type="email"
                   class="w-full bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all placeholder:text-slate-400"
                   value="{{ old('email', $user->email) }}"
                   required
                   autocomplete="username"
                   placeholder="email@example.com" />
            @if ($errors->get('email'))
                <p class="text-xs text-red-500 font-semibold mt-1.5 tracking-wide">{{ $errors->get('email')[0] }}</p>
            @endif

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-4 bg-amber-50 border border-amber-300 rounded-xl text-xs text-amber-800">
                    <p class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span>Địa chỉ email của bạn chưa được xác minh.</span>
                        <button form="send-verification" class="underline font-bold text-indigo-700 hover:text-indigo-900 transition">
                            Bấm vào đây để gửi lại email xác minh.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-semibold text-emerald-600 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Liên kết xác minh mới đã được gửi tới hòm thư của bạn!</span>
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Số điện thoại liên hệ -->
        <div>
            <label for="phone" class="block text-slate-700 font-semibold text-sm uppercase tracking-wide mb-2 flex items-center gap-1.5">
                {{ __('SỐ ĐIỆN THOẠI LIÊN HỆ') }}
            </label>
            <input id="phone"
                   name="phone"
                   type="text"
                   class="w-full bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all placeholder:text-slate-400"
                   value="{{ old('phone', auth()->user()->phone) }}"
                   placeholder="Nhập số điện thoại liên hệ (ví dụ: 0912345678)..." />
            @if ($errors->get('phone'))
                <p class="text-xs text-red-500 font-semibold mt-1.5 tracking-wide">{{ $errors->get('phone')[0] }}</p>
            @endif
        </div>

        <!-- Địa chỉ giao sách mặc định (Ký túc xá / Nhà riêng) -->
        <div>
            <label for="address" class="block text-slate-700 font-semibold text-sm uppercase tracking-wide mb-2 flex items-center gap-1.5">
                {{ __('ĐỊA CHỈ GIAO SÁCH MẶC ĐỊNH (KÝ TÚC XÁ / NHÀ RIÊNG)') }}
            </label>
            <input id="address"
                   name="address"
                   type="text"
                   class="w-full bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all placeholder:text-slate-400"
                   value="{{ old('address', auth()->user()->address) }}"
                   placeholder="Ví dụ: Phòng 402 KTX Khu B hoặc 123 Đường Nguyễn Huệ..." />
            @if ($errors->get('address'))
                <p class="text-xs text-red-500 font-semibold mt-1.5 tracking-wide">{{ $errors->get('address')[0] }}</p>
            @endif
        </div>

        <!-- Nút lưu thay đổi -->
        <div class="flex items-center gap-4 pt-2">
            <button type="submit" 
                    :disabled="isSubmitting"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-sm flex items-center gap-2 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
                <!-- Icon loading spinner khi submit -->
                <svg x-show="isSubmitting" x-cloak class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
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
                    class="text-sm font-medium text-emerald-600 flex items-center gap-1.5"
                >
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ session('success') ?? __('Cập nhật thông tin hồ sơ và ảnh đại diện thành công!') }}</span>
                </p>
            @endif
        </div>
    </form>

    <!-- ========================================================================= -->
    <!-- MODAL CẮT HÌNH ẢNH ĐẠI DIỆN AVATAR PROFILE (DISCORD DARK THEME STYLE)      -->
    <!-- ========================================================================= -->
    <div x-show="cropperOpen" 
         x-cloak
         @keydown.escape.window="if (cropperOpen) closeCropper()"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-[2px]">
        
        <!-- Hộp Modal Chỉnh sửa Hình ảnh -->
        <div @click.away="closeCropper()"
             class="w-full max-w-[460px] bg-[#313338] text-white rounded-2xl shadow-2xl overflow-hidden border border-[#3f4147] flex flex-col">
            
            <!-- Header Modal -->
            <div class="px-6 pt-5 pb-3 flex items-center justify-between">
                <h3 class="text-base font-bold text-white tracking-wide">
                    Chỉnh sửa Hình ảnh
                </h3>
                <button type="button" 
                        @click="closeCropper()" 
                        title="Đóng"
                        class="text-zinc-400 hover:text-white p-1 rounded-lg hover:bg-[#383a40] transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Viewport Cắt ảnh tròn (Kéo thả, căn chỉnh, phóng to thu nhỏ) -->
            <div class="mx-6 my-2 h-[290px] bg-[#1e1f22] rounded-xl relative overflow-hidden flex items-center justify-center select-none cursor-grab active:cursor-grabbing touch-none"
                 @mousedown="startDrag($event)"
                 @mousemove="onDrag($event)"
                 @mouseup="stopDrag()"
                 @mouseleave="stopDrag()"
                 @touchstart.passive="startDrag($event)"
                 @touchmove="onDrag($event)"
                 @touchend="stopDrag()"
                 @wheel.prevent="onWheel($event)">
                
                <!-- Hình ảnh người dùng chọn -->
                <template x-if="rawImageSrc">
                    <img :src="rawImageSrc" 
                         :style="`width: ${origW}px; height: ${origH}px; max-width: none; max-height: none; transform: translate(calc(-50% + ${panX}px), calc(-50% + ${panY}px)) rotate(${rotation}deg) scale(${zoom * baseScale}); transform-origin: center center;`" 
                         class="absolute left-1/2 top-1/2 pointer-events-none select-none transition-none"
                         draggable="false"
                         alt="Avatar Crop Target">
                </template>

                <!-- Vòng tròn viền trắng & lớp đổ bóng che ngoài (Discord circular viewport) -->
                <div class="absolute w-[240px] h-[240px] rounded-full border-[3px] border-white shadow-[0_0_0_9999px_rgba(0,0,0,0.58)] pointer-events-none z-10 left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2"></div>
            </div>

            <!-- Thanh điều khiển Zoom Slider & Xoay 90° -->
            <div class="flex items-center gap-3 px-6 py-3">
                <!-- Icon ảnh nhỏ -->
                <svg class="w-4 h-4 text-zinc-400 shrink-0 select-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>

                <!-- Thanh trượt Zoom Slider -->
                <input type="range" 
                       min="1" 
                       max="3" 
                       step="0.01" 
                       x-model.number="zoom"
                       class="discord-crop-slider w-full h-1.5 bg-[#4e5058] rounded-lg appearance-none cursor-pointer accent-[#5865f2] focus:outline-none">

                <!-- Icon ảnh lớn -->
                <svg class="w-6 h-6 text-zinc-400 shrink-0 select-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>

                <!-- Nút Xoay 90 độ -->
                <button type="button" 
                        @click="rotateClockwise()" 
                        title="Xoay ảnh 90°"
                        class="ml-1 p-2 rounded-lg text-zinc-300 hover:text-white hover:bg-[#383a40] transition shrink-0 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
            </div>

            <!-- Footer chứa các nút hành động -->
            <div class="px-6 py-4 bg-[#232428] flex items-center justify-between border-t border-[#35373c]/60">
                <!-- Đặt lại -->
                <button type="button" 
                        @click="resetCropper()" 
                        class="text-sm font-medium text-zinc-300 hover:text-white hover:underline transition cursor-pointer">
                    Đặt lại
                </button>

                <!-- Hủy bỏ & Tham Gia -->
                <div class="flex items-center gap-3">
                    <button type="button" 
                            @click="closeCropper()" 
                            class="px-5 py-2.5 rounded-lg text-sm font-semibold text-white bg-[#4e5058] hover:bg-[#6d6f78] active:bg-[#474950] transition cursor-pointer shadow-xs">
                        Hủy bỏ
                    </button>
                    <button type="button" 
                            @click="applyCroppedImage()" 
                            class="px-6 py-2.5 rounded-lg text-sm font-bold text-white bg-[#5865f2] hover:bg-[#4752c4] active:bg-[#3c45a5] transition cursor-pointer shadow-md flex items-center gap-2">
                        Tham Gia
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tùy chỉnh thanh trượt Discord Cropper Thumb -->
    <style>
        .discord-crop-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #ffffff;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0,0,0,0.5);
        }
        .discord-crop-slider::-moz-range-thumb {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #ffffff;
            border: none;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0,0,0,0.5);
        }
    </style>

    <!-- Logic quản lý Avatar & Bộ cắt ảnh tròn Canvas -->
    <script>
    function profileAvatarCropper(initialAvatarUrl) {
        return {
            avatarPreview: initialAvatarUrl,
            avatarBase64: '',
            isSubmitting: false,
            cropperOpen: false,
            rawImageSrc: '',
            imgElement: null,
            origW: 0,
            origH: 0,
            baseScale: 1,
            zoom: 1,
            rotation: 0,
            panX: 0,
            panY: 0,
            isDragging: false,
            dragStartX: 0,
            dragStartY: 0,
            initialPanX: 0,
            initialPanY: 0,
            cropSize: 240, // Kích thước đường kính vòng tròn cắt (px)

            openCropperWithFile(event) {
                const file = event.target.files && event.target.files[0];
                if (!file) return;

                if (!file.type.match(/^image\/(jpeg|png|webp|jpg)$/i)) {
                    alert('Vui lòng chọn file hình ảnh (JPG, PNG, WEBP).');
                    event.target.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = new Image();
                    img.onload = () => {
                        this.imgElement = img;
                        this.origW = img.naturalWidth || img.width;
                        this.origH = img.naturalHeight || img.height;
                        
                        // Tính tỉ lệ phóng ban đầu để ảnh phủ kín hoàn toàn vòng tròn cắt 240px
                        this.baseScale = Math.max(this.cropSize / this.origW, this.cropSize / this.origH);
                        this.zoom = 1;
                        this.rotation = 0;
                        this.panX = 0;
                        this.panY = 0;
                        this.rawImageSrc = e.target.result;
                        this.cropperOpen = true;
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
                event.target.value = '';
            },

            startDrag(e) {
                this.isDragging = true;
                const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                this.dragStartX = clientX;
                this.dragStartY = clientY;
                this.initialPanX = this.panX;
                this.initialPanY = this.panY;
            },

            onDrag(e) {
                if (!this.isDragging) return;
                if (e.cancelable && e.touches) {
                    e.preventDefault();
                }
                const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                const deltaX = clientX - this.dragStartX;
                const deltaY = clientY - this.dragStartY;
                this.panX = this.initialPanX + deltaX;
                this.panY = this.initialPanY + deltaY;
            },

            stopDrag() {
                this.isDragging = false;
            },

            onWheel(e) {
                const delta = e.deltaY < 0 ? 0.08 : -0.08;
                let newZoom = this.zoom + delta;
                newZoom = Math.min(Math.max(newZoom, 1), 3);
                this.zoom = parseFloat(newZoom.toFixed(2));
            },

            rotateClockwise() {
                this.rotation = (this.rotation + 90) % 360;
            },

            resetCropper() {
                this.zoom = 1;
                this.panX = 0;
                this.panY = 0;
                this.rotation = 0;
            },

            closeCropper() {
                this.cropperOpen = false;
                this.rawImageSrc = '';
                this.imgElement = null;
            },

            applyCroppedImage() {
                if (!this.imgElement) return;

                const outputSize = 512;
                const canvas = document.createElement('canvas');
                canvas.width = outputSize;
                canvas.height = outputSize;
                const ctx = canvas.getContext('2d');

                if (!ctx) return;

                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = 'high';

                const scaleFactor = outputSize / this.cropSize;

                ctx.save();
                // Di chuyển tâm canvas
                ctx.translate(outputSize / 2, outputSize / 2);

                // Tịnh tiến theo độ dịch chuyển (pan)
                ctx.translate(this.panX * scaleFactor, this.panY * scaleFactor);

                // Xoay góc
                ctx.rotate((this.rotation * Math.PI) / 180);

                // Phóng to / Thu nhỏ
                const totalScale = this.baseScale * this.zoom * scaleFactor;
                ctx.scale(totalScale, totalScale);

                // Vẽ ảnh gốc căn giữa tâm
                ctx.drawImage(this.imgElement, -this.origW / 2, -this.origH / 2, this.origW, this.origH);
                ctx.restore();

                // Xuất ảnh Base64 chất lượng cao
                const dataUrl = canvas.toDataURL('image/jpeg', 0.92);
                this.avatarPreview = dataUrl;
                this.avatarBase64 = dataUrl;

                // Đưa file vào input[name=avatar] qua DataTransfer
                try {
                    canvas.toBlob((blob) => {
                        if (blob) {
                            const file = new File([blob], 'avatar_cropped.jpg', { type: 'image/jpeg' });
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(file);
                            const fileInput = document.getElementById('avatar_final_input');
                            if (fileInput) {
                                fileInput.files = dataTransfer.files;
                            }
                        }
                    }, 'image/jpeg', 0.92);
                } catch (err) {
                    console.warn('DataTransfer fallback to avatar_base64:', err);
                }

                this.cropperOpen = false;
            }
        };
    }
    </script>
</section>
