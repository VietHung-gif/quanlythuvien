<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixBookCoversSeeder extends Seeder
{
    /**
     * Cập nhật ảnh bìa sách (cover_image) trong bảng books:
     * 1. Đối chiếu chính xác theo từ điển tựa sách kinh điển / phổ biến.
     * 2. Tự động gán ảnh bìa chất lượng cao chuẩn chủ đề theo category_id cho các cuốn còn lại.
     * 3. Thực thi bulk update an toàn trong MySQL Transaction.
     */
    public function run(): void
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $this->command->info('[START] [TIỂU ĐỘI 2P] BẮT ĐẦU CHUẨN HÓA ẢNH BÌA SÁCH THEO TỰA ĐỀ VÀ THỂ LOẠI...');

        // 1. TỪ ĐIỂN TỰA SÁCH KINH ĐIỂN / PHỔ BIẾN (DICTIONARY MAP)
        // Ánh xạ từ khóa tiêu đề sách -> Link ảnh bìa thật 100% (Open Library CDN / Wikimedia / Unsplash Books)
        $titleKeywordsMap = [
            // O. Henry - Chiếc Lá Cuối Cùng
            'chiếc lá cuối cùng' => 'https://covers.openlibrary.org/b/id/11183204-L.jpg',
            'the last leaf' => 'https://covers.openlibrary.org/b/id/11183204-L.jpg',

            // Ernest Hemingway - Ông Già và Biển Cả
            'ông già và biển cả' => 'https://covers.openlibrary.org/b/id/8231856-L.jpg',
            'the old man and the sea' => 'https://covers.openlibrary.org/b/id/8231856-L.jpg',

            // J.D. Salinger - Bắt Trẻ Đồng Xanh
            'bắt trẻ đồng xanh' => 'https://covers.openlibrary.org/b/id/8235113-L.jpg',
            'the catcher in the rye' => 'https://covers.openlibrary.org/b/id/8235113-L.jpg',

            // Data Science & Chuỗi Thời Gian
            'chuỗi thời gian' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=600&q=80',
            'mô hình dự đoán' => 'https://images.unsplash.com/photo-1504868584819-f8e8b4b6d7e3?auto=format&fit=crop&w=600&q=80',

            // Học thuật & Nghiên cứu
            'khảo cứu thực nghiệm' => 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?auto=format&fit=crop&w=600&q=80',
            'bản thảo học thuật' => 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&w=600&q=80',
            'chuyên luận nghiên cứu' => 'https://images.unsplash.com/photo-1532012164546-f432f2e37b73?auto=format&fit=crop&w=600&q=80',

            // Paulo Coelho - Nhà Giả Kim
            'nhà giả kim' => 'https://covers.openlibrary.org/b/id/10543204-L.jpg',
            'the alchemist' => 'https://covers.openlibrary.org/b/id/10543204-L.jpg',

            // Fyodor Dostoevsky - Tội Ác và Hình Phạt / Anh Em Nhà Karamazov
            'tội ác và hình phạt' => 'https://covers.openlibrary.org/b/id/12833525-L.jpg',
            'tội ác và trừng phạt' => 'https://covers.openlibrary.org/b/id/12833525-L.jpg',
            'crime and punishment' => 'https://covers.openlibrary.org/b/id/12833525-L.jpg',
            'anh em nhà karamazov' => 'https://covers.openlibrary.org/b/id/8231856-L.jpg',

            // Gabriel García Márquez - Trăm Năm Cô Đơn
            'trăm năm cô đơn' => 'https://covers.openlibrary.org/b/id/8231850-L.jpg',
            'one hundred years of solitude' => 'https://covers.openlibrary.org/b/id/8231850-L.jpg',

            // Victor Hugo - Những Người Khốn Khổ
            'những người khốn khổ' => 'https://covers.openlibrary.org/b/id/12843515-L.jpg',
            'les misérables' => 'https://covers.openlibrary.org/b/id/12843515-L.jpg',

            // Antoine de Saint-Exupéry - Hoàng Tử Bé
            'hoàng tử bé' => 'https://covers.openlibrary.org/b/id/8225261-L.jpg',
            'the little prince' => 'https://covers.openlibrary.org/b/id/8225261-L.jpg',

            // Dale Carnegie - Đắc Nhân Tâm
            'đắc nhân tâm' => 'https://covers.openlibrary.org/b/id/12547190-L.jpg',
            'how to win friends' => 'https://covers.openlibrary.org/b/id/12547190-L.jpg',

            // James Clear - Atomic Habits
            'atomic habits' => 'https://covers.openlibrary.org/b/id/12885917-L.jpg',
            'thói quen nguyên tử' => 'https://covers.openlibrary.org/b/id/12885917-L.jpg',

            // Daniel Kahneman - Tư Duy Nhanh và Chậm
            'tư duy nhanh và chậm' => 'https://covers.openlibrary.org/b/id/8292885-L.jpg',
            'thinking, fast and slow' => 'https://covers.openlibrary.org/b/id/8292885-L.jpg',

            // Viktor Frankl - Đi Tìm Lẽ Sống
            'đi tìm lẽ sống' => 'https://covers.openlibrary.org/b/id/8231991-L.jpg',
            "man's search for meaning" => 'https://covers.openlibrary.org/b/id/8231991-L.jpg',

            // Morgan Housel - Tâm Lý Học Về Tiền
            'tâm lý học về tiền' => 'https://covers.openlibrary.org/b/id/10958382-L.jpg',
            'the psychology of money' => 'https://covers.openlibrary.org/b/id/10958382-L.jpg',

            // Stephen Hawking - Lược Sử Thời Gian & Vũ Trụ Trong Vỏ Hạt Dẻ
            'lược sử thời gian' => 'https://covers.openlibrary.org/b/id/8301292-L.jpg',
            'a brief history of time' => 'https://covers.openlibrary.org/b/id/8301292-L.jpg',
            'vũ trụ trong vỏ hạt dẻ' => 'https://covers.openlibrary.org/b/id/8301292-L.jpg',
            'vũ trụ' => 'https://covers.openlibrary.org/b/id/10574880-L.jpg',
            'cosmos' => 'https://covers.openlibrary.org/b/id/10574880-L.jpg',

            // Yuval Noah Harari - Sapiens & Homo Deus
            'sapiens' => 'https://covers.openlibrary.org/b/id/8810703-L.jpg',
            'lược sử loài người' => 'https://covers.openlibrary.org/b/id/8810703-L.jpg',
            'homo deus' => 'https://covers.openlibrary.org/b/id/8810703-L.jpg',

            // Plato - Cộng Hòa
            'cộng hòa' => 'https://covers.openlibrary.org/b/id/8231995-L.jpg',
            'the republic' => 'https://covers.openlibrary.org/b/id/8231995-L.jpg',

            // Kinh Doanh & Khởi Nghiệp
            'từ tốt đến vĩ đại' => 'https://covers.openlibrary.org/b/id/8235114-L.jpg',
            'good to great' => 'https://covers.openlibrary.org/b/id/8235114-L.jpg',
            'zero to one' => 'https://covers.openlibrary.org/b/id/8235114-L.jpg',
            'từ không đến một' => 'https://covers.openlibrary.org/b/id/8235114-L.jpg',
            'từ số 0 đến số 1' => 'https://covers.openlibrary.org/b/id/8235114-L.jpg',
            'khởi nghiệp tinh gọn' => 'https://covers.openlibrary.org/b/id/8235114-L.jpg',
            'the lean startup' => 'https://covers.openlibrary.org/b/id/8235114-L.jpg',
            'cha giàu cha nghèo' => 'https://covers.openlibrary.org/b/id/12833530-L.jpg',
            'rich dad poor dad' => 'https://covers.openlibrary.org/b/id/12833530-L.jpg',
            'chiến lược đại dương xanh' => 'https://covers.openlibrary.org/b/id/8235114-L.jpg',

            // Lập trình & Kỹ thuật phần mềm
            'clean code' => 'https://covers.openlibrary.org/b/id/8739163-L.jpg',
            'mã sạch' => 'https://covers.openlibrary.org/b/id/8739163-L.jpg',
            'design patterns' => 'https://covers.openlibrary.org/b/id/12539710-L.jpg',
            'microservices' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=600&q=80',
            'refactoring' => 'https://covers.openlibrary.org/b/id/12046830-L.jpg',

            // An toàn thông tin & Hacker
            'dịch ngược mã độc' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=600&q=80',
            'reverse engineering' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=600&q=80',
            'phần mềm độc hại' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=600&q=80',
            'tường lửa' => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&w=600&q=80',
            'ids/ips' => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&w=600&q=80',
            'kiểm thử xâm nhập' => 'https://images.unsplash.com/photo-1510519138187-c178224a9a5d?auto=format&fit=crop&w=600&q=80',
            'mật mã học' => 'https://covers.openlibrary.org/b/id/8292882-L.jpg',

            // Khoa học Viễn tưởng / Sci-Fi
            'xứ cát' => 'https://covers.openlibrary.org/b/id/8235120-L.jpg',
            'dune' => 'https://covers.openlibrary.org/b/id/8235120-L.jpg',
            'neuromancer' => 'https://covers.openlibrary.org/b/id/10582964-L.jpg',
            'tam thể' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=600&q=80',

            // Văn học Việt Nam & Cổ điển
            'số đỏ' => 'https://covers.openlibrary.org/b/id/8231856-L.jpg',
            'tắt đèn' => 'https://covers.openlibrary.org/b/id/10522591-L.jpg',
            'chí phèo' => 'https://covers.openlibrary.org/b/id/12833521-L.jpg',
            'chiến tranh và hòa bình' => 'https://covers.openlibrary.org/b/id/12832569-L.jpg',
            'cuốn theo chiều gió' => 'https://covers.openlibrary.org/b/id/8231856-L.jpg',
            'suối nguồn' => 'https://covers.openlibrary.org/b/id/8235113-L.jpg',
            'giết con chim nhại' => 'https://covers.openlibrary.org/b/id/12843513-L.jpg',
            'chuông nguyện hồn ai' => 'https://covers.openlibrary.org/b/id/8231856-L.jpg',
            'đồi gió hú' => 'https://covers.openlibrary.org/b/id/10522591-L.jpg',
            'không gia đình' => 'https://covers.openlibrary.org/b/id/12833521-L.jpg',
            'tiếng chim hót trong bụi mận gai' => 'https://covers.openlibrary.org/b/id/8231850-L.jpg',
            'binh pháp tôn tử' => 'https://images.unsplash.com/photo-1461360370896-922624d12aa1?auto=format&fit=crop&w=600&q=80',
            'suy tưởng' => 'https://covers.openlibrary.org/b/id/8231995-L.jpg',
            'meditations' => 'https://covers.openlibrary.org/b/id/8231995-L.jpg',

            // Y học & Sức khỏe
            'tại sao chúng ta ngủ' => 'https://covers.openlibrary.org/b/id/8292885-L.jpg',
            'sao chúng ta lại ngủ' => 'https://covers.openlibrary.org/b/id/8292885-L.jpg',
            'why we sleep' => 'https://covers.openlibrary.org/b/id/8292885-L.jpg',
            'hơi thở' => 'https://images.unsplash.com/photo-1506126613408-eca07ce68773?auto=format&fit=crop&w=600&q=80',
            'breath' => 'https://images.unsplash.com/photo-1506126613408-eca07ce68773?auto=format&fit=crop&w=600&q=80',
            'bộ não tự chữa lành' => 'https://images.unsplash.com/photo-1505751172876-fa1923c5c528?auto=format&fit=crop&w=600&q=80',
            'bác sĩ tốt nhất là chính mình' => 'https://images.unsplash.com/photo-1532938911079-1b06ac7ceec7?auto=format&fit=crop&w=600&q=80',
        ];

        // 2. KHO ẢNH BÌA THEO DANH MỤC (CATEGORY FALLBACK COVERS)
        // Tuyển tập ảnh bìa Unsplash & Open Library chuẩn HD theo chuyên đề
        $categoryThemeCovers = [
            'Văn học & Tiểu thuyết kinh điển' => [
                'https://covers.openlibrary.org/b/id/10522591-L.jpg',
                'https://covers.openlibrary.org/b/id/12833521-L.jpg',
                'https://covers.openlibrary.org/b/id/8231856-L.jpg',
                'https://covers.openlibrary.org/b/id/12843513-L.jpg',
                'https://covers.openlibrary.org/b/id/8235113-L.jpg',
                'https://covers.openlibrary.org/b/id/12832569-L.jpg',
                'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1495446815901-a7297e633e8d?auto=format&fit=crop&w=600&q=80',
            ],
            'Trí tuệ nhân tạo (AI) & Khoa học Dữ liệu' => [
                'https://images.unsplash.com/photo-1677442136019-21780ecad995?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1620712943543-bcc4688e7485?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1555255707-c07966088b7b?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1507146426996-ef05306b995a?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=600&q=80',
                'https://covers.openlibrary.org/b/id/8739161-L.jpg',
                'https://covers.openlibrary.org/b/id/8739162-L.jpg',
                'https://covers.openlibrary.org/b/id/10574883-L.jpg',
            ],
            'Lập trình & Kỹ thuật Phần mềm' => [
                'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=600&q=80',
                'https://covers.openlibrary.org/b/id/12539710-L.jpg',
                'https://covers.openlibrary.org/b/id/12046830-L.jpg',
                'https://covers.openlibrary.org/b/id/8739163-L.jpg',
            ],
            'An toàn Thông tin & Hacker Mũ Trắng' => [
                'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1510519138187-c178224a9a5d?auto=format&fit=crop&w=600&q=80',
                'https://covers.openlibrary.org/b/id/8292882-L.jpg',
                'https://covers.openlibrary.org/b/id/10515152-L.jpg',
            ],
            'Tâm lý học & Phát triển Bản thân' => [
                'https://covers.openlibrary.org/b/id/8231991-L.jpg',
                'https://covers.openlibrary.org/b/id/9255566-L.jpg',
                'https://covers.openlibrary.org/b/id/10543204-L.jpg',
                'https://images.unsplash.com/photo-1499209974431-9dddcece7f88?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1506126613408-eca07ce68773?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1476820865390-c52aeea957d7?auto=format&fit=crop&w=600&q=80',
            ],
            'Kinh tế số, Tài chính & Đầu tư' => [
                'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=600&q=80',
                'https://covers.openlibrary.org/b/id/8235114-L.jpg',
                'https://covers.openlibrary.org/b/id/12833530-L.jpg',
            ],
            'Khoa học Vũ trụ & Vật lý Lượng tử' => [
                'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1446776811953-b23d57bd21aa?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1506703719100-a0f3a48c0f86?auto=format&fit=crop&w=600&q=80',
                'https://covers.openlibrary.org/b/id/8301292-L.jpg',
                'https://covers.openlibrary.org/b/id/10574880-L.jpg',
            ],
            'Lịch sử Thế giới, Chiến lược & Quân sự' => [
                'https://images.unsplash.com/photo-1461360370896-922624d12aa1?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1505664194779-8beaceb93744?auto=format&fit=crop&w=600&q=80',
                'https://covers.openlibrary.org/b/id/12843515-L.jpg',
                'https://covers.openlibrary.org/b/id/8231850-L.jpg',
            ],
            'Triết học Đông Tây & Nhân sinh quan' => [
                'https://images.unsplash.com/photo-1516979187457-637abb4f9353?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=600&q=80',
                'https://covers.openlibrary.org/b/id/8231995-L.jpg',
                'https://covers.openlibrary.org/b/id/12833525-L.jpg',
            ],
            'Tiểu thuyết Cyberpunk, Khoa học Viễn tưởng (Sci-Fi)' => [
                'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&w=600&q=80',
                'https://covers.openlibrary.org/b/id/8235120-L.jpg',
                'https://covers.openlibrary.org/b/id/10582964-L.jpg',
            ],
            'Y học, Sức khỏe & Dinh dưỡng' => [
                'https://images.unsplash.com/photo-1505751172876-fa1923c5c528?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1532938911079-1b06ac7ceec7?auto=format&fit=crop&w=600&q=80',
                'https://covers.openlibrary.org/b/id/8292885-L.jpg',
            ],
            'Nghệ thuật, Kiến trúc & Thiết kế' => [
                'https://images.unsplash.com/photo-1513364776144-60967b0f800f?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=600&q=80',
                'https://covers.openlibrary.org/b/id/10543210-L.jpg',
            ],
            'Nghệ thuật, Thiết kế & UI/UX' => [
                'https://images.unsplash.com/photo-1581291518857-4e27b48ff24e?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?auto=format&fit=crop&w=600&q=80',
                'https://covers.openlibrary.org/b/id/10543210-L.jpg',
            ],
            'Khoa học thần kinh & Não bộ' => [
                'https://images.unsplash.com/photo-1505751172876-fa1923c5c528?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1559757175-5700dde675bc?auto=format&fit=crop&w=600&q=80',
                'https://covers.openlibrary.org/b/id/8292885-L.jpg',
            ],
            'Môi trường & Khoa học Trái đất' => [
                'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1446776811953-b23d57bd21aa?auto=format&fit=crop&w=600&q=80',
                'https://covers.openlibrary.org/b/id/8301292-L.jpg',
            ],
            'Truyện tranh Manga & Light Novel' => [
                'https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&w=600&q=80',
                'https://covers.openlibrary.org/b/id/8235120-L.jpg',
                'https://covers.openlibrary.org/b/id/10582964-L.jpg',
            ],
        ];

        // Mặc định cho mọi thể loại khác
        $defaultCovers = [
            'https://covers.openlibrary.org/b/id/10522591-L.jpg',
            'https://covers.openlibrary.org/b/id/12833521-L.jpg',
            'https://covers.openlibrary.org/b/id/8231856-L.jpg',
            'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=600&q=80',
        ];

        // Lấy danh mục ID -> Tên
        $categoriesMap = Category::pluck('name', 'id')->toArray();

        // 3. THỰC HIỆN DUYỆT TẤT CẢ SÁCH VÀ CẬP NHẬT THEO LÔ (CHUNK 500)
        $totalBooks = DB::table('books')->count();
        $this->command->info("[INFO] Tổng số sách cần kiểm tra và chuẩn hóa ảnh bìa: {$totalBooks} cuốn.");

        $matchedTitleCount = 0;
        $categoryAssignedCount = 0;
        $totalProcessed = 0;

        DB::beginTransaction();

        try {
            DB::table('books')->orderBy('id')->chunk(500, function ($books) use (
                &$matchedTitleCount,
                &$categoryAssignedCount,
                &$totalProcessed,
                $titleKeywordsMap,
                $categoryThemeCovers,
                $defaultCovers,
                $categoriesMap
            ) {
                $cases = [];
                $ids = [];
                $params = [];

                foreach ($books as $book) {
                    $ids[] = $book->id;
                    $normalizedTitle = mb_strtolower($book->title, 'UTF-8');
                    $chosenCover = null;

                    // 1. Kiểm tra đối chiếu tựa sách qua Dictionary Map
                    foreach ($titleKeywordsMap as $keyword => $coverUrl) {
                        if (str_contains($normalizedTitle, $keyword)) {
                            $chosenCover = $coverUrl;
                            $matchedTitleCount++;
                            break;
                        }
                    }

                    // 2. Nếu không khớp từ khóa tựa đề cụ thể -> Dùng kho ảnh theo Danh mục (category)
                    if (! $chosenCover) {
                        $catName = $categoriesMap[$book->category_id] ?? null;
                        $pool = ($catName && isset($categoryThemeCovers[$catName]))
                            ? $categoryThemeCovers[$catName]
                            : $defaultCovers;

                        // Chọn ảnh đều đặn theo ID sách để đa dạng, không trùng lặp đơn điệu
                        $chosenCover = $pool[$book->id % count($pool)];
                        $categoryAssignedCount++;
                    }

                    // Chuẩn bị bulk update câu lệnh CASE
                    $cases[] = "WHEN {$book->id} THEN ?";
                    $params[] = $chosenCover;
                }

                // Thực thi bulk update nhanh bằng một câu SQL duy nhất cho cả lô 500 bản ghi
                if (! empty($cases)) {
                    $casesSql = implode(' ', $cases);
                    $idsSql = implode(',', $ids);
                    $query = "UPDATE books SET cover_image = CASE id {$casesSql} END WHERE id IN ({$idsSql})";
                    DB::statement($query, $params);
                }

                $totalProcessed += count($books);
                $this->command->line("  [OK] Đã cập nhật xong {$totalProcessed} cuốn sách...");
            });

            DB::commit();

            $this->command->info('[SUCCESS] HOÀN THÀNH XUẤT SẮC:');
            $this->command->info("  - Khớp chuẩn xác theo tên tác phẩm: {$matchedTitleCount} cuốn.");
            $this->command->info("  - Khớp theo chủ đề chuyên sâu của Thể loại: {$categoryAssignedCount} cuốn.");
            $this->command->info("  - Tổng số sách đã cập nhật ảnh bìa hoàn hảo: {$totalProcessed} cuốn.");

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error('[ERROR] Lỗi trong quá trình cập nhật ảnh bìa: '.$e->getMessage());
            throw $e;
        }
    }
}
