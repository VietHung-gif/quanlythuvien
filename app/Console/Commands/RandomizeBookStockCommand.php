<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;

class RandomizeBookStockCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:randomize-stock 
                            {--min=5 : Số lượng tối thiểu mỗi cuốn sách}
                            {--max=25 : Số lượng tối đa mỗi cuốn sách}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động tạo số lượng tồn kho và khả dụng ngẫu nhiên cho tất cả các đầu sách trong thư viện';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $min = max(1, (int) $this->option('min'));
        $max = max($min, (int) $this->option('max'));

        $this->info("=== [ĐỒNG BỘ & TẠO SỐ LƯỢNG SÁCH NGẪU NHIÊN ({$min} - {$max} CUỐN)] ===");

        $books = Book::all();

        if ($books->isEmpty()) {
            $this->warn('Không có cuốn sách nào trong cơ sở dữ liệu.');

            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($books->count());
        $bar->start();

        foreach ($books as $book) {
            $randomQuantity = rand($min, $max);

            $book->update([
                'quantity' => $randomQuantity,
                'available_copies' => $randomQuantity,
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("[OK] Đã cập nhật thành công số lượng cho {$books->count()} cuốn sách.");
        $this->line('• Tổng số lượng sách trong kho: <options=bold>'.Book::sum('quantity').'</> cuốn');
        $this->line('• Sách có sẵn để mượn (In Stock): <options=bold>'.Book::where('available_copies', '>', 0)->count().'</> đầu sách');

        return Command::SUCCESS;
    }
}
