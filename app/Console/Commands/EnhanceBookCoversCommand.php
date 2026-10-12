<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class EnhanceBookCoversCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:enhance-covers 
                            {--width=500 : Chiều rộng tối thiểu chuẩn HD (mặc định: 500px)}
                            {--force : Bắt buộc làm nét lại toàn bộ ảnh}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động nâng cấp độ phân giải, khử mờ và làm sắc nét (Super Resolution & Sharpening) cho toàn bộ ảnh bìa sách';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $targetWidth = max(400, (int) $this->option('width'));

        $this->info("=== [BỘ TĂNG CƯỜNG ĐỘ NÉT & LÀM RÕ ẢNH BÌA SÁCH (CHUẨN HD {$targetWidth}PX)] ===");

        $books = Book::all();

        if ($books->isEmpty()) {
            $this->warn('Không tìm thấy cuốn sách nào trong cơ sở dữ liệu.');

            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($books->count());
        $bar->start();

        $enhancedCount = 0;
        $downloadedCount = 0;
        $failedCount = 0;

        foreach ($books as $book) {
            try {
                $rawCover = $book->cover_image;

                // 1. Nếu ảnh là URL ngoài, tải về máy local storage trước
                if (Str::startsWith($rawCover, ['http://', 'https://'])) {
                    $downloadedPath = $this->downloadRemoteImage($rawCover, $book);
                    if ($downloadedPath) {
                        $book->update(['cover_image' => $downloadedPath]);
                        $rawCover = $downloadedPath;
                        $downloadedCount++;
                    }
                }

                if (! $rawCover || Str::startsWith($rawCover, ['http://', 'https://'])) {
                    $bar->advance();

                    continue;
                }

                $filePath = storage_path('app/public/'.ltrim($rawCover, '/'));

                if (! file_exists($filePath)) {
                    $bar->advance();

                    continue;
                }

                // 2. Kiểm tra kích thước và xử lý làm sắc nét ảnh
                $info = @getimagesize($filePath);
                if (! $info) {
                    $bar->advance();

                    continue;
                }

                $origWidth = $info[0];
                $origHeight = $info[1];
                $mime = $info['mime'];

                // Bảo vệ các ảnh chuẩn 4K Ultra không bị can thiệp
                if (str_contains($rawCover, '4k')) {
                    $bar->advance();

                    continue;
                }

                // Chỉ xử lý nếu ảnh nhỏ hơn chuẩn HD hoặc có cờ --force
                if ($origWidth < $targetWidth || $this->option('force')) {
                    $enhanced = $this->enhanceImage($filePath, $origWidth, $origHeight, $targetWidth, $mime);
                    if ($enhanced) {
                        $enhancedCount++;
                    }
                }

                $bar->advance();
            } catch (Throwable $e) {
                $failedCount++;
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('=== HOÀN TẤT QUÁ TRÌNH LÀM RÕ NÉT ẢNH BÌA ===');
        $this->info("[OK] Đã nâng cấp độ nét & siêu phân giải: {$enhancedCount} cuốn sách.");
        if ($downloadedCount > 0) {
            $this->line("[OK] Đã tải và lưu trữ nội bộ: {$downloadedCount} ảnh URL ngoài.");
        }
        if ($failedCount > 0) {
            $this->warn("[WARN] Bỏ qua {$failedCount} cuốn do lỗi định dạng ảnh.");
        }

        return Command::SUCCESS;
    }

    /**
     * Tải ảnh từ URL về local storage
     */
    protected function downloadRemoteImage(string $url, Book $book): ?string
    {
        try {
            $response = Http::withoutVerifying()->timeout(12)->get($url);
            if ($response->successful() && ! empty($response->body())) {
                $fileName = Str::slug($book->title).'-hd-'.time().'.jpg';
                $savePath = 'books/'.$fileName;
                Storage::disk('public')->put($savePath, $response->body());

                return $savePath;
            }
        } catch (Throwable $e) {
        }

        return null;
    }

    /**
     * Nâng cấp độ phân giải (Bicubic Upscaling) và làm sắc nét (Unsharp Mask Filter)
     */
    protected function enhanceImage(string $filePath, int $origWidth, int $origHeight, int $targetWidth, string $mime): bool
    {
        // 1. Tạo GD Image resource từ file gốc
        $src = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($filePath),
            'image/png' => @imagecreatefrompng($filePath),
            'image/webp' => @imagecreatefromwebp($filePath),
            default => null,
        };

        if (! $src) {
            return false;
        }

        // 2. Tính toán kích thước mới với tỷ lệ chuẩn
        $newWidth = max($origWidth, $targetWidth);
        $ratio = $newWidth / $origWidth;
        $newHeight = (int) round($origHeight * $ratio);

        $dst = imagecreatetruecolor($newWidth, $newHeight);

        // Kích hoạt nội suy Bicubic cao cấp nhất của GD
        if (defined('IMG_BICUBIC')) {
            imagesetinterpolation($dst, IMG_BICUBIC);
        }

        // Giữ độ trong suốt nếu là PNG/WebP
        if (in_array($mime, ['image/png', 'image/webp'])) {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
        }

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

        // 3. Áp dụng ma trận làm rõ nét dịu nhẹ, không gây răng cưa hay vỡ hạt (Subtle Edge Clarifier)
        $sharpenMatrix = [
            [-0.1, -0.2, -0.1],
            [-0.2,  2.2, -0.2],
            [-0.1, -0.2, -0.1],
        ];
        $divisor = array_sum(array_map('array_sum', $sharpenMatrix));
        if ($divisor > 0) {
            imageconvolution($dst, $sharpenMatrix, $divisor, 0);
        }

        // 4. Lưu đè lại file ảnh với chất lượng nén cao 95%
        $saved = match ($mime) {
            'image/png' => imagepng($dst, $filePath, 9),
            'image/webp' => imagewebp($dst, $filePath, 95),
            default => imagejpeg($dst, $filePath, 95),
        };

        imagedestroy($src);
        imagedestroy($dst);

        return (bool) $saved;
    }
}
