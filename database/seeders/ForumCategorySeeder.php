<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\ForumCategory;
use App\Models\ForumComment;
use App\Models\ForumPost;
use App\Models\User;
use Illuminate\Database\Seeder;

class ForumCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Góc Review Sách',
                'slug' => 'review-sach',
                'icon' => 'star',
                'description' => 'Cảm nhận, đánh giá và chia sẻ góc nhìn chân thực về các cuốn sách trong kho thư viện.',
                'color' => 'cyan',
            ],
            [
                'name' => 'Hỏi Đáp & Tìm Sách',
                'slug' => 'hoi-dap-tim-tai-lieu',
                'icon' => 'question',
                'description' => 'Nơi độc giả đặt câu hỏi, tìm kiếm giáo trình, tài liệu chuyên ngành và nhờ thủ thư tư vấn.',
                'color' => 'amber',
            ],
            [
                'name' => 'Luận Bàn Tri Thức',
                'slug' => 'luan-ban-tri-thuc',
                'icon' => 'chat',
                'description' => 'Thảo luận sâu về triết học, khoa học công nghệ, kỹ năng phát triển bản thân và cuộc sống.',
                'color' => 'purple',
            ],
            [
                'name' => 'Thông Báo Thư Viện',
                'slug' => 'thong-bao-thu-vien',
                'icon' => 'megaphone',
                'description' => 'Cập nhật sách mới nhập kho, quy định mượn trả và các sự kiện giao lưu đặc biệt.',
                'color' => 'pink',
            ],
        ];

        foreach ($categories as $cat) {
            ForumCategory::firstOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }

        // Tạo một số bài viết mẫu nếu chưa có bài viết nào
        if (ForumPost::count() === 0) {
            $admin = User::where('role', 'admin')->first() ?? User::first();
            $book = Book::first();
            $reviewCat = ForumCategory::where('slug', 'review-sach')->first();
            $noticeCat = ForumCategory::where('slug', 'thong-bao-thu-vien')->first();

            if ($admin && $reviewCat) {
                $post1 = ForumPost::create([
                    'user_id' => $admin->id,
                    'category_id' => $reviewCat->id,
                    'book_id' => $book ? $book->id : null,
                    'title' => 'Cảm nhận về cuốn sách "'.($book ? $book->title : 'Hành Trình Tri Thức').'" - Đọc xong như được khai sáng!',
                    'content' => "Chào các đồng môn thư viện TIỂU ĐỘI 2P,\n\nHôm qua mình vừa hoàn thành cuốn sách này trong khu vực đọc sách Cyber Hub. Cuốn sách mang lại những góc nhìn cực kỳ sâu sắc về tư duy phát triển bản thân và hành trình chinh phục mục tiêu lớn.\n\nĐiểm mình thích nhất là lối hành văn súc tích, các ví dụ thực tiễn dễ áp dụng ngay vào đời sống học tập. Rất khuyến khích anh em mượn ngay về nghiền ngẫm!",
                    'likes_count' => 8,
                    'views_count' => 65,
                    'is_pinned' => false,
                ]);

                ForumComment::create([
                    'post_id' => $post1->id,
                    'user_id' => $admin->id,
                    'content' => 'Rất đồng tình với quan điểm của bạn! Cuốn này mình đọc 2 lần rồi vẫn thấy thấm.',
                ]);
            }

            if ($admin && $noticeCat) {
                ForumPost::create([
                    'user_id' => $admin->id,
                    'category_id' => $noticeCat->id,
                    'book_id' => null,
                    'title' => 'CHÀO MỪNG ĐỘC GIẢ ĐẾN VỚI "HỘI QUÁN TRI THỨC" // NỘI QUY & GIAO LƯU',
                    'content' => "Kính gửi toàn thể độc giả và hội viên TIỂU ĐỘI 2P,\n\nPhân hệ Hội Quán Tri Thức chính thức khai mở để chúng ta cùng nhau chia sẻ những cuốn sách hay, đặt câu hỏi học thuật và giao lưu văn minh.\n\nLưu ý quan trọng:\n1. Diễn đàn hoàn toàn KHÔNG CỘNG EXP khi đăng bài/bình luận nhằm tránh spam bài rác.\n2. Vui lòng tôn trọng quan điểm cá nhân và sử dụng ngôn từ văn minh.\n3. Khi review sách, hãy nhớ chọn cuốn sách tương ứng trong thư viện để bạn đọc khác có thể mượn ngay bằng 1 click!\n\nChúc mọi người có những phút giây thư thái và bổ ích tại thư viện!",
                    'likes_count' => 15,
                    'views_count' => 120,
                    'is_pinned' => true,
                ]);
            }
        }
    }
}
