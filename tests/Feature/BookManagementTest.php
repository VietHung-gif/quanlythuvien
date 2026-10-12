<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test admin can create a new book and it syncs properly with database and list.
     */
    public function test_admin_can_create_book_and_it_syncs_with_database(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'test_admin@library.local'],
            ['name' => 'Admin Tester', 'password' => bcrypt('password'), 'role' => 'admin']
        );

        $category = Category::firstOrCreate(
            ['name' => 'Công Nghệ Thông Tin']
        );

        $response = $this->actingAs($admin)->post(route('books.store'), [
            'title' => 'Sách Lập Trình Laravel Hiện Đại 2026',
            'author' => 'Tác Giả Kiểm Thử',
            'category_id' => $category->id,
            'quantity' => 15,
            'publisher' => 'NXB Giáo Dục',
            'publish_year' => 2026,
            'description' => 'Mô tả sách mẫu kiểm thử tự động.',
        ]);

        $response->assertRedirect(route('books.index'));
        $response->assertSessionHas('success');

        // Kiểm tra database đã lưu đúng dữ liệu
        $this->assertDatabaseHas('books', [
            'title' => 'Sách Lập Trình Laravel Hiện Đại 2026',
            'author' => 'Tác Giả Kiểm Thử',
            'category_id' => $category->id,
            'quantity' => 15,
            'available_copies' => 15,
            'publisher' => 'NXB Giáo Dục',
            'publish_year' => 2026,
        ]);

        // Kiểm tra sách mới xuất hiện ngay trên trang danh sách
        $indexResponse = $this->actingAs($admin)->get(route('books.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('Sách Lập Trình Laravel Hiện Đại 2026');
        $indexResponse->assertSee('Còn sách');
    }

    /**
     * Test randomize stock command sets positive quantity and available copies.
     */
    public function test_randomize_stock_command_sets_positive_quantities(): void
    {
        $category = Category::create(['name' => 'Kinh Tế']);
        for ($i = 1; $i <= 3; $i++) {
            Book::create([
                'category_id' => $category->id,
                'title' => "Cuốn Sách Mẫu {$i}",
                'author' => 'Tác Giả',
                'quantity' => 1,
                'available_copies' => 0,
            ]);
        }

        $this->artisan('books:randomize-stock --min=5 --max=20')
            ->assertSuccessful();

        $allBooks = Book::all();
        $this->assertGreaterThan(0, $allBooks->count());

        foreach ($allBooks as $book) {
            $this->assertGreaterThanOrEqual(5, $book->quantity);
            $this->assertLessThanOrEqual(20, $book->quantity);
            $this->assertEquals($book->quantity, $book->available_copies);
        }
    }

    /**
     * Test stock status display and borrow restriction when stock is 0.
     */
    public function test_book_displays_correct_stock_labels_and_disables_borrow_when_out_of_stock(): void
    {
        $category = Category::create(['name' => 'Công Nghệ']);

        $inStockBook = Book::create([
            'category_id' => $category->id,
            'title' => 'Sách Nhiều Bản In',
            'author' => 'Tác Giả A',
            'quantity' => 15,
            'available_copies' => 15,
        ]);

        $lowStockBook = Book::create([
            'category_id' => $category->id,
            'title' => 'Sách Sắp Hết Bản In',
            'author' => 'Tác Giả B',
            'quantity' => 5,
            'available_copies' => 5,
        ]);

        $outOfStockBook = Book::create([
            'category_id' => $category->id,
            'title' => 'Sách Hết Hàng Hoàn Toàn',
            'author' => 'Tác Giả C',
            'quantity' => 0,
            'available_copies' => 0,
        ]);

        $this->assertEquals('in_stock', $inStockBook->stock_status);
        $this->assertEquals('Còn sách', $inStockBook->stock_status_label);
        $this->assertTrue($inStockBook->can_borrow);

        $this->assertEquals('low_stock', $lowStockBook->stock_status);
        $this->assertEquals('Sắp hết', $lowStockBook->stock_status_label);
        $this->assertTrue($lowStockBook->can_borrow);

        $this->assertEquals('out_of_stock', $outOfStockBook->stock_status);
        $this->assertEquals('Hết sách', $outOfStockBook->stock_status_label);
        $this->assertFalse($outOfStockBook->can_borrow);

        $user = User::firstOrCreate(
            ['email' => 'reader_tester@library.local'],
            ['name' => 'Reader Tester', 'password' => bcrypt('password'), 'role' => 'reader']
        );

        $response = $this->actingAs($user)->get(route('books.index'));
        $response->assertOk();
        $response->assertSee('Còn sách');
        $response->assertSee('Sắp hết');
        $response->assertSee('Hết sách');
        $response->assertSee('Không thể chọn');

        $detailResponse = $this->actingAs($user)->get(route('books.show', $outOfStockBook));
        $detailResponse->assertOk();
        $detailResponse->assertSee('Không thể chọn');
    }
}
