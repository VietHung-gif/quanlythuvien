<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartBorrowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_cart_or_add_to_cart(): void
    {
        $response = $this->get('/cart');
        $response->assertRedirect('/login');

        $responseAdd = $this->post('/cart/add', ['book_id' => 1]);
        $responseAdd->assertRedirect('/login');
    }

    public function test_authenticated_user_can_add_book_to_cart_and_view_it(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Công nghệ thông tin', 'description' => 'Sách IT']);
        $book = Book::create([
            'category_id' => $category->id,
            'title' => 'Lập trình Laravel Chuyên Sâu',
            'author' => 'Tiểu Đội 2P',
            'quantity' => 10,
            'available_copies' => 10,
        ]);

        $response = $this->actingAs($user)
            ->from(route('books.show', $book->id))
            ->post(route('cart.add'), [
                'book_id' => $book->id,
            ]);

        $response->assertRedirect(route('books.show', $book->id));
        $response->assertSessionHas('success');
        $response->assertSessionHas('cart');

        $cart = session('cart');
        $this->assertArrayHasKey($book->id, $cart);
        $this->assertEquals('Lập trình Laravel Chuyên Sâu', $cart[$book->id]['title']);
        $this->assertEquals(1, $cart[$book->id]['quantity']);

        // Xem trang giỏ hàng
        $cartPage = $this->actingAs($user)->get(route('cart.index'));
        $cartPage->assertStatus(200);
        $cartPage->assertSee('Lập trình Laravel Chuyên Sâu');
        $cartPage->assertSee('[ĐĂNG KÝ MƯỢN]');
    }

    public function test_duplicate_add_auto_increments_quantity_with_success_toast(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Khoa học', 'description' => 'Science']);
        $book = Book::create([
            'category_id' => $category->id,
            'title' => 'Vũ trụ trong vỏ hạt dẻ',
            'author' => 'Stephen Hawking',
            'quantity' => 5,
            'available_copies' => 5,
        ]);

        // Lần 1: Thêm vào giỏ
        $res1 = $this->actingAs($user)->postJson(route('cart.add'), ['book_id' => $book->id]);
        $res1->assertStatus(200);
        $res1->assertJson([
            'success' => true,
            'cart_count' => 1,
            'message' => 'Thêm sách thành công! Đã thêm vào giỏ mượn',
        ]);

        // Lần 2: Thêm tiếp cuốn đó -> Tự động tăng số lượng lên 2
        $res2 = $this->actingAs($user)->postJson(route('cart.add'), ['book_id' => $book->id]);
        $res2->assertStatus(200);
        $res2->assertJson([
            'success' => true,
            'cart_count' => 2,
            'message' => 'Thêm sách thành công! Đã thêm vào giỏ mượn',
        ]);

        $cart = session('cart');
        $this->assertEquals(2, $cart[$book->id]['quantity']);
    }

    public function test_user_can_increment_and_decrement_cart_quantity(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Toán học', 'description' => 'Math']);
        $book = Book::create([
            'category_id' => $category->id,
            'title' => 'Đại số tuyến tính',
            'author' => 'Giáo sư Toán',
            'quantity' => 10,
            'available_copies' => 10,
        ]);

        $this->actingAs($user)->post(route('cart.add'), ['book_id' => $book->id]);

        // Tăng số lượng (+)
        $resInc = $this->actingAs($user)->post(route('cart.update-quantity'), [
            'book_id' => $book->id,
            'action' => 'increment',
        ]);
        $resInc->assertRedirect();
        $this->assertEquals(2, session('cart')[$book->id]['quantity']);

        // Giảm số lượng (-)
        $resDec = $this->actingAs($user)->post(route('cart.update-quantity'), [
            'book_id' => $book->id,
            'action' => 'decrement',
        ]);
        $resDec->assertRedirect();
        $this->assertEquals(1, session('cart')[$book->id]['quantity']);
    }

    public function test_user_can_remove_book_from_cart(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Kiến trúc phần mềm', 'description' => 'Architecture']);
        $book = Book::create([
            'category_id' => $category->id,
            'title' => 'Design Patterns PHP',
            'author' => 'GoF',
            'quantity' => 5,
            'available_copies' => 5,
        ]);

        // Thêm vào giỏ
        $this->actingAs($user)->post(route('cart.add'), ['book_id' => $book->id]);

        // Xóa khỏi giỏ
        $response = $this->actingAs($user)
            ->from(route('cart.index'))
            ->post(route('cart.remove'), [
                'book_id' => $book->id,
            ]);

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('success');

        $cart = session('cart');
        $this->assertEmpty($cart);
    }

    public function test_user_can_submit_borrow_request_from_cart_without_submitting_phone(): void
    {
        $user = User::factory()->create([
            'phone_number' => '0901234567',
        ]);
        $category = Category::create(['name' => 'Công nghệ', 'description' => 'Tech']);
        $book = Book::create([
            'category_id' => $category->id,
            'title' => 'Clean Architecture',
            'author' => 'Robert C. Martin',
            'quantity' => 5,
            'available_copies' => 5,
        ]);

        // Thêm vào giỏ 2 cuốn
        $this->actingAs($user)->post(route('cart.add'), ['book_id' => $book->id]);
        $this->actingAs($user)->post(route('cart.add'), ['book_id' => $book->id]);

        // Submit form không cần gửi phone (trường phone đã bị xóa khỏi view)
        $response = $this->actingAs($user)->post(route('borrow.create'), [
            'notes' => 'Mượn đọc nghiên cứu',
        ]);

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('success');

        // Giỏ sách đã được làm sạch
        $this->assertEmpty(session('cart', []));

        // Bản ghi phiếu mượn được lưu trong CSDL với total_quantity = 2 và borrower_phone lấy từ profile
        $this->assertDatabaseHas('borrows', [
            'user_id' => $user->id,
            'borrower_phone' => '0901234567',
            'total_quantity' => 2,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('borrow_details', [
            'book_id' => $book->id,
            'quantity' => 2,
        ]);
    }

    public function test_user_can_view_books_index_and_books_link_directly_to_details(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Khoa học', 'description' => 'Science']);
        $book = Book::create([
            'category_id' => $category->id,
            'title' => 'Lược Sử Thời Gian',
            'author' => 'Stephen Hawking',
            'quantity' => 10,
            'available_copies' => 10,
        ]);

        $response = $this->actingAs($user)->get(route('books.index'));
        $response->assertStatus(200);
        $response->assertSee('Lược Sử Thời Gian');
        $response->assertSee(route('books.show', $book->id));
        $response->assertDontSee('openQuickView');
        $response->assertDontSee('data-book=');
    }
}
