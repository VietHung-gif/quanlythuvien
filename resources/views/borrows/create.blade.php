<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('borrows.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-xl text-slate-900 tracking-tight">
                    {{ __('LẬP PHIẾU MƯỢN SÁCH MỚI') }}
                </h2>
                <p class="text-xs text-slate-500">Tạo phiếu xuất mượn sách trực tiếp tại quầy cho độc giả</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        users: {{ Js::from($users) }},
        selectedUserId: '{{ old('user_id', '') }}',
        borrowerName: '{{ old('borrower_name', '') }}',
        borrowerEmail: '{{ old('borrower_email', '') }}',
        onUserChange() {
            if (!this.selectedUserId) return;
            const u = this.users.find(item => item.id == this.selectedUserId);
            if (u) {
                this.borrowerName = u.name;
                this.borrowerEmail = u.email;
            }
        }
    }">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8 shadow-xs">
                <header class="mb-6 border-b border-slate-100 pb-4">
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                        Thông tin phiếu xuất mượn
                    </h3>
                    <p class="mt-1 text-xs text-slate-500">
                        Hệ thống sẽ tự động kiểm tra và trừ đúng số lượng sách khả dụng trong kho sau khi lập phiếu thành công.
                    </p>
                </header>

                @if (session('error'))
                    <div class="mb-6 p-4 bg-rose-50 border border-rose-200/80 rounded-xl shadow-xs flex items-center text-rose-800">
                        <span class="text-xs font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('borrows.store') }}" class="space-y-6">
                    @csrf

                    <!-- Chọn sách -->
                    <div>
                        <label for="book_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <span class="text-rose-500">*</span> Chọn đầu sách cần mượn
                        </label>
                        @if($books->isEmpty())
                            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
                                ⚠️ Hiện tại không có đầu sách nào còn hàng trong kho để cho mượn!
                            </div>
                        @else
                            <select id="book_id"
                                    name="book_id"
                                    required
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-800 py-2.5 px-3.5 transition">
                                <option value="" class="text-slate-400">-- Chọn đầu sách trong kho --</option>
                                @foreach($books as $b)
                                    <option value="{{ $b->id }}" {{ old('book_id') == $b->id ? 'selected' : '' }}>
                                        📖 {{ $b->title }} - Tác giả: {{ $b->author }} (Còn {{ $b->available_copies }} cuốn khả dụng)
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        @error('book_id')
                            <p class="text-xs text-rose-600 font-medium mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Số lượng sách mượn -->
                    <div>
                        <label for="quantity" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <span class="text-rose-500">*</span> Số lượng mượn (cuốn)
                        </label>
                        <input id="quantity"
                               name="quantity"
                               type="number"
                               min="1"
                               value="{{ old('quantity', 1) }}"
                               required
                               placeholder="1"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-800 py-2.5 px-3.5 font-mono transition" />
                        @error('quantity')
                            <p class="text-xs text-rose-600 font-medium mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="border-t border-slate-100 pt-5">
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Thông tin độc giả (Người mượn)</h4>

                        <!-- Chọn User hệ thống nếu có -->
                        <div class="mb-4">
                            <label for="user_id" class="block text-xs font-semibold text-slate-600 mb-1.5">
                                Chọn tài khoản hệ thống (Tùy chọn)
                            </label>
                            <select id="user_id"
                                    name="user_id"
                                    x-model="selectedUserId"
                                    @change="onUserChange()"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-800 py-2.5 px-3.5 transition">
                                <option value="" class="text-slate-400">-- Khách vãng lai (nhập thông tin bên dưới) --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">
                                        👤 {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tên người mượn -->
                        <div class="mb-4">
                            <label for="borrower_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <span class="text-rose-500">*</span> Họ tên độc giả
                            </label>
                            <input id="borrower_name"
                                   name="borrower_name"
                                   type="text"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-800 py-2.5 px-3.5 transition"
                                   x-model="borrowerName"
                                   required
                                   placeholder="Họ và tên người mượn..." />
                            @error('borrower_name')
                                <p class="text-xs text-rose-600 font-medium mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Số điện thoại & Email -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="borrower_phone" class="block text-xs font-semibold text-slate-600 mb-1.5">
                                    Số điện thoại
                                </label>
                                <input id="borrower_phone"
                                       name="borrower_phone"
                                       type="text"
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-800 py-2.5 px-3.5 font-mono transition"
                                       value="{{ old('borrower_phone') }}"
                                       placeholder="Ví dụ: 0987654321" />
                            </div>

                            <div>
                                <label for="borrower_email" class="block text-xs font-semibold text-slate-600 mb-1.5">
                                    Địa chỉ Email
                                </label>
                                <input id="borrower_email"
                                       name="borrower_email"
                                       type="email"
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-800 py-2.5 px-3.5 transition"
                                       x-model="borrowerEmail"
                                       placeholder="email@example.com" />
                            </div>
                        </div>
                    </div>

                    <!-- Thời gian mượn & Hạn trả -->
                    <div class="border-t border-slate-100 pt-5">
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Mốc thời gian mượn trả</h4>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="borrow_date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                    <span class="text-rose-500">*</span> Ngày bắt đầu mượn
                                </label>
                                <input id="borrow_date"
                                       name="borrow_date"
                                       type="date"
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-800 py-2.5 px-3.5 font-mono transition"
                                       value="{{ old('borrow_date', $defaultBorrowDate) }}"
                                       required />
                            </div>

                            <div>
                                <label for="due_date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                    <span class="text-rose-500">*</span> Hạn chót trả sách
                                </label>
                                <input id="due_date"
                                       name="due_date"
                                       type="date"
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-800 py-2.5 px-3.5 font-mono transition"
                                       value="{{ old('due_date', $defaultDueDate) }}"
                                       required />
                                <span class="text-[11px] text-slate-400 font-mono mt-1 block">Mặc định: 14 ngày tính từ ngày lập phiếu.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Ghi chú mượn -->
                    <div class="border-t border-slate-100 pt-5">
                        <label for="notes" class="block text-xs font-semibold text-slate-600 mb-1.5">
                            Ghi chú tình trạng khi giao sách
                        </label>
                        <textarea id="notes"
                                  name="notes"
                                  rows="3"
                                  placeholder="Ghi chú về tình trạng bìa, số trang, phụ lục đi kèm..."
                                  class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-800 py-2 px-3 transition">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Nút thao tác -->
                    <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-100">
                        <a href="{{ route('borrows.index') }}"
                           class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs tracking-wide uppercase transition">
                            Hủy bỏ
                        </a>
                        <button type="submit"
                                :disabled="{{ $books->isEmpty() ? 'true' : 'false' }}"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs tracking-wide uppercase transition shadow-xs disabled:opacity-50">
                            Xác nhận cho mượn sách
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
