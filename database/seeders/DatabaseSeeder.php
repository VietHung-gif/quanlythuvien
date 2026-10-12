<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Dọn sạch Database cũ trước khi chạy
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('books')->truncate();
        DB::table('categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Danh sách 100 cuốn sách chuẩn (4 thể loại, mỗi loại 25 cuốn)
        $data = [
            'CNTT' => [
                'Clean Code: A Handbook of Agile Software Craftsmanship',
                'The Clean Coder',
                'Clean Architecture',
                'Clean Agile: Back to Basics',
                'The Pragmatic Programmer',
                'Design Patterns: Elements of Reusable Object-Oriented Software',
                'Refactoring: Improving the Design of Existing Code',
                'Code Complete',
                'Head First Design Patterns',
                'Cracking the Coding Interview',
                'Introduction to Algorithms',
                'Structure and Interpretation of Computer Programs',
                "Don't Make Me Think",
                'The Mythical Man-Month',
                'Eloquent JavaScript',
                'You Don\'t Know JS',
                'Python Crash Course',
                'Effective Java',
                'Designing Data-Intensive Applications',
                'System Design Interview',
                'Patterns of Enterprise Application Architecture',
                'Soft Skills: The software developer\'s life manual',
                'Continuous Delivery',
                'Building Microservices',
                'Domain-Driven Design',
            ],
            'Kinh doanh' => [
                // 18 cuốn của bạn
                'The Personal MBA',
                'The Lean Startup',
                'The Mom Test',
                'The E-Myth Revisited',
                'The $100 Startup',
                'Thinking in Bets',
                'Quit',
                'The Hard Thing About Hard Things',
                'Super Thinking',
                'Risk',
                'Zero to One',
                'The Startup Owner\'s Manual',
                'Blue Ocean Strategy',
                'Traction',
                'Profit First',
                'Good to Great',
                'The Innovator\'s Dilemma',
                'Built to Sell',
                // 7 cuốn bổ sung cực xịn
                'Rework',
                'Shoe Dog',
                'Atomic Habits',
                'Deep Work',
                'Start with Why',
                'Measure What Matters',
                'Hooked: How to Build Habit-Forming Products',
            ],
            'Chứng khoán' => [
                'The Intelligent Investor',
                'Reminiscences of a Stock Operator',
                'How to Make Money in Stocks',
                'One Up On Wall Street',
                'Beating the Street',
                'Common Stocks and Uncommon Profits',
                'A Random Walk Down Wall Street',
                'The Little Book That Still Beats the Market',
                'Security Analysis',
                'Market Wizards',
                'The New Market Wizards',
                'Unknown Market Wizards',
                'Trade Like a Stock Market Wizard',
                'The Psychology of Money',
                'Rich Dad Poor Dad',
                'Think and Grow Rich',
                'The Bogleheads\' Guide to Investing',
                'Principles: Life and Work',
                'Fooled by Randomness',
                'The Black Swan',
                'Margin of Safety',
                'Mastering the Market Cycle',
                'Value Investing: From Graham to Buffett',
                'The Alchemy of Finance',
                'Trading for a Living',
            ],
            'Truyện Anime' => [
                'Manga One Piece Vol 1',
                'Manga Naruto Vol 1',
                'Manga Dragon Ball Vol 1',
                'Manga Bleach Vol 1',
                'Manga Detective Conan Vol 1',
                'Manga Doraemon Vol 1',
                'Manga Attack on Titan Vol 1',
                'Manga Demon Slayer Vol 1',
                'Manga Jujutsu Kaisen Vol 1',
                'Manga My Hero Academia Vol 1',
                'Manga Hunter x Hunter Vol 1',
                'Manga Fullmetal Alchemist Vol 1',
                'Manga Death Note Vol 1',
                'Manga Tokyo Ghoul Vol 1',
                'Manga One Punch Man Vol 1',
                'Manga Haikyuu!! Vol 1',
                'Manga Spy x Family Vol 1',
                'Manga Chainsaw Man Vol 1',
                'Manga Black Clover Vol 1',
                'Manga Fairy Tail Vol 1',
                'Manga Gintama Vol 1',
                'Manga JoJo\'s Bizarre Adventure Vol 1',
                'Manga Sailor Moon Vol 1',
                'Manga Slam Dunk Vol 1',
                'Manga Yu-Gi-Oh! Vol 1',
            ],
        ];

        $this->command->info('Bắt đầu tải ảnh bìa từ Google Books... Vui lòng đợi nhé!');

        // 3. Vòng lặp tự động tìm và tải ảnh
        foreach ($data as $categoryName => $books) {
            // Tạo danh mục
            $categoryId = DB::table('categories')->insertGetId([
                'name' => $categoryName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($books as $title) {
                usleep(200000); // 0.2s thay vì 2s để tăng tốc độ seed
                $this->command->info('Đang tải ảnh cho cuốn: '.$title);

                $fileName = Str::slug($title).'-'.time().'.jpg';
                $savePath = 'books/'.$fileName;
                $imageUrl = null;

                // 1. Thử lấy ảnh từ Google Books API
                $apiKey = env('GOOGLE_BOOKS_API_KEY');
                $googleParams = ['q' => 'intitle:'.$title, 'maxResults' => 1];
                if ($apiKey) {
                    $googleParams['key'] = $apiKey;
                }

                try {
                    $response = Http::withoutVerifying()->timeout(8)->get('https://www.googleapis.com/books/v1/volumes', $googleParams);
                    if ($response->successful() && isset($response->json()['items'][0]['volumeInfo']['imageLinks']['thumbnail'])) {
                        $imageUrl = $response->json()['items'][0]['volumeInfo']['imageLinks']['thumbnail'];
                        $imageUrl = str_replace('http:', 'https:', $imageUrl);
                    }
                } catch (\Throwable $e) {
                }

                // 2. Nếu Google Books không có hoặc lỗi rate limit (429), fallback sang Open Library
                if (! $imageUrl) {
                    $imageUrl = $this->fetchOpenLibraryCover($title);
                }

                // 3. Nếu vẫn không có, dùng placeholder UI-Avatars làm giải pháp cuối
                if (! $imageUrl) {
                    $imageUrl = 'https://ui-avatars.com/api/?name='.urlencode($title).'&background=random&size=400&color=fff';
                }

                // Tải ảnh về lưu thẳng vào thư mục storage/app/public/books
                try {
                    $imageResponse = Http::withoutVerifying()->timeout(15)->get($imageUrl);
                    if ($imageResponse->successful() && ! empty($imageResponse->body())) {
                        Storage::disk('public')->put($savePath, $imageResponse->body());
                    } else {
                        $savePath = $imageUrl;
                    }
                } catch (\Exception $e) {
                    $savePath = $imageUrl;
                }

                // Sinh số lượng tồn kho ngẫu nhiên (5 - 25 cuốn) để độc giả có thể mượn sách
                $randomQty = rand(5, 25);

                // Lưu thông tin sách vào Database
                DB::table('books')->insert([
                    'category_id' => $categoryId,
                    'title' => $title,
                    'author' => 'Nhiều tác giả', // Có thể cập nhật chi tiết sau
                    'publisher' => 'NXB '.$categoryName,
                    'publish_year' => rand(2010, 2024),
                    'description' => 'Đây là một cuốn sách tiêu biểu thuộc thể loại '.$categoryName.' dành cho sinh viên và người đi làm.',
                    'cover_image' => $savePath, // Lưu đường dẫn Local Storage
                    'quantity' => $randomQty,
                    'available_copies' => $randomQty,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->call(UpdateBookAuthorsAndDescriptionsSeeder::class);
        $this->call(ExpandTo400BooksSeeder::class);
        $this->call(UpdateAllBookCoversSeeder::class);

        $this->command->info('HOÀN TẤT! 400 cuốn sách tinh hoa và 3.618 bản sách đã sẵn sàng trên thư viện!');
    }

    /**
     * Tìm ảnh bìa từ Open Library API với bộ lọc tên sách thông minh
     */
    protected function fetchOpenLibraryCover(string $rawTitle): ?string
    {
        $aliases = [
            'Detective Conan' => 'Case Closed',
            'Demon Slayer' => 'Kimetsu no Yaiba',
            'Gintama' => 'Gin Tama',
            'Hunter x Hunter' => 'Hunter x Hunter',
        ];

        $clean = trim((string) preg_replace('/\s*[\(\[\{].*?[\)\]\}]\s*/', ' ', $rawTitle));
        $clean = trim(preg_replace('/^manga\s+/i', '', $clean));

        $candidates = [];

        foreach ($aliases as $key => $alias) {
            if (stripos($clean, $key) !== false) {
                $candidates[] = $alias;
            }
        }

        $short = trim(explode(':', $clean)[0]);
        $short = trim(explode(' - ', $short)[0]);
        $candidates[] = $short;

        if (preg_match('/vol\s*\d+/i', $short)) {
            $candidates[] = trim(preg_replace('/vol\s*\d+/i', '', $short));
        }

        $candidates[] = $clean;
        $candidates = array_unique(array_filter($candidates));

        foreach ($candidates as $term) {
            try {
                $res = Http::withoutVerifying()->timeout(8)->get('https://openlibrary.org/search.json', [
                    'title' => $term,
                    'limit' => 1,
                ]);

                $coverId = $res->json('docs.0.cover_i');

                if (! $coverId) {
                    $resQ = Http::withoutVerifying()->timeout(8)->get('https://openlibrary.org/search.json', [
                        'q' => $term,
                        'limit' => 1,
                    ]);
                    $coverId = $resQ->json('docs.0.cover_i');
                }

                if ($coverId) {
                    return "https://covers.openlibrary.org/b/id/{$coverId}-L.jpg";
                }
            } catch (\Throwable $e) {
            }
        }

        return null;
    }
}
