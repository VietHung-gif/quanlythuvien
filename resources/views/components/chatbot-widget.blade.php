<!-- WIDGET CHATBOT AI (CYBER ORACLE // TIỂU ĐỘI 2P - MODERN MINIMALIST LIGHT) -->
<div id="ai-chatbot-widget" class="fixed bottom-6 right-6 z-50 font-sans" style="font-family: 'Be Vietnam Pro', system-ui, sans-serif;">
    
    <style>
        /* Tùy biến thanh cuộn mỏng đẹp theo phong cách Light Mode Minimalist */
        .custom-chatbot-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: rgba(148, 163, 184, 0.4) transparent;
        }
        .custom-chatbot-scrollbar::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        .custom-chatbot-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-chatbot-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(148, 163, 184, 0.35);
            border-radius: 9999px;
        }
        .custom-chatbot-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: rgba(100, 116, 139, 0.6);
        }

        /* Thanh cuộn ngang danh sách câu hỏi nhanh */
        #quick-prompts-container {
            scrollbar-width: thin;
            scrollbar-color: rgba(203, 213, 225, 0.6) transparent;
        }
        #quick-prompts-container::-webkit-scrollbar {
            height: 4px;
        }
        #quick-prompts-container::-webkit-scrollbar-track {
            background: transparent;
        }
        #quick-prompts-container::-webkit-scrollbar-thumb {
            background-color: rgba(203, 213, 225, 0.8);
            border-radius: 9999px;
        }
        #quick-prompts-container::-webkit-scrollbar-thumb:hover {
            background-color: rgba(148, 163, 184, 0.8);
        }

        /* Định dạng nội dung tin nhắn bot */
        .bot-markdown-content pre {
            background: #0f172a;
            border: 1px solid #1e293b;
            border-radius: 8px;
            padding: 8px 12px;
            overflow-x: auto;
            margin: 6px 0;
            font-family: 'Fira Code', monospace;
            font-size: 11px;
            color: #38bdf8;
        }
        .bot-markdown-content code:not(pre code) {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #4f46e5;
            padding: 1px 5px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 11px;
            font-weight: 600;
        }
        .bot-markdown-content blockquote {
            border-left: 3px solid #6366f1;
            padding-left: 8px;
            color: #64748b;
            font-style: italic;
            margin: 6px 0;
        }
        .bot-markdown-content strong {
            color: #0f172a;
            font-weight: 700;
        }
        .bot-markdown-content ul {
            list-style-type: disc;
            padding-left: 18px;
            margin: 4px 0;
        }
        .bot-markdown-content li {
            margin-bottom: 2px;
        }
    </style>

    <!-- CỬA SỔ HỘP THOẠI CHAT (CHAT WINDOW) -->
    <div id="chatbot-window" 
         class="hidden flex-col w-[360px] sm:w-[440px] h-[580px] max-h-[85vh] bg-white/95 border border-slate-200/90 shadow-2xl rounded-2xl backdrop-blur-md overflow-hidden transition-all duration-300 transform scale-95 opacity-0 origin-bottom-right mb-4">
        
        <!-- Header hộp thoại -->
        <div class="px-4 py-3 bg-slate-50/90 border-b border-slate-200/80 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-200 flex items-center justify-center text-indigo-600 shadow-xs">
                        <!-- AI Quantum Icon -->
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <!-- Chấm trạng thái Online -->
                    <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-emerald-500 border-2 border-white shadow-xs"></span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 tracking-wide flex items-center gap-1.5 font-['Orbitron']">
                        <span>CYBER ORACLE</span>
                        <span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-200">AI 2.0</span>
                    </h3>
                    <p class="text-[10px] text-slate-500 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                        <span>TIỂU ĐỘI 2P • Sẵn sàng giải đáp mọi điều</span>
                    </p>
                </div>
            </div>

            <!-- Nút điều khiển -->
            <div class="flex items-center gap-1">
                <button type="button" 
                        id="chatbot-clear-btn" 
                        title="Xóa đoạn chat & Reset bộ nhớ AI" 
                        class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-slate-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
                <button type="button" 
                        id="chatbot-close-btn" 
                        title="Thu nhỏ cửa sổ chat" 
                        class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- KHUNG DANH SÁCH TIN NHẮN (LIGHT MODE) -->
        <div id="chatbot-messages" 
             class="flex-1 min-h-0 h-[400px] max-h-[400px] overflow-y-auto p-4 space-y-3.5 text-xs sm:text-sm text-slate-700 bg-slate-50/50 scroll-smooth custom-chatbot-scrollbar">
            
            <!-- Tin nhắn chào mừng ban đầu từ Cyber Oracle -->
            <div class="flex items-start gap-2.5 message-bot">
                <div class="w-7 h-7 rounded-lg bg-indigo-50 border border-indigo-200 flex items-center justify-center text-indigo-600 shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div class="max-w-[88%] bg-white border border-slate-200/90 rounded-2xl rounded-tl-sm px-3.5 py-2.5 leading-relaxed shadow-xs text-slate-700 bot-markdown-content">
                    Xin chào bạn! Tôi là <strong>CYBER ORACLE</strong> — Trợ lý AI của thư viện <strong>TIỂU ĐỘI 2P</strong>.<br><br>
                    Tôi có thể đồng hành cùng bạn mọi lúc:<br>
                    • <strong>Thư viện & Sách</strong>: Tra cứu kho sách, hướng dẫn mượn/trả, giải thích phí phạt nợ.<br>
                    • <strong>Gamification</strong>: Mẹo điểm danh nhận EXP, thăng cấp Danh hiệu Võ Lâm.<br>
                    • <strong>Tâm sự & Gợi ý</strong>: Chia sẻ tâm trạng, đề xuất sách chữa lành hoặc truyền cảm hứng.<br>
                    • <strong>Kiến thức CNTT & Đời sống</strong>: Giải đáp lập trình, viết code, làm thơ, giải bài tập...<br><br>
                    <span class="text-indigo-600 font-semibold">Hôm nay bạn muốn Oracle giải đáp điều gì?</span>
                </div>
            </div>

        </div>

        <!-- Thanh câu hỏi nhanh (Quick Prompt Chips) -->
        <div id="quick-prompts-container" 
             class="bg-white border-t border-slate-200/80 shrink-0 text-[11px] flex items-center gap-2 overflow-x-auto overflow-y-hidden whitespace-nowrap py-2 px-2.5 select-none">
            <button type="button" class="quick-chip flex-shrink-0 px-2.5 py-1 rounded-full bg-slate-50 hover:bg-indigo-50 hover:text-indigo-600 text-slate-600 border border-slate-200 hover:border-indigo-200 transition shadow-2xs font-medium" data-prompt="Hôm nay tôi thấy mệt mỏi, nên đọc sách gì để thư giãn?">
                Tâm sự & Gợi ý sách
            </button>
            <button type="button" class="quick-chip flex-shrink-0 px-2.5 py-1 rounded-full bg-slate-50 hover:bg-indigo-50 hover:text-indigo-600 text-slate-600 border border-slate-200 hover:border-indigo-200 transition shadow-2xs font-medium" data-prompt="Quy định mượn sách, thời hạn và tiền phạt quá hạn như thế nào?">
                Quy định & Phí phạt
            </button>
            <button type="button" class="quick-chip flex-shrink-0 px-2.5 py-1 rounded-full bg-slate-50 hover:bg-indigo-50 hover:text-indigo-600 text-slate-600 border border-slate-200 hover:border-indigo-200 transition shadow-2xs font-medium" data-prompt="Cách kiếm EXP để lên rank Thách Đấu Buff Bẩn?">
                Cày EXP Võ Lâm
            </button>
            <button type="button" class="quick-chip flex-shrink-0 px-2.5 py-1 rounded-full bg-slate-50 hover:bg-indigo-50 hover:text-indigo-600 text-slate-600 border border-slate-200 hover:border-indigo-200 transition shadow-2xs font-medium" data-prompt="Có sách gì hay về Lập trình, AI hoặc Khoa học không?">
                Sách Công nghệ & AI
            </button>
            <button type="button" class="quick-chip flex-shrink-0 px-2.5 py-1 rounded-full bg-slate-50 hover:bg-indigo-50 hover:text-indigo-600 text-slate-600 border border-slate-200 hover:border-indigo-200 transition shadow-2xs font-medium" data-prompt="Giải thích cho tôi cách hoạt động của Laravel Framework?">
                Hỏi kiến thức IT / Code
            </button>
        </div>

        <!-- Khung nhập tin nhắn & nút gửi -->
        <form id="chatbot-form" class="p-3 bg-white border-t border-slate-200/80 shrink-0">
            <div class="flex items-center gap-2">
                <input type="text" 
                       id="chatbot-input" 
                       placeholder="Hỏi mọi thứ: Sách, code, tâm sự, tri thức..." 
                       autocomplete="off"
                       class="flex-1 bg-slate-50 border border-slate-200 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 placeholder-slate-400 outline-none transition">
                <button type="submit" 
                        id="chatbot-send-btn" 
                        title="Gửi câu hỏi"
                        class="p-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition shadow-xs hover:shadow disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>
            <div class="flex items-center justify-between text-[10px] text-slate-400 mt-1.5 px-1 font-mono">
                <span>Nhấn Enter để gửi</span>
                <span class="text-indigo-600 font-semibold">CYBER ORACLE // TIỂU ĐỘI 2P</span>
            </div>
        </form>

    </div>

    <!-- NÚT BẤM MỞ CHAT TRÒN DẠNG FLOATING BUTTON -->
    <button type="button" 
            id="chatbot-toggle-btn" 
            aria-label="Mở Trợ lý Cyber Oracle AI"
            class="group relative flex items-center justify-center w-14 h-14 rounded-full bg-gradient-to-tr from-indigo-600 to-indigo-700 text-white font-bold shadow-lg shadow-indigo-600/25 hover:shadow-indigo-600/40 transition-all duration-300 hover:scale-105 active:scale-95 cursor-pointer">
        
        <!-- Chấm Online xanh lá góc -->
        <span class="absolute top-0 right-0 w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-white shadow-xs"></span>

        <!-- Biểu tượng Robot / Quantum -->
        <svg class="w-6 h-6 transition-transform group-hover:scale-110 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>

        <!-- Tooltip gợi ý (tự ẩn sau tương tác) -->
        <span id="chatbot-tooltip" class="absolute right-16 top-1/2 -translate-y-1/2 px-3 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-semibold whitespace-nowrap shadow-lg pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <span>Hỏi Trợ lý AI Oracle</span>
        </span>
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('chatbot-toggle-btn');
    const closeBtn = document.getElementById('chatbot-close-btn');
    const clearBtn = document.getElementById('chatbot-clear-btn');
    const windowEl = document.getElementById('chatbot-window');
    const messagesEl = document.getElementById('chatbot-messages');
    const formEl = document.getElementById('chatbot-form');
    const inputEl = document.getElementById('chatbot-input');
    const sendBtn = document.getElementById('chatbot-send-btn');
    const chips = document.querySelectorAll('.quick-chip');
    const tooltipEl = document.getElementById('chatbot-tooltip');

    let isOpen = false;
    let isSending = false;

    // Lấy CSRF token an toàn
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Trình phân tích Markdown toàn diện (Headers, Bold, Italic, Code blocks, Lists, Blockquotes)
    function formatMarkdown(text) {
        if (!text) return '';
        
        let html = text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');

        // Code blocks: ```lang ... ```
        html = html.replace(/```([a-zA-Z0-9_-]*)\n([\s\S]*?)```/g, (match, lang, code) => {
            return `<pre><div class="text-[9px] uppercase tracking-wider text-slate-400 font-bold mb-1">${lang || 'code'}</div><code>${code.trim()}</code></pre>`;
        });

        // Inline code: `code`
        html = html.replace(/`([^`]+)`/g, '<code>$1</code>');

        // Bold: **text** hoặc __text__
        html = html.replace(/(\*\*|__)(.*?)\1/g, '<strong>$2</strong>');

        // Italic: *text* hoặc _text_
        html = html.replace(/(\*|_)(.*?)\1/g, '<em>$2</em>');

        // Headers: ###, ##, #
        html = html.replace(/^### (.*$)/gim, '<h4 class="font-bold text-slate-900 mt-2 mb-1">$1</h4>');
        html = html.replace(/^## (.*$)/gim, '<h3 class="font-bold text-slate-900 text-sm mt-2 mb-1">$1</h3>');
        html = html.replace(/^# (.*$)/gim, '<h2 class="font-extrabold text-slate-900 text-base mt-2 mb-1">$1</h2>');

        // Blockquotes: > quote
        html = html.replace(/^\> (.*$)/gim, '<blockquote>$1</blockquote>');

        // Unordered lists: * item hoặc - item
        html = html.replace(/^\s*[\-\*]\s+(.*$)/gim, '<li>$1</li>');
        html = html.replace(/(<li>.*?<\/li>)/gs, '<ul>$1</ul>');

        // Xuống dòng đôi thành đoạn mới, xuống dòng đơn thành <br>
        html = html.replace(/\n\n/g, '<br><br>');
        html = html.replace(/\n/g, '<br>');

        return html;
    }

    function scrollToBottom() {
        if (messagesEl) {
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }
    }

    function openChat() {
        isOpen = true;
        windowEl.classList.remove('hidden');
        // trigger animation
        setTimeout(() => {
            windowEl.classList.remove('scale-95', 'opacity-0');
            windowEl.classList.add('scale-100', 'opacity-100');
            scrollToBottom();
            inputEl.focus();
        }, 10);
        if (tooltipEl) tooltipEl.style.display = 'none';
    }

    function closeChat() {
        isOpen = false;
        windowEl.classList.remove('scale-100', 'opacity-100');
        windowEl.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            windowEl.classList.add('hidden');
        }, 300);
    }

    toggleBtn.addEventListener('click', () => {
        if (isOpen) {
            closeChat();
        } else {
            openChat();
        }
    });

    closeBtn.addEventListener('click', closeChat);

    // Reset lịch sử tin nhắn
    clearBtn.addEventListener('click', () => {
        messagesEl.innerHTML = `
            <div class="flex items-start gap-2.5 message-bot">
                <div class="w-7 h-7 rounded-lg bg-indigo-50 border border-indigo-200 flex items-center justify-center text-indigo-600 shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div class="max-w-[88%] bg-white border border-slate-200/90 rounded-2xl rounded-tl-sm px-3.5 py-2.5 leading-relaxed shadow-xs text-slate-700 bot-markdown-content">
                    Đoạn hội thoại đã được làm mới! Tôi đã sẵn sàng tiếp nhận câu hỏi tiếp theo của bạn.
                </div>
            </div>
        `;
        scrollToBottom();
    });

    // Thêm tin nhắn của User
    function appendUserMessage(text) {
        const div = document.createElement('div');
        div.className = 'flex justify-end';
        div.innerHTML = `
            <div class="max-w-[85%] bg-indigo-600 text-white rounded-2xl rounded-tr-sm px-3.5 py-2.5 leading-relaxed shadow-sm font-medium">
                ${text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')}
            </div>
        `;
        messagesEl.appendChild(div);
        scrollToBottom();
    }

    // Hiển thị tin nhắn Bot
    function typeBotMessage(rawText) {
        const div = document.createElement('div');
        div.className = 'flex items-start gap-2.5 message-bot';
        div.innerHTML = `
            <div class="w-7 h-7 rounded-lg bg-indigo-50 border border-indigo-200 flex items-center justify-center text-indigo-600 shrink-0 mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div class="max-w-[88%] bg-white border border-slate-200/90 rounded-2xl rounded-tl-sm px-3.5 py-2.5 leading-relaxed shadow-xs text-slate-700 bot-markdown-content message-text-container"></div>
        `;
        messagesEl.appendChild(div);

        const container = div.querySelector('.message-text-container');
        const formattedFull = formatMarkdown(rawText);
        container.innerHTML = formattedFull;
        scrollToBottom();
    }

    // Hiển thị hiệu ứng chờ gõ tin nhắn (Typing indicator)
    function showTypingIndicator() {
        const div = document.createElement('div');
        div.id = 'chatbot-typing-indicator';
        div.className = 'flex items-start gap-2.5';
        div.innerHTML = `
            <div class="w-7 h-7 rounded-lg bg-indigo-50 border border-indigo-200 flex items-center justify-center text-indigo-600 shrink-0 mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl rounded-tl-sm px-4 py-3 flex items-center gap-2 shadow-xs">
                <span class="text-[11px] font-mono text-slate-500">Oracle đang suy nghĩ...</span>
                <div class="flex items-center gap-1 ms-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-bounce" style="animation-delay: 0ms"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-bounce" style="animation-delay: 150ms"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-bounce" style="animation-delay: 300ms"></span>
                </div>
            </div>
        `;
        messagesEl.appendChild(div);
        scrollToBottom();
    }

    function removeTypingIndicator() {
        const indicator = document.getElementById('chatbot-typing-indicator');
        if (indicator) indicator.remove();
    }

    // Gửi tin nhắn đến Backend Controller
    async function sendMessage(text) {
        const trimmed = text.trim();
        if (!trimmed || isSending) return;

        isSending = true;
        sendBtn.disabled = true;
        inputEl.value = '';

        // Hiển thị tin nhắn người dùng
        appendUserMessage(trimmed);
        showTypingIndicator();

        try {
            const res = await fetch('{{ route('chatbot.message') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ message: trimmed })
            });

            const data = await res.json();
            removeTypingIndicator();

            const responseText = data?.message || data?.reply;

            if (data && data.success && responseText) {
                typeBotMessage(responseText);
            } else {
                typeBotMessage('Máy chủ đang bận hoặc có sự cố kết nối. Vui lòng thử lại sau giây lát!');
            }
        } catch (err) {
            console.error('Lỗi gửi tin nhắn chatbot:', err);
            removeTypingIndicator();
            typeBotMessage('Không thể kết nối đến máy chủ. Vui lòng kiểm tra đường truyền và thử lại!');
        } finally {
            isSending = false;
            sendBtn.disabled = false;
            inputEl.focus();
        }
    }

    // Xử lý gửi Form
    formEl.addEventListener('submit', (e) => {
        e.preventDefault();
        sendMessage(inputEl.value);
    });

    // Xử lý phím bấm câu hỏi gợi ý nhanh (Chips)
    chips.forEach(chip => {
        chip.addEventListener('click', () => {
            const prompt = chip.getAttribute('data-prompt');
            if (prompt) {
                sendMessage(prompt);
            }
        });
    });

    // Xử lý lăn chuột cuộn ngang (Horizontal Scroll by Wheel) cho câu hỏi gợi ý nhanh
    const quickPromptsContainer = document.getElementById('quick-prompts-container');
    if (quickPromptsContainer) {
        quickPromptsContainer.addEventListener('wheel', (evt) => {
            evt.preventDefault();
            quickPromptsContainer.scrollLeft += evt.deltaY;
        });
    }
});
</script>
