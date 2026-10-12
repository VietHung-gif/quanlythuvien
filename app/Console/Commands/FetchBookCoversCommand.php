<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class FetchBookCoversCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:fetch-covers 
                            {--force : Cập nhật lại ảnh bìa cho toàn bộ sách trong hệ thống}
                            {--limit= : Giới hạn số lượng cuốn sách cần quét}
                            {--key= : Google Books API Key (tùy chọn)}
                            {--no-download : Chỉ lưu URL ảnh thay vì tải file về local storage}
                            {--no-fallback : Tắt tính năng tự động fallback qua Open Library}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động tìm kiếm và tải ảnh bìa chất lượng cao cho sách qua Google Books API và Open Library';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('=== [BỘ TẢI ẢNH BÌA SÁCH TỰ ĐỘNG (GOOGLE BOOKS & OPEN LIBRARY)] ===');

        $query = Book::query();

        if (! $this->option('force')) {
            $query->where(function ($q) {
                $q->whereNull('cover_image')
                    ->orWhere('cover_image', '')
                    ->orWhere('cover_image', 'not like', 'http%');
            });
        }

        if ($limit = $this->option('limit')) {
            $query->limit((int) $limit);
        }

        $books = $query->get();

        if ($books->isEmpty()) {
            $this->info('[OK] Không có cuốn sách nào cần cập nhật ảnh bìa.');
            $this->line('<fg=gray>[INFO] Mẹo: Dùng cờ --force để quét và làm mới toàn bộ sách: php artisan books:fetch-covers --force</>');

            return Command::SUCCESS;
        }

        $this->info("[SEARCH] Tìm thấy {$books->count()} cuốn sách cần xử lý. Bắt đầu tìm kiếm ảnh bìa...");
        $this->newLine();

        $updatedCount = 0;
        $failedCount = 0;
        $apiKey = $this->option('key') ?: env('GOOGLE_BOOKS_API_KEY');

        foreach ($books as $index => $book) {
            $num = $index + 1;
            $this->line("<fg=cyan>[{$num}/{$books->count()}]</> Đang tìm ảnh cho: <options=bold>{$book->title}</>");

            try {
                $thumbnail = null;
                $source = null;

                // 1. Thử lấy từ Google Books API nếu có API key hoặc thử trước
                $thumbnail = $this->fetchCoverFromGoogleBooks($book->title, $apiKey);
                if ($thumbnail) {
                    $source = 'Google Books';
                }

                // 2. Nếu Google Books không có ảnh hoặc bị chặn (429 Rate Limit), fallback sang Open Library
                if (! $thumbnail && ! $this->option('no-fallback')) {
                    $thumbnail = $this->fetchCoverFromOpenLibrary($book->title);
                    if ($thumbnail) {
                        $source = 'Open Library';
                    }
                }

                if ($thumbnail) {
                    $finalPath = $thumbnail;

                    // 3. Tải ảnh về thư mục storage/app/public/books nếu không dùng cờ --no-download
                    if (! $this->option('no-download')) {
                        $downloadedPath = $this->downloadAndSaveImage($thumbnail, $book);
                        if ($downloadedPath) {
                            $finalPath = $downloadedPath;
                        }
                    }

                    $book->update([
                        'cover_image' => $finalPath,
                    ]);

                    $this->info("   [OK] Thành công [{$source}]: {$finalPath}");
                    $updatedCount++;
                } else {
                    $this->warn('   [WARN] Không tìm thấy ảnh phù hợp.');
                    $failedCount++;
                }

                // Giãn cách nhẹ 150ms để không bị rate limit
                usleep(150000);
            } catch (Throwable $e) {
                $this->error("   [ERROR] Lỗi xử lý: {$e->getMessage()}");
                $failedCount++;
            }
        }

        $this->newLine();
        $this->info('=== HOÀN TẤT TIẾN TRÌNH TẢI ẢNH ===');
        $this->info("[OK] Đã cập nhật ảnh thành công: {$updatedCount} cuốn sách.");
        if ($failedCount > 0) {
            $this->warn("[WARN] Bỏ qua / Không tìm thấy ảnh: {$failedCount} cuốn sách.");
        }

        return Command::SUCCESS;
    }

    /**
     * Tìm ảnh bìa từ Google Books API
     */
    protected function fetchCoverFromGoogleBooks(string $title, ?string $apiKey = null): ?string
    {
        $cleanTitle = trim((string) preg_replace('/\s*[\(\[\{].*?[\)\]\}]\s*/', ' ', $title)) ?: trim($title);

        $queryParams = [
            'q' => 'intitle:'.$cleanTitle,
            'maxResults' => 1,
        ];

        if ($apiKey) {
            $queryParams['key'] = $apiKey;
        }

        try {
            $response = Http::withoutVerifying()
                ->timeout(8)
                ->get('https://www.googleapis.com/books/v1/volumes', $queryParams);

            if ($response->successful()) {
                $thumbnail = data_get($response->json(), 'items.0.volumeInfo.imageLinks.thumbnail')
                    ?? data_get($response->json(), 'items.0.volumeInfo.imageLinks.smallThumbnail');

                if ($thumbnail) {
                    return str_replace('http://', 'https://', $thumbnail);
                }
            }
        } catch (Throwable $e) {
            // Lỗi mạng hoặc timeout
        }

        return null;
    }

    /**
     * Tìm ảnh bìa từ Open Library API với bộ phân giải từ khóa thông minh
     */
    protected function fetchCoverFromOpenLibrary(string $rawTitle): ?string
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

        // Kiểm tra alias
        foreach ($aliases as $key => $alias) {
            if (stripos($clean, $key) !== false) {
                $candidates[] = $alias;
            }
        }

        // Tách tiêu đề chính trước dấu hai chấm hoặc gạch ngang
        $short = trim(explode(':', $clean)[0]);
        $short = trim(explode(' - ', $short)[0]);
        $candidates[] = $short;

        // Nếu có Vol ..., thêm phiên bản bỏ Vol
        if (preg_match('/vol\s*\d+/i', $short)) {
            $candidates[] = trim(preg_replace('/vol\s*\d+/i', '', $short));
        }

        $candidates[] = $clean;
        $candidates = array_unique(array_filter($candidates));

        foreach ($candidates as $term) {
            try {
                // Thử tìm theo tiêu đề (title)
                $res = Http::withoutVerifying()->timeout(8)->get('https://openlibrary.org/search.json', [
                    'title' => $term,
                    'limit' => 1,
                ]);

                $coverId = $res->json('docs.0.cover_i');

                // Nếu không có cover theo title, thử tìm tổng quát (q)
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
            } catch (Throwable $e) {
                // Tiếp tục thử ứng viên tiếp theo
            }
        }

        return null;
    }

    /**
     * Tải ảnh về máy và lưu vào local storage
     */
    protected function downloadAndSaveImage(string $imageUrl, Book $book): ?string
    {
        try {
            $response = Http::withoutVerifying()->timeout(15)->get($imageUrl);

            if ($response->successful() && ! empty($response->body())) {
                $fileName = Str::slug($book->title).'-'.time().'.jpg';
                $savePath = 'books/'.$fileName;

                Storage::disk('public')->put($savePath, $response->body());

                return $savePath;
            }
        } catch (Throwable $e) {
            // Trả về null nếu tải lỗi để dùng trực tiếp URL gốc
        }

        return null;
    }
}
