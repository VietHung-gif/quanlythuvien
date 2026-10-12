<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('forum.index') }}" 
                   class="cyber-btn-cyan px-3.5 py-2 rounded-lg text-xs font-['Orbitron'] font-bold tracking-wider uppercase transition">
                    &larr; HỘI QUÁN
                </a>
                <div>
                    <h2 class="font-['Orbitron'] font-bold text-xl text-white tracking-wide">
                        SOẠN BÀI VIẾT MỚI
                    </h2>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="cyber-card rounded-2xl p-6 sm:p-8 border border-cyan-500/40 shadow-[0_0_35px_rgba(0,240,255,0.2)]">
                
                <form action="{{ route('forum.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <h3 class="font-['Orbitron'] text-xl font-bold text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            <span>CHIA SẺ TRI THỨC VÀ GÓC NHÌN</span>
                        </h3>
                        <p class="text-xs text-slate-400 font-mono mt-1">
                            Lưu ý: Diễn đàn không cộng EXP khi đăng bài để duy trì chất lượng thảo luận.
                        </p>
                    </div>

                    <!-- Khối hiển thị lỗi validation -->
                    @if ($errors->any())
                        <div class="mb-4 p-3 rounded-lg bg-rose-500/20 border border-rose-500 text-rose-300 text-sm">
                            @foreach ($errors->all() as $error) 
                                <div>• {{ $error }}</div> 
                            @endforeach
                        </div>
                    @endif

                    <!-- Khối hiển thị lỗi session nếu có -->
                    @if (session('error'))
                        <div class="mb-4 p-3 rounded-lg bg-rose-500/20 border border-rose-500 text-rose-300 text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Tiêu đề -->
                    <div>
                        <label class="block text-xs font-['Orbitron'] font-bold text-cyan-300 mb-1.5 uppercase">
                            TIÊU ĐỀ BÀI VIẾT <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" 
                               name="title" 
                               value="{{ old('title') }}" 
                               required 
                               placeholder="Ví dụ: Review sách Đắc Nhân Tâm - Bài học giao tiếp vượt thời gian..."
                               class="cyber-input w-full rounded-xl text-sm py-2.5 px-3.5">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Chuyên mục -->
                        <div>
                            <label class="block text-xs font-['Orbitron'] font-bold text-cyan-300 mb-1.5 uppercase">
                                CHUYÊN MỤC <span class="text-rose-400">*</span>
                            </label>
                            <select name="category_id" required class="cyber-input w-full rounded-xl text-sm py-2.5 px-3">
                                <option value="">-- Chọn chuyên mục bài viết --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
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
                                    <option value="{{ $b->id }}" {{ old('book_id') == $b->id ? 'selected' : '' }}>
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
                                  placeholder="Soạn nội dung bài viết của bạn tại đây... Hãy chia sẻ cảm nhận chân thực, các trích dẫn hay hoặc vấn đề bạn muốn mọi người cùng bàn luận."
                                  class="cyber-input w-full rounded-xl text-sm py-3 px-3.5 leading-relaxed">{{ old('content') }}</textarea>
                    </div>

                    <!-- Nút bấm submit -->
                    <div class="flex items-center justify-between pt-4 border-t border-cyan-500/20">
                        <a href="{{ route('forum.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-mono text-slate-400 hover:text-white border border-slate-700 hover:border-slate-500 transition">
                            HỦY BỎ
                        </a>

                        <button type="submit" class="cyber-btn-cyan px-6 py-2.5 rounded-xl font-['Orbitron'] font-bold text-xs tracking-wider uppercase shadow-[0_0_15px_rgba(0,240,255,0.4)]">
                            ĐĂNG BÀI
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
