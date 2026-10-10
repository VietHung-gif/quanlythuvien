<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('forum.show', $post->id) }}" 
                   class="cyber-btn-cyan px-3.5 py-2 rounded-lg text-xs font-['Orbitron'] font-bold tracking-wider uppercase transition">
                    &larr; QUAY LẠI BÀI VIẾT
                </a>
                <div>
                    <h2 class="font-['Orbitron'] font-bold text-xl text-white tracking-wide">
                        CHỈNH SỬA BÀI VIẾT
                    </h2>
                    <p class="text-xs text-slate-400 font-mono mt-0.5">
                        Cập nhật nội dung hoặc chuyên mục cho bài viết #{{ $post->id }}
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="cyber-card rounded-2xl p-6 sm:p-8 border border-cyan-500/40 shadow-[0_0_35px_rgba(0,240,255,0.2)]">
                
                <form action="{{ route('forum.update', $post->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <h3 class="font-['Orbitron'] text-xl font-bold text-white flex items-center gap-2">
                            <span class="text-cyan-400">✏️</span> CẬP NHẬT NỘI DUNG CHIA SẺ
                        </h3>
                        <p class="text-xs text-slate-400 font-mono mt-1">
                            Lưu ý: Mọi chỉnh sửa sẽ được lưu và hiển thị ngay trên bài viết ở Hội Quán.
                        </p>
                    </div>

                    <!-- Hiển thị lỗi validation nếu có -->
                    @if ($errors->any())
                        <div class="p-4 rounded-xl bg-rose-950/70 border border-rose-500/50 text-rose-300 text-xs space-y-1">
                            <div class="font-bold">Vui lòng kiểm tra lại thông tin:</div>
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Tiêu đề -->
                    <div>
                        <label class="block text-xs font-['Orbitron'] font-bold text-cyan-300 mb-1.5 uppercase">
                            TIÊU ĐỀ BÀI VIẾT <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" 
                               name="title" 
                               value="{{ old('title', $post->title) }}"
                               required 
                               minlength="5"
                               placeholder="Nhập tiêu đề bài viết..."
                               class="cyber-input w-full rounded-xl text-sm py-2.5 px-3.5">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Chuyên mục -->
                        <div>
                            <label class="block text-xs font-['Orbitron'] font-bold text-cyan-300 mb-1.5 uppercase">
                                CHUYÊN MỤC <span class="text-rose-400">*</span>
                            </label>
                            <select name="category_id" required class="cyber-input w-full rounded-xl text-sm py-2.5 px-3">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $post->category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->icon }} {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Gắn thẻ sách (Book Tagging) -->
                        <div>
                            <label class="block text-xs font-['Orbitron'] font-bold text-cyan-300 mb-1.5 uppercase">
                                GẮN THẺ SÁCH (TÙY CHỌN):
                            </label>
                            <select name="book_id" class="cyber-input w-full rounded-xl text-sm py-2.5 px-3">
                                <option value="">-- Không gắn thẻ sách --</option>
                                @foreach($books as $b)
                                    <option value="{{ $b->id }}" {{ old('book_id', $post->book_id) == $b->id ? 'selected' : '' }}>
                                        {{ $b->title }} (Tác giả: {{ $b->author }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Nội dung bài viết -->
                    <div>
                        <label class="block text-xs font-['Orbitron'] font-bold text-cyan-300 mb-1.5 uppercase">
                            NỘI DUNG BÀI VIẾT <span class="text-rose-400">*</span>
                        </label>
                        <textarea name="content" 
                                  rows="10" 
                                  required 
                                  minlength="10"
                                  placeholder="Soạn nội dung bài viết của bạn tại đây..."
                                  class="cyber-input w-full rounded-xl text-sm py-3 px-3.5 leading-relaxed">{{ old('content', $post->content) }}</textarea>
                    </div>

                    <!-- Nút bấm submit -->
                    <div class="flex items-center justify-between pt-4 border-t border-cyan-500/20">
                        <a href="{{ route('forum.show', $post->id) }}" class="px-4 py-2.5 rounded-xl text-xs font-mono text-slate-400 hover:text-white border border-slate-700 hover:border-slate-500 transition">
                            HỦY BỎ
                        </a>

                        <button type="submit" class="cyber-btn-cyan px-6 py-2.5 rounded-xl font-['Orbitron'] font-bold text-xs tracking-wider uppercase shadow-[0_0_15px_rgba(0,240,255,0.4)]">
                            💾 LƯU CẬP NHẬT
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
