<section class="space-y-6">
    <header class="border-b border-rose-200 pb-5">
        <h2 class="text-xl font-bold text-rose-700 tracking-tight flex items-center gap-2.5">
            <span class="w-2.5 h-2.5 rounded-full bg-rose-600 animate-pulse"></span>
            {{ __('KHU VỰC NGUY HIỂM // XÓA TÀI KHOẢN VĨNH VIỄN') }}
        </h2>

        <p class="mt-1.5 text-sm text-rose-600 font-medium">
            {{ __('CẢNH BÁO: Toàn bộ dữ liệu, lịch sử mượn sách và thông tin cá nhân của bạn sẽ bị xóa sạch khỏi hệ thống. Thao tác này KHÔNG THỂ khôi phục!') }}
        </p>
    </header>

    <div>
        <button
            type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="bg-rose-600 hover:bg-rose-700 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-sm flex items-center gap-2 cursor-pointer text-xs uppercase tracking-wider"
        >
            <svg class="w-4 h-4 shrink-0 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
            </svg>
            {{ __('XÓA TÀI KHOẢN VĨNH VIỄN') }}
        </button>
    </div>

    <!-- Modal Xác Nhận Xóa Tài Khoản -->
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 sm:p-8 bg-white border border-rose-200 rounded-2xl shadow-2xl text-left">
            @csrf
            @method('delete')

            <div class="text-center pb-5 border-b border-slate-200">
                <div class="w-12 h-12 mx-auto rounded-full bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-600 shadow-xs mb-3">
                    <svg class="w-6 h-6 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 tracking-tight">
                    XÁC NHẬN HỦY TÀI KHOẢN
                </h3>
                <p class="text-sm text-rose-600 font-medium mt-1">
                    Hành động này sẽ xóa vĩnh viễn quyền truy cập và dữ liệu cá nhân của bạn.
                </p>
            </div>

            <div class="mt-6 space-y-4">
                <label for="password" class="block text-sm text-slate-700 font-semibold mb-1">
                    Vui lòng nhập mật khẩu hiện tại của bạn để xác thực yêu cầu:
                </label>

                <div>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="w-full bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl px-4 py-3 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all placeholder:text-slate-400"
                        placeholder="Nhập mật khẩu của bạn..."
                        required
                    />
                    @if ($errors->userDeletion->get('password'))
                        <p class="text-xs text-rose-600 font-semibold mt-1.5 tracking-wide">{{ $errors->userDeletion->get('password')[0] }}</p>
                    @endif
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <button
                    type="button"
                    x-on:click="$dispatch('close')"
                    class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs tracking-wider uppercase px-4 py-2.5 rounded-xl transition cursor-pointer"
                >
                    HỦY THAO TÁC
                </button>

                <button
                    type="submit"
                    class="bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs tracking-wider uppercase px-5 py-2.5 rounded-xl transition shadow-sm flex items-center gap-1.5 cursor-pointer"
                >
                    XÁC NHẬN XÓA NGAY
                </button>
            </div>
        </form>
    </x-modal>
</section>