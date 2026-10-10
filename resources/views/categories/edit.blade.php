<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('categories.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-xl text-slate-900 tracking-tight">
                    {{ __('CHỈNH SỬA THỂ LOẠI') }}
                </h2>
                <p class="text-xs text-slate-500">Cập nhật thông tin phân loại cho thể loại: <strong class="text-indigo-600 font-semibold">{{ $category->name }}</strong></p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8 shadow-xs">
                <header class="mb-6 border-b border-slate-100 pb-4">
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Thông tin thể loại
                    </h3>
                    <p class="mt-1 text-xs text-slate-500">
                        Chỉnh sửa tên và thông tin mô tả chi tiết của thể loại.
                    </p>
                </header>

                <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <!-- Tên Thể Loại -->
                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            <span class="text-rose-500">*</span> Tên thể loại
                        </label>
                        <input id="name"
                               name="name"
                               type="text"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-800 placeholder-slate-400 py-2.5 px-3.5 transition"
                               value="{{ old('name', $category->name) }}"
                               required
                               autofocus />
                        @error('name')
                            <p class="text-xs text-rose-600 font-medium mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mô Tả -->
                    <div>
                        <label for="description" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Mô tả danh mục
                        </label>
                        <textarea id="description"
                                  name="description"
                                  rows="4"
                                  class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-800 placeholder-slate-400 py-2 px-3 transition">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                            <p class="text-xs text-rose-600 font-medium mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nút thao tác -->
                    <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-100">
                        <a href="{{ route('categories.index') }}"
                           class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs tracking-wide uppercase transition">
                            Hủy bỏ
                        </a>
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs tracking-wide uppercase transition shadow-xs">
                            Cập nhật thể loại
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
