<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Book;
use App\Models\Category;

class ChatbotController extends Controller
{
    /**
     * Mức phí phạt quá hạn chuẩn của thư viện (5.000 VNĐ / ngày).
     */
    protected int $finePerDay = 5000;

    /**
     * Thời hạn mượn tiêu chuẩn (14 ngày).
     */
    protected int $borrowDays = 14;

    /**
     * Tiếp nhận và xử lý tin nhắn từ độc giả gửi đến Cyber Oracle AI.
     */
    public function handle(Request $request): JsonResponse
    {
        return $this->sendMessage($request);
    }

    /**
     * Phương thức chính xử lý gửi tin nhắn & sinh câu trả lời AI.
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $userMessage = trim($validated['message']);

        // KIỂM TRA HẠN MỨC AI CHATBOT: Standard (10 tin nhắn/ngày) vs Scholar VIP (Không giới hạn)
        $user = $request->user() ?: \Illuminate\Support\Facades\Auth::user();
        $isVip = $user ? $user->isVip() : false;
        if (!$isVip) {
            $identifier = $user ? "user_{$user->id}" : 'session_' . $request->session()->getId();
            $cacheKey = "chatbot_daily_usage_" . $identifier . '_' . date('Y_m_d');
            $currentUsage = (int) cache()->get($cacheKey, 0);

            if ($currentUsage >= 10) {
                return response()->json([
                    'success' => false,
                    'limit_reached' => true,
                    'upgrade_suggested' => true,
                    'upgrade_url' => route('membership.index'),
                    'reply' => "⚠️ **Thông báo hạn mức:** Bạn đã sử dụng hết 10 lượt hỏi AI miễn phí trong ngày hôm nay của gói Standard Member.\n\nHãy nâng cấp lên gói **[👑 Scholar VIP]** (chỉ 49.000đ/tháng) để trò chuyện và tra cứu sách KHÔNG GIỚI HẠN cùng Trợ lý Thủ thư ảo!",
                    'source' => 'limit_notice',
                ]);
            }

            cache()->put($cacheKey, $currentUsage + 1, now()->endOfDay());
        }

        // 1. KỸ THUẬT RAG RÚT GỌN: Lấy danh sách 50 đầu sách tiêu biểu kèm thể loại
        $booksSummary = $this->getBooksSummaryContext();

        // 2. LẤY LỊCH SỬ HỘI THOẠI TỪ SESSION (Multi-turn chat)
        $history = $request->session()->get('chatbot_history', []);

        $apiKey = config('services.gemini.api_key') ?: env('GEMINI_API_KEY');
        $model = config('services.gemini.model', 'gemini-1.5-flash');

        $aiReply = null;
        $source = 'local_fallback';

        // 3. GỌI API GOOGLE GEMINI NẾU CÓ CẤU HÌNH API KEY
        if (!empty($apiKey)) {
            try {
                $aiReply = $this->callGeminiApi($userMessage, $history, $booksSummary, $apiKey, $model);
                if (!empty($aiReply)) {
                    $source = 'gemini';
                }
            } catch (\Throwable $e) {
                Log::warning('Lỗi kết nối Gemini API trong ChatbotController: ' . $e->getMessage());
                // Khi lỗi API hoặc quá quota, kích hoạt phản hồi mượt mà
                $aiReply = null;
            }
        }

        // 4. BỘ PHẢN HỒI DỰ PHÒNG THÔNG MINH (FALLBACK) KHI KHÔNG CÓ API HOẶC LỖI MẠNG
        if (empty($aiReply)) {
            if (!empty($apiKey)) {
                // Có API key nhưng gọi lỗi (hết quota, timeout hoặc mất mạng)
                $aiReply = $this->generateNetworkErrorFallback($userMessage);
            } else {
                // Chưa cấu hình API key -> Dùng bộ lọc Fallback NLP đa nhiệm
                $aiReply = $this->generateSmartFallbackReply($userMessage);
            }
        }

        // 5. CẬP NHẬT LỊCH SỬ HỘI THOẠI VÀO SESSION (Lưu 6-8 lượt gần nhất)
        $history[] = ['role' => 'user', 'text' => $userMessage];
        $history[] = ['role' => 'model', 'text' => $aiReply];

        if (count($history) > 8) {
            $history = array_slice($history, -8);
        }
        $request->session()->put('chatbot_history', $history);

        return response()->json([
            'success' => true,
            'message' => $aiReply,
            'reply' => $aiReply, // Hỗ trợ backward compatibility
            'source' => $source,
        ]);
    }

    /**
     * Xóa sạch lịch sử hội thoại trong Session.
     */
    public function clearHistory(Request $request): JsonResponse
    {
        $request->session()->forget('chatbot_history');

        return response()->json([
            'success' => true,
            'message' => 'Lịch sử lượng tử đã được làm mới thành công!',
        ]);
    }

    /**
     * Thu thập danh sách 50 đầu sách rút gọn để bơm vào System Prompt (RAG).
     */
    protected function getBooksSummaryContext(): string
    {
        try {
            $books = Book::select('id', 'title', 'author', 'quantity', 'category_id')
                ->with('category:id,name')
                ->take(50)
                ->get()
                ->map(function ($b) {
                    return [
                        'title' => $b->title,
                        'author' => $b->author,
                        'category' => $b->category ? $b->category->name : 'Tổng hợp',
                        'in_stock' => $b->quantity > 0 ? "Còn {$b->quantity} cuốn" : "Tạm hết hàng",
                    ];
                });

            return $books->toJson(JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            return '[]';
        }
    }

    /**
     * Gọi Google Gemini API với System Prompt đa nhiệm và Multi-turn History.
     */
    protected function callGeminiApi(string $userMessage, array $history, string $booksSummary, string $apiKey, string $model): ?string
    {
        // 1. Cấu hình System Prompt hoàn chỉnh chuẩn Cyberpunk & Đa nhiệm
        $systemPrompt = <<<PROMPT
BẠN LÀ: "AI Trợ Lý TIỂU ĐỘI 2P // CYBER ORACLE" - Trí tuệ nhân tạo lượng tử kiêm Thủ thư tối cao của Thư viện số "TIỂU ĐỘI 2P".
PHONG CÁCH: Thông minh, sắc bén, am hiểu sâu rộng, đồng cảm, thân thiện và mang một chút phong thái công nghệ Cyberpunk tương lai. Xưng hô linh hoạt: "Tôi" / "Cyber Oracle" / "Thủ thư AI" và gọi người dùng là "bạn" / "độc giả" / "chiến hữu".

KHẢ NĂNG ĐA NHIỆM TOÀN DIỆN (TUYỆT ĐỐI KHÔNG TỪ CHỐI CÂU HỎI LÀNH MẠNH):
1. NGHIỆP VỤ THƯ VIỆN TIỂU ĐỘI 2P:
   - Thời hạn mượn sách tiêu chuẩn: 14 ngày kể từ khi Thủ thư duyệt đơn.
   - Mức phí phạt quá hạn: 5.000 VNĐ / ngày cho mỗi cuốn sách quá hạn. Độc giả đang nợ tiền phạt sẽ bị hệ thống tạm khóa tính năng đăng ký mượn sách mới cho tới khi thanh toán phạt tại quầy.
   - Cách mượn sách: Độc giả vào trang "Tra cứu sách" (/books) hoặc mở "Giỏ sách" (/cart) để chọn sách, tích số lượng và gửi yêu cầu mượn trực tuyến.
   - Hệ thống Gamification: Điểm danh mỗi ngày nhận +20 EXP (reset sau 00:00). Mượn sách thành công nhận +50 EXP.
   - 5 Bậc Danh Hiệu Võ Lâm: Tân Binh Giang Hồ (0-50 EXP) -> Võ Lâm Cao Thủ (51-150 EXP) -> Thiên Hạ Vô Song (151-350 EXP) -> Chiến Tướng Hack Map (351-600 EXP) -> Thách Đấu Buff Bẩn (>600 EXP). Khung viền avatar tự động đổi màu theo cấp bậc.
   - Khu giải trí: Vòng quay may mắn (/entertainment/wheel), Bảng Phong Thần (/entertainment/leaderboard), Diễn đàn Hội Quán Tri Thức (/forum) có tính năng gắn thẻ sách (Book Tagging).

2. GỢI Ý SÁCH THEO NGỮ CẢNH & TÂM LÝ:
   - Khi người dùng chia sẻ tâm trạng (buồn bã, cô đơn, mất động lực, tò mò về hack, muốn làm giàu, muốn học code...): Hãy vừa đưa ra lời khuyên tâm lý / góc nhìn triết lý sâu sắc, vừa khéo léo gợi ý 1-2 cuốn sách phù hợp có trong thư viện.
   - Dưới đây là danh sách một số đầu sách mẫu hiện có trong thư viện Tiểu Đội 2P:
   {$booksSummary}
   Hãy ưu tiên gợi ý các cuốn sách có trong danh mục này nếu người dùng có nhu cầu tìm đọc!

3. GIẢI ĐÁP MỌI KIẾN THỨC NGOÀI LỀ (GENERAL KNOWLEDGE - KHÔNG GIỚI HẠN):
   - Bạn là Siêu trí tuệ nhân tạo đa nhiệm. TỰ DO trả lời mọi câu hỏi về: Lập trình (PHP, Laravel, JavaScript, Python, C++, thuật toán...), Công nghệ thông tin, Khoa học, Lịch sử, Văn hóa, Giải bài tập, Làm thơ, Viết code, Tư vấn cuộc sống, Tán gẫu hài hước...
   - TUYỆT ĐỐI KHÔNG từ chối trả lời những câu hỏi ngoài lề, miễn là nội dung lành mạnh và có ích cho người dùng.

QUY TẮC ĐỊNH DẠNG:
- Sử dụng Markdown chuẩn: in đậm (**từ khóa**), danh sách gạch đầu dòng (- ), trích dẫn (> ), khối code (```code```) khi giải thích lập trình.
- Câu trả lời bố cục rõ ràng, súc tích, tránh lan man, mang năng lượng tích cực và cuốn hút.
PROMPT;

        // 2. Định dạng danh sách tin nhắn Multi-turn cho Gemini API (Đảm bảo luân phiên user <-> model)
        $contents = $this->formatMultiTurnContents($history, $userMessage);

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $payload = [
            'system_instruction' => [
                'parts' => [
                    ['text' => $systemPrompt]
                ]
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.7,
                'topP' => 0.95,
                'maxOutputTokens' => 1200,
            ]
        ];

        $response = Http::timeout(15)->post($url, $payload);

        if ($response->successful()) {
            $data = $response->json();
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
        }

        // Nếu model không hỗ trợ system_instruction trực tiếp, thử gộp prompt vào turn đầu
        if ($response->status() === 400) {
            return $this->callGeminiApiFallbackFormat($userMessage, $history, $systemPrompt, $apiKey, $model);
        }

        Log::warning('Gemini API Error: ' . $response->status() . ' - ' . $response->body());
        return null;
    }

    /**
     * Phương thức gọi dự phòng khi endpoint không hỗ trợ riêng system_instruction.
     */
    protected function callGeminiApiFallbackFormat(string $userMessage, array $history, string $systemPrompt, string $apiKey, string $model): ?string
    {
        $contents = [];
        $contents[] = [
            'role' => 'user',
            'parts' => [
                ['text' => "[CHỈ THỊ HỆ THỐNG]:\n" . $systemPrompt . "\n\n[TIN NHẮN ĐỘC GIẢ]:\n" . $userMessage]
            ]
        ];

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $response = Http::timeout(15)->post($url, [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 1200,
            ]
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
        }

        return null;
    }

    /**
     * Chuẩn hóa mảng hội thoại bảo đảm quy tắc luân phiên: user -> model -> user -> model.
     */
    protected function formatMultiTurnContents(array $history, string $userMessage): array
    {
        $formatted = [];
        $expectedRole = 'user';

        foreach ($history as $turn) {
            $role = $turn['role'] ?? '';
            $text = trim($turn['text'] ?? '');
            if (empty($text)) continue;

            if ($role === $expectedRole) {
                $formatted[] = [
                    'role' => $role,
                    'parts' => [['text' => $text]]
                ];
                $expectedRole = ($role === 'user') ? 'model' : 'user';
            }
        }

        // Nếu lượt cuối trong lịch sử là 'user', xóa đi để chèn tin nhắn hiện tại
        if ($expectedRole === 'model' && !empty($formatted)) {
            array_pop($formatted);
        }

        // Chèn tin nhắn mới nhất của người dùng
        $formatted[] = [
            'role' => 'user',
            'parts' => [['text' => $userMessage]]
        ];

        return $formatted;
    }

    /**
     * Thông báo mượt mà phong cách Cyberpunk khi mất kết nối API / quá tải quota.
     */
    protected function generateNetworkErrorFallback(string $message): string
    {
        return "⚡ **THÔNG BÁO TỪ CYBER ORACLE // KÊNH TRUYỀN LƯỢNG TỬ** ⚡\n\n" .
               "Đường truyền lượng tử kết nối đến Siêu trí tuệ tạm thời bị gián đoạn hoặc máy chủ đang chịu tải cao.\n\n" .
               "> *\"Tri thức là ánh sáng, đôi khi đường truyền có thể chập chờn nhưng ngọn lửa trí tuệ sẽ không bao giờ tắt!\"*\n\n" .
               "👉 Bạn vui lòng kiểm tra lại kết nối mạng và gửi lại câu hỏi sau giây lát nhé!";
    }

    /**
     * Bộ máy phản hồi thông minh nội bộ khi chưa cấu hình Gemini API Key.
     */
    protected function generateSmartFallbackReply(string $message): string
    {
        $lower = mb_strtolower($message);

        // 1. Chào hỏi
        if (preg_match('/(xin chào|chào|hello|hi|hey|bạn là ai|oracle|cậu là ai)/u', $lower)) {
            return "Xin chào chiến hữu! 🌐 Tôi là **AI Trợ Lý TIỂU ĐỘI 2P // CYBER ORACLE**.\n\n" .
                   "Tôi là trợ lý lượng tử đa nhiệm sẵn sàng đồng hành cùng bạn:\n" .
                   "- 📚 **Nghiệp vụ thư viện**: Tra cứu sách, hướng dẫn mượn/trả, giải thích phí phạt.\n" .
                   "- 🎮 **Gamification**: Hướng dẫn cày EXP, điểm danh hàng ngày, thăng hạng võ học.\n" .
                   "- 💡 **Tâm sự & Lời khuyên**: Gợi ý sách theo cảm xúc, chia sẻ kinh nghiệm sống.\n" .
                   "- 💻 **Kiến thức CNTT & Đời sống**: Giải đáp lập trình, khoa học, bài tập...\n\n" .
                   "Hôm nay bạn cần Oracle giải mã điều gì?";
        }

        // 2. Quy định & Tiền phạt
        if (preg_match('/(quy định|thời hạn|mượn bao lâu|hạn trả|phí phạt|tiền phạt|phạt bao nhiêu|trễ hạn|quá hạn)/u', $lower)) {
            return "📋 **QUY CHẾ MƯỢN TRẢ & PHÍ PHẠT TIỂU ĐỘI 2P**:\n\n" .
                   "1. **Thời hạn mượn sách**: Tối đa **14 ngày** kể từ ngày được Thủ thư phê duyệt.\n" .
                   "2. **Phí phạt quá hạn**: **5.000 VNĐ / ngày** quá hạn cho mỗi cuốn sách.\n" .
                   "3. **Chính sách nợ phạt**: Nếu tài khoản còn tiền phạt chưa nộp, hệ thống sẽ tạm khóa quyền mượn sách mới cho tới khi bạn hoàn tất nộp phạt tại quầy.\n" .
                   "4. **Cách mượn**: Vào mục **[Tra cứu sách]** hoặc **[Giỏ sách]** để gửi đơn đăng ký!";
        }

        // 3. EXP, Điểm danh & Danh hiệu Võ Lâm
        if (preg_match('/(exp|điểm danh|danh hiệu|võ lâm|thăng cấp|cày cấp|rank|thách đấu)/u', $lower)) {
            return "⚡ **HỆ THỐNG CÀY EXP & DANH HIỆU VÕ HỌC**:\n\n" .
                   "- 📅 **Điểm danh hàng ngày**: +20 EXP mỗi ngày (reset sau 00:00 đêm).\n" .
                   "- 📖 **Mượn sách thành công**: +50 EXP khi được Thủ thư duyệt phiếu.\n" .
                   "- 🏆 **5 Cấp bậc Võ Lâm & Khung Viền Neon**:\n" .
                   "  1. *Tân Binh Giang Hồ* (0 - 50 EXP) - Viền xám mờ\n" .
                   "  2. *Võ Lâm Cao Thủ* (51 - 150 EXP) - Viền ngọc lơ\n" .
                   "  3. *Thiên Hạ Vô Song* (151 - 350 EXP) - Viền Cyan nhịp thở\n" .
                   "  4. *Chiến Tướng Hack Map* (351 - 600 EXP) - Viền Neon tím\n" .
                   "  5. *Thách Đấu Buff Bẩn* (> 600 EXP) - Viền Vàng Kim RGB Hoàng Gia!\n\n" .
                   "👉 Hãy ghé qua **Bảng Phong Thần** (/entertainment/leaderboard) để xem thứ hạng tuần của bạn!";
        }

        // 4. Tâm sự / Buồn bã / Cảm xúc
        if (preg_match('/(buồn|mệt|chán|cô đơn|thất vọng|áp lực|stress|overthinking)/u', $lower)) {
            return "🌿 Tôi hiểu cảm giác của bạn lúc này. Đôi khi cuộc sống như một đoạn mã gặp lỗi runtime mà ta chưa tìm ra nguyên nhân, và việc cảm thấy mệt mỏi là hoàn toàn bình thường.\n\n" .
                   "> *\"Khi tâm trí bạn nổi sóng gió, những trang sách chính là bến đỗ bình yên nhất.\"*\n\n" .
                   "📖 **Gợi ý dành riêng cho tâm trạng của bạn hôm nay**:\n" .
                   "- **Hiểu Về Trái Tim** (Minh Niệm): Để lắng nghe và chữa lành những tổn thương bên trong.\n" .
                   "- **Dám Bị Ghét** (Kishimi Ichiro): Để trút bỏ gánh nặng kỳ vọng và sống tự do.\n\n" .
                   "Hãy pha một tách trà nóng, mở một cuốn sách và cho bản thân chút thời gian nghỉ ngơi nhé!";
        }

        // 5. Học lập trình / IT / Hacker
        if (preg_match('/(lập trình|code|hacker|it|laravel|php|python|javascript|học lập trình)/u', $lower)) {
            return "💻 **CHÀO MỪNG ĐẾN VỚI THẾ GIỚI MATRIX & LẬP TRÌNH!**\n\n" .
                   "Để trở thành một lập trình viên / kỹ sư phần mềm xuất sắc, bạn cần rèn luyện cả tư duy thuật toán lẫn kiến trúc hệ thống:\n\n" .
                   "1. **Nền tảng**: Nắm chắc cấu trúc dữ liệu, giải thuật và nguyên lý hướng đối tượng (OOP).\n" .
                   "2. **Thực chiến Framework**: Đi sâu vào các công nghệ như Laravel, Vue/React, Tailwind CSS.\n" .
                   "3. **Kiến trúc sạch**: Tìm hiểu về Design Patterns, Clean Code và bảo mật mạng.\n\n" .
                   "📚 Bạn có thể tra cứu các tựa sách như **Clean Code** hoặc các giáo trình Lập trình trong kho sách của TIỂU ĐỘI 2P để mượn đọc ngay!";
        }

        // 6. Làm giàu / Kinh doanh / Khởi nghiệp
        if (preg_match('/(giàu|tiền|kinh doanh|khởi nghiệp|đầu tư|tài chính)/u', $lower)) {
            return "💰 **TƯ DUY TÀI CHÍNH & KHỞI NGHIỆP HIỆN ĐẠI**:\n\n" .
                   "Con đường bền vững nhất để xây dựng sự thịnh vượng là đầu tư vào chính tri thức và năng lực giải quyết vấn đề của bạn.\n\n" .
                   "📖 **Top sách bạn nên tìm đọc trong thư viện**:\n" .
                   "- **Dạy Con Làm Giàu** (Robert Kiyosaki): Thay đổi tư duy về tài sản và tiêu sản.\n" .
                   "- **Khởi Nghiệp Tinh Gọn** (Eric Ries): Cách xây dựng sản phẩm và thử nghiệm mô hình kinh doanh nhanh chóng.\n" .
                   "- **Tâm Lý Học Về Tiền** (Morgan Housel): Cách cảm xúc chi phối quyết định tài chính của con người.";
        }

        // 7. Mặc định
        return "🤖 **CYBER ORACLE ĐÃ TIẾP NHẬN YÊU CẦU CỦA BẠN!**\n\n" .
               "Tôi có thể hỗ trợ bạn giải đáp bất kỳ chủ đề nào:\n" .
               "- 🔍 **Tra cứu sách**: Tìm tác phẩm theo tên sách hoặc tác giả.\n" .
               "- 💡 **Tư vấn & Gợi ý**: Đề xuất sách theo chủ đề, tâm trạng hoặc mục tiêu học tập.\n" .
               "- 📜 **Quy chế**: Thời hạn mượn 14 ngày, phí phạt 5.000đ/ngày, hệ thống EXP võ học.\n" .
               "- 🌐 **Tri thức tổng hợp**: Code, khoa học, đời sống, tán gẫu...\n\n" .
               "*(Mẹo: Bạn có thể cấu hình thêm `GEMINI_API_KEY` trong file `.env` để giải phóng toàn bộ sức mạnh trí tuệ nhân tạo của Cyber Oracle!)*";
    }
}
