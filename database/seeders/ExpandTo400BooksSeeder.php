<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExpandTo400BooksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== BẮT ĐẦU MỞ RỘNG THƯ VIỆN LÊN 400 ĐẦU SÁCH TINH HOA & 3.618 BẢN SÁCH ===');

        // 1. Tạo 4 danh mục mới nếu chưa có
        $newCategories = [
            'Học ngoại ngữ' => 'Tuyển tập giáo trình luyện thi chứng chỉ quốc tế IELTS, TOEIC, HSK, JLPT và phương pháp giao tiếp đa ngôn ngữ.',
            'Tâm lý học' => 'Công trình nghiên cứu kinh điển về hành vi con người, tư duy nhận thức, trí tuệ cảm xúc và nghệ thuật thấu hiểu nội tâm.',
            'Kỹ năng sống' => 'Cẩm nang rèn luyện nhân cách, nghệ thuật giao tiếp đối nhân xử thế, quản trị bản thân và tư duy kiến tạo thành công.',
            'Văn học' => 'Kiệt tác văn học kinh điển của Việt Nam và thế giới, nuôi dưỡng tâm hồn và chiều sâu tư tưởng qua các thời đại.',
        ];

        foreach ($newCategories as $catName => $desc) {
            $cat = DB::table('categories')->where('name', $catName)->first();
            if (! $cat) {
                DB::table('categories')->insert([
                    'name' => $catName,
                    'description' => $desc,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->command->info("+ Đã tạo danh mục: {$catName}");
            }
        }

        // Lấy category ID map
        $categoryMap = DB::table('categories')->pluck('id', 'name')->toArray();

        // 2. Định nghĩa danh sách 300 cuốn sách mới (thực tế 100%, có tác giả và tóm tắt sâu sắc)
        $newBooks = $this->getBooksCatalog();

        $storagePath = storage_path('app/public/books/');
        $publicStoragePath = public_path('storage/books/');
        if (! is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }
        if (! is_dir($publicStoragePath)) {
            mkdir($publicStoragePath, 0755, true);
        }

        $insertedCount = 0;

        foreach ($newBooks as $catName => $books) {
            $catId = $categoryMap[$catName] ?? null;
            if (! $catId) {
                continue;
            }

            foreach ($books as $b) {
                // Kiểm tra xem sách đã tồn tại chưa
                $exists = DB::table('books')->where('title', $b['title'])->exists();
                if ($exists) {
                    continue;
                }

                $slug = Str::slug($b['title']);
                $fileName = "cover-{$slug}.jpg";
                $fullStorageFile = $storagePath.$fileName;
                $fullPublicFile = $publicStoragePath.$fileName;

                // Render bìa sách nghệ thuật chuẩn Typography tiếng Việt nếu chưa có
                if (! file_exists($fullStorageFile)) {
                    $this->renderBookCover($b['title'], $b['author'], $catName, $fullStorageFile);
                    @copy($fullStorageFile, $fullPublicFile);
                } elseif (! file_exists($fullPublicFile)) {
                    @copy($fullStorageFile, $fullPublicFile);
                }

                DB::table('books')->insert([
                    'category_id' => $catId,
                    'title' => $b['title'],
                    'author' => $b['author'],
                    'publisher' => $b['publisher'] ?? "NXB {$catName}",
                    'publish_year' => $b['publish_year'] ?? rand(2015, 2024),
                    'description' => $b['description'],
                    'cover_image' => "books/{$fileName}",
                    'quantity' => 10, // Sẽ cân chỉnh chính xác ở bước sau
                    'available_copies' => 10,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $insertedCount++;
            }
        }

        $this->command->info("[OK] Đã thêm {$insertedCount} đầu sách mới vào hệ thống.");

        // 3. Cân bằng tồn kho toàn bộ thư viện đạt CHÍNH XÁC 3.618 CUỐN
        $this->balanceInventoryTo3618();

        $totalBooks = DB::table('books')->count();
        $totalCopies = DB::table('books')->sum('quantity');
        $this->command->info("[SUCCESS] HOÀN TẤT: Hiện có {$totalBooks} đầu sách tinh hoa và TỔNG {$totalCopies} cuốn sách lưu hành trong kho!");
    }

    /**
     * Thuật toán phân bổ tồn kho đạt chính xác 3.618 cuốn
     */
    protected function balanceInventoryTo3618(): void
    {
        $books = DB::table('books')->orderBy('id')->get();
        $totalBooks = $books->count();
        if ($totalBooks === 0) {
            return;
        }

        $targetTotal = 3618;

        // Phân bổ ngẫu nhiên có kiểm soát theo các mức:
        // - 15 cuốn hết sách (qty = 0)
        // - 165 cuốn sắp hết (qty = 3 .. 8)
        // - Còn lại là còn sách (qty = 11 .. 22)
        $allocated = [];
        $runningSum = 0;

        foreach ($books as $index => $book) {
            if ($index < 12) {
                // Một vài cuốn hết hàng để test trạng thái khóa chọn
                $qty = 0;
            } elseif ($index < 165) {
                // Sắp hết (3 đến 8 bản)
                $qty = rand(3, 8);
            } else {
                // Còn sách dồi dào (11 đến 20 bản)
                $qty = rand(11, 20);
            }
            $allocated[$book->id] = $qty;
            $runningSum += $qty;
        }

        // Điều chỉnh sai số để tổng đạt chính xác targetTotal
        $diff = $targetTotal - $runningSum;
        $candidateIds = array_keys(array_filter($allocated, fn ($q) => $q > 0));

        while ($diff !== 0 && count($candidateIds) > 0) {
            $id = $candidateIds[array_rand($candidateIds)];
            if ($diff > 0) {
                $allocated[$id]++;
                $diff--;
            } elseif ($diff < 0 && $allocated[$id] > 2) {
                $allocated[$id]--;
                $diff++;
            }
        }

        // Cập nhật vào DB
        foreach ($allocated as $id => $qty) {
            DB::table('books')->where('id', $id)->update([
                'quantity' => $qty,
                'available_copies' => $qty,
                'updated_at' => now(),
            ]);
        }

        $this->command->info('[OK] Đã phân bổ tồn kho thành công: Tổng cộng đạt '.array_sum($allocated).' cuốn!');
    }

    /**
     * Render ảnh bìa sách chất lượng cao chuẩn Typography tiếng Việt
     */
    protected function renderBookCover(string $title, string $author, string $categoryName, string $destPath): void
    {
        $w = 600;
        $h = 900;
        $img = imagecreatetruecolor($w, $h);

        $palettes = [
            'CNTT' => [
                'bgTop' => [10, 25, 47], 'bgBottom' => [15, 23, 42],
                'border' => [56, 189, 248], 'accent' => [129, 140, 248],
                'badgeBg' => [14, 165, 233], 'badgeText' => [255, 255, 255],
                'title' => [255, 255, 255], 'author' => [224, 231, 255],
                'tag' => 'KỸ THUẬT & CÔNG NGHỆ THÔNG TIN',
            ],
            'Kinh doanh' => [
                'bgTop' => [6, 78, 59], 'bgBottom' => [15, 23, 42],
                'border' => [234, 179, 8], 'accent' => [52, 211, 153],
                'badgeBg' => [16, 185, 129], 'badgeText' => [255, 255, 255],
                'title' => [255, 255, 255], 'author' => [254, 240, 138],
                'tag' => 'QUẢN TRỊ & CHIẾN LƯỢC KINH DOANH',
            ],
            'Chứng khoán' => [
                'bgTop' => [69, 10, 10], 'bgBottom' => [24, 24, 27],
                'border' => [251, 191, 36], 'accent' => [239, 68, 68],
                'badgeBg' => [185, 28, 28], 'badgeText' => [255, 255, 255],
                'title' => [255, 255, 255], 'author' => [254, 243, 199],
                'tag' => 'CHỨNG KHOÁN & ĐẦU TƯ TÀI CHÍNH',
            ],
            'Truyện Anime' => [
                'bgTop' => [24, 24, 27], 'bgBottom' => [39, 39, 42],
                'border' => [244, 63, 94], 'accent' => [251, 113, 133],
                'badgeBg' => [225, 29, 72], 'badgeText' => [255, 255, 255],
                'title' => [255, 255, 255], 'author' => [254, 205, 211],
                'tag' => 'MANGA & GRAPHIC NOVEL KINH ĐIỂN',
            ],
            'Học ngoại ngữ' => [
                'bgTop' => [12, 74, 110], 'bgBottom' => [3, 105, 161],
                'border' => [245, 158, 11], 'accent' => [56, 189, 248],
                'badgeBg' => [2, 132, 199], 'badgeText' => [255, 255, 255],
                'title' => [255, 255, 255], 'author' => [254, 243, 199],
                'tag' => 'GIÁO TRÌNH HỌC NGOẠI NGỮ CHUẨN',
            ],
            'Tâm lý học' => [
                'bgTop' => [59, 7, 100], 'bgBottom' => [88, 28, 135],
                'border' => [216, 180, 254], 'accent' => [168, 85, 247],
                'badgeBg' => [147, 51, 234], 'badgeText' => [255, 255, 255],
                'title' => [255, 255, 255], 'author' => [243, 232, 255],
                'tag' => 'TÂM LÝ HỌC & NHẬN THỨC CON NGƯỜI',
            ],
            'Kỹ năng sống' => [
                'bgTop' => [120, 53, 15], 'bgBottom' => [180, 83, 9],
                'border' => [253, 224, 71], 'accent' => [245, 158, 11],
                'badgeBg' => [217, 119, 6], 'badgeText' => [255, 255, 255],
                'title' => [255, 255, 255], 'author' => [254, 249, 195],
                'tag' => 'KỸ NĂNG SỐNG & PHÁT TRIỂN BẢN THÂN',
            ],
            'Văn học' => [
                'bgTop' => [41, 37, 36], 'bgBottom' => [68, 64, 60],
                'border' => [217, 119, 6], 'accent' => [245, 158, 11],
                'badgeBg' => [168, 162, 158], 'badgeText' => [28, 25, 23],
                'title' => [255, 255, 255], 'author' => [254, 243, 199],
                'tag' => 'TÁC PHẨM VĂN HỌC TINH HOA',
            ],
        ];

        $pal = $palettes[$categoryName] ?? $palettes['Văn học'];

        // Gradient nền dọc mềm mại
        for ($y = 0; $y < $h; $y++) {
            $factor = $y / $h;
            $r = (int) ($pal['bgTop'][0] * (1 - $factor) + $pal['bgBottom'][0] * $factor);
            $g = (int) ($pal['bgTop'][1] * (1 - $factor) + $pal['bgBottom'][1] * $factor);
            $b = (int) ($pal['bgTop'][2] * (1 - $factor) + $pal['bgBottom'][2] * $factor);
            $color = imagecolorallocate($img, $r, $g, $b);
            imageline($img, 0, $y, $w, $y, $color);
        }

        $cBorder = imagecolorallocate($img, $pal['border'][0], $pal['border'][1], $pal['border'][2]);
        $cTitle = imagecolorallocate($img, $pal['title'][0], $pal['title'][1], $pal['title'][2]);
        $cTitleShadow = imagecolorallocate($img, 10, 10, 15);
        $cAuthor = imagecolorallocate($img, $pal['author'][0], $pal['author'][1], $pal['author'][2]);
        $cBadgeBg = imagecolorallocate($img, $pal['badgeBg'][0], $pal['badgeBg'][1], $pal['badgeBg'][2]);
        $cBadgeText = imagecolorallocate($img, $pal['badgeText'][0], $pal['badgeText'][1], $pal['badgeText'][2]);
        $cGold = imagecolorallocate($img, 245, 158, 11);
        $cWhite = imagecolorallocate($img, 255, 255, 255);

        // Viền kép sang trọng
        imagesetthickness($img, 2);
        imagerectangle($img, 20, 20, $w - 20, $h - 20, $cBorder);
        imagerectangle($img, 26, 26, $w - 26, $h - 26, $cBorder);

        // Góc hoa văn kim loại
        imagefilledrectangle($img, 18, 18, 28, 28, $cGold);
        imagefilledrectangle($img, $w - 28, 18, $w - 18, 28, $cGold);
        imagefilledrectangle($img, 18, $h - 28, 28, $h - 18, $cGold);
        imagefilledrectangle($img, $w - 28, $h - 28, $w - 18, $h - 18, $cGold);

        // Font chữ: Dùng Arial Bold / Times New Roman hỗ trợ tiếng Việt UTF-8 chuẩn xác
        $fontTitle = 'C:/Windows/Fonts/arialbd.ttf';
        $fontSans = 'C:/Windows/Fonts/arial.ttf';
        if ($categoryName === 'Văn học' || $categoryName === 'Tâm lý học') {
            $fontTitle = 'C:/Windows/Fonts/times.ttf';
        }

        // 1. Badge thể loại
        $badgeText = '• '.$pal['tag'].' •';
        $badgeBox = imagettfbbox(11, 0, $fontSans, $badgeText);
        $badgeW = abs($badgeBox[4] - $badgeBox[0]);
        $badgeX = (int) (($w - $badgeW) / 2);
        $badgeY = 90;

        imagefilledrectangle($img, $badgeX - 16, $badgeY - 20, $badgeX + $badgeW + 16, $badgeY + 8, $cBadgeBg);
        imagerectangle($img, $badgeX - 16, $badgeY - 20, $badgeX + $badgeW + 16, $badgeY + 8, $cWhite);
        imagettftext($img, 11, 0, $badgeX, $badgeY - 2, $cBadgeText, $fontSans, $badgeText);

        // 2. Tiêu đề sách (Wrap dòng chuẩn)
        $words = explode(' ', $title);
        $lines = [];
        $currentLine = '';
        $fontSize = (mb_strlen($title) > 35) ? 22 : ((mb_strlen($title) > 22) ? 26 : 30);

        foreach ($words as $word) {
            $testLine = $currentLine ? ($currentLine.' '.$word) : $word;
            $box = imagettfbbox($fontSize, 0, $fontTitle, $testLine);
            $testW = abs($box[4] - $box[0]);
            if ($testW > ($w - 110)) {
                if ($currentLine) {
                    $lines[] = $currentLine;
                }
                $currentLine = $word;
            } else {
                $currentLine = $testLine;
            }
        }
        if ($currentLine) {
            $lines[] = $currentLine;
        }

        $lineHeight = (int) ($fontSize * 1.55);
        $totalTitleH = count($lines) * $lineHeight;
        $startY = (int) (($h / 2) - ($totalTitleH / 2) - 30);

        foreach ($lines as $i => $line) {
            $box = imagettfbbox($fontSize, 0, $fontTitle, $line);
            $lineW = abs($box[4] - $box[0]);
            $lineX = (int) (($w - $lineW) / 2);
            $lineY = $startY + ($i * $lineHeight);

            // Đổ bóng chữ
            imagettftext($img, $fontSize, 0, $lineX + 2, $lineY + 2, $cTitleShadow, $fontTitle, $line);
            // Chữ chính
            imagettftext($img, $fontSize, 0, $lineX, $lineY, $cTitle, $fontTitle, $line);
        }

        // 3. Đường chỉ chia cách
        $divY = $startY + $totalTitleH + 20;
        imageline($img, (int) ($w / 2 - 80), $divY, (int) ($w / 2 - 15), $divY, $cGold);
        imagefilledellipse($img, (int) ($w / 2), $divY, 10, 10, $cGold);
        imageline($img, (int) ($w / 2 + 15), $divY, (int) ($w / 2 + 80), $divY, $cGold);

        // 4. Tên tác giả
        $authorText = mb_strtoupper($author, 'UTF-8');
        $authorFontSize = (mb_strlen($authorText) > 30) ? 12 : 15;
        $authorBox = imagettfbbox($authorFontSize, 0, $fontTitle, $authorText);
        $authorW = abs($authorBox[4] - $authorBox[0]);
        $authorX = (int) (($w - $authorW) / 2);
        $authorY = $divY + 45;
        imagettftext($img, $authorFontSize, 0, $authorX, $authorY, $cAuthor, $fontTitle, $authorText);

        // 5. Chân trang nhận diện thư viện
        $footerText = 'TIỂU ĐỘI 2P • THƯ VIỆN SỐ QUỐC GIA';
        $fBox = imagettfbbox(10, 0, $fontSans, $footerText);
        $fW = abs($fBox[4] - $fBox[0]);
        $fX = (int) (($w - $fW) / 2);
        imagettftext($img, 10, 0, $fX, $h - 50, $cGold, $fontSans, $footerText);

        imagejpeg($img, $destPath, 92);
        imagedestroy($img);
    }

    /**
     * Danh mục 300 đầu sách kinh điển có thật 100%
     */
    protected function getBooksCatalog(): array
    {
        return [
            // ==========================================
            // I. CNTT (25 CUỐN BỔ SUNG, TỔNG THÀNH 50)
            // ==========================================
            'CNTT' => [
                [
                    'title' => 'The DevOps Handbook',
                    'author' => 'Gene Kim, Jez Humble, Patrick Debois, John Willis',
                    'publisher' => 'IT Revolution Press',
                    'description' => 'Cẩm nang thực chiến hàng đầu về tích hợp văn hóa DevOps, tự động hóa quy trình phân phối phần mềm và tối ưu hóa phản hồi khách hàng trong doanh nghiệp công nghệ.',
                ],
                [
                    'title' => 'Accelerate: The Science of Lean Software and DevOps',
                    'author' => 'Nicole Forsgren, Jez Humble, Gene Kim',
                    'publisher' => 'IT Revolution Press',
                    'description' => 'Công trình nghiên cứu khoa học 4 năm chứng minh tác động trực tiếp của thực hành DevOps và năng lực kỹ thuật xuất sắc lên hiệu suất kinh doanh vượt bậc của tổ chức.',
                ],
                [
                    'title' => 'Kubernetes in Action',
                    'author' => 'Marko Luksa',
                    'publisher' => 'Manning Publications',
                    'description' => 'Hướng dẫn toàn diện từ nền tảng đến nâng cao về điều phối container với Kubernetes, giải quyết bài toán mở rộng quy mô, tự phục hồi và triển khai ứng dụng đám mây mượt mà.',
                ],
                [
                    'title' => 'Designing Distributed Systems',
                    'author' => 'Brendan Burns',
                    'publisher' => "O'Reilly Media",
                    'description' => 'Tuyển tập các mẫu thiết kế và giải pháp kiến trúc phân tán lặp lại giúp lập trình viên nhanh chóng xây dựng hệ sinh thái container ổn định, dễ quản trị.',
                ],
                [
                    'title' => 'Microservices Patterns',
                    'author' => 'Chris Richardson',
                    'publisher' => 'Manning Publications',
                    'description' => 'Phân tích 44 mẫu thiết kế thực tế giải quyết triệt để các bài toán hóc búa về phân rã cơ sở dữ liệu, quản lý giao dịch saga và truy vấn dữ liệu trong kiến trúc vi dịch vụ.',
                ],
                [
                    'title' => 'Site Reliability Engineering (SRE)',
                    'author' => 'Betsy Beyer, Chris Jones, Jennifer Petoff, Niall Murphy',
                    'publisher' => "O'Reilly Media",
                    'description' => 'Bí quyết vận hành các hệ thống phân tán quy mô toàn cầu từ các kỹ sư Google, kết hợp hoàn hảo giữa tư duy phần mềm và quản trị hạ tầng vận hành độ tin cậy cao.',
                ],
                [
                    'title' => 'The Phoenix Project',
                    'author' => 'Gene Kim, Kevin Behr, George Spafford',
                    'publisher' => 'IT Revolution Press',
                    'description' => 'Tiểu thuyết kinh điển về quản trị CNTT, khắc họa sống động hành trình cứu vãn một dự án công nghệ bờ vực phá sản bằng tư duy tinh gọn và văn hóa hợp tác DevOps.',
                ],
                [
                    'title' => 'Grokking Algorithms',
                    'author' => 'Aditya Bhargava',
                    'publisher' => 'Manning Publications',
                    'description' => 'Cách tiếp cận thuật toán và cấu trúc dữ liệu trực quan sinh động thông qua hình ảnh minh họa dễ hiểu, giúp bạn làm chủ thuật toán tìm kiếm, sắp xếp và đồ thị một cách hào hứng.',
                ],
                [
                    'title' => 'Clean Craftsmanship: Disciplines, Standards, and Ethics',
                    'author' => 'Robert C. Martin (Uncle Bob)',
                    'publisher' => 'Addison-Wesley',
                    'description' => 'Tuyên ngôn đạo đức và kỷ luật nghề nghiệp lập trình của Uncle Bob, tái khẳng định sức mạnh của Test-Driven Development (TDD), Refactoring và thiết kế đơn giản.',
                ],
                [
                    'title' => 'Cloud Native Patterns',
                    'author' => 'Cornelia Davis',
                    'publisher' => 'Manning Publications',
                    'description' => 'Khám phá các nguyên lý cốt lõi của phần mềm đám mây bản địa, từ tính độc lập dữ liệu đến khả năng phục hồi lỗi tự động và giao tiếp hướng sự kiện linh hoạt.',
                ],
                [
                    'title' => 'Learning React: Modern Patterns for Developing React Apps',
                    'author' => 'Alex Banks & Eve Porcello',
                    'publisher' => "O'Reilly Media",
                    'description' => 'Giáo trình thực hành React hiện đại từ tư duy lập trình hàm, React Hooks đến quản lý trạng thái và tối ưu hiệu năng render giao diện ứng dụng web phức tạp.',
                ],
                [
                    'title' => 'Pro Git',
                    'author' => 'Scott Chacon & Ben Straub',
                    'publisher' => 'Apress',
                    'description' => 'Kinh thánh về hệ thống quản lý phiên bản Git, hướng dẫn chi tiết từ các thao tác nhánh cơ bản đến kỹ thuật giải quyết xung đột và nội vi hệ thống tệp tin Git.',
                ],
                [
                    'title' => 'High Performance Browser Networking',
                    'author' => 'Ilya Grigorik',
                    'publisher' => "O'Reilly Media",
                    'description' => 'Phân tích sâu sắc các giao thức mạng HTTP/2, TCP, UDP và WebSocket nhằm giúp kỹ sư tối ưu hóa tốc độ tải trang và trải nghiệm người dùng web đến từng mili-giây.',
                ],
                [
                    'title' => 'Database Internals: A Deep Dive into How Distributed Systems Work',
                    'author' => 'Alex Petrov',
                    'publisher' => "O'Reilly Media",
                    'description' => 'Khám phá cơ chế hoạt động tầng sâu của các công cụ lưu trữ dữ liệu, cấu trúc B-Tree, LSM-Tree và thuật toán đồng thuận phân tán Raft/Paxos chuẩn xác.',
                ],
                [
                    'title' => 'Modern Operating Systems',
                    'author' => 'Andrew S. Tanenbaum & Herbert Bos',
                    'publisher' => 'Pearson',
                    'description' => 'Giáo trình kinh điển toàn cầu về hệ điều hành, làm sáng tỏ cơ chế quản lý tiến trình, bộ nhớ ảo, hệ thống tệp và bảo mật lõi từ cấp độ hệ thống máy tính.',
                ],
                [
                    'title' => 'Computer Networks',
                    'author' => 'Andrew S. Tanenbaum & David J. Wetherall',
                    'publisher' => 'Pearson',
                    'description' => 'Tác phẩm chuẩn mực thế giới giải thích cặn kẽ 5 tầng kiến trúc mạng máy tính, giao thức định tuyến và bảo mật thông tin trên môi trường Internet toàn cầu.',
                ],
                [
                    'title' => 'Deep Learning',
                    'author' => 'Ian Goodfellow, Yoshua Bengio, Aaron Courville',
                    'publisher' => 'MIT Press',
                    'description' => 'Cuốn sách nền tảng toán học và kỹ thuật về học sâu từ ba cha đẻ AI thế giới, bao quát mạng nơ-ron tích chập, mạng hồi quy và mô hình sinh tạo hiện đại.',
                ],
                [
                    'title' => 'Hands-On Machine Learning with Scikit-Learn, Keras, and TensorFlow',
                    'author' => 'Aurélien Géron',
                    'publisher' => "O'Reilly Media",
                    'description' => 'Cẩm nang thực hành trí tuệ nhân tạo toàn diện nhất, hướng dẫn từng bước xây dựng các mô hình máy học và mạng nơ-ron nhân tạo giải quyết bài toán thực tế.',
                ],
                [
                    'title' => 'Artificial Intelligence: A Modern Approach',
                    'author' => 'Stuart Russell & Peter Norvig',
                    'publisher' => 'Pearson',
                    'description' => 'Bách khoa toàn thư số một hành tinh về trí tuệ nhân tạo, đưa độc giả từ các tác tử thông minh cơ bản đến học máy, xử lý ngôn ngữ tự nhiên và triết học AI.',
                ],
                [
                    'title' => 'Practical Object-Oriented Design (POODR)',
                    'author' => 'Sandi Metz',
                    'publisher' => 'Addison-Wesley',
                    'description' => 'Nghệ thuật thiết kế hướng đối tượng thực tế giúp giảm thiểu chi phí bảo trì, áp dụng nguyên lý SOLID và kỹ thuật phân tách trách nhiệm mã nguồn linh hoạt.',
                ],
                [
                    'title' => 'The Art of Computer Programming (Vol 1)',
                    'author' => 'Donald E. Knuth',
                    'publisher' => 'Addison-Wesley',
                    'description' => 'Công trình vĩ đại của huyền thoại Donald Knuth đặt nền móng toán học chặt chẽ cho toàn bộ ngành khoa học máy tính và phân tích thuật toán chuẩn mực.',
                ],
                [
                    'title' => 'Compilers: Principles, Techniques, and Tools (Dragon Book)',
                    'author' => 'Alfred V. Aho, Monica S. Lam, Ravi Sethi, Jeffrey D. Ullman',
                    'publisher' => 'Pearson',
                    'description' => 'Sách Rồng kinh điển về thiết kế trình biên dịch, làm sáng tỏ các kỹ thuật phân tích từ vựng, cú pháp, tối ưu mã nguồn và sinh mã máy nhị phân.',
                ],
                [
                    'title' => 'Hacking: The Art of Exploitation',
                    'author' => 'Jon Erickson',
                    'publisher' => 'No Starch Press',
                    'description' => 'Khám phá bản chất an ninh mạng và kiến trúc bộ nhớ thông qua lập trình C, phân tích mã độc và cách thức phòng thủ các lỗ hổng tràn bộ đệm hệ thống.',
                ],
                [
                    'title' => 'Clean Architecture in Python',
                    'author' => 'Leonardo Giordani',
                    'publisher' => 'Packt Publishing',
                    'description' => 'Ứng dụng các nguyên lý kiến trúc sạch vào hệ sinh thái Python, giúp phân tách rõ ràng giữa nghiệp vụ cốt lõi, framework web và tầng lưu trữ dữ liệu.',
                ],
                [
                    'title' => 'The Unicorn Project',
                    'author' => 'Gene Kim',
                    'publisher' => 'IT Revolution Press',
                    'description' => 'Góc nhìn của kiến trúc sư phần mềm về 5 lý tưởng công nghệ: Đơn giản, Tự chủ, Tập trung, An toàn tâm lý và Thấu hiểu khách hàng trong kỷ nguyên chuyển đổi số.',
                ],
            ],

            // ==========================================
            // II. KINH DOANH (25 CUỐN BỔ SUNG, TỔNG THÀNH 50)
            // ==========================================
            'Kinh doanh' => [
                [
                    'title' => 'Competitive Strategy',
                    'author' => 'Michael E. Porter',
                    'publisher' => 'Free Press',
                    'description' => 'Nền tảng phân tích chiến lược cạnh tranh kinh điển của giáo sư Harvard, giới thiệu mô hình 5 áp lực cạnh tranh định hình lợi nhuận và vị thế dài hạn của ngành.',
                ],
                [
                    'title' => 'Built to Last: Successful Habits of Visionary Companies',
                    'author' => 'Jim Collins & Jerry I. Porras',
                    'publisher' => 'HarperBusiness',
                    'description' => 'Khảo cứu công phu chỉ ra cách các tập đoàn trường tồn qua nhiều thập kỷ bằng cách gìn giữ hệ giá trị cốt lõi song song với liên tục thúc đẩy đổi mới.',
                ],
                [
                    'title' => 'Good Strategy Bad Strategy',
                    'author' => 'Richard Rumelt',
                    'publisher' => 'Crown Business',
                    'description' => 'Tác phẩm bóc trần những khẩu hiệu rỗng tuếch và chỉ ra bản chất của một chiến lược sắc bén: chẩn đoán đúng vấn đề, định hướng hành động tập trung và giải quyết dứt điểm.',
                ],
                [
                    'title' => 'Blitzscaling: The Lightning-Fast Path to Building Massively Valuable Companies',
                    'author' => 'Reid Hoffman & Chris Yeh',
                    'publisher' => 'Currency',
                    'description' => 'Bí quyết tăng trưởng thần tốc ở quy mô bùng nổ của các kỳ lân Thung lũng Silicon, chấp nhận rủi ro và đánh đổi hiệu quả lấy tốc độ chiếm lĩnh thị trường.',
                ],
                [
                    'title' => 'Crossing the Chasm',
                    'author' => 'Geoffrey A. Moore',
                    'publisher' => 'HarperBusiness',
                    'description' => 'Chiến lược tiếp thị công nghệ then chốt giúp các sản phẩm đổi mới sáng tạo vượt qua "hố ngăn cách" giữa nhóm khách hàng cuồng nhiệt sang thị trường đại chúng màu mỡ.',
                ],
                [
                    'title' => 'Sprint: How to Solve Big Problems and Test New Ideas in Just Five Days',
                    'author' => 'Jake Knapp, John Zeratsky, Braden Kowitz',
                    'publisher' => 'Simon & Schuster',
                    'description' => 'Phương pháp Design Sprint độc quyền từ Google Ventures giúp đội ngũ thử nghiệm ý tưởng và kiểm chứng phản hồi khách hàng thực tế chỉ trong vòng 5 ngày làm việc.',
                ],
                [
                    'title' => 'Lean Analytics',
                    'author' => 'Alistair Croll & Benjamin Yoskovitz',
                    'publisher' => "O'Reilly Media",
                    'description' => 'Xác định chỉ số quan trọng duy nhất (OMTM) tại từng giai đoạn khởi nghiệp, giúp doanh nhân đưa ra quyết định dựa trên dữ liệu chuẩn xác thay vì cảm tính.',
                ],
                [
                    'title' => 'The Ride of a Lifetime',
                    'author' => 'Robert Iger',
                    'publisher' => 'Random House',
                    'description' => 'Hồi ký lãnh đạo xuất sắc của cựu CEO Disney, chia sẻ 15 năm tái thiết đế chế truyền thông toàn cầu thông qua các thương vụ lịch sử Pixar, Marvel và Star Wars.',
                ],
                [
                    'title' => 'Principles for Dealing with the Changing World Order',
                    'author' => 'Ray Dalio',
                    'publisher' => 'Avid Reader Press',
                    'description' => 'Nghiên cứu sâu sắc về các đại chu kỳ kinh tế, chính trị và tiền tệ 500 năm qua, giúp các nhà lãnh đạo chuẩn bị vị thế vững vàng trước những biến động địa chính trị lớn.',
                ],
                [
                    'title' => 'The Infinite Game',
                    'author' => 'Simon Sinek',
                    'publisher' => 'Portfolio',
                    'description' => 'Chuyển đổi tư duy từ cuộc chơi hữu hạn thắng-thua sang trò chơi vô cực, xây dựng tổ chức kiên cường có khả năng phát triển bền bỉ qua nhiều thế hệ lãnh đạo.',
                ],
                [
                    'title' => 'Leaders Eat Last',
                    'author' => 'Simon Sinek',
                    'publisher' => 'Portfolio',
                    'description' => 'Khám phá bí mật xây dựng vòng an toàn tâm lý trong tổ chức, nơi người lãnh đạo đặt lợi ích và sự phát triển của cấp dưới lên trên đặc quyền cá nhân.',
                ],
                [
                    'title' => 'Delivering Happiness: A Path to Profits, Passion, and Purpose',
                    'author' => 'Tony Hsieh',
                    'publisher' => 'Business Plus',
                    'description' => 'Câu chuyện đầy cảm hứng về văn hóa dịch vụ khách hàng huyền thoại tại Zappos, chứng minh hạnh phúc của nhân viên là cội nguồn tạo nên đế chế tỷ đô.',
                ],
                [
                    'title' => 'Made to Stick: Why Some Ideas Survive and Others Die',
                    'author' => 'Chip Heath & Dan Heath',
                    'publisher' => 'Random House',
                    'description' => 'Mô hình SUCCES giải mã lý do tại sao một số thông điệp có khả năng in sâu vào tâm trí công chúng, công cụ đắt giá cho tiếp thị, truyền thông và đào tạo.',
                ],
                [
                    'title' => 'Contagious: Why Things Catch On',
                    'author' => 'Jonah Berger',
                    'publisher' => 'Simon & Schuster',
                    'description' => 'Khám phá 6 nguyên lý STEPPS của giáo sư Wharton về cơ chế lan truyền tự nhiên của thông tin và sản phẩm mà không cần ngân sách quảng cáo đắt đỏ.',
                ],
                [
                    'title' => 'Influence: The Psychology of Persuasion',
                    'author' => 'Robert B. Cialdini',
                    'publisher' => 'Harper Business',
                    'description' => 'Kinh thánh về tâm lý học thuyết phục chỉ ra 6 vũ khí tác động tinh vi chi phối quyết định của con người trong kinh doanh, đàm phán và đời sống thường ngày.',
                ],
                [
                    'title' => 'Pre-Suasion: A Revolutionary Way to Influence and Persuade',
                    'author' => 'Robert B. Cialdini',
                    'publisher' => 'Simon & Schuster',
                    'description' => 'Nghệ thuật chuẩn bị tâm lý người nghe trước thời điểm đưa ra thông điệp chính, làm chủ khoảnh khắc then chốt để gia tăng tối đa tỷ lệ đồng thuận.',
                ],
                [
                    'title' => 'Grit: The Power of Passion and Perseverance',
                    'author' => 'Angela Duckworth',
                    'publisher' => 'Scribner',
                    'description' => 'Nghiên cứu tâm lý khẳng định lòng kiên trì và niềm đam mê dài hạn (Grit) đóng vai trò quyết định thành công vượt trội hơn nhiều so với tài năng bẩm sinh.',
                ],
                [
                    'title' => 'Outliers: The Story of Success',
                    'author' => 'Malcolm Gladwell',
                    'publisher' => 'Little, Brown and Company',
                    'description' => 'Giải mã quy luật 10.000 giờ và các yếu tố văn hóa, thời điểm lịch sử đứng sau thành công phi thường của Bill Gates, The Beatles và những nhân vật kiệt xuất.',
                ],
                [
                    'title' => 'The Tipping Point: How Little Things Can Make a Big Difference',
                    'author' => 'Malcolm Gladwell',
                    'publisher' => 'Little, Brown and Company',
                    'description' => 'Khám phá điểm bùng phát – thời khắc kỳ diệu khi một ý tưởng, xu hướng xã hội hay sản phẩm nhỏ bỗng chốc lan tỏa thành một làn sóng toàn cầu.',
                ],
                [
                    'title' => 'Trillion Dollar Coach: The Leadership Playbook of Silicon Valley',
                    'author' => 'Eric Schmidt, Jonathan Rosenberg, Alan Eagle',
                    'publisher' => 'Harper Business',
                    'description' => 'Những bài học lãnh đạo vô giá từ Bill Campbell – người thầy huấn luyện bí mật đứng sau thành công vang dội của Steve Jobs, Larry Page và Sundar Pichai.',
                ],
                [
                    'title' => 'Radical Candor: Be a Kick-Ass Boss Without Losing Your Humanity',
                    'author' => 'Kim Scott',
                    'publisher' => "St. Martin's Press",
                    'description' => 'Phương pháp giao tiếp thẳng thắn triệt để kết hợp với sự quan tâm chân thành, giúp nhà quản lý xây dựng đội ngũ gắn kết và đạt hiệu suất vượt trội.',
                ],
                [
                    'title' => 'Multipliers: How the Best Leaders Make Everyone Smarter',
                    'author' => 'Liz Wiseman',
                    'publisher' => 'Harper Business',
                    'description' => 'Phân biệt giữa người lãnh đạo nhân bản trí tuệ (Multiplier) giúp nhân viên tỏa sáng gấp đôi và kẻ bòn rút năng lượng (Diminisher) triệt tiêu sáng tạo trong tổ chức.',
                ],
                [
                    'title' => 'Turn the Ship Around!: A True Story of Turning Followers into Leaders',
                    'author' => 'L. David Marquet',
                    'publisher' => 'Portfolio',
                    'description' => 'Hành trình biến tàu ngầm tồi tệ nhất hải quân Mỹ thành chiến hạm xuất sắc nhất bằng cách chuyển đổi từ mô hình chỉ huy-phục tùng sang trao quyền tự chủ.',
                ],
                [
                    'title' => 'Essentialism: The Disciplined Pursuit of Less',
                    'author' => 'Greg McKeown',
                    'publisher' => 'Crown Business',
                    'description' => 'Triết lý theo đuổi sự tối giản có kỷ luật, dạy bạn dũng cảm loại bỏ những điều vụn vặt để dồn toàn bộ tâm sức vào những nhiệm vụ thực sự quan trọng và ý nghĩa.',
                ],
                [
                    'title' => 'Blink: The Power of Thinking Without Thinking',
                    'author' => 'Malcolm Gladwell',
                    'publisher' => 'Back Bay Books',
                    'description' => 'Khám phá sức mạnh của giác quan thứ sáu và những quyết định chớp nhoáng trong hai giây đầu tiên, nơi tiềm thức xử lý thông tin sắc bén không ngờ.',
                ],
            ],

            // ==========================================
            // III. CHỨNG KHOÁN (25 CUỐN BỔ SUNG, TỔNG THÀNH 50)
            // ==========================================
            'Chứng khoán' => [
                [
                    'title' => 'The Most Important Thing Illuminated',
                    'author' => 'Howard Marks',
                    'publisher' => 'Columbia Business School Publishing',
                    'description' => '21 triết lý cốt lõi về tư duy cấp độ hai, định giá tài sản và quản trị rủi ro từ nhà sáng lập Oaktree Capital, tác phẩm được Warren Buffett khuyên đọc nhiều lần.',
                ],
                [
                    'title' => 'The Dhandho Investor',
                    'author' => 'Mohnish Pabrai',
                    'publisher' => 'Wiley',
                    'description' => 'Chiến lược đầu tư giá trị Dhandho đậm chất thực dụng: "Ngửa thì tôi thắng lớn, sấp thì tôi chẳng mất bao nhiêu", tối ưu hóa tỷ lệ lợi nhuận trên rủi ro thấp.',
                ],
                [
                    'title' => "Poor Charlie's Almanack",
                    'author' => 'Peter D. Kaufman & Charlie Munger',
                    'publisher' => 'Warranting',
                    'description' => 'Tuyển tập trí tuệ đa ngành và các mô hình nhận thức đắt giá của bộ óc vĩ đại Charlie Munger, kim chỉ nam rèn luyện tư duy duy lý và đạo đức tài chính.',
                ],
                [
                    'title' => 'The Warren Buffett Way',
                    'author' => 'Robert G. Hagstrom',
                    'publisher' => 'Wiley',
                    'description' => 'Đúc kết chi tiết 12 nguyên tắc đầu tư kinh điển của Warren Buffett trên các khía cạnh doanh nghiệp, quản trị, tài chính và định giá biên an toàn thực tế.',
                ],
                [
                    'title' => 'The Snowball: Warren Buffett and the Business of Life',
                    'author' => 'Alice Schroeder',
                    'publisher' => 'Bantam Books',
                    'description' => 'Tiểu sử toàn diện và chân thực nhất về cuộc đời, sự nghiệp tích lũy của cải và bài học nhân sinh của nhà đầu tư thành công nhất mọi thời đại.',
                ],
                [
                    'title' => 'University of Berkshire Hathaway',
                    'author' => 'Daniel Pecaut & Corey Wrenn',
                    'publisher' => 'Pecaut & Co.',
                    'description' => 'Ghi chép quý giá suốt 30 năm tham dự các đại hội cổ đông thường niên của Berkshire Hathaway, chắt lọc tinh hoa đối đáp giữa Warren Buffett và Charlie Munger.',
                ],
                [
                    'title' => 'Quality Investing: Owning the Best Companies for the Long Term',
                    'author' => 'Lawrence A. Cunningham, Torkell T. Eide, Patrick Hargreaves',
                    'publisher' => 'Harriman House',
                    'description' => 'Khung thẩm định các công ty chất lượng cao có lợi thế cạnh tranh bền vững, tăng trưởng lợi nhuận ổn định và khả năng tạo dòng tiền tự do vượt trội.',
                ],
                [
                    'title' => 'Expectations Investing',
                    'author' => 'Michael J. Mauboussin & Alfred Rappaport',
                    'publisher' => 'Columbia Business School Publishing',
                    'description' => 'Phương pháp tiếp cận thị trường ngược dòng bằng cách giải mã những kỳ vọng tài chính ẩn sau giá cổ phiếu hiện tại để phát hiện cơ hội định giá sai lầm.',
                ],
                [
                    'title' => 'More Than You Know: Finding Financial Wisdom in Unconventional Places',
                    'author' => 'Michael J. Mauboussin',
                    'publisher' => 'Columbia Business School Publishing',
                    'description' => 'Kết nối tài chính với sinh học, vật lý và lý thuyết trò chơi để xây dựng tư duy đầu tư đa chiều, giúp bạn tách bạch giữa may mắn và kỹ năng giao dịch.',
                ],
                [
                    'title' => 'The Essays of Warren Buffett',
                    'author' => 'Lawrence A. Cunningham',
                    'publisher' => 'The Cunningham Group',
                    'description' => 'Tuyển tập các bức thư gửi cổ đông được sắp xếp theo chuyên đề quản trị công ty, kế toán tài chính, sáp nhập doanh nghiệp và định giá cổ phiếu mẫu mực.',
                ],
                [
                    'title' => 'When Genius Failed: The Rise and Fall of Long-Term Capital Management',
                    'author' => 'Roger Lowenstein',
                    'publisher' => 'Random House',
                    'description' => 'Bài học đắt giá về sự sụp đổ của quỹ phòng hộ LTCM quy tụ các nhà kinh tế đoạt giải Nobel, minh chứng cho sự nguy hiểm chết người của đòn bẩy tài chính quá mức.',
                ],
                [
                    'title' => 'Against the Gods: The Remarkable Story of Risk',
                    'author' => 'Peter L. Bernstein',
                    'publisher' => 'Wiley',
                    'description' => 'Biên niên sử hấp dẫn về lịch sử kiểm soát rủi ro của nhân loại từ thời cổ đại đến các công cụ phái sinh hiện đại, định hình nền tảng thế giới tài chính ngày nay.',
                ],
                [
                    'title' => 'The Big Short: Inside the Doomsday Machine',
                    'author' => 'Michael Lewis',
                    'publisher' => 'W. W. Norton & Company',
                    'description' => 'Câu chuyện ly kỳ về một số ít nhà đầu tư nhìn thấy trước quả bom nổ chậm của thị trường bất động sản dưới chuẩn Mỹ năm 2008 và thu lợi nhuận khổng lồ.',
                ],
                [
                    'title' => "Liar's Poker",
                    'author' => 'Michael Lewis',
                    'publisher' => 'W. W. Norton & Company',
                    'description' => 'Bức tranh trần trụi và hài hước về văn hóa Phố Wall thập niên 1980 tại Salomon Brothers, nơi lòng tham và các chiêu trò buôn bán trái phiếu đạt đỉnh điểm.',
                ],
                [
                    'title' => 'Flash Boys: A Wall Street Revolt',
                    'author' => 'Michael Lewis',
                    'publisher' => 'W. W. Norton & Company',
                    'description' => 'Vạch trần mặt tối của giao dịch tần suất cao (HFT) nơi các thuật toán siêu máy tính thao túng thị trường chứng khoán trước mắt các nhà đầu tư nhỏ lẻ.',
                ],
                [
                    'title' => 'The Man Who Solved the Market',
                    'author' => 'Gregory Zuckerman',
                    'publisher' => 'Portfolio',
                    'description' => 'Giải mã huyền thoại Jim Simons và quỹ định lượng Renaissance Technologies, cỗ máy in tiền thuật toán đạt tỷ suất sinh lời vô địch trong lịch sử tài chính.',
                ],
                [
                    'title' => 'How to Trade in Stocks',
                    'author' => 'Jesse Livermore',
                    'publisher' => 'McGraw-Hill',
                    'description' => 'Những quy tắc giao dịch nguyên bản do chính nhà đầu cơ huyền thoại Jesse Livermore chấp bút về thời điểm then chốt, quản lý tiền và kỷ luật tâm lý thép.',
                ],
                [
                    'title' => 'Japanese Candlestick Charting Techniques',
                    'author' => 'Steve Nison',
                    'publisher' => 'New York Institute of Finance',
                    'description' => 'Giáo trình đưa mô hình biểu đồ nến Nhật Bản ra toàn cầu, hướng dẫn chi tiết cách nhận diện tín hiệu đảo chiều và xu hướng tiếp diễn trong phân tích kỹ thuật.',
                ],
                [
                    'title' => 'Technical Analysis of the Financial Markets',
                    'author' => 'John J. Murphy',
                    'publisher' => 'New York Institute of Finance',
                    'description' => 'Bách khoa toàn thư hoàn chỉnh về phân tích kỹ thuật, bao quát lý thuyết Dow, mô hình giá, chỉ báo kỹ thuật và mối liên hệ liên thị trường chuyên sâu.',
                ],
                [
                    'title' => 'Encyclopedia of Chart Patterns',
                    'author' => 'Thomas N. Bulkowski',
                    'publisher' => 'Wiley',
                    'description' => 'Nghiên cứu thống kê định lượng chi tiết nhất về độ chuẩn xác và xác suất thắng của từng mô hình biểu đồ giá trong lịch sử giao dịch chứng khoán.',
                ],
                [
                    'title' => 'Trading in the Zone',
                    'author' => 'Mark Douglas',
                    'publisher' => 'New York Institute of Finance',
                    'description' => 'Kiệt tác làm chủ tâm lý giao dịch, giúp bạn xây dựng tư duy xác suất, chấp nhận rủi ro hoàn toàn và loại bỏ nỗi sợ hãi tê liệt trước mọi lệnh mua bán.',
                ],
                [
                    'title' => 'The Disciplined Trader',
                    'author' => 'Mark Douglas',
                    'publisher' => 'New York Institute of Finance',
                    'description' => 'Hướng dẫn phát triển kỷ luật tinh thần tự giác để kiểm soát cảm xúc tham lam và hoảng loạn, yếu tố then chốt quyết định 90% sự thành bại của nhà đầu tư.',
                ],
                [
                    'title' => 'Market Mind Games',
                    'author' => 'Denise Shull',
                    'publisher' => 'McGraw-Hill',
                    'description' => 'Vận dụng khoa học thần kinh để khai thác trực giác và cảm xúc như những dữ liệu quý giá trong việc ra quyết định đầu tư tại các thời khắc căng thẳng.',
                ],
                [
                    'title' => 'The Zurich Axioms',
                    'author' => 'Max Gunther',
                    'publisher' => 'Harriman House',
                    'description' => '12 quy tắc vàng về rủi ro và đầu cơ tài chính của các chủ ngân hàng Thụy Sĩ, khẳng định dám chấp nhận rủi ro có tính toán là con đường duy nhất để làm giàu.',
                ],
                [
                    'title' => 'Beyond Candlesticks',
                    'author' => 'Steve Nison',
                    'publisher' => 'Wiley',
                    'description' => 'Nâng cấp kỹ thuật nến Nhật với các công cụ đồ thị ba đường gãy (Three-Line Break), Renko và Kagi giúp lọc nhiễu thị trường và nắm bắt xu hướng lớn.',
                ],
            ],

            // ==========================================
            // IV. TRUYỆN ANIME / MANGA (25 CUỐN BỔ SUNG, TỔNG THÀNH 50)
            // ==========================================
            'Truyện Anime' => [
                [
                    'title' => 'Manga Berserk Vol 1',
                    'author' => 'Kentaro Miura',
                    'publisher' => 'Hakusensha',
                    'description' => 'Tuyệt tác dark fantasy đỉnh cao về kiếm sĩ đen Guts mang thanh đại kiếm Dragon Slayer và ấn ký tế lễ, dấn thân vào hành trình báo thù khốc liệt chống lại định mệnh thần thánh.',
                ],
                [
                    'title' => 'Manga Vagabond Vol 1',
                    'author' => 'Takehiko Inoue',
                    'publisher' => 'Kodansha',
                    'description' => 'Khắc họa cuộc đời kiếm thánh huyền thoại Miyamoto Musashi bằng nét vẽ mực tàu siêu phàm, hành trình giác ngộ kiếm đạo và tìm kiếm bản ngã chân thật.',
                ],
                [
                    'title' => 'Manga Vinland Saga Vol 1',
                    'author' => 'Makoto Yukimura',
                    'publisher' => 'Kodansha',
                    'description' => 'Sử thi Viking tráng lệ về chàng thiếu niên Thorfinn nung nấu ý chí trả thù cho cha, từng bước nhận ra sự vô nghĩa của bạo lực và đi tìm miền đất hứa Vinland không có chiến tranh.',
                ],
                [
                    'title' => 'Manga Monster Vol 1',
                    'author' => 'Naoki Urasawa',
                    'publisher' => 'Shogakukan',
                    'description' => 'Cuộc đấu trí nghẹt thở của bác sĩ phẫu thuật não Kenzo Tenma khi nhận ra đứa trẻ mình từng cứu mạng năm xưa nay đã trở thành một con quái vật tâm thần tàn bạo Johan Liebert.',
                ],
                [
                    'title' => 'Manga 20th Century Boys Vol 1',
                    'author' => 'Naoki Urasawa',
                    'publisher' => 'Shogakukan',
                    'description' => 'Nhóm bạn thời thơ ấu phát hiện những trò chơi giả tưởng năm xưa bỗng biến thành âm mưu tận thế có thật do giáo chủ bí ẩn "Bạn" giật dây, mở ra cuộc điều tra nghẹt thở.',
                ],
                [
                    'title' => 'Manga Kingdom Vol 1',
                    'author' => 'Yasuhisa Hara',
                    'publisher' => 'Shueisha',
                    'description' => 'Thời kỳ Chiến Quốc khói lửa khốc liệt, cậu bé nô lệ Tín mang ước mơ trở thành Thiên hạ Đệ nhất Đại tướng quân, kề vai sát cánh cùng Doanh Chính thống nhất Trung Hoa.',
                ],
                [
                    'title' => 'Manga Inuyasha Vol 1',
                    'author' => 'Rumiko Takahashi',
                    'publisher' => 'Shogakukan',
                    'description' => 'Nữ sinh Kagome bị kéo về thời Chiến Quốc qua chiếc giếng cổ, giải phong ấn cho bán yêu Khuyển Dạ Xoa và bước vào hành trình thu thập các mảnh vỡ Ngọc Tứ Hồn quyền năng.',
                ],
                [
                    'title' => 'Manga Ranma 1/2 Vol 1',
                    'author' => 'Rumiko Takahashi',
                    'publisher' => 'Shogakukan',
                    'description' => 'Chàng thiếu niên võ thuật Saotome Ranma bị dính lời nguyền suối ma thuật: chạm nước lạnh biến thành con gái, dội nước nóng trở lại thành con trai, tạo nên vô số tình huống dở khóc dở cười.',
                ],
                [
                    'title' => 'Manga Yu Yu Hakusho Vol 1',
                    'author' => 'Yoshihiro Togashi',
                    'publisher' => 'Shueisha',
                    'description' => 'Cậu học sinh ngỗ nghịch Yusuke Urameshi hy sinh thân mình cứu một đứa bé và được linh giới ban cho cơ hội hồi sinh với tư cách Thám tử Linh giới trừ tà bảo vệ nhân gian.',
                ],
                [
                    'title' => 'Manga Rurouni Kenshin Vol 1',
                    'author' => 'Nobuhiro Watsuki',
                    'publisher' => 'Shueisha',
                    'description' => 'Kiếm khách lang thang Himura Kenshin với thanh kiếm lưỡi ngược thề không bao giờ sát sinh, dùng kiếm thuật phi thiên ngự kiếm để chuộc lại quá khứ sát thủ thời Mạc Phủ.',
                ],
                [
                    'title' => 'Manga Tokyo Revengers Vol 1',
                    'author' => 'Ken Wakui',
                    'publisher' => 'Kodansha',
                    'description' => 'Chàng thanh niên thất bại Takemichi bất ngờ quay ngược thời gian về 12 năm trước thời học sinh, quyết tâm thay đổi số phận để cứu người bạn gái thanh xuân khỏi tay băng đảng Tokyo Manji.',
                ],
                [
                    'title' => 'Manga Blue Lock Vol 1',
                    'author' => 'Muneyuki Kaneshiro & Yusuke Nomura',
                    'publisher' => 'Kodansha',
                    'description' => 'Dự án đào tạo bóng đá khắc nghiệt Blue Lock giam cầm 300 tiền đạo trẻ nhằm nhào nặn ra một "tiền đạo ích kỷ" đỉnh cao nhất thế giới đưa Nhật Bản vô địch World Cup.',
                ],
                [
                    'title' => 'Manga Oshi no Ko Vol 1',
                    'author' => 'Aka Akasaka & Mengo Yokoyari',
                    'publisher' => 'Shueisha',
                    'description' => 'Góc khuất đen tối và hào nhoáng của ngành công nghiệp thần tượng Nhật Bản qua hành trình chuyển sinh và báo thù đầy kịch tính của hai đứa con của thần tượng huyền thoại Hoshino Ai.',
                ],
                [
                    'title' => 'Manga Kaguya-sama: Love Is War Vol 1',
                    'author' => 'Aka Akasaka',
                    'publisher' => 'Shueisha',
                    'description' => 'Cuộc chiến cân não hài hước giữa hai thiên tài hội học sinh Shirogane Miyuki và Shinomiya Kaguya với triết lý: Trong tình yêu, kẻ nào tỏ tình trước là kẻ thất bại!',
                ],
                [
                    'title' => 'Manga Mob Psycho 100 Vol 1',
                    'author' => 'ONE',
                    'publisher' => 'Shogakukan',
                    'description' => 'Cậu học sinh nhút nhát Shigeo Kageyama sở hữu siêu năng lực tâm linh cấp độ hủy diệt nhưng chỉ khao khát sống cuộc đời bình thường, luôn phải kiềm chế cảm xúc không chạm ngưỡng 100%.',
                ],
                [
                    'title' => 'Manga Dr. Stone Vol 1',
                    'author' => 'Riichiro Inagaki & Boichi',
                    'publisher' => 'Shueisha',
                    'description' => 'Toàn bộ nhân loại bỗng chốc bị hóa đá suốt hàng ngàn năm; thiên tài khoa học Senku thức tỉnh và quyết tâm vận dụng sức mạnh khoa học để tái thiết lại nền văn minh từ con số không.',
                ],
                [
                    'title' => 'Manga Fire Force Vol 1',
                    'author' => 'Atsushi Ohkubo',
                    'publisher' => 'Kodansha',
                    'description' => 'Thế giới nơi con người tự bốc hỏa biến thành quái vật Diệt Hỏa; chàng thiếu niên Shinra mang đôi chân bốc lửa gia nhập Đội Lính Cứu Hỏa Đặc Biệt để tìm kiếm sự thật về gia đình.',
                ],
                [
                    'title' => 'Manga Soul Eater Vol 1',
                    'author' => 'Atsushi Ohkubo',
                    'publisher' => 'Square Enix',
                    'description' => 'Học viện đào tạo tử thần và vũ khí Shibusen, nơi các cặp đôi vũ khí - thợ săn linh hồn cùng nhau săn lùng 99 linh hồn quỷ dữ và 1 linh hồn phù thủy để trở thành Lưỡi hái Tử thần.',
                ],
                [
                    'title' => 'Manga Frieren: Beyond Journey\'s End Vol 1',
                    'author' => 'Kanehito Yamada & Tsukasa Abe',
                    'publisher' => 'Shogakukan',
                    'description' => 'Hành trình tĩnh lặng và sâu lắng của pháp sư Elf trường sinh Frieren sau khi đánh bại Ma Vương, từng bước học cách thấu hiểu giá trị của những khoảnh khắc hữu hạn bên con người.',
                ],
                [
                    'title' => 'Manga Delicious in Dungeon Vol 1',
                    'author' => 'Ryoko Kui',
                    'publisher' => 'Enterbrain',
                    'description' => 'Nhóm thám hiểm hầm ngục mạo hiểm sinh tồn bằng cách biến chính những quái vật hầm ngục như nấm đi bộ hay rồng lửa thành các món ẩm thực thơm ngon tuyệt hảo.',
                ],
                [
                    'title' => 'Manga Hell\'s Paradise Vol 1',
                    'author' => 'Yuji Kaku',
                    'publisher' => 'Shueisha',
                    'description' => 'Nhẫn giả tử tù bất tử Gabimaru được trao cơ hội ân xá nếu tìm được thuốc trường sinh trên một hòn đảo thiên đường bí ẩn đầy ắp những sinh vật kỳ dị và chết chóc.',
                ],
                [
                    'title' => 'Manga D.Gray-man Vol 1',
                    'author' => 'Katsura Hoshino',
                    'publisher' => 'Shueisha',
                    'description' => 'Tổ chức Giáo đoàn Đen cùng các Exorcist mang sức mạnh Innocence chiến đấu chống lại Bá tước Thiên Niên Kỷ và binh đoàn quái vật Akuma được tạo ra từ nỗi bi thương của nhân loại.',
                ],
                [
                    'title' => 'Manga Noragami Vol 1',
                    'author' => 'Adachitoka',
                    'publisher' => 'Kodansha',
                    'description' => 'Vị thần vô danh lang thang Yato chỉ nhận tiền công 5 yên để làm mọi việc vặt, cùng cô nữ sinh nửa người nửa yêu Hiyori và vũ khí linh hồn Yukine diệt trừ tà ma.',
                ],
                [
                    'title' => 'Manga The Promised Neverland Vol 1',
                    'author' => 'Kaiu Shirai & Posuka Demizu',
                    'publisher' => 'Shueisha',
                    'description' => 'Trại trẻ mồ côi tưởng chừng như thiên đường của Emma, Norman và Ray thực chất là nông trại nuôi dưỡng trẻ em làm thức ăn cho quỷ, châm ngòi cho kế hoạch đào tẩu sinh tử.',
                ],
                [
                    'title' => 'Manga Nana Vol 1',
                    'author' => 'Ai Yazawa',
                    'publisher' => 'Shueisha',
                    'description' => 'Hai cô gái cùng tên Nana với hai tính cách và số phận đối lập gặp nhau trên chuyến tàu đến Tokyo, mở ra câu chuyện xúc động về âm nhạc, tình yêu và những tổn thương tuổi trẻ.',
                ],
            ],

            // ==========================================
            // V. HỌC NGOẠI NGỮ (50 CUỐN KINH ĐIỂN)
            // ==========================================
            'Học ngoại ngữ' => [
                [
                    'title' => 'Cambridge English Grammar in Use',
                    'author' => 'Raymond Murphy',
                    'publisher' => 'Cambridge University Press',
                    'description' => 'Giáo trình ngữ pháp tiếng Anh bán chạy nhất mọi thời đại, giải thích cấu trúc ngữ pháp ngắn gọn, dễ hiểu kèm bài tập thực hành chuẩn mực toàn cầu.',
                ],
                [
                    'title' => 'English Vocabulary in Use: Upper-Intermediate',
                    'author' => 'Michael McCarthy & Felicity O\'Dell',
                    'publisher' => 'Cambridge University Press',
                    'description' => 'Nâng cao vốn từ vựng học thuật theo ngữ cảnh thực tế, cung cấp các mẹo ghi nhớ từ vựng tự nhiên và bài tập ứng dụng cho người học nâng cao.',
                ],
                [
                    'title' => 'Cambridge IELTS 18 Academic',
                    'author' => 'Cambridge University Press & Assessment',
                    'publisher' => 'Cambridge University Press',
                    'description' => 'Bộ đề thi thử IELTS Academic mới nhất với cấu trúc sát thực tế 100%, kèm giải thích đáp án chi tiết và bài mẫu band điểm cao cho 4 kỹ năng.',
                ],
                [
                    'title' => 'The Official Cambridge Guide to IELTS',
                    'author' => 'Pauline Cullen, Amanda French, Vanessa Jakeman',
                    'publisher' => 'Cambridge University Press',
                    'description' => 'Cẩm nang hướng dẫn chiến thuật làm bài toàn diện từ ban giám khảo Cambridge, rèn luyện kỹ năng xử lý mọi dạng câu hỏi trong bài thi IELTS.',
                ],
                [
                    'title' => 'Collins English for IELTS: Reading',
                    'author' => 'Els Van Geyte',
                    'publisher' => 'Collins',
                    'description' => 'Phương pháp rèn luyện kỹ năng đọc hiểu chuyên sâu cho kỳ thi IELTS, cung cấp chiến lược Skimming, Scanning và mở rộng vốn từ vựng học thuật theo chủ đề.',
                ],
                [
                    'title' => 'Collins English for IELTS: Writing',
                    'author' => 'Matt Baywood',
                    'publisher' => 'Collins',
                    'description' => 'Hướng dẫn từng bước cấu trúc bài viết Task 1 và Task 2, rèn luyện tư duy lập luận logic và sử dụng ngữ pháp nâng cao để đạt điểm tiêu chuẩn.',
                ],
                [
                    'title' => 'Collins English for IELTS: Listening',
                    'author' => 'Fiona Aish & Jo Tomlinson',
                    'publisher' => 'Collins',
                    'description' => 'Luyện nghe tiếng Anh có hệ thống cho kỳ thi IELTS, nhận diện bẫy phát âm và cải thiện độ nhạy bắt từ khóa trong mọi tình huống hội thoại.',
                ],
                [
                    'title' => 'Collins English for IELTS: Speaking',
                    'author' => 'Karen Kovacs',
                    'publisher' => 'Collins',
                    'description' => 'Tự tin phát âm chuẩn và mở rộng câu trả lời cho cả 3 phần thi nói IELTS, trang bị cụm từ nối tự nhiên và phong thái giao tiếp lưu loát.',
                ],
                [
                    'title' => 'Destination B1: Grammar and Vocabulary',
                    'author' => 'Malcolm Mann & Steve Taylore-Knowles',
                    'publisher' => 'Macmillan Education',
                    'description' => 'Nền tảng ngữ pháp và từ vựng then chốt cho trình độ B1/PET, hệ thống bài tập thực hành đa dạng củng cố chắc chắn kiến thức nền tảng.',
                ],
                [
                    'title' => 'Destination B2: Grammar and Vocabulary',
                    'author' => 'Malcolm Mann & Steve Taylore-Knowles',
                    'publisher' => 'Macmillan Education',
                    'description' => 'Tài liệu bắt buộc cho người học hướng tới chứng chỉ B2/FCE hoặc IELTS 6.0+, đào sâu vào các cấu trúc phức tạp và cụm động từ (Phrasal Verbs).',
                ],
                [
                    'title' => 'Destination C1 & C2: Grammar and Vocabulary',
                    'author' => 'Malcolm Mann & Steve Taylore-Knowles',
                    'publisher' => 'Macmillan Education',
                    'description' => 'Đỉnh cao ngữ pháp và từ vựng học thuật dành cho trình độ cao cấp C1-C2 và IELTS 7.5-8.5+, làm chủ các thành ngữ và biến thể ngôn ngữ tinh tế.',
                ],
                [
                    'title' => 'Oxford Advanced Learner\'s Dictionary (10th Edition)',
                    'author' => 'A. S. Hornby',
                    'publisher' => 'Oxford University Press',
                    'description' => 'Từ điển học tiếng Anh uy tín hàng đầu thế giới với định nghĩa chuẩn xác, ví dụ ngữ cảnh phong phú và hướng dẫn phát âm chuẩn Quốc tế IPA.',
                ],
                [
                    'title' => 'Oxford Word Skills: Intermediate',
                    'author' => 'Ruth Gairns & Stuart Redman',
                    'publisher' => 'Oxford University Press',
                    'description' => 'Học và vận dụng hơn 2.000 từ vựng tiếng Anh thông dụng theo chủ đề đời sống, kết hợp các mẹo ngữ dụng học giúp giao tiếp tự nhiên.',
                ],
                [
                    'title' => 'Hack Não 1500 Từ Tiếng Anh',
                    'author' => 'Nguyễn Văn Hiệp',
                    'publisher' => 'NXB Thế Giới',
                    'description' => 'Phương pháp ghi nhớ từ vựng đột phá bằng kỹ thuật âm thanh tương tự và truyện chêm, giúp người học ghi nhớ 1.500 từ cốt lõi nhẹ nhàng.',
                ],
                [
                    'title' => 'Giải Mã IELTS Writing Task 2',
                    'author' => 'Kiên Trần',
                    'publisher' => 'NXB Dân Trí',
                    'description' => 'Chiến lược đơn giản hóa bài viết nghị luận xã hội IELTS, tập trung vào diễn đạt gãy gọn, mạch lạc và tránh lối hành văn sáo rỗng.',
                ],
                [
                    'title' => 'Tự Học 2000 Từ Vựng Tiếng Anh Theo Chủ Đề',
                    'author' => 'The Windy',
                    'publisher' => 'NXB Hồng Đức',
                    'description' => 'Phân loại từ vựng thông dụng theo 40 chủ đề giao tiếp thường nhật, kèm phiên âm chuẩn và ví dụ minh họa giúp người học tự tin ứng dụng.',
                ],
                [
                    'title' => 'Vừa Lười Vừa Bận Vẫn Giỏi Tiếng Anh',
                    'author' => 'Nguyễn Văn Hiệp',
                    'publisher' => 'NXB Thanh Niên',
                    'description' => 'Phương pháp luyện nghe vô thức và phản xạ giao tiếp tự nhiên dành cho người bận rộn, biến việc học ngoại ngữ thành thói quen giải trí.',
                ],
                [
                    'title' => 'Luyện Siêu Trí Nhớ Từ Vựng Tiếng Anh',
                    'author' => 'Nguyễn Ngọc Nam',
                    'publisher' => 'NXB Đại Học Quốc Gia',
                    'description' => 'Áp dụng các kỹ thuật ghi nhớ của các kỷ lục gia thế giới vào việc thuộc lòng từ vựng TOEIC/IELTS theo sơ đồ tư duy Mindmap logic.',
                ],
                [
                    'title' => 'Ngữ Pháp Tiếng Anh Căn Bản',
                    'author' => 'Mai Lan Hương & Hà Thanh Uyên',
                    'publisher' => 'NXB Đà Nẵng',
                    'description' => 'Cẩm nang ngữ pháp tiếng Anh gối đầu giường của học sinh, sinh viên Việt Nam, trình bày cô đọng, dễ hiểu kèm hệ thống bài tập thực hành.',
                ],
                [
                    'title' => '600 Từ Vựng Luyện Thi TOEIC Căn Bản',
                    'author' => 'Lin Lougheed',
                    'publisher' => 'Barron\'s Educational Series',
                    'description' => '50 chủ đề từ vựng thiết yếu trong môi trường kinh doanh và công sở quốc tế, giúp thí sinh đạt điểm mục tiêu TOEIC 650+ nhanh chóng.',
                ],
                [
                    'title' => 'ETS TOEIC Regular Test Practice 1000',
                    'author' => 'YBM & Educational Testing Service (ETS)',
                    'publisher' => 'ETS',
                    'description' => 'Tuyển tập 10 đề thi TOEIC chuẩn format quốc tế từ chính đơn vị ra đề ETS, là tài liệu luyện thi sát thực nhất trước kỳ thi thật.',
                ],
                [
                    'title' => 'Very Easy TOEIC',
                    'author' => 'Anne Taylor & Garrett Byrne',
                    'publisher' => 'Compass Publishing',
                    'description' => 'Giáo trình nhập môn TOEIC hoàn hảo cho người mới bắt đầu, cung cấp các điểm ngữ pháp then chốt và dạng bài nghe cơ bản dễ tiếp thu.',
                ],
                [
                    'title' => 'Target TOEIC Up to 750+',
                    'author' => 'Anne Taylor',
                    'publisher' => 'Compass Publishing',
                    'description' => 'Chiến lược bứt phá điểm số TOEIC từ mức trung cấp lên trên 750 điểm với các bài tập phân tích bẫy đề thi nâng cao.',
                ],
                [
                    'title' => 'HSK 1 Tiêu Chuẩn - Standard Course',
                    'author' => 'Khương Lệ Bình',
                    'publisher' => 'NXB Đại Học Ngôn Ngữ Bắc Kinh',
                    'description' => 'Giáo trình chuẩn mực toàn cầu cho người mới bắt đầu học tiếng Trung, làm quen với phiên âm Pinyin, chữ Hán và các mẫu câu cơ bản.',
                ],
                [
                    'title' => 'HSK 2 Tiêu Chuẩn - Standard Course',
                    'author' => 'Khương Lệ Bình',
                    'publisher' => 'NXB Đại Học Ngôn Ngữ Bắc Kinh',
                    'description' => 'Mở rộng vốn từ vựng lên 300 từ và ngữ pháp giao tiếp hàng ngày cho kỳ thi HSK 2, kèm đĩa nghe chuẩn giọng phổ thông Bắc Kinh.',
                ],
                [
                    'title' => 'HSK 3 Tiêu Chuẩn - Standard Course',
                    'author' => 'Khương Lệ Bình',
                    'publisher' => 'NXB Đại Học Ngôn Ngữ Bắc Kinh',
                    'description' => 'Trang bị 600 từ vựng và kỹ năng giao tiếp lưu loát các chủ đề xã hội, công việc thường nhật, chuẩn bị vững chắc cho kỳ thi HSK 3.',
                ],
                [
                    'title' => 'HSK 4 Tiêu Chuẩn - Standard Course Tập 1',
                    'author' => 'Khương Lệ Bình',
                    'publisher' => 'NXB Đại Học Ngôn Ngữ Bắc Kinh',
                    'description' => 'Nâng tầm kỹ năng nghe, đọc, viết tiếng Trung học thuật cho trình độ trung cấp HSK 4, đào sâu vào các cấu trúc liên từ và thành ngữ.',
                ],
                [
                    'title' => 'HSK 4 Tiêu Chuẩn - Standard Course Tập 2',
                    'author' => 'Khương Lệ Bình',
                    'publisher' => 'NXB Đại Học Ngôn Ngữ Bắc Kinh',
                    'description' => 'Hoàn thiện 1.200 từ vựng HSK 4 và kỹ năng viết đoạn văn ngắn logic, tạo bước đệm hoàn hảo để tiếp cận tiếng Trung chuyên sâu.',
                ],
                [
                    'title' => 'HSK 5 Tiêu Chuẩn - Standard Course Tập 1',
                    'author' => 'Khương Lệ Bình',
                    'publisher' => 'NXB Đại Học Ngôn Ngữ Bắc Kinh',
                    'description' => 'Làm chủ các chủ đề văn hóa, kinh tế, xã hội phức tạp với 2.500 từ vựng, tự tin đọc báo và xem truyền hình Trung Quốc không cần phụ đề.',
                ],
                [
                    'title' => 'Giáo Trình Hán Ngữ 6 Tập (Bộ Mới)',
                    'author' => 'Dương Ký Châu',
                    'publisher' => 'NXB Đại Học Ngôn Ngữ Bắc Kinh',
                    'description' => 'Bộ giáo trình tiếng Hán kinh điển phổ biến nhất tại các khoa ngôn ngữ đại học Việt Nam, hệ thống ngữ pháp chặt chẽ và bài bản.',
                ],
                [
                    'title' => 'Tập Viết Chữ Hán Theo Giáo Trình Hán Ngữ',
                    'author' => 'Dương Ký Châu',
                    'publisher' => 'NXB Thanh Niên',
                    'description' => 'Quy tắc thuận bút, nét viết và bộ thủ cơ bản, giúp người học nắm vững nghệ thuật viết chữ Hán đúng chuẩn và đẹp mắt.',
                ],
                [
                    'title' => '345 Câu Khẩu Ngữ Tiếng Hán',
                    'author' => 'Trương Bằng Bằng',
                    'publisher' => 'NXB Đại Học Quốc Gia',
                    'description' => 'Luyện phản xạ giao tiếp nhanh thông qua 345 mẫu câu khẩu ngữ thông dụng nhất trong giao tiếp đời thường của người Trung Quốc.',
                ],
                [
                    'title' => 'Minna no Nihongo I - Bản Dịch Và Giải Thích Ngữ Pháp',
                    'author' => '3A Corporation',
                    'publisher' => '3A Corporation',
                    'description' => 'Giáo trình tiếng Nhật sơ cấp N5 chuẩn mực toàn cầu, hướng dẫn chi tiết bảng chữ cái, trợ từ, thể động từ và văn hóa giao tiếp Nhật Bản.',
                ],
                [
                    'title' => 'Minna no Nihongo II - Bản Dịch Và Giải Thích Ngữ Pháp',
                    'author' => '3A Corporation',
                    'publisher' => '3A Corporation',
                    'description' => 'Tiếp nối trình độ sơ cấp N4, đào sâu vào thể khả năng, thể bị động, thể sai khiến và kính ngữ (Keigo) trang trọng trong tiếng Nhật.',
                ],
                [
                    'title' => 'Nihongo Soumatome N3 Ngữ Pháp',
                    'author' => 'Sasaki Hitoko & Matsumoto Noriko',
                    'publisher' => 'Ask Publishing',
                    'description' => 'Lộ trình ôn thi JLPT N3 khoa học trong 6 tuần, phân loại các cấu trúc ngữ pháp tương đồng giúp học viên tránh nhầm lẫn khi làm bài thi.',
                ],
                [
                    'title' => 'Nihongo Soumatome N2 Từ Vựng',
                    'author' => 'Sasaki Hitoko & Matsumoto Noriko',
                    'publisher' => 'Ask Publishing',
                    'description' => 'Tổng hợp các từ vựng và cụm từ nâng cao cho kỳ thi JLPT N2 kèm hình ảnh minh họa sinh động, phục vụ giao tiếp công sở chuyên nghiệp.',
                ],
                [
                    'title' => 'Shinkanzen Master N3 Ngữ Pháp',
                    'author' => 'Etsuko Tomomatsu',
                    'publisher' => '3A Corporation',
                    'description' => 'Phân tích bản chất sâu xa của từng mẫu ngữ pháp N3, bài tập bám sát 100% đề thi thật JLPT giúp thí sinh đạt điểm tối đa.',
                ],
                [
                    'title' => 'Kanji Look and Learn: 512 Chữ Hán Qua Hình Ảnh',
                    'author' => 'Banno Eri, Yoko Ikeda, Chikako Shinagawa',
                    'publisher' => 'The Japan Times',
                    'description' => 'Ghi nhớ 512 chữ Hán từ N5 đến N3 dễ dàng thông qua các hình vẽ liên tưởng ngộ nghĩnh và câu chuyện văn hóa thú vị.',
                ],
                [
                    'title' => 'Giáo Trình Tiếng Hàn Tổng Hợp Dành Cho Người Việt 1',
                    'author' => 'Cho Hang Rok & Lee Mi Hye',
                    'publisher' => 'KB Kookmin Bank & NXB ĐHQG',
                    'description' => 'Giáo trình tiếng Hàn sơ cấp chuẩn mực được biên soạn riêng cho người Việt, hướng dẫn chi tiết bảng chữ Hangul và ngữ pháp cơ bản.',
                ],
                [
                    'title' => 'Giáo Trình Tiếng Hàn Tổng Hợp Dành Cho Người Việt 2',
                    'author' => 'Cho Hang Rok & Lee Mi Hye',
                    'publisher' => 'KB Kookmin Bank & NXB ĐHQG',
                    'description' => 'Nâng cao kỹ năng giao tiếp tiếng Hàn sơ cấp 2, mở rộng chủ đề sang mua sắm, ẩm thực, du lịch và thói quen sinh hoạt tại Hàn Quốc.',
                ],
                [
                    'title' => 'Ngữ Pháp Tiếng Hàn Thông Dụng - Sơ Cấp',
                    'author' => 'Ahn Jean-myung, Lee Kyung-ah, Han Hoo-young',
                    'publisher' => 'Darakwon',
                    'description' => 'Bách khoa toàn thư ngữ pháp tiếng Hàn sơ cấp, phân biệt cặn kẽ các cấu trúc dễ gây nhầm lẫn kèm bài tập ngữ cảnh thực tiễn.',
                ],
                [
                    'title' => 'Ngữ Pháp Tiếng Hàn Thông Dụng - Trung Cấp',
                    'author' => 'Ahn Jean-myung, Min Jin-young',
                    'publisher' => 'Darakwon',
                    'description' => 'Giải thích chuyên sâu các cấu trúc ngữ pháp trung cấp TOPIK 3-4, nâng cao năng lực diễn đạt tự nhiên chuẩn phong cách người bản xứ.',
                ],
                [
                    'title' => 'Tự Học Tiếng Pháp Cho Người Mới Bắt Đầu',
                    'author' => 'Hồng Hạnh',
                    'publisher' => 'NXB Hồng Đức',
                    'description' => 'Làm quen với tiếng Pháp qua phát âm chuẩn, danh từ giống đực/giống cái và các thì cơ bản trong giao tiếp hàng ngày tại nước Pháp.',
                ],
                [
                    'title' => 'Giáo Trình Tiếng Đức Studio d A1',
                    'author' => 'Hermann Funk, Christina Kuhn, Silke Demme',
                    'publisher' => 'Cornelsen Verlag',
                    'description' => 'Giáo trình tiếng Đức nhập môn hiện đại chuẩn khung châu Âu, rèn luyện kỹ năng nghe nói tương tác và văn hóa đời sống nước Đức.',
                ],
                [
                    'title' => 'Nghệ Thuật Nói Trước Công Chúng Bằng Tiếng Anh',
                    'author' => 'Dale Carnegie',
                    'publisher' => 'Pocket Books',
                    'description' => 'Cẩm nang diễn thuyết tự tin bằng tiếng Anh, cách thu hút sự chú ý của người nghe và truyền cảm hứng mạnh mẽ trên các diễn đàn quốc tế.',
                ],
                [
                    'title' => 'Luyện Phát Âm Tiếng Anh Chuẩn - Ship or Sheep?',
                    'author' => 'Ann Baker',
                    'publisher' => 'Cambridge University Press',
                    'description' => 'Phân biệt các cặp âm tối thiểu (Minimal Pairs) dễ gây nhầm lẫn trong tiếng Anh, giúp bạn hoàn thiện ngữ điệu và phát âm chuẩn bản xứ.',
                ],
                [
                    'title' => 'English Collocations in Use: Advanced',
                    'author' => 'Michael McCarthy & Felicity O\'Dell',
                    'publisher' => 'Cambridge University Press',
                    'description' => 'Làm chủ các cụm từ kết hợp tự nhiên (Collocations) giúp văn phong nói và viết tiếng Anh trở nên thanh lịch, tự nhiên như người bản ngữ.',
                ],
                [
                    'title' => 'English Idioms in Use: Intermediate',
                    'author' => 'Michael McCarthy & Felicity O\'Dell',
                    'publisher' => 'Cambridge University Press',
                    'description' => 'Khám phá hơn 1.000 thành ngữ tiếng Anh phổ biến trong đời sống, hiểu rõ nguồn gốc văn hóa và cách sử dụng dí dỏm, đúng ngữ cảnh.',
                ],
                [
                    'title' => 'Practical English Usage (4th Edition)',
                    'author' => 'Michael Swan',
                    'publisher' => 'Oxford University Press',
                    'description' => 'Giải đáp toàn bộ những thắc mắc hóc búa nhất về ngữ pháp và cách dùng từ trong tiếng Anh, tài liệu tra cứu không thể thiếu của mọi giáo viên.',
                ],
                [
                    'title' => 'IELTS Advantage: Writing Skills',
                    'author' => 'Richard Brown & Lewis Richards',
                    'publisher' => 'Delta Publishing',
                    'description' => 'Chiến lược bứt phá điểm số Writing từ 6.5 lên 7.5+, tập trung vào phát triển luận điểm thuyết phục và sử dụng từ vựng học thuật đỉnh cao.',
                ],
            ],

            // ==========================================
            // VI. TÂM LÝ HỌC (50 CUỐN KINH ĐIỂN)
            // ==========================================
            'Tâm lý học' => [
                [
                    'title' => 'Tư Duy Nhanh Và Chậm (Thinking, Fast and Slow)',
                    'author' => 'Daniel Kahneman',
                    'publisher' => 'Farrar, Straus and Giroux',
                    'description' => 'Kiệt tác kinh điển của nhà tâm lý học đoạt giải Nobel, làm sáng tỏ hai hệ thống tư duy chi phối nhận thức và những thiên kiến vô thức của con người.',
                ],
                [
                    'title' => 'Tâm Lý Học Đám Đông (The Crowd: A Study of the Popular Mind)',
                    'author' => 'Gustave Le Bon',
                    'publisher' => 'Batoche Books',
                    'description' => 'Khảo luận kinh điển giải mã bản chất phi lý trí và tâm lý bầy đàn của đám đông khi bị cuốn vào các phong trào xã hội, chính trị và kinh tế.',
                ],
                [
                    'title' => 'Thao Túng Tâm Lý (Psychological Manipulation)',
                    'author' => 'Shannon Thomas',
                    'publisher' => 'WorldCastle Publishing',
                    'description' => 'Nhận diện các thủ thuật lạm dụng tâm lý độc hại (Gaslighting), vạch trần kẻ thao túng và tìm lại sự tự chủ tinh thần cùng bình an nội tại.',
                ],
                [
                    'title' => 'Hiểu Về Trái Tim',
                    'author' => 'Minh Niệm',
                    'publisher' => 'NXB Trẻ',
                    'description' => 'Khám phá 50 trạng thái tâm lý của con người từ đau khổ, tức giận đến hạnh phúc và tha thứ, soi chiếu bằng lăng kính thiền định sâu sắc.',
                ],
                [
                    'title' => 'Khi Hơi Thở Hóa Thinh Không (When Breath Becomes Air)',
                    'author' => 'Paul Kalanithi',
                    'publisher' => 'Random House',
                    'description' => 'Hồi ký xúc động của một bác sĩ phẫu thuật não đối mặt với căn bệnh ung thư giai đoạn cuối, kiếm tìm câu trả lời cho ý nghĩa đích thực của sự sống.',
                ],
                [
                    'title' => 'Cởi Trói Linh Hồn (The Untethered Soul)',
                    'author' => 'Michael A. Singer',
                    'publisher' => 'New Harbinger Publications',
                    'description' => 'Hành trình vượt qua tiếng nói lảm nhảm trong tâm trí, buông bỏ những tắc nghẽn năng lượng cũ và chạm tới trạng thái tự do tâm linh thuần khiết.',
                ],
                [
                    'title' => 'Đi Tìm Lẽ Sống (Man\'s Search for Meaning)',
                    'author' => 'Viktor E. Frankl',
                    'publisher' => 'Beacon Press',
                    'description' => 'Trải nghiệm sinh tồn phi thường trong trại tập trung Auschwitz và sự ra đời của liệu pháp ý nghĩa: Con người có thể vượt qua mọi nghịch cảnh nếu tìm thấy lý do để sống.',
                ],
                [
                    'title' => 'Trí Tuệ Cảm Xúc (Emotional Intelligence)',
                    'author' => 'Daniel Goleman',
                    'publisher' => 'Bantam Books',
                    'description' => 'Chứng minh chỉ số EQ quan trọng hơn IQ trong việc quyết định hạnh phúc và thành công, hướng dẫn rèn luyện khả năng thấu cảm và làm chủ cảm xúc.',
                ],
                [
                    'title' => 'Nghịch Lý Của Sự Lựa Chọn (The Paradox of Choice)',
                    'author' => 'Barry Schwartz',
                    'publisher' => 'Ecco',
                    'description' => 'Tại sao càng có nhiều lựa chọn con người lại càng bớt hạnh phúc và dễ rơi vào hối tiếc? Tác phẩm chỉ ra lối thoát khỏi cái bẫy cầu toàn hiện đại.',
                ],
                [
                    'title' => 'Phi Lý Trí (Predictably Irrational)',
                    'author' => 'Dan Ariely',
                    'publisher' => 'HarperCollins',
                    'description' => 'Khám phá những sai lầm có hệ thống trong quyết định hàng ngày của chúng ta, từ việc chi tiêu tiền bạc đến thói quen trì hoãn và quan hệ xã hội.',
                ],
                [
                    'title' => 'Bản Chất Của Dối Trá (The Honest Truth About Dishonesty)',
                    'author' => 'Dan Ariely',
                    'publisher' => 'HarperCollins',
                    'description' => 'Giải mã động cơ tâm lý khiến hầu hết chúng ta đều gian lận một chút mà vẫn tự huyễn hoặc mình là những người trung thực, chính trực.',
                ],
                [
                    'title' => 'Đọc Vị Bất Kỳ Ai (You Can Read Anyone)',
                    'author' => 'David J. Lieberman',
                    'publisher' => 'St. Martin\'s Press',
                    'description' => 'Phương pháp phân tích ngôn ngữ cơ thể, ánh mắt và giọng điệu giúp bạn nắm bắt suy nghĩ ẩn giấu và ý định thực sự của đối phương trong vài phút.',
                ],
                [
                    'title' => 'Tâm Lý Học Tội Phạm (Inside the Criminal Mind)',
                    'author' => 'Stanton E. Samenow',
                    'publisher' => 'Crown',
                    'description' => 'Đột phá vào thế giới nội tâm u tối của những kẻ phạm tội, bóc trần những kiểu ngụy biện méo mó và cách thức hình thành hành vi tội ác.',
                ],
                [
                    'title' => 'Im Lặng: Sức Mạnh Của Người Hướng Nội (Quiet)',
                    'author' => 'Susan Cain',
                    'publisher' => 'Crown Publishing',
                    'description' => 'Tôn vinh giá trị sâu sắc của người hướng nội trong một thế giới không ngừng nói, khẳng định năng lực sáng tạo và lãnh đạo tĩnh lặng phi thường.',
                ],
                [
                    'title' => 'Nghệ Thuật Yêu (The Art of Loving)',
                    'author' => 'Erich Fromm',
                    'publisher' => 'Harper & Row',
                    'description' => 'Tình yêu không phải là một cảm xúc ngẫu nhiên may rủi mà là một nghệ thuật đòi hỏi sự hiểu biết, lòng kiên nhẫn, tinh thần trách nhiệm và sự tận tụy.',
                ],
                [
                    'title' => 'Trốn Chạy Tự Do (Escape from Freedom)',
                    'author' => 'Erich Fromm',
                    'publisher' => 'Farrar & Rinehart',
                    'description' => 'Phân tích tâm lý học xã hội giải thích tại sao con người hiện đại có xu hướng sợ hãi tự do và sẵn sàng quy phục các chế độ độc tài toàn trị.',
                ],
                [
                    'title' => 'Bản Đồ Tâm Hồn Của Jung (Jung\'s Map of the Soul)',
                    'author' => 'Murray Stein',
                    'publisher' => 'Open Court',
                    'description' => 'Hệ thống hóa toàn diện lý thuyết tâm lý học phân tích của Carl Jung: Bản ngã (Ego), Mặt nạ (Persona), Bóng tối (Shadow) và Tiềm thức tập thể.',
                ],
                [
                    'title' => 'Phân Tâm Học Nhập Môn (Introductory Lectures on Psycho-Analysis)',
                    'author' => 'Sigmund Freud',
                    'publisher' => 'W. W. Norton & Company',
                    'description' => 'Giáo trình kinh điển của Sigmund Freud về vô thức, cơ chế dồn nén, hành vi lỡ lời và vai trò của xung năng tính dục trong nhân cách con người.',
                ],
                [
                    'title' => 'Giải Mã Giấc Mơ (The Interpretation of Dreams)',
                    'author' => 'Sigmund Freud',
                    'publisher' => 'Basic Books',
                    'description' => 'Công trình đột phá coi giấc mơ là con đường vương đạo dẫn vào tiềm thức, nơi thỏa mãn những ước nguyện bị kìm nén của con người.',
                ],
                [
                    'title' => 'Dám Hạnh Phúc (The Courage to Be Happy)',
                    'author' => 'Kishimi Ichiro & Koga Fumitake',
                    'publisher' => 'NXB Lao Động',
                    'description' => 'Phần tiếp theo của "Dám bị ghét", đào sâu vào triết lý tâm lý học Adler về tình yêu, công việc và lòng dũng cảm sống tự do giữa cuộc đời.',
                ],
                [
                    'title' => 'Nghệ Thuật Sống Tự Tại',
                    'author' => 'Kishimi Ichiro',
                    'publisher' => 'NXB Kim Đồng',
                    'description' => 'Học cách chấp nhận sự bất toàn của bản thân, không sống để làm vừa lòng kỳ vọng của người khác và tìm thấy niềm vui trong từng khoảnh khắc hiện tại.',
                ],
                [
                    'title' => 'Tâm Lý Học Giao Tiếp Thực Chiến',
                    'author' => 'Lưu Gia',
                    'publisher' => 'NXB Phụ Nữ',
                    'description' => 'Ứng dụng các nguyên lý tâm lý học vào nghệ thuật ứng xử hàng ngày, giúp bạn làm chủ các tình huống đối thoại khó khăn và tạo thiện cảm bền vững.',
                ],
                [
                    'title' => 'Hiệu Ứng Chim Mồi',
                    'author' => 'Huy Đức & Ngọc Bích',
                    'publisher' => 'NXB Trẻ',
                    'description' => 'Bật mí những thủ thuật tâm lý định giá tinh vi của các doanh nghiệp bán lẻ khiến người tiêu dùng tự nguyện chi tiền nhiều hơn dự định ban đầu.',
                ],
                [
                    'title' => 'Tâm Lý Học Thành Công (Mindset)',
                    'author' => 'Carol S. Dweck',
                    'publisher' => 'Ballantine Books',
                    'description' => 'Phân biệt giữa tư duy cố định (Fixed Mindset) và tư duy phát triển (Growth Mindset), chìa khóa khai phóng tiềm năng vô hạn của trí tuệ con người.',
                ],
                [
                    'title' => 'Đánh Thức Con Người Phi Thường Trong Bạn',
                    'author' => 'Tony Robbins',
                    'publisher' => 'Free Press',
                    'description' => 'Hệ thống công cụ tâm lý học thần kinh (NLP) giúp bạn làm chủ cảm xúc, tài chính, mối quan hệ và tạo ra bước ngoặt phi thường cho cuộc đời.',
                ],
                [
                    'title' => 'Ai Lấy Miếng Pho Mát Của Tôi?',
                    'author' => 'Spencer Johnson',
                    'publisher' => 'Putnam',
                    'description' => 'Câu chuyện ngụ ngôn sâu sắc về 4 nhân vật trong mê cung, dạy chúng ta cách đón nhận sự thay đổi bất ngờ trong công việc và cuộc sống với tâm thế tích cực.',
                ],
                [
                    'title' => 'Đừng Bao Giờ Đi Ăn Một Mình',
                    'author' => 'Keith Ferrazzi',
                    'publisher' => 'Crown Business',
                    'description' => 'Triết lý xây dựng mạng lưới quan hệ dựa trên sự hào phóng và chân thành, chứng minh thành công bền vững luôn đến từ sự gắn kết giữa con người.',
                ],
                [
                    'title' => 'Sức Mạnh Của Thói Quen (The Power of Habit)',
                    'author' => 'Charles Duhigg',
                    'publisher' => 'Random House',
                    'description' => 'Giải mã vòng lặp thói quen (Gợi ý - Hành động - Phần thưởng) trong não bộ và phương pháp khoa học để tái lập trình các thói quen tích cực.',
                ],
                [
                    'title' => 'Động Lực 3.0 (Drive: The Surprising Truth About What Motivates Us)',
                    'author' => 'Daniel H. Pink',
                    'publisher' => 'Riverhead Books',
                    'description' => 'Bác bỏ mô hình cây gậy và củ cà rốt truyền thống, chỉ ra 3 động lực nội tại đích thực giúp con người cống hiến: Tự chủ, Tinh thông và Mục đích sống.',
                ],
                [
                    'title' => 'Bản Lĩnh Đổi Mới Tư Duy (Think Again)',
                    'author' => 'Adam Grant',
                    'publisher' => 'Viking',
                    'description' => 'Sức mạnh của việc dám nghi ngờ những gì mình đã biết và sẵn sàng từ bỏ các niềm tin lỗi thời để liên tục học hỏi và trưởng thành.',
                ],
                [
                    'title' => 'Cho Đi Và Nhận Lại (Give and Take)',
                    'author' => 'Adam Grant',
                    'publisher' => 'Viking',
                    'description' => 'Nghiên cứu chỉ ra những người có tâm thế cho đi hào phóng (Giver) luôn là những người đạt tới đỉnh cao thành công bền vững nhất trong xã hội.',
                ],
                [
                    'title' => 'Sức Mạnh Của Sự Tĩnh Lặng (Stillness Is the Key)',
                    'author' => 'Ryan Holiday',
                    'publisher' => 'Portfolio',
                    'description' => 'Bài học từ các bậc triết gia khắc kỷ và lãnh đạo vĩ đại về việc nuôi dưỡng sự tĩnh lặng nội tâm giữa một thế giới hỗn loạn đầy xao nhãng.',
                ],
                [
                    'title' => 'Khắc Kỷ: Từ Zeno Đến Marcus Aurelius',
                    'author' => 'Ryan Holiday & Stephen Hanselman',
                    'publisher' => 'Portfolio',
                    'description' => 'Tuyển tập 366 suy ngẫm triết học khắc kỷ mỗi ngày giúp rèn luyện lòng dũng cảm, sự tự chủ và khả năng bình thản trước mọi biến cố cuộc đời.',
                ],
                [
                    'title' => 'Chủ Nghĩa Khắc Kỷ Trong Đời Sống Hiện Đại',
                    'author' => 'Massimo Pigliucci',
                    'publisher' => 'Basic Books',
                    'description' => 'Ứng dụng triết lý của Epictetus vào đời sống hiện đại, dạy bạn phân biệt rõ ràng giữa những điều mình có thể kiểm soát và những điều không thể.',
                ],
                [
                    'title' => 'Trò Chuyện Với Bản Thân (Chatter)',
                    'author' => 'Ethan Kross',
                    'publisher' => 'Crown',
                    'description' => 'Khoa học thần kinh về tiếng nói nội tâm, cung cấp các công cụ kiểm soát những suy nghĩ tiêu cực và chuyển hóa chúng thành động lực hành động.',
                ],
                [
                    'title' => 'Tâm Lý Học Hành Vi (Behaviorism)',
                    'author' => 'John B. Watson',
                    'publisher' => 'W. W. Norton & Company',
                    'description' => 'Tác phẩm khai sinh trường phái hành vi trong tâm lý học, tập trung vào các phản xạ có điều kiện và cơ chế học tập thông qua môi trường sống.',
                ],
                [
                    'title' => 'Nỗi Đau Này Không Thuộc Về Bạn (It Didn\'t Start with You)',
                    'author' => 'Mark Wolynn',
                    'publisher' => 'Viking',
                    'description' => 'Khám phá chấn thương tâm lý di truyền qua các thế hệ gia đình và phương pháp trị liệu giải phóng những nỗi đau tiềm ẩn từ tổ tiên.',
                ],
                [
                    'title' => 'Chữa Lành Đứa Trẻ Bên Trong Bạn',
                    'author' => 'Charles L. Whitfield',
                    'publisher' => 'Health Communications',
                    'description' => 'Hướng dẫn nhận diện và hàn gắn những vết thương tuổi thơ chưa được thấu hiểu, giúp bạn tái kết nối với bản thể chân thật và tràn đầy sức sống.',
                ],
                [
                    'title' => 'Yêu Thương Đôi Khi Là Buông Bỏ',
                    'author' => 'Thích Nhất Hạnh',
                    'publisher' => 'NXB Hồng Đức',
                    'description' => 'Những bài học thiền định nhẹ nhàng mà sâu sắc về nghệ thuật buông xả, thấu hiểu và nuôi dưỡng tình yêu đích thực không ràng buộc, không chiếm hữu.',
                ],
                [
                    'title' => 'Giận: Nghệ Thuật Chuyển Hóa Cơn Giận',
                    'author' => 'Thích Nhất Hạnh',
                    'publisher' => 'NXB Tôn Giáo',
                    'description' => 'Phương thuốc chữa lành tâm hồn dạy chúng ta cách ôm ấp và chuyển hóa năng lượng sân giận thành lòng từ bi và sự hòa giải sâu sắc.',
                ],
                [
                    'title' => 'Phép Lạ Của Sự Tỉnh Thức',
                    'author' => 'Thích Nhất Hạnh',
                    'publisher' => 'NXB Tôn Giáo',
                    'description' => 'Cuốn sách gối đầu giường về chánh niệm, hướng dẫn thực hành thiền tập trong từng hơi thở, bước chân và công việc thường nhật của đời sống.',
                ],
                [
                    'title' => 'Năng Đoạn Kim Cương',
                    'author' => 'Geshe Michael Roach',
                    'publisher' => 'Doubleday',
                    'description' => 'Ứng dụng trí tuệ cổ xưa của Phật giáo Tây Tạng vào kinh doanh, gieo trồng các hạt giống nghiệp thiện lành để gặt hái tự do tài chính và an lạc.',
                ],
                [
                    'title' => 'Quản Lý Nghiệp',
                    'author' => 'Geshe Michael Roach, Lama Christie McNally, Michael Gordon',
                    'publisher' => 'NXB Tri Thức',
                    'description' => '8 quy luật quản lý nghiệp thực tiễn giúp bạn đạt được mọi mục tiêu dự án bằng cách trước hết giúp đỡ đối tác của mình thành công viên mãn.',
                ],
                [
                    'title' => 'Muôn Kiếp Nhân Sinh (Tập 1)',
                    'author' => 'Nguyên Phong',
                    'publisher' => 'NXB Tổng Hợp TP.HCM',
                    'description' => 'Bức tranh đại cảnh về luật nhân quả và luân hồi chuyển kiếp qua những trải nghiệm tiền kiếp kỳ bí tại nền văn minh Atlantis và Ai Cập cổ đại.',
                ],
                [
                    'title' => 'Hành Trình Về Phương Đông',
                    'author' => 'Nguyên Phong',
                    'publisher' => 'NXB Tổng Hợp TP.HCM',
                    'description' => 'Chuyến khảo cứu tâm linh của phái đoàn khoa học Hoàng gia Anh tại Ấn Độ, mở ra cánh cửa tiếp cận minh triết cổ xưa và sức mạnh huyền bí của Yoga.',
                ],
                [
                    'title' => 'Vượt Qua Nỗi Sợ Hãi (Feel the Fear and Do It Anyway)',
                    'author' => 'Susan Jeffers',
                    'publisher' => 'Ballantine Books',
                    'description' => 'Chiến lược tâm lý đột phá giúp bạn biến nỗi sợ hãi từ kẻ thù làm tê liệt hành động thành người bạn đồng hành tiếp thêm dũng khí bứt phá.',
                ],
                [
                    'title' => 'Bẫy Tâm Lý Trong Cuộc Sống (You Are Not So Smart)',
                    'author' => 'David McRaney',
                    'publisher' => 'Gotham Books',
                    'description' => 'Bóc trần 48 thiên kiến nhận thức và ngụy biện logic phổ biến khiến con người luôn tin rằng mình thông minh hơn thực tế vốn có.',
                ],
                [
                    'title' => 'Trầm Cảm: Căn Bệnh Thế Kỷ',
                    'author' => 'Stephen S. Ilardi',
                    'publisher' => 'Da Capo Lifelong Books',
                    'description' => 'Chương trình phục hồi 6 bước dựa trên lối sống lành mạnh tự nhiên giúp điều trị trầm cảm hiệu quả mà không phụ thuộc hoàn toàn vào thuốc.',
                ],
                [
                    'title' => 'Thuyết Hiện Sinh Là Một Thuyết Nhân Bản',
                    'author' => 'Jean-Paul Sartre',
                    'publisher' => 'Gallimard',
                    'description' => 'Tuyên ngôn triết học hiện sinh khẳng định sự tồn tại có trước bản chất, con người hoàn toàn tự do và phải chịu trách nhiệm tuyệt đối về cuộc đời mình.',
                ],
                [
                    'title' => 'Nghệ Thuật Sống (The Art of Living)',
                    'author' => 'Epictetus (Sharon Lebell dịch)',
                    'publisher' => 'HarperOne',
                    'description' => 'Cẩm nang triết học đạo đức kinh điển hướng dẫn cách giữ tâm thế an nhiên, bình tĩnh và đĩnh đạc trước mọi thăng trầm của số phận.',
                ],
            ],

            // ==========================================
            // VII. KỸ NĂNG SỐNG (50 CUỐN KINH ĐIỂN)
            // ==========================================
            'Kỹ năng sống' => [
                [
                    'title' => 'Đắc Nhân Tâm (How to Win Friends and Influence People)',
                    'author' => 'Dale Carnegie',
                    'publisher' => 'Simon & Schuster',
                    'description' => 'Cuốn sách nghệ thuật ứng xử kinh điển nhất mọi thời đại, hướng dẫn chân thành cách thu phục lòng người, tạo thiện cảm và khơi gợi sự hợp tác.',
                ],
                [
                    'title' => 'Quẳng Gánh Lo Đi Và Vui Sống',
                    'author' => 'Dale Carnegie',
                    'publisher' => 'Simon & Schuster',
                    'description' => 'Phương thuốc chữa trị căn bệnh lo âu kinh niên, chia sẻ các nguyên tắc thực tế để đóng chặt cánh cửa quá khứ và sống trọn vẹn trong hiện tại.',
                ],
                [
                    'title' => '7 Thói Quen Của Người Thành Đạt (The 7 Habits of Highly Effective People)',
                    'author' => 'Stephen R. Covey',
                    'publisher' => 'Free Press',
                    'description' => 'Khung rèn luyện nhân cách và năng lực hành động vượt thời gian từ tư duy làm chủ bản thân đến nghệ thuật hợp tác đôi bên cùng có lợi (Win-Win).',
                ],
                [
                    'title' => 'Thói Quen Thứ 8: Từ Hiệu Quả Đến Vĩ Đại',
                    'author' => 'Stephen R. Covey',
                    'publisher' => 'Free Press',
                    'description' => 'Nâng tầm từ người làm việc hiệu quả thành nhà lãnh đạo truyền cảm hứng, giúp người khác tìm thấy tiếng nói đích thực và phát huy tiềm năng.',
                ],
                [
                    'title' => 'Khéo Ăn Nói Sẽ Có Được Thiên Hạ',
                    'author' => 'Trác Nhã',
                    'publisher' => 'NXB Văn Học',
                    'description' => 'Nghệ thuật giao tiếp thực chiến trong mọi hoàn cảnh: từ phỏng vấn xin việc, trò chuyện nơi công sở đến đàm phán thương vụ và gìn giữ hạnh phúc gia đình.',
                ],
                [
                    'title' => 'Dám Bị Ghét (The Courage to Be Disliked)',
                    'author' => 'Kishimi Ichiro & Koga Fumitake',
                    'publisher' => 'NXB Lao Động',
                    'description' => 'Đối thoại triết học sâu sắc theo tư tưởng Alfred Adler, giúp bạn dũng cảm rũ bỏ gánh nặng kỳ vọng xã hội và tự tin sống cuộc đời tự do đích thực.',
                ],
                [
                    'title' => 'Sức Mạnh Của Hiện Tại (The Power of Now)',
                    'author' => 'Eckhart Tolle',
                    'publisher' => 'New World Library',
                    'description' => 'Kim chỉ nam khai sáng tâm linh dạy bạn thoát khỏi sự giam cầm của quá khứ hối tiếc và tương lai lo âu để sống trọn vẹn trong giây phút bây giờ.',
                ],
                [
                    'title' => 'Một Lối Sống Mới (A New Earth)',
                    'author' => 'Eckhart Tolle',
                    'publisher' => 'Dutton',
                    'description' => 'Vượt qua sự thống trị của bản ngã giả tạo (Ego) để đánh thức nhận thức tâm linh cao hơn, kiến tạo một xã hội nhân văn và hòa bình bền vững.',
                ],
                [
                    'title' => 'Nghệ Thuật Tinh Tế Của Việc Đếch Quan Tâm',
                    'author' => 'Mark Manson',
                    'publisher' => 'HarperOne',
                    'description' => 'Góc nhìn thực tế trần trụi về cuộc sống, khuyến khích bạn chọn lọc những giá trị thực sự đáng để bận tâm và dũng cảm đón nhận khó khăn.',
                ],
                [
                    'title' => 'Mọi Thứ Đều Đang Vụn Vỡ (Everything Is F*cked)',
                    'author' => 'Mark Manson',
                    'publisher' => 'HarperOne',
                    'description' => 'Khảo sát sâu sắc về niềm hy vọng, công nghệ và đức tin trong kỷ nguyên hiện đại, thôi thúc con người xây dựng lẽ sống vững chãi từ bên trong.',
                ],
                [
                    'title' => 'Làm Chủ Cảm Xúc (Master Your Emotions)',
                    'author' => 'Thibaut Meurisse',
                    'publisher' => 'Independently Published',
                    'description' => 'Cẩm nang hành động thực tiễn giúp bạn hiểu rõ cơ chế vận hành của cảm xúc tiêu cực và từng bước lập trình lại tâm trí tích cực mỗi ngày.',
                ],
                [
                    'title' => 'Đời Thay Đổi Khi Chúng Ta Thay Đổi',
                    'author' => 'Andrew Matthews',
                    'publisher' => 'Seashell Publishers',
                    'description' => 'Tác phẩm hài hước với những nét vẽ ngộ nghĩnh truyền tải thông điệp sống lạc quan: Thái độ của bạn quyết định chất lượng cuộc đời bạn.',
                ],
                [
                    'title' => 'Càng Kỷ Luật Càng Tự Do',
                    'author' => 'Vãn Tình',
                    'publisher' => 'NXB Thế Giới',
                    'description' => 'Những bài học đắt giá về tính tự giác, quản trị thời gian và bản lĩnh tự chủ của người phụ nữ hiện đại để làm chủ vận mệnh chính mình.',
                ],
                [
                    'title' => 'Bạn Đắt Giá Bao Nhiêu?',
                    'author' => 'Vãn Tình',
                    'publisher' => 'NXB Văn Học',
                    'description' => 'Đánh thức giá trị tự tôn và độc lập của phái nữ, khuyến khích các bạn trẻ không ngừng hoàn thiện tri thức, nhân cách và bản lĩnh sống.',
                ],
                [
                    'title' => 'Khí Chất Tạo Nên Sự Quyến Rũ',
                    'author' => 'Vãn Tình',
                    'publisher' => 'NXB Thế Giới',
                    'description' => 'Bí quyết rèn luyện phong thái đĩnh đạc, tự tin và nội tâm sâu sắc, vẻ đẹp trường tồn theo năm tháng vượt lên trên nhan sắc bên ngoài.',
                ],
                [
                    'title' => 'Tuổi Trẻ Đáng Giá Bao Nhiêu?',
                    'author' => 'Rosie Nguyễn',
                    'publisher' => 'NXB Nhã Nam',
                    'description' => 'Cuốn sách truyền cảm hứng sống đẹp cho hàng triệu bạn trẻ Việt Nam về thói quen tự học, niềm đam mê đọc sách và dũng cảm dấn thân trải nghiệm.',
                ],
                [
                    'title' => 'Nghệ Thuật Tư Duy Rành Mạch (The Art of Thinking Clearly)',
                    'author' => 'Rolf Dobelli',
                    'publisher' => 'Harper',
                    'description' => 'Nhận diện 99 cạm bẫy tư duy và sai lầm logic phổ biến giúp bạn đưa ra những quyết định sáng suốt hơn trong công việc và cuộc sống.',
                ],
                [
                    'title' => 'Lối Sống Tối Giản Của Người Nhật',
                    'author' => 'Sasaki Fumio',
                    'publisher' => 'NXB Lao Động',
                    'description' => 'Trút bỏ gánh nặng vật chất dư thừa để tìm lại sự thảnh thơi tâm hồn, tập trung năng lượng vào các trải nghiệm và mối quan hệ ý nghĩa.',
                ],
                [
                    'title' => 'Nghệ Thuật Sống Gọn Gàng Của Người Nhật',
                    'author' => 'Marie Kondo',
                    'publisher' => 'Ten Speed Press',
                    'description' => 'Phương pháp dọn dẹp KonMari thần kỳ: chỉ giữ lại những đồ vật mang lại niềm vui (Spark Joy), biến ngôi nhà thành không gian bình yên.',
                ],
                [
                    'title' => 'Nghệ Thuật Giao Tiếp Để Thành Công (How to Talk to Anyone)',
                    'author' => 'Leil Lowndes',
                    'publisher' => 'McGraw-Hill',
                    'description' => '92 thủ thuật giao tiếp tinh tế giúp bạn tự tin bắt chuyện, gây ấn tượng khó phai và xây dựng mối quan hệ tin cậy với bất kỳ ai.',
                ],
                [
                    'title' => 'Kỹ Năng Giải Quyết Vấn Đề (Problem Solving 101)',
                    'author' => 'Ken Watanabe',
                    'publisher' => 'Portfolio',
                    'description' => 'Phương pháp tư duy logic và cây vấn đề từ cựu chuyên gia tư vấn McKinsey, biến các bài toán phức tạp thành các bước hành động đơn giản.',
                ],
                [
                    'title' => '6 Chiếc Nón Tư Duy (Six Thinking Hats)',
                    'author' => 'Edward de Bono',
                    'publisher' => 'Little, Brown and Company',
                    'description' => 'Mô hình phân tách tư duy đa chiều (dữ liệu, cảm xúc, rủi ro, lạc quan, sáng tạo, điều phối) giúp các cuộc họp và quyết định đạt hiệu quả tối đa.',
                ],
                [
                    'title' => 'Thiết Kế Cuộc Đời Đáng Sống (Designing Your Life)',
                    'author' => 'Bill Burnett & Dave Evans',
                    'publisher' => 'Knopf',
                    'description' => 'Ứng dụng tư duy thiết kế (Design Thinking) của Đại học Stanford để kiến tạo sự nghiệp viên mãn và một cuộc đời tràn đầy ý nghĩa.',
                ],
                [
                    'title' => 'Kỷ Luật Tự Giác (No Excuses!: The Power of Self-Discipline)',
                    'author' => 'Brian Tracy',
                    'publisher' => 'Vanguard Press',
                    'description' => '21 phẩm chất rèn luyện kỷ luật thép bản thân trong mọi khía cạnh sự nghiệp, tài chính, sức khỏe và mối quan hệ cá nhân.',
                ],
                [
                    'title' => 'Bắt Đầu Ngày Mới Với 1 Con Ếch (Eat That Frog!)',
                    'author' => 'Brian Tracy',
                    'publisher' => 'Berrett-Koehler Publishers',
                    'description' => '21 nguyên tắc đánh bại thói quen trì hoãn, tập trung giải quyết dứt điểm nhiệm vụ quan trọng và khó khăn nhất ngay đầu ngày làm việc.',
                ],
                [
                    'title' => 'Bí Quyết Đàm Phán Thành Công (Getting to Yes)',
                    'author' => 'Roger Fisher & William Ury',
                    'publisher' => 'Penguin Books',
                    'description' => 'Phương pháp đàm phán dựa trên nguyên tắc của Dự án Đàm phán Harvard, hướng tới giải pháp thỏa đáng cho đôi bên mà không làm rạn nứt quan hệ.',
                ],
                [
                    'title' => 'Tư Duy Phản Biện (Critical Thinking)',
                    'author' => 'Zoe McKey',
                    'publisher' => 'Independently Published',
                    'description' => 'Rèn luyện khả năng phân tích dữ liệu độc lập, bóc trần thông tin sai lệch và đưa ra các lập luận chặt chẽ trong kỷ nguyên bùng nổ thông tin.',
                ],
                [
                    'title' => 'Nghệ Thuật Tập Trung (The Power of Focus)',
                    'author' => 'Jack Canfield, Mark Victor Hansen, Les Hewitt',
                    'publisher' => 'Health Communications',
                    'description' => 'Chiến lược loại bỏ các yếu tố gây xao nhãng, rèn luyện sự tập trung cao độ vào những mục tiêu mang lại kết quả đột phá nhất.',
                ],
                [
                    'title' => 'Nghệ Thuật Lắng Nghe Trong Giao Tiếp',
                    'author' => 'Michael P. Nichols',
                    'publisher' => 'Guilford Press',
                    'description' => 'Khám phá sức mạnh hàn gắn kỳ diệu của việc thực sự lắng nghe mà không phán xét, xây dựng sự gắn kết cảm xúc sâu sắc giữa người với người.',
                ],
                [
                    'title' => 'Làm Chủ Giọng Nói Trong Đầu',
                    'author' => 'Blair Singer',
                    'publisher' => 'BPS Books',
                    'description' => 'Kiểm soát những cuộc độc thoại nội tâm tiêu cực, dập tắt nỗi sợ hãi thất bại và đánh thức sự tự tin của một nhà vô địch bên trong bạn.',
                ],
                [
                    'title' => 'Quản Trị Căng Thẳng Hiệu Quả',
                    'author' => 'David Posen',
                    'publisher' => 'Key Porter Books',
                    'description' => 'Phương pháp y học và tâm lý thực tế giúp bạn nhận diện các dấu hiệu kiệt sức (Burnout), tái tạo năng lượng và cân bằng cuộc sống.',
                ],
                [
                    'title' => 'Thuật Xử Thế Của Người Xưa',
                    'author' => 'Nguyễn Hiến Lê',
                    'publisher' => 'NXB Văn Hóa Thông Tin',
                    'description' => 'Chắt lọc tinh hoa xử thế phương Đông từ Nho giáo, Lão giáo và Trang Tử, bồi dưỡng nhân cách khiêm nhường, đĩnh đạc và đức độ.',
                ],
                [
                    'title' => 'Dám Nghĩ Lớn (The Magic of Thinking Big)',
                    'author' => 'David J. Schwartz',
                    'publisher' => 'Simon & Schuster',
                    'description' => 'Mở rộng tầm nhìn và niềm tin vào bản thân, chứng minh quy mô thành công của bạn được quyết định bởi quy mô suy nghĩ của chính bạn.',
                ],
                [
                    'title' => 'Khát Vọng Đổi Đời (Live Your Dreams)',
                    'author' => 'Les Brown',
                    'publisher' => 'Avon Books',
                    'description' => 'Câu chuyện truyền cảm hứng bất tận về nghị lực vượt khó, thôi thúc bạn kiên định theo đuổi ước mơ bất chấp mọi nghịch cảnh số phận.',
                ],
                [
                    'title' => 'Nói Sao Để Trẻ Chịu Nghe, Nghe Sao Để Trẻ Chịu Nói',
                    'author' => 'Adele Faber & Elaine Mazlish',
                    'publisher' => 'Scribner',
                    'description' => 'Cẩm nang nuôi dạy con chuẩn mực thế giới, hướng dẫn cha mẹ cách thấu cảm cảm xúc của trẻ và xây dựng sự hợp tác gia đình ấm áp.',
                ],
                [
                    'title' => 'Nghệ Thuật Sống Đẹp',
                    'author' => 'Lâm Ngữ Đường',
                    'publisher' => 'Reynal & Hitchcock',
                    'description' => 'Triết lý thưởng thức cuộc sống thi vị phương Đông: trân trọng tách trà thơm, ngắm hoa nở, yêu thiên nhiên và giữ tâm hồn an nhiên tự tại.',
                ],
                [
                    'title' => 'Để Không Bị Thao Túng',
                    'author' => 'Christel Petitcollin',
                    'publisher' => 'NXB Hà Nội',
                    'description' => 'Nhận diện các kiểu người thao túng tâm lý trong công sở và gia đình, thiết lập ranh giới cá nhân vững chắc để bảo vệ sự bình an của bản thân.',
                ],
                [
                    'title' => 'Mình Là Nắng Việc Của Mình Là Chói Chang',
                    'author' => 'Kazuko Watanabe',
                    'publisher' => 'NXB Thế Giới',
                    'description' => 'Những tản văn nhẹ nhàng giàu lòng trắc ẩn dạy bạn cách chấp nhận những giới hạn của hoàn cảnh và kiên cường nở hoa ngay nơi bạn được gieo xuống.',
                ],
                [
                    'title' => 'Không Tự Khinh Bỉ Không Phí Hoài Năm Tháng',
                    'author' => 'Vãn Tình',
                    'publisher' => 'NXB Thế Giới',
                    'description' => 'Tiếp thêm dũng khí cho các cô gái trẻ vượt qua định kiến xã hội, tự tay xây dựng sự nghiệp độc lập và cuộc sống hạnh phúc viên mãn.',
                ],
                [
                    'title' => 'Thuật Hùng Biện (Rhetoric)',
                    'author' => 'Aristotle',
                    'publisher' => 'Dover Publications',
                    'description' => 'Tác phẩm đặt nền móng cho nghệ thuật hùng biện phương Tây qua tam giác thuyết phục bất hủ: Đạo đức (Ethos), Cảm xúc (Pathos) và Lý lẽ (Logos).',
                ],
                [
                    'title' => 'Thao Túng Tâm Lý Nơi Công Sở',
                    'author' => 'Amy Gallo',
                    'publisher' => 'Harvard Business Review Press',
                    'description' => 'Khung xử lý thực tế 8 kiểu đồng nghiệp khó tính nơi làm việc, giữ vững sự chuyên nghiệp và bảo vệ sức khỏe tâm thần của bạn.',
                ],
                [
                    'title' => 'Xây Dựng Đội Nhóm Vô Địch',
                    'author' => 'Blair Singer',
                    'publisher' => 'BPS Books',
                    'description' => 'Quy tắc danh dự của tổ chức và phương pháp gắn kết nhân viên thành một khối thống nhất cùng hướng tới chiến thắng vang dội.',
                ],
                [
                    'title' => 'Vua Bán Hàng (SalesDogs)',
                    'author' => 'Blair Singer',
                    'publisher' => 'BPS Books',
                    'description' => 'Nhận diện phong cách bán hàng bẩm sinh của bản thân và phát huy thế mạnh cá nhân để trở thành siêu sao bán hàng xuất sắc.',
                ],
                [
                    'title' => 'Nghệ Thuật Xử Thế Của Cổ Nhân',
                    'author' => 'Lâm Ngữ Đường',
                    'publisher' => 'NXB Văn Hóa',
                    'description' => 'Học hỏi trí tuệ nhân sinh thâm trầm từ các bậc hiền triết phương Đông để ứng xử nhu hòa, cương nhu đúng lúc trong thời thế tao loạn.',
                ],
                [
                    'title' => 'Tối Đa Hóa Năng Lực Bản Thân',
                    'author' => 'Brian Tracy',
                    'publisher' => 'Amacom',
                    'description' => 'Phương pháp giải phóng tiềm năng não bộ chưa khai phá, gia tăng gấp đôi hiệu suất công việc và đẩy nhanh tốc độ chạm tới thành công.',
                ],
                [
                    'title' => 'Đặt Mục Tiêu Và Hoàn Thành Mục Tiêu',
                    'author' => 'Brian Tracy',
                    'publisher' => 'Berrett-Koehler Publishers',
                    'description' => 'Quy trình 12 bước thiết lập mục tiêu khoa học, chuyển hóa những mơ ước trừu tượng thành kế hoạch hành động cụ thể từng ngày.',
                ],
                [
                    'title' => 'Để Hiệu Quả Trong Công Việc (Time Management)',
                    'author' => 'Brian Tracy',
                    'publisher' => 'Amacom',
                    'description' => '21 kỹ thuật quản lý thời gian đắt giá giúp bạn làm chủ lịch trình, ưu tiên công việc tạo giá trị cao và tận hưởng cuộc sống.',
                ],
                [
                    'title' => 'Hãy Sống Cuộc Đời Như Bạn Muốn',
                    'author' => 'Vãn Tình',
                    'publisher' => 'NXB Thế Giới',
                    'description' => 'Lời nhắn nhủ tha thiết về lòng dũng cảm sống thật với chính mình, không thỏa hiệp với số phận và kiên trì theo đuổi lý tưởng cá nhân.',
                ],
                [
                    'title' => 'Nghệ Thuật Tập Trung (The Art of Concentration)',
                    'author' => 'Dandapani',
                    'publisher' => 'Portfolio',
                    'description' => 'Phương pháp rèn luyện tâm trí không xao nhãng của tu sĩ Hindu, biến khả năng chú tâm thành một sức mạnh ý chí kiên định.',
                ],
                [
                    'title' => 'Tư Duy Đa Chiều (Lateral Thinking)',
                    'author' => 'Edward de Bono',
                    'publisher' => 'Harper Colophon',
                    'description' => 'Phá vỡ lối mòn tư duy tuyến tính, khuyến khích tìm kiếm các góc nhìn đột phá và giải pháp sáng tạo cho những thách thức bế tắc.',
                ],
            ],

            // ==========================================
            // VIII. VĂN HỌC (50 CUỐN KINH ĐIỂN)
            // ==========================================
            'Văn học' => [
                [
                    'title' => 'Nhà Giả Kim (The Alchemist)',
                    'author' => 'Paulo Coelho',
                    'publisher' => 'HarperOne',
                    'description' => 'Hành trình vượt sa mạc đi tìm kho báu của chàng chăn cừu Santiago, khúc ca ngợi ca lòng dũng cảm theo đuổi "Vận mệnh Cá nhân" và lắng nghe tiếng nói trái tim.',
                ],
                [
                    'title' => 'Hoàng Tử Bé (The Little Prince)',
                    'author' => 'Antoine de Saint-Exupéry',
                    'publisher' => 'Reynal & Hitchcock',
                    'description' => 'Kiệt tác văn học dịu dàng nhắc nhở chúng ta: "Người ta chỉ nhìn thấy thật rõ ràng bằng trái tim, điều cốt yếu thì mắt thường không thể thấy."',
                ],
                [
                    'title' => 'Ông Già Và Biển Cả (The Old Man and the Sea)',
                    'author' => 'Ernest Hemingway',
                    'publisher' => 'Charles Scribner\'s Sons',
                    'description' => 'Biểu tượng bất diệt về ý chí kiên cường của con người trước thiên nhiên dữ dội qua cuộc chiến đơn độc của lão đánh cá Santiago với con cá kiếm khổng lồ.',
                ],
                [
                    'title' => 'Trăm Năm Cô Đơn (One Hundred Years of Solitude)',
                    'author' => 'Gabriel García Márquez',
                    'publisher' => 'Editorial Sudamericana',
                    'description' => 'Đỉnh cao chủ nghĩa hiện thực huyền ảo Mỹ Latinh về biên niên sử bảy thế hệ dòng họ Buendía tại ngôi làng Macondo, thiên anh hùng ca bi tráng về nỗi cô đơn nhân loại.',
                ],
                [
                    'title' => 'Rừng Na Uy (Norwegian Wood)',
                    'author' => 'Haruki Murakami',
                    'publisher' => 'Kodansha',
                    'description' => 'Bản tình ca u buồn và ám ảnh về tuổi trẻ, nỗi cô đơn, mất mát và tình yêu day dứt của thế hệ thanh niên Nhật Bản thập niên 1960.',
                ],
                [
                    'title' => 'Biên Niên Sử Chim Vặn Dây Cót',
                    'author' => 'Haruki Murakami',
                    'publisher' => 'Shinchosha',
                    'description' => 'Hành trình kỳ ảo đan xen giữa thực tại và cõi mộng của Toru Okada đi tìm chú mèo thất lạc và người vợ mất tích, khám phá vết thương lịch sử chiến tranh chìm sâu.',
                ],
                [
                    'title' => 'Kafka Bên Bờ Biển',
                    'author' => 'Haruki Murakami',
                    'publisher' => 'Shinchosha',
                    'description' => 'Hai mạch truyện song hành huyền bí giữa cậu bé 15 tuổi chạy trốn lời nguyền Oedipus và ông lão Nakata biết nói chuyện với mèo, mở ra thế giới siêu thực đầy mê hoặc.',
                ],
                [
                    'title' => 'Chiến Tranh Và Hòa Bình (War and Peace)',
                    'author' => 'Leo Tolstoy',
                    'publisher' => 'The Russian Messenger',
                    'description' => 'Thiên sử thi đồ sộ bậc nhất văn học nhân loại tái hiện xã hội Nga thời kỳ chiến tranh Napoleon, bức tranh toàn cảnh tráng lệ về số phận con người và lịch sử.',
                ],
                [
                    'title' => 'Anna Karenina',
                    'author' => 'Leo Tolstoy',
                    'publisher' => 'The Russian Messenger',
                    'description' => 'Bi kịch tình yêu say đắm và tuyệt vọng của người phụ nữ quý tộc Anna Karenina thách thức các định kiến đạo đức hà khắc của xã hội thượng lưu Nga thế kỷ 19.',
                ],
                [
                    'title' => 'Tội Ác Và Hình Phạt (Crime and Punishment)',
                    'author' => 'Fyodor Dostoevsky',
                    'publisher' => 'The Russian Messenger',
                    'description' => 'Cuộc đấu tranh lương tâm xâu xé dữ dội của chàng sinh viên nghèo Raskolnikov sau khi giết mụ cầm đồ, hành trình sám hối và sự cứu rỗi linh hồn qua tình yêu thương.',
                ],
                [
                    'title' => 'Anh Em Nhà Karamazov (The Brothers Karamazov)',
                    'author' => 'Fyodor Dostoevsky',
                    'publisher' => 'The Russian Messenger',
                    'description' => 'Tuyệt tác triết học đỉnh cao của Dostoevsky bàn về đức tin, sự hoài nghi, tự do ý chí và đạo đức qua bi kịch gia đình Karamazov.',
                ],
                [
                    'title' => 'Những Người Khốn Khổ (Les Misérables)',
                    'author' => 'Victor Hugo',
                    'publisher' => 'A. Lacroix, Verboeckhoven & Cie',
                    'description' => 'Bản anh hùng ca bất hủ về lòng nhân ái qua cuộc đời lưu đày và tái sinh của Jean Valjean, bức tranh hiện thực tráng lệ về sự nghèo đói và công lý xã hội.',
                ],
                [
                    'title' => 'Nhà Thờ Đức Bà Paris (The Hunchback of Notre-Dame)',
                    'author' => 'Victor Hugo',
                    'publisher' => 'Gosselin',
                    'description' => 'Câu chuyện tình yêu cao thượng đầy bi kịch của chàng gù dị dạng Quasimodo dành cho nàng vũ nữ Esmeralda dưới bóng ngôi thánh đường Gothic cổ kính.',
                ],
                [
                    'title' => 'Đồi Gió Hú (Wuthering Heights)',
                    'author' => 'Emily Brontë',
                    'publisher' => 'Thomas Cautley Newby',
                    'description' => 'Bản tình ca hoang dại, dữ dội và đầy hận thù giữa Heathcliff và Catherine trên cánh đồng hoang Yorkshire lộng gió, tác phẩm Gothic kinh điển vượt thời đại.',
                ],
                [
                    'title' => 'Jane Eyre',
                    'author' => 'Charlotte Brontë',
                    'publisher' => 'Smith, Elder & Co.',
                    'description' => 'Hành trình vượt lên nghịch cảnh của cô gái mồ côi Jane Eyre kiên định giữ vững phẩm giá, tự do và tình yêu chân thành bên cạnh ngài Rochester.',
                ],
                [
                    'title' => 'Kiêu Hãnh Và Định Kiến (Pride and Prejudice)',
                    'author' => 'Jane Austen',
                    'publisher' => 'T. Egerton',
                    'description' => 'Tác phẩm lãng mạn kinh điển châm biếm sâu cay những định kiến hôn nhân và giai cấp qua mối tình duyên trắc trở giữa Elizabeth Bennet và quý tộc Darcy.',
                ],
                [
                    'title' => 'Bá Tước Monte Cristo (The Count of Monte Cristo)',
                    'author' => 'Alexandre Dumas',
                    'publisher' => 'Pétion',
                    'description' => 'Cuộc báo thù ngoạn mục của chàng thủy thủ Edmond Dantès sau 14 năm bị giam oan trong ngục tối lâu đài If, bài học thấm thía về sự công bằng và lòng khoan dung.',
                ],
                [
                    'title' => 'Ba Người Lính Ngự Lâm (The Three Musketeers)',
                    'author' => 'Alexandre Dumas',
                    'publisher' => 'Baudry',
                    'description' => 'Những chuyến phiêu lưu hào hùng, đầy ắp nghĩa khí hiệp sĩ của chàng thanh niên d\'Artagnan cùng ba ngự lâm quân Athos, Porthos và Aramis: "Một người vì mọi người, mọi người vì một người!"',
                ],
                [
                    'title' => 'Không Gia Đình (Sans Famille)',
                    'author' => 'Hector Malot',
                    'publisher' => 'Édouard Dentu',
                    'description' => 'Hành trình lưu lạc đầy nghị lực và tình người của cậu bé mồ côi Rémi cùng đoàn xiếc rong cụ Vitalis khắp nẻo đường nước Pháp, biểu tượng của lòng dũng cảm và nhân ái.',
                ],
                [
                    'title' => 'Trong Gia Đình (En Famille)',
                    'author' => 'Hector Malot',
                    'publisher' => 'Flammarion',
                    'description' => 'Câu chuyện cảm động về cô bé Perrine mồ côi kiên cường tự lập, dùng sự chăm chỉ và trái tim trong sáng để cảm hóa người ông nghiêm khắc giàu có.',
                ],
                [
                    'title' => 'Tiếng Chim Hót Trong Bụi Mận Gai (The Thorn Birds)',
                    'author' => 'Colleen McCullough',
                    'publisher' => 'Harper & Row',
                    'description' => 'Bản tình ca bi tráng trải dài ba thế hệ tại vùng đồng cỏ hoang vu nước Úc giữa cô tiểu thư Meggie và cha xứ Ralph, biểu tượng về sự trả giá cho hạnh phúc tột cùng.',
                ],
                [
                    'title' => 'Cuốn Theo Chiều Gió (Gone with the Wind)',
                    'author' => 'Margaret Mitchell',
                    'publisher' => 'Macmillan Publishers',
                    'description' => 'Sức sống kiên cường bất khuất của nàng Scarlett O\'Hara giữa bão táp cuộc nội chiến Nam - Bắc Mỹ và mối tình đầy đam mê trắc trở với Rhett Butler.',
                ],
                [
                    'title' => 'Hai Số Phận (Kane and Abel)',
                    'author' => 'Jeffrey Archer',
                    'publisher' => 'Hodder & Stoughton',
                    'description' => 'Cuộc đối đầu kịch tính suốt nửa thế kỷ giữa hai người đàn ông sinh cùng ngày: một thiếu gia ngân hàng Mỹ giàu có và một người tị nạn Ba Lan trắng tay lập nghiệp.',
                ],
                [
                    'title' => 'Bố Già (The Godfather)',
                    'author' => 'Mario Puzo',
                    'publisher' => 'G. P. Putnam\'s Sons',
                    'description' => 'Kiệt tác bất hủ về thế giới ngầm mafia Mỹ qua đế chế gia tộc Don Vito Corleone, bản khảo sát sâu sắc về quyền lực, danh dự và lòng trung thành gia đình.',
                ],
                [
                    'title' => 'Gatsby Vĩ Đại (The Great Gatsby)',
                    'author' => 'F. Scott Fitzgerald',
                    'publisher' => 'Charles Scribner\'s Sons',
                    'description' => 'Bức tranh rực rỡ nhưng ảo mộng về "Giấc mơ Mỹ" thời kỳ nhạc Jazz qua tình yêu tuyệt vọng của triệu phú bí ẩn Jay Gatsby dành cho nàng Daisy Buchanan.',
                ],
                [
                    'title' => 'Giết Con Chim Nhại (To Kill a Mockingbird)',
                    'author' => 'Harper Lee',
                    'publisher' => 'J. B. Lippincott & Co.',
                    'description' => 'Bài học đạo đức và lòng can đảm chống lại nạn phân biệt chủng tộc qua đôi mắt trẻ thơ của cô bé Scout và người cha luật sư Atticus Finch chính trực.',
                ],
                [
                    'title' => '1984',
                    'author' => 'George Orwell',
                    'publisher' => 'Secker & Warburg',
                    'description' => 'Tiểu thuyết phản địa đàng (Dystopia) cảnh báo rùng mình về một xã hội bị kiểm soát toàn trị tuyệt đối bởi "Đại Huynh" (Big Brother) và cảnh sát tư tưởng.',
                ],
                [
                    'title' => 'Trại Súc Vật (Animal Farm)',
                    'author' => 'George Orwell',
                    'publisher' => 'Secker & Warburg',
                    'description' => 'Truyện ngụ ngôn châm biếm sâu cay về cuộc nổi dậy của loài vật chống lại loài người, vạch trần sự tha hóa của quyền lực tuyệt đối qua tuyên ngôn nổi tiếng.',
                ],
                [
                    'title' => 'Suối Nguồn (The Fountainhead)',
                    'author' => 'Ayn Rand',
                    'publisher' => 'Bobbs-Merrill Company',
                    'description' => 'Bản tuyên ngôn triết học khách quan tôn vinh chủ nghĩa cá nhân sáng tạo và lòng kiên định không thỏa hiệp của kiến trúc sư tài ba Howard Roark.',
                ],
                [
                    'title' => 'Bắt Trẻ Đồng Xanh (The Catcher in the Rye)',
                    'author' => 'J. D. Salinger',
                    'publisher' => 'Little, Brown and Company',
                    'description' => 'Tiếng nói nổi loạn và cô đơn của cậu thiếu niên Holden Caulfield lạc lõng giữa thế giới người lớn giả tạo, khao khát bảo vệ sự thuần khiết tuổi thơ.',
                ],
                [
                    'title' => 'Chuông Nguyện Hồn Ai (For Whom the Bell Tolls)',
                    'author' => 'Ernest Hemingway',
                    'publisher' => 'Charles Scribner\'s Sons',
                    'description' => 'Khúc ca bi tráng về cuộc nội chiến Tây Ban Nha qua ba ngày đêm chiến đấu sinh tử của chiến sĩ tình nguyện Robert Jordan vì tự do và lý tưởng cao cả.',
                ],
                [
                    'title' => 'Số Đỏ',
                    'author' => 'Vũ Trọng Phụng',
                    'publisher' => 'NXB Hà Nội',
                    'description' => 'Kiệt tác văn học hiện thực trào phúng đỉnh cao của Việt Nam, vạch trần xã hội thực dân nửa phong kiến nhố nhăng qua hành trình đổi đời ngoạn mục của Xuân Tóc Đỏ.',
                ],
                [
                    'title' => 'Giông Tố',
                    'author' => 'Vũ Trọng Phụng',
                    'publisher' => 'NXB Văn Học',
                    'description' => 'Bức tranh hiện thực xã hội dữ dội về sự tha hóa của đồng tiền và tội ác của giai cấp thống trị qua nhân vật phản diện Nghị Hách.',
                ],
                [
                    'title' => 'Tắt Đèn',
                    'author' => 'Ngô Tất Tố',
                    'publisher' => 'NXB Mai Lĩnh',
                    'description' => 'Tác phẩm hiện thực phê phán mẫu mực khắc họa nỗi thống khổ cùng cực của người nông dân trước thuế sưu khóa mục nát và vẻ đẹp kiên cường của Chị Dậu.',
                ],
                [
                    'title' => 'Lều Chõng',
                    'author' => 'Ngô Tất Tố',
                    'publisher' => 'NXB Mai Lĩnh',
                    'description' => 'Bức tranh sinh động tái hiện cảnh thi cử khoa cử phong kiến thời xưa với những cảnh ngộ bi hài của các sĩ tử chốn trường quy lều chõng.',
                ],
                [
                    'title' => 'Chí Phèo',
                    'author' => 'Nam Cao',
                    'publisher' => 'NXB Đời Mới',
                    'description' => 'Bi kịch tha hóa đau đớn của người nông dân bị xã hội tàn nhẫn cướp mất nhân hình lẫn nhân tính cùng tiếng kêu xé lòng tuyệt vọng: "Ai cho tao lương thiện?"',
                ],
                [
                    'title' => 'Lão Hạc',
                    'author' => 'Nam Cao',
                    'publisher' => 'NXB Văn Nghệ',
                    'description' => 'Tấm lòng cha con thiêng liêng và nhân cách cao đẹp của người nông dân nghèo thà chọn cái chết đau đớn chứ nhất quyết không bán rẻ lương tâm và mảnh đất của con.',
                ],
                [
                    'title' => 'Sống Mòn',
                    'author' => 'Nam Cao',
                    'publisher' => 'NXB Văn Hóa',
                    'description' => 'Nỗi dằn vặt bế tắc của người trí thức tiểu tư sản Thứ trước cảnh sống mòn mỏi tầm thường, khát khao vươn lên lý tưởng cao đẹp nhưng bị cơm áo ghì sát đất.',
                ],
                [
                    'title' => 'Vợ Nhặt',
                    'author' => 'Kim Lân',
                    'publisher' => 'NXB Văn Học',
                    'description' => 'Bức tranh nạn đói năm 1945 thê thảm nhưng ánh lên tình người ấm áp, khát vọng sống và niềm tin bất diệt vào tương lai của những số phận nghèo khổ.',
                ],
                [
                    'title' => 'Vang Bóng Một Thời',
                    'author' => 'Nguyễn Tuân',
                    'publisher' => 'NXB Tân Dân',
                    'description' => 'Tuyển tập tản văn và truyện ngắn nghệ thuật bậc thầy tôn vinh những thú chơi tao nhã, nét đẹp văn hóa cổ truyền và khí phách của các bậc tài hoa nghĩa khí.',
                ],
                [
                    'title' => 'Truyện Kiều',
                    'author' => 'Nguyễn Du',
                    'publisher' => 'NXB Văn Học',
                    'description' => 'Đỉnh cao thi ca dân tộc Việt Nam, khúc đoạn trường tân thanh về số phận tài hoa bạc mệnh của nàng Thúy Kiều và tấm lòng nhân đạo bao la của Đại thi hào.',
                ],
                [
                    'title' => 'Nỗi Buồn Chiến Tranh',
                    'author' => 'Bảo Ninh',
                    'publisher' => 'NXB Hội Nhà Văn',
                    'description' => 'Tác phẩm văn học chiến tranh chấn động thế giới, khắc họa nỗi đau thương mất mát khôn nguôi và dư chấn tinh thần của người lính bước ra từ bom đạn.',
                ],
                [
                    'title' => 'Mắt Biếc',
                    'author' => 'Nguyễn Nhật Ánh',
                    'publisher' => 'NXB Trẻ',
                    'description' => 'Khúc tình ca trong trẻo nhưng đượm buồn về mối tình đơn phương trọn đời của Ngạn dành cho Hà Lan nơi làng quê Đo Đo thanh bình ấu thơ.',
                ],
                [
                    'title' => 'Cho Tôi Xin Một Vé Đi Tuổi Thơ',
                    'author' => 'Nguyễn Nhật Ánh',
                    'publisher' => 'NXB Trẻ',
                    'description' => 'Tấm vé kỳ diệu đưa người lớn trở về với thế giới tuổi thơ ngây ngô, nghịch ngợm nhưng chan chứa những bài học nhân văn sâu lắng về cuộc đời.',
                ],
                [
                    'title' => 'Tôi Thấy Hoa Vàng Trên Cỏ Xanh',
                    'author' => 'Nguyễn Nhật Ánh',
                    'publisher' => 'NXB Trẻ',
                    'description' => 'Những câu chuyện tuổi thơ ngọt ngào và rung động giữa hai anh em Thiều và Tường, tình bạn, tình anh em và những rung động đầu đời trong sáng.',
                ],
                [
                    'title' => 'Dế Mèn Phiêu Lưu Ký',
                    'author' => 'Tô Hoài',
                    'publisher' => 'NXB Kim Đồng',
                    'description' => 'Tác phẩm thiếu nhi kinh điển nhất Việt Nam kể về những chuyến phiêu lưu kỳ thú của Dế Mèn, bài học về tình bạn, lòng nhân ái và khát vọng hòa bình muôn loài.',
                ],
                [
                    'title' => 'Đất Rừng Phương Nam',
                    'author' => 'Đoàn Giỏi',
                    'publisher' => 'NXB Kim Đồng',
                    'description' => 'Bức tranh thiên nhiên Nam Bộ hoang sơ hùng vĩ qua hành trình lưu lạc tìm cha của cậu bé An, ngợi ca con người hào sảng, trọng nghĩa khinh tài.',
                ],
                [
                    'title' => 'Cánh Đồng Bất Tận',
                    'author' => 'Nguyễn Ngọc Tư',
                    'publisher' => 'NXB Trẻ',
                    'description' => 'Những phận người trôi dạt trên cánh đồng miền Tây sông nước, nỗi đau xót xa nhưng thấm đượm tình người và sức sống bền bỉ của người dân châu thổ.',
                ],
                [
                    'title' => 'Người Đua Diều (The Kite Runner)',
                    'author' => 'Khaled Hosseini',
                    'publisher' => 'Riverhead Books',
                    'description' => 'Câu chuyện cảm động rơi nước mắt về tình bạn, sự phản bội và hành trình chuộc lại lỗi lầm của Amir giữa bối cảnh đất nước Afghanistan đầy biến động.',
                ],
                [
                    'title' => 'Ngàn Mặt Trời Rực Rỡ (A Thousand Splendid Suns)',
                    'author' => 'Khaled Hosseini',
                    'publisher' => 'Riverhead Books',
                    'description' => 'Khúc ca ngợi ca tình mẫu tử và nghị lực phi thường của hai người phụ nữ Mariam và Laila gắn kết bên nhau giữa bom đạn và áp bức xã hội.',
                ],
            ],
        ];
    }
}
