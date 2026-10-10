<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('books.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-xl text-slate-900 tracking-tight">
                    {{ __('HIỆU CHỈNH THÔNG SỐ ĐẦU SÁCH') }}
                </h2>
                <p class="text-xs text-slate-500">Cập nhật thông tin chi tiết cho: <strong class="text-indigo-600 font-semibold">{{ $book->title }}</strong></p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8 shadow-xs">
                <header class="mb-6 border-b border-slate-100 pb-4">
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Thông tin đầu sách
                    </h3>
                    <p class="mt-1 text-xs text-slate-500">
                        Cập nhật các thuộc tính và số lượng bản in tồn kho trong thư viện.
                    </p>
                </header>

                <form method="POST" action="{{ route('books.update', $book->id) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <!-- Tên Sách -->
                    <div>
                        <label for="title" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            <span class="text-rose-500">*</span> Tên đầu sách
                        </label>
                        <input id="title"
                               name="title"
                               type="text"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-800 placeholder-slate-400 py-2.5 px-3.5 transition"
                               value="{{ old('title', $book->title) }}"
                               required
                               autofocus />
                        @error('title')
                            <p class="text-xs text-rose-600 font-medium mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tác Giả -->
                    <div>
                        <label for="author" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            <span class="text-rose-500">*</span> Tác giả
                        </label>
                        <input id="author"
                               name="author"
                               type="text"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-800 placeholder-slate-400 py-2.5 px-3.5 transition"
                               value="{{ old('author', $book->author) }}"
                               required />
                        @error('author')
                            <p class="text-xs text-rose-600 font-medium mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Link Ảnh Bìa Sách (URL) -->
                    <div>
                        <label for="cover_image" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                            <span>Ảnh bìa sách (URL tùy chọn)</span>
                            <span class="text-[11px] text-slate-400 font-mono">Unsplash / OpenLibrary</span>
                        </label>
                        <input id="cover_image"
                               name="cover_image"
                               type="url"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-800 placeholder-slate-400 py-2.5 px-3.5 transition"
                               value="{{ old('cover_image', $book->cover_image) }}"
                               placeholder="https://images.unsplash.com/... hoặc để trống để dùng ảnh mặc định" />
                        @error('cover_image')
                            <p class="text-xs text-rose-600 font-medium mt-1.5">{{ $message }}</p>
                        @enderror
                        @if($book->cover_image)
                            <div class="mt-3 flex items-center gap-3 p-2 rounded-xl bg-slate-50 border border-slate-200/80 w-fit">
                                <img src="{{ $book->cover_url }}" alt="Preview" class="w-10 h-14 object-cover rounded-lg border border-slate-200 shadow-2xs">
                                <span class="text-xs text-slate-500">Ảnh bìa hiện tại</span>
                            </div>
                        @endif
                    </div>

                    <!-- Mô tả / Tóm tắt nội dung sách -->
                    <div>
                        <label for="description" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                            <span>Mô tả / Tóm tắt nội dung sách</span>
                            <span class="text-[11px] text-slate-400 font-mono">Hiển thị trong gợi ý</span>
                        </label>
                        <textarea id="description"
                                  name="description"
                                  rows="3"
                                  class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-800 placeholder-slate-400 py-2.5 px-3.5 transition"
                                  placeholder="Nhập tóm tắt nội dung cuốn sách hoặc thông điệp chính...">{{ old('description', $book->description) }}</textarea>
                        @error('description')
                            <p class="text-xs text-rose-600 font-medium mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Thể Loại -->
                    <div>
                        <label for="category_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            <span class="text-rose-500">*</span> Phân loại thể loại
                        </label>
                        <select id="category_id"
                                name="category_id"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-800 py-2.5 px-3.5 transition">
                            <option value="" class="text-slate-400">-- Chọn thể loại từ danh mục --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>
                                    🏷️ {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-xs text-rose-600 font-medium mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Số Lượng Tồn Kho -->
                    <div>
                        <label for="quantity" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            <span class="text-rose-500">*</span> Số lượng tồn hiện có
                        </label>
                        <input id="quantity"
                               name="quantity"
                               type="number"
                               min="0"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-800 py-2.5 px-3.5 font-mono transition"
                               value="{{ old('quantity', $book->quantity) }}"
                               required />
                        @error('quantity')
                            <p class="text-xs text-rose-600 font-medium mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nút Thao Tác -->
                    <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-100">
                        <a href="{{ route('books.index') }}"
                           class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs tracking-wide uppercase transition">
                            Hủy bỏ
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs tracking-wide uppercase transition shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Cập nhật dữ liệu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
