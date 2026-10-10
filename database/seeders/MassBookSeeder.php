<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class MassBookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Nạp chính xác 1.000 cuốn sách đa thể loại với hiệu năng cao (Batch Insert).
     */
    public function run(): void
    {
        $this->command->info('🚀 [TIỂU ĐỘI 2P] BẮT ĐẦU NẠP 1.000 CUỐN SÁCH QUY MÔ LỚN...');

        // 1. KHỞI TẠO 12 THỂ LOẠI CHUẨN
        $categoriesDefinition = [
            'Văn học & Tiểu thuyết kinh điển' => 'Các kiệt tác văn học kinh điển thế giới, phản ánh chiều sâu nhân sinh, tâm hồn và các giá trị nhân đạo trường tồn.',
            'Trí tuệ nhân tạo (AI) & Khoa học Dữ liệu' => 'Nghiên cứu mô hình học máy, học sâu, AI tạo sinh (Generative AI), mô hình ngôn ngữ lớn (LLM) và xử lý dữ liệu lớn.',
            'Lập trình & Kỹ thuật Phần mềm' => 'Kiến trúc hệ thống, mã sạch (Clean Code), mẫu thiết kế (Design Patterns), vi dịch vụ (Microservices) và DevOps hiện đại.',
            'An toàn Thông tin & Hacker Mũ Trắng' => 'Kỹ thuật kiểm thử xâm nhập (Penetration Testing), an ninh mạng thực chiến, mật mã học và phòng thủ không gian mạng.',
            'Tâm lý học & Phát triển Bản thân' => 'Rèn luyện thói quen nguyên tử, tâm lý học hành vi, quản lý cảm xúc, tư duy tích cực và bứt phá giới hạn bản thân.',
            'Kinh tế số, Tài chính & Đầu tư' => 'Tư duy tài chính thông minh, khởi nghiệp tinh gọn, phân tích thị trường chứng khoán và kinh tế học trong thời đại số.',
            'Khoa học Vũ trụ & Vật lý Lượng tử' => 'Khám phá bí ẩn vụ nổ Big Bang, cơ học lượng tử, lỗ đen, thuyết tương đối và bức tranh kỳ vĩ của không thời gian.',
            'Lịch sử Thế giới, Chiến lược & Quân sự' => 'Biến thiên các đế chế cổ đại, chiến lược quân sự kinh điển, các cuộc chiến tranh định hình thế giới và bài học lịch sử.',
            'Triết học Đông Tây & Nhân sinh quan' => 'Tư tưởng Khắc kỷ (Stoicism), triết học Hy Lạp cổ đại, triết học phương Đông, Lão Trang và nghệ thuật sống ý nghĩa.',
            'Tiểu thuyết Cyberpunk, Khoa học Viễn tưởng (Sci-Fi)' => 'Tương lai viễn tưởng công nghệ cao, thực tại ảo (Virtual Reality), người máy sinh học Cyborg và du hành liên sao.',
            'Truyện tranh Manga & Light Novel' => 'Kiệt tác truyện tranh Nhật Bản, tiểu thuyết phiêu lưu kỳ ảo, thế giới giả tưởng và hành trình vượt qua nghịch cảnh.',
            'Y học, Sức khỏe & Dinh dưỡng' => 'Khoa học về giấc ngủ, hô hấp, hệ miễn dịch tự nhiên, chế độ dinh dưỡng tối ưu và y học phòng ngừa trường thọ.',
        ];

        $categories = [];
        foreach ($categoriesDefinition as $catName => $catDesc) {
            $categories[$catName] = Category::updateOrCreate(
                ['name' => $catName],
                ['description' => $catDesc]
            );
        }

        $categoryNames = array_keys($categoriesDefinition);
        $totalCategories = count($categoryNames);

        // 2. KHO ẢNH BÌA THẬT CHẤT LƯỢNG CAO (COVER POOL - 50+ LINK THẬT 100%)
        $coverPool = [
            // Open Library Covers (Chuẩn tỉ lệ 3:4 & 2:3)
            'https://covers.openlibrary.org/b/id/10522591-L.jpg',
            'https://covers.openlibrary.org/b/id/12833521-L.jpg',
            'https://covers.openlibrary.org/b/id/10582964-L.jpg',
            'https://covers.openlibrary.org/b/id/10543227-L.jpg',
            'https://covers.openlibrary.org/b/id/12539710-L.jpg',
            'https://covers.openlibrary.org/b/id/8231856-L.jpg',
            'https://covers.openlibrary.org/b/id/12843513-L.jpg',
            'https://covers.openlibrary.org/b/id/8302521-L.jpg',
            'https://covers.openlibrary.org/b/id/8235113-L.jpg',
            'https://covers.openlibrary.org/b/id/12832569-L.jpg',
            'https://covers.openlibrary.org/b/id/8739161-L.jpg',
            'https://covers.openlibrary.org/b/id/8739162-L.jpg',
            'https://covers.openlibrary.org/b/id/10574883-L.jpg',
            'https://covers.openlibrary.org/b/id/12046830-L.jpg',
            'https://covers.openlibrary.org/b/id/8739163-L.jpg',
            'https://covers.openlibrary.org/b/id/8292882-L.jpg',
            'https://covers.openlibrary.org/b/id/10515152-L.jpg',
            'https://covers.openlibrary.org/b/id/8231991-L.jpg',
            'https://covers.openlibrary.org/b/id/12648719-L.jpg',
            'https://covers.openlibrary.org/b/id/12716388-L.jpg',
            'https://covers.openlibrary.org/b/id/10515153-L.jpg',
            'https://covers.openlibrary.org/b/id/8292883-L.jpg',
            'https://covers.openlibrary.org/b/id/12539712-L.jpg',
            'https://covers.openlibrary.org/b/id/8231992-L.jpg',
            'https://covers.openlibrary.org/b/id/8278278-L.jpg',
            'https://covers.openlibrary.org/b/id/8231993-L.jpg',
            'https://covers.openlibrary.org/b/id/12833522-L.jpg',
            'https://covers.openlibrary.org/b/id/8739164-L.jpg',
            'https://covers.openlibrary.org/b/id/8739165-L.jpg',
            'https://covers.openlibrary.org/b/id/8231995-L.jpg',
            'https://covers.openlibrary.org/b/id/10574884-L.jpg',
            'https://covers.openlibrary.org/b/id/12832570-L.jpg',
            'https://covers.openlibrary.org/b/id/12539704-L.jpg',
            'https://covers.openlibrary.org/b/id/12539706-L.jpg',
            'https://covers.openlibrary.org/b/id/12539705-L.jpg',
            'https://covers.openlibrary.org/b/id/8231996-L.jpg',
            'https://covers.openlibrary.org/b/id/8231997-L.jpg',
            'https://covers.openlibrary.org/b/id/10574885-L.jpg',
            'https://covers.openlibrary.org/b/id/10574886-L.jpg',
            'https://covers.openlibrary.org/b/id/12833523-L.jpg',
            'https://covers.openlibrary.org/b/id/12833524-L.jpg',
            'https://covers.openlibrary.org/b/id/10574887-L.jpg',
            'https://covers.openlibrary.org/b/id/10574888-L.jpg',
            'https://covers.openlibrary.org/b/id/12539707-L.jpg',
            'https://covers.openlibrary.org/b/id/10574889-L.jpg',
            // Unsplash HD Book Covers (Sắc nét, tỉ lệ đẹp)
            'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1532012164546-f432f2e3777a?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1495446815901-a7297e633e8d?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1506880018603-83d5b814b5a6?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1516979187457-637abb4f9353?auto=format&fit=crop&w=600&q=80',
        ];
        $totalCovers = count($coverPool);

        // 3. TỪ VỰNG CHUYÊN SÂU THEO TỪNG THỂ LOẠI
        $categoryTopics = [
            'Văn học & Tiểu thuyết kinh điển' => [
                'Tiếng Gọi Nơi Hoang Dã', 'Đồi Gió Hú', 'Đêm Trắng Thơ Mộng', 'Chiếc Lá Cuối Cùng', 
                'Chuông Nguyện Hồn Ai', 'Ông Già và Biển Cả', 'Người Xa Lạ', 'Nắng Tháng Tám', 
                'Suối Nguồn Tư Tưởng', 'Bắt Trẻ Đồng Xanh', 'Gia Đình Karamazov', 'Bá Tước Monte Cristo',
                'Kiêu Hãnh và Định Kiến', 'Vụ Án Bí Ẩn', 'Thần Thoại Sisyphus', 'Cuộc Đời Của Pi',
                'Đại Gia Gatsby', 'Gió Qua Rặng Liễu', 'Tiếng Chim Hót Trong Bụi Mận Gai', 'Túp Lều Bác Tom'
            ],
            'Trí tuệ nhân tạo (AI) & Khoa học Dữ liệu' => [
                'Mạng Nơ-ron Tích Chập (CNN)', 'Transformer và Kiến Trúc LLM', 'Học Tăng Cường (Reinforcement Learning)',
                'Thị Giác Máy Tính Hiện Đại', 'Xử Lý Ngôn Ngữ Tự Nhiên (NLP)', 'AI Tạo Sinh (Generative AI)',
                'Khai Phá Dữ Liệu Lớn (Big Data Mining)', 'Kỹ Nghệ Prompt Chuyên Nghiệp', 'Học Máy Với Scikit-Learn',
                'Mô Hình Dự Đoán Chuỗi Thời Gian', 'Học Sâu Với PyTorch Thực Chiến', 'Đạo Đức Và Pháp Lý Trong AI',
                'Trí Tuệ Nhân Tạo Phổ Quát (AGI)', 'Mạng Sinh Đối Kháng (GAN)', 'Tối Ưu Hóa Hyperparameter'
            ],
            'Lập trình & Kỹ thuật Phần mềm' => [
                'Clean Code và Mã Sạch', 'Kiến Trúc Microservices Thực Chiến', 'Mẫu Thiết Kế Hướng Đối Tượng (GoF)',
                'Phát Triển Hướng Tên Miền (DDD)', 'Lập Trình Bất Đồng Bộ Hiện Đại', 'DevOps và Tự Động Hóa CI/CD',
                'Thiết Kế API RESTful & GraphQL', 'Tối Ưu Hiệu Năng Cơ Sở Dữ Liệu', 'Cấu Trúc Dữ Liệu & Giải Thuật',
                'Hệ Điều Hành Linux Dành Cho Kỹ Sư', 'Docker và Kubernetes Toàn Tập', 'Lập Trình Web Full-Stack',
                'Kiến Trúc Hệ Thống Phân Tán', 'Kiểm Thử Phần Mềm Tự Động (TDD)', 'Bảo Trì và Tái Cấu Trúc Mã Nguồn'
            ],
            'An toàn Thông tin & Hacker Mũ Trắng' => [
                'Kiểm Thử Xâm Nhập (Penetration Testing)', 'Kỹ Thuật Dịch Ngược Mã Độc (Reverse Engineering)',
                'Khai Thác Lỗ Hổng Ứng Dụng Web', 'Mật Mã Học Ứng Dụng Trong Kỷ Nguyên Số', 'Phòng Thủ Trung Tâm SOC',
                'An Ninh Điện Toán Đám Mây', 'Phân Tích Phần Mềm Độc Hại (Malware Analysis)', 'Kỹ Nghệ Xã Hội (Social Engineering)',
                'Điều Tra Dấu Vết Kỹ Thuật Số (Digital Forensics)', 'Bảo Vệ Hạ Tầng Trọng Yếu', 'Tường Lửa và Hệ Thống IDS/IPS',
                'Phát Hiện và Ứng Cứu Sự Cố An Ninh Mạng', 'Chiến Lược Zero Trust Toàn Diện', 'Lập Trình An Toàn (Secure Coding)'
            ],
            'Tâm lý học & Phát triển Bản thân' => [
                'Thói Quen Nguyên Tử và Kỷ Luật', 'Trí Tuệ Cảm Xúc (EQ) Ứng Dụng', 'Tâm Lý Học Đám Đông và Dư Luận',
                'Nghệ Thuật Tập Trung Sâu (Deep Work)', 'Vượt Qua Vùng An Toàn', 'Khắc Phục Thói Quen Trì Hoãn',
                'Thao Túng Tâm Lý Trong Đàm Phán', 'Tư Duy Phản Biện Sắc Bén', 'Xây Dựng Sự Tự Tin Tuyệt Đối',
                'Tâm Lý Học Tích Cực', 'Nghệ Thuật Thấu Cảm', 'Quản Trị Năng Lượng Cá Nhân',
                'Định Vị Bản Thân Trong Kỷ Nguyên Số', 'Khai Sáng Tiềm Năng Vô Hạn', 'Liệu Pháp Nhận Thức Hành Vi (CBT)'
            ],
            'Kinh tế số, Tài chính & Đầu tư' => [
                'Chiến Lược Đầu Tư Giá Trị', 'Tâm Lý Học Về Tiền và Thị Trường', 'Khởi Nghiệp Tinh Gọn (Lean Startup)',
                'Quản Trị Rủi Ro Danh Mục Đầu Tư', 'Kinh Tế Học Hành Vi Ứng Dụng', 'Phân Tích Kỹ Thuật Thị Trường Tài Chính',
                'Tài Chính Doanh Nghiệp Toàn Tập', 'Mô Hình Kinh Doanh Trong Nền Kinh Tế Số', 'Chiến Lược Đại Dương Xanh',
                'Xây Dựng Hệ Sinh Thái Khởi Nghiệp', 'Kinh Tế Vĩ Mô Dành Cho Nhà Đầu Tư', 'Định Giá Doanh Nghiệp',
                'Thương Mại Điện Tử & Bán Lẻ Đa Kênh', 'Chuỗi Cung Ứng Toàn Cầu', 'Đàm Phán Thương Lượng Đỉnh Cao'
            ],
            'Khoa học Vũ trụ & Vật lý Lượng tử' => [
                'Lỗ Đen và Không Thời Gian Uốn Cong', 'Cơ Học Lượng Tử Dành Cho Mọi Người', 'Bản Đồ Vũ Trụ và Vụ Nổ Big Bang',
                'Thuyết Tương Đối Rộng và Hẹp', 'Vật Chất Tối và Năng Lượng Tối', 'Hạt Higgs và Mô Hình Chuẩn',
                'Lý Thuyết Dây và Vũ Trụ Mười Chiều', 'Bí Ẩn Các Ngôi Sao Neutron', 'Hành Trình Tìm Kiếm Sự Sống Ngoài Trái Đất',
                'Vật Lý Thiên Văn Thực Nghiệm', 'Lịch Sử Khám Phá Không Gian', 'Sóng Hấp Dẫn và Kính Thiên Văn James Webb',
                'Nghịch Lý Thời Gian và Lỗ Sâu', 'Bức Xạ Tàn Dư Vũ Trụ (CMB)', 'Nhiệt Động Lực Học Vũ Trụ'
            ],
            'Lịch sử Thế giới, Chiến lược & Quân sự' => [
                'Binh Pháp Tôn Tử và 36 Kế', 'Lược Sử Đế Chế La Mã Hùng Cường', 'Đại Chiến Thế Giới Lần Thứ Hai',
                'Các Nền Văn Minh Cổ Đại Lưỡng Hà', 'Khảo Cổ Học Ai Cập và Kim Tự Tháp', 'Chiến Lược Địa Chính Trị Toàn Cầu',
                'Đại Việt Sử Ký và Nghệ Thuật Dựng Nước', 'Chiến Lược Quân Sự Của Napoleon', 'Con Đường Tơ Lụa Qua Các Triều Đại',
                'Cách Mạng Công Nghiệp và Sự Trỗi Dậy Của Các Cường Quốc', 'Chiến Tranh Lạnh và Trật Tự Hai Cực',
                'Đế Chế Mông Cổ và Kỵ Binh Thảo Nguyên', 'Lịch Sử Phong Kiến Đông Á', 'Hải Chiến và Quyền Lực Biển'
            ],
            'Triết học Đông Tây & Nhân sinh quan' => [
                'Chủ Nghĩa Khắc Kỷ (Stoicism) Đời Thường', 'Đạo Đức Kinh và Triết Lý Lão Trang', 'Cộng Hòa và Tư Tưởng Plato',
                'Ý Chí Quyền Lực Của Nietzsche', 'Hiện Sinh Là Gì Với Jean-Paul Sartre', 'Luận Ngữ và Đạo Làm Người Của Khổng Tử',
                'Phép Biện Chứng Hegel', 'Bàn Về Tự Do Cá Nhân', 'Triết Học Về Thời Gian và Hư Vô',
                'Đạo Trị Quốc Trong Triết Học Cổ Đại', 'Nghệ Thuật Sống Thanh Thản', 'Bản Thể Luận và Nhận Thức Luận',
                'Minh Triết Phương Đông', 'Tư Tưởng Phật Giáo Dưới Góc Nhìn Khoa Học', 'Luận Đạo Giữa Cuộc Đời Biến Động'
            ],
            'Tiểu thuyết Cyberpunk, Khoa học Viễn tưởng (Sci-Fi)' => [
                'Thành Phố Neon 2077', 'Kẻ Săn Người Máy (Blade Runner)', 'Ma Trận Thực Tại Ảo',
                'Trạm Không Gian Bỏ Hoang', 'Cuộc Chiến Vì Sao Xa', 'Trí Tuệ Nhân Tạo Nổi Loạn',
                'Kỷ Nguyên Cyborg và Người Nhân Tạo', 'Dữ Liệu Lượng Tử Tối Mật', 'Hành Tinh Băng Giá',
                'Đột Biến Di Truyền Thế Hệ Mới', 'Cổng Không Gian Vô Định', 'Bóng Đêm Trên Siêu Đô Thị',
                'Chiến Binh Ánh Sáng Vũ Trụ', 'Thuật Toán Thống Trị Trái Đất', 'Những Kẻ Sống Ngoài Vòng Mạng Lưới'
            ],
            'Truyện tranh Manga & Light Novel' => [
                'Chuyển Sinh Thành Dũng Sĩ Hư Cấu', 'Thợ Săn Hầm Ngục Cấp SSS', 'Học Viện Ma Thuật Tối Thượng',
                'Truyền Thuyết Kiếm Sĩ Bóng Đêm', 'Nhật Ký Xứ Sở Phù Thủy', 'Vương Triều Huyễn Tưởng',
                'Cuộc Chiến Giữa Các Bang Hội', 'Kẻ Độc Hành Vượt Qua Giới Hạn', 'Học Thuyết Giả Kim Thuật Biến Ảo',
                'Kỵ Sĩ Rồng và Nàng Công Chúa', 'Hành Trình Trở Thành Huyền Thoại', 'Đấu Trường Sinh Tử Tân Thế Giới',
                'Sức Mạnh Triệu Hồi Linh Hồn', 'Bí Mật Của Vùng Đất Cấm', 'Khởi Đầu Lại Từ Điểm Số Không'
            ],
            'Y học, Sức khỏe & Dinh dưỡng' => [
                'Khoa Học Giấc Ngủ và Phục Hồi Thần Kinh', 'Bí Quyết Kéo Dài Tuổi Thọ Tế Bào', 'Tăng Cường Miễn Dịch Tự Nhiên',
                'Hơi Thở và Nghệ Thuật Dưỡng Sinh', 'Chế Độ Dinh Dưỡng Khoa Học Tối Ưu', 'Giải Phẫu Sinh Lý Học Ứng Dụng',
                'Hệ Vi Sinh Đường Ruột và Sức Khỏe', 'Phòng Ngừa Bệnh Tim Mạch và Đột Quỵ', 'Y Học Não Bộ và Trí Nhớ Siêu Phàm',
                'Thanh Lọc Cơ Thể Dưới Góc Nhìn Khoa Học', 'Cẩm Nang Sơ Cấp Cứu Thực Hành', 'Liệu Pháp Vận Động Chữa Lành',
                'Sức Khỏe Tinh Thần Trong Thời Đại Kỹ Thuật Số', 'Khoa Học Về Nội Tiết Tố', 'Cân Bằng Sinh Học và Lối Sống Lành Mạnh'
            ],
        ];

        // Mảng tiền tố & hậu tố đa dạng tạo 1.000 đầu sách độc nhất
        $prefixes = [
            'Cẩm Nang', 'Giáo Trình', 'Nghệ Thuật', 'Bậc Thầy', 'Tự Học', 
            'Khám Phá', 'Giải Mã', 'Nguyên Lý', 'Lược Sử', 'Bí Quyết', 
            'Tuyệt Kỹ', 'Nền Tảng', 'Phân Tích', 'Toàn Thư', 'Bản Đồ', 
            'Kiến Trúc', 'Thực Chiến', 'Cốt Lõi', 'Hướng Dẫn', 'Chiến Lược',
            'Bí Thuật', 'Đột Phá', 'Thực Hành', 'Bách Khoa', 'Sổ Tay'
        ];

        $suffixes = [
            'Từ Cơ Bản Đến Nâng Cao', 'Toàn Tập', 'Trong Kỷ Nguyên Số', 'Dành Cho Chuyên Gia', 
            'Ứng Dụng Thực Tiễn', 'Hiện Đại', 'Thế Kỷ 21', 'Dưới Góc Nhìn Đa Chiều', 
            'Đột Phá và Thực Chiến', 'Chuyên Sâu', 'Thực Hành Ứng Dụng', 'Mới Nhất', 
            'Bỏ Túi', 'Tập 1: Khởi Đầu', 'Tập 2: Đỉnh Cao', 'Bản Đặc Biệt', 
            'Tái Bản Lần Thứ 3', 'Phân Tích Chuyên Sâu', 'Chiến Lược Dài Hạn', 'Cẩm Nang Toàn Diện'
        ];

        // Pool 40+ tác giả danh tiếng quốc tế & Việt Nam
        $authors = [
            'Robert C. Martin', 'Martin Fowler', 'Andrew Hunt', 'Erich Gamma', 'Linus Torvalds', 
            'Stuart Russell', 'Peter Norvig', 'Ian Goodfellow', 'Aurélien Géron', 'Donald Knuth',
            'Kevin Mitnick', 'Bruce Schneier', 'Gene Kim', 'Chris Sanders', 'Georgia Weidman',
            'Stephen Hawking', 'Carl Sagan', 'Neil deGrasse Tyson', 'Richard Feynman', 'Michio Kaku',
            'Yuval Noah Harari', 'Jared Diamond', 'Peter Frankopan', 'Will Durant', 'Tôn Tử (Sun Tzu)',
            'Marcus Aurelius', 'Seneca', 'Epictetus', 'Plato', 'Aristotle', 
            'Friedrich Nietzsche', 'Jean-Paul Sartre', 'Lão Tử', 'Khổng Tử', 'Trang Tử',
            'James Clear', 'Daniel Kahneman', 'Dale Carnegie', 'Stephen R. Covey', 'Cal Newport',
            'Peter Thiel', 'Eric Ries', 'Jim Collins', 'Morgan Housel', 'Warren Buffett',
            'Philip K. Dick', 'William Gibson', 'Isaac Asimov', 'Frank Herbert', 'Arthur C. Clarke',
            'Matthew Walker', 'James Nestor', 'David Sinclair', 'Peter Attia', 'Atul Gawande',
            'Nguyễn Du', 'Nam Cao', 'Vũ Trọng Phụng', 'Nguyễn Nhật Ánh', 'Thích Nhất Hạnh'
        ];
        $totalAuthors = count($authors);

        // 4. SINH CHÍNH XÁC 1.000 CUỐN SÁCH KHÔNG TRÙNG LẶP
        $targetTotal = 1000;
        $generatedBooks = [];
        $usedTitles = [];
        $now = Carbon::now();

        // Lấy danh sách tiêu đề sách đã có sẵn trong DB để không bị trùng
        $existingTitles = Book::pluck('title')->flip()->toArray();

        $bookCounter = 0;
        while ($bookCounter < $targetTotal) {
            // Luân phiên phân bổ đều qua 12 thể loại
            $catIndex = $bookCounter % $totalCategories;
            $catName = $categoryNames[$catIndex];
            $catModel = $categories[$catName];
            $topics = $categoryTopics[$catName];

            $prefix = $prefixes[array_rand($prefixes)];
            $topic = $topics[array_rand($topics)];
            $suffix = $suffixes[array_rand($suffixes)];
            $author = $authors[$bookCounter % $totalAuthors];

            // Cấu trúc tiêu đề sách đa dạng
            $variant = $bookCounter % 4;
            if ($variant === 0) {
                $title = "{$prefix} {$topic}";
            } elseif ($variant === 1) {
                $title = "{$topic}: {$suffix}";
            } elseif ($variant === 2) {
                $title = "{$prefix} {$topic} - {$suffix}";
            } else {
                $title = "{$topic} ({$prefix})";
            }

            // Đảm bảo tính độc nhất 100% của tiêu đề
            if (isset($usedTitles[$title]) || isset($existingTitles[$title])) {
                $title .= " [Tập " . (($bookCounter % 5) + 1) . "]";
            }
            if (isset($usedTitles[$title]) || isset($existingTitles[$title])) {
                $title .= " (Bản #" . ($bookCounter + 1) . ")";
            }

            $usedTitles[$title] = true;

            // Ảnh bìa bốc ngẫu nhiên từ Cover Pool chất lượng cao
            $coverImage = $coverPool[$bookCounter % $totalCovers];

            // Tồn kho: Cứ 20 cuốn thì có 1 cuốn hết hàng (quantity = 0) để test trạng thái, còn lại 3-15 cuốn
            $quantity = ($bookCounter % 20 === 0) ? 0 : rand(3, 15);

            $description = "Tác phẩm xuất sắc thuộc chủ đề {$catName}, cung cấp kiến thức nền tảng vững chắc và định hướng tư duy ứng dụng sâu sắc dành cho độc giả yêu tri thức.";

            $generatedBooks[] = [
                'category_id' => $catModel->id,
                'title' => $title,
                'author' => $author,
                'cover_image' => $coverImage,
                'quantity' => $quantity,
                'description' => $description,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $bookCounter++;
        }

        // 5. TỐI ƯU HIỆU NĂNG: BATCH INSERT THEO TỪNG CHUNK 200 BẢN GHI
        $this->command->info("⚡ Đang chèn {$targetTotal} cuốn sách vào Database theo các chunk 200 bản ghi...");

        $chunks = array_chunk($generatedBooks, 200);
        foreach ($chunks as $idx => $chunk) {
            Book::insert($chunk);
            $chunkNumber = $idx + 1;
            $insertedSoFar = min($targetTotal, $chunkNumber * 200);
            $this->command->line("  ✓ Đã nạp khối {$chunkNumber}/" . count($chunks) . " (Tổng cộng: {$insertedSoFar}/{$targetTotal} cuốn)");
        }

        $this->command->info("🎉 HOÀN THÀNH: Đã nạp thành công đúng {$targetTotal} cuốn sách vào hệ thống Thư viện TIỂU ĐỘI 2P!");
    }
}
