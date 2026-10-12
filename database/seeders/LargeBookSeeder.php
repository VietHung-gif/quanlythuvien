<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LargeBookSeeder extends Seeder
{
    /**
     * Nạp từ 5.000 đến 10.000 bản ghi sách chất lượng cao vào bảng books.
     * Áp dụng kỹ thuật Batch Insert (Chunk 500 records) và MySQL Transaction.
     */
    public function run(): void
    {
        // Tăng giới hạn bộ nhớ và thời gian thực thi cho tác vụ Data Engineering lớn
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $this->command->info('[START] [TIỂU ĐỘI 2P] BẮT ĐẦU NẠP DỮ LIỆU SÁCH QUY MÔ LỚN (5.000 - 10.000 BẢN GHI)...');

        // 1. ĐẢM BẢO DANH SÁCH 12 CHUYÊN MỤC HỌC THUẬT CHUẨN
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
            'Y học, Sức khỏe & Dinh dưỡng' => 'Khoa học về giấc ngủ, hô hấp, hệ miễn dịch tự nhiên, chế độ dinh dưỡng tối ưu và y học phòng ngừa trường thọ.',
            'Nghệ thuật, Kiến trúc & Thiết kế' => 'Lịch sử hội họa thế giới, tinh hoa kiến trúc cổ điển đến hiện đại, tư duy thẩm mỹ và nguyên lý thiết kế đồ họa.',
        ];

        $categories = [];
        foreach ($categoriesDefinition as $catName => $catDesc) {
            $categories[$catName] = Category::firstOrCreate(
                ['name' => $catName],
                ['description' => $catDesc]
            );
        }

        // Bổ sung thêm bất kỳ category nào đã có sẵn trong database nếu chưa có trong mảng trên
        $existingCategories = Category::all();
        foreach ($existingCategories as $existingCat) {
            if (! isset($categories[$existingCat->name])) {
                $categories[$existingCat->name] = $existingCat;
            }
        }

        $categoryList = array_values($categories);
        $totalCategories = count($categoryList);

        // 2. KHO ẢNH BÌA CHUYÊN ĐỀ THEO TỪNG THỂ LOẠI (100% LINK THẬT, CHUẨN TỈ LỆ 3:4 & 2:3)
        $categoryCovers = [
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
                'https://covers.openlibrary.org/b/id/8739161-L.jpg',
                'https://covers.openlibrary.org/b/id/8739162-L.jpg',
                'https://covers.openlibrary.org/b/id/10574883-L.jpg',
                'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=600&q=80',
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
                'https://images.unsplash.com/photo-1499209974431-9dddcece7f88?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1506126613408-eca07ce68773?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1518609878373-06d740f60d8b?auto=format&fit=crop&w=600&q=80',
                'https://covers.openlibrary.org/b/id/8231991-L.jpg',
                'https://covers.openlibrary.org/b/id/12648719-L.jpg',
                'https://covers.openlibrary.org/b/id/12716388-L.jpg',
            ],
            'Kinh tế số, Tài chính & Đầu tư' => [
                'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=600&q=80',
                'https://covers.openlibrary.org/b/id/8231992-L.jpg',
                'https://covers.openlibrary.org/b/id/8278278-L.jpg',
            ],
            'Khoa học Vũ trụ & Vật lý Lượng tử' => [
                'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1446776811953-b23d57bd21aa?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1506703719100-a0f3a48c0f86?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1462331940025-496dfbfc7564?auto=format&fit=crop&w=600&q=80',
                'https://covers.openlibrary.org/b/id/8231993-L.jpg',
                'https://covers.openlibrary.org/b/id/12833522-L.jpg',
            ],
            'Lịch sử Thế giới, Chiến lược & Quân sự' => [
                'https://images.unsplash.com/photo-1461360370896-922624d12aa1?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1505664194779-8beaceb93744?auto=format&fit=crop&w=600&q=80',
                'https://covers.openlibrary.org/b/id/8739164-L.jpg',
                'https://covers.openlibrary.org/b/id/8739165-L.jpg',
                'https://covers.openlibrary.org/b/id/8231995-L.jpg',
            ],
            'Triết học Đông Tây & Nhân sinh quan' => [
                'https://images.unsplash.com/photo-1532012164546-f432f2e3777a?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1506880018603-83d5b814b5a6?auto=format&fit=crop&w=600&q=80',
                'https://covers.openlibrary.org/b/id/10574884-L.jpg',
                'https://covers.openlibrary.org/b/id/12832570-L.jpg',
            ],
            'Tiểu thuyết Cyberpunk, Khoa học Viễn tưởng (Sci-Fi)' => [
                'https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1508739773434-c26b3d09e071?auto=format&fit=crop&w=600&q=80',
                'https://covers.openlibrary.org/b/id/12539704-L.jpg',
                'https://covers.openlibrary.org/b/id/12539706-L.jpg',
            ],
            'Y học, Sức khỏe & Dinh dưỡng' => [
                'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1505751172876-fa1923c5c528?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1532938911079-1b06ac7ceec7?auto=format&fit=crop&w=600&q=80',
                'https://covers.openlibrary.org/b/id/10574885-L.jpg',
                'https://covers.openlibrary.org/b/id/10574886-L.jpg',
            ],
            'Nghệ thuật, Kiến trúc & Thiết kế' => [
                'https://images.unsplash.com/photo-1513364776144-60967b0f800f?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1500462918059-b1a0cb512f1d?auto=format&fit=crop&w=600&q=80',
                'https://covers.openlibrary.org/b/id/10582964-L.jpg',
                'https://covers.openlibrary.org/b/id/10543227-L.jpg',
            ],
        ];

        // Dự phòng cover chung chất lượng cao
        $fallbackCovers = [
            'https://covers.openlibrary.org/b/id/10522591-L.jpg',
            'https://covers.openlibrary.org/b/id/12833521-L.jpg',
            'https://covers.openlibrary.org/b/id/10582964-L.jpg',
            'https://covers.openlibrary.org/b/id/10543227-L.jpg',
            'https://covers.openlibrary.org/b/id/12539710-L.jpg',
            'https://covers.openlibrary.org/b/id/8231856-L.jpg',
            'https://covers.openlibrary.org/b/id/12843513-L.jpg',
            'https://covers.openlibrary.org/b/id/8302521-L.jpg',
            'https://covers.openlibrary.org/b/id/8235113-L.jpg',
            'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&w=600&q=80',
        ];

        // 3. TỪ VỰNG CHỦ ĐỀ CHUYÊN NGÀNH
        $categoryTopics = [
            'Văn học & Tiểu thuyết kinh điển' => [
                'Tiếng Gọi Nơi Hoang Dã', 'Đồi Gió Hú', 'Đêm Trắng Thơ Mộng', 'Chiếc Lá Cuối Cùng',
                'Chuông Nguyện Hồn Ai', 'Ông Già và Biển Cả', 'Người Xa Lạ', 'Nắng Tháng Tám',
                'Suối Nguồn Tư Tưởng', 'Bắt Trẻ Đồng Xanh', 'Gia Đình Karamazov', 'Bá Tước Monte Cristo',
                'Kiêu Hãnh và Định Kiến', 'Vụ Án Bí Ẩn', 'Thần Thoại Sisyphus', 'Cuộc Đời Của Pi',
                'Đại Gia Gatsby', 'Gió Qua Rặng Liễu', 'Tiếng Chim Hót Trong Bụi Mận Gai', 'Túp Lều Bác Tom',
                'Tội Ác Và Hình Phạt', 'Chiến Tranh Và Hòa Bình', 'Những Người Khốn Khổ', 'Nhà Thờ Đức Bà Paris',
                'Trăm Năm Cô Đơn', 'Hoàng Tử Bé', 'Biên Niên Sử Narnia', 'Bố Già', 'Chúa Tể Những Chiếc Nhẫn',
            ],
            'Trí tuệ nhân tạo (AI) & Khoa học Dữ liệu' => [
                'Mạng Nơ-ron Tích Chập (CNN)', 'Transformer và Kiến Trúc LLM', 'Học Tăng Cường (Reinforcement Learning)',
                'Thị Giác Máy Tính Hiện Đại', 'Xử Lý Ngôn Ngữ Tự Nhiên (NLP)', 'AI Tạo Sinh (Generative AI)',
                'Khai Phá Dữ Liệu Lớn (Big Data Mining)', 'Kỹ Nghệ Prompt Chuyên Nghiệp', 'Học Máy Với Scikit-Learn',
                'Mô Hình Dự Đoán Chuỗi Thời Gian', 'Học Sâu Với PyTorch Thực Chiến', 'Đạo Đức Và Pháp Lý Trong AI',
                'Trí Tuệ Nhân Tạo Phổ Quát (AGI)', 'Mạng Sinh Đối Kháng (GAN)', 'Tối Ưu Hóa Hyperparameter',
                'Hệ Thống Khuyến Nghị Thông Minh', 'Tác Nhân Tự Trị (Autonomous Agents)', 'Xử Lý Tín Hiệu Âm Thanh Bằng AI',
            ],
            'Lập trình & Kỹ thuật Phần mềm' => [
                'Clean Code và Mã Sạch', 'Kiến Trúc Microservices Thực Chiến', 'Mẫu Thiết Kế Hướng Đối Tượng (GoF)',
                'Phát Triển Hướng Tên Miền (DDD)', 'Lập Trình Bất Đồng Bộ Hiện Đại', 'DevOps và Tự Động Hóa CI/CD',
                'Thiết Kế API RESTful & GraphQL', 'Tối Ưu Hiệu Năng Cơ Sở Dữ Liệu', 'Cấu Trúc Dữ Liệu & Giải Thuật',
                'Hệ Điều Hành Linux Dành Cho Kỹ Sư', 'Docker và Kubernetes Toàn Tập', 'Lập Trình Web Full-Stack',
                'Kiến Trúc Hệ Thống Phân Tán', 'Kiểm Thử Phần Mềm Tự Động (TDD)', 'Bảo Trì và Tái Cấu Trúc Mã Nguồn',
                'Kỹ Nghệ Lập Trình Rust Hiện Đại', 'Ngôn Ngữ Go và Xử Lý Đồng Thời', 'Hệ Thống Message Broker Kafka',
            ],
            'An toàn Thông tin & Hacker Mũ Trắng' => [
                'Kiểm Thử Xâm Nhập (Penetration Testing)', 'Kỹ Thuật Dịch Ngược Mã Độc (Reverse Engineering)',
                'Khai Thác Lỗ Hổng Ứng Dụng Web', 'Mật Mã Học Ứng Dụng Trong Kỷ Nguyên Số', 'Phòng Thủ Trung Tâm SOC',
                'An Ninh Điện Toán Đám Mây', 'Phân Tích Phần Mềm Độc Hại (Malware Analysis)', 'Kỹ Nghệ Xã Hội (Social Engineering)',
                'Điều Tra Dấu Vết Kỹ Thuật Số (Digital Forensics)', 'Bảo Vệ Hạ Tầng Trọng Yếu', 'Tường Lửa và Hệ Thống IDS/IPS',
                'Phát Hiện và Ứng Cứu Sự Cố An Ninh Mạng', 'Chiến Lược Zero Trust Toàn Diện', 'Lập Trình An Toàn (Secure Coding)',
                'Săn Lùng Lỗ Hổng Bug Bounty', 'Giao Thức Mạng và Đánh Chặn Lưu Lượng', 'Bảo Mật Cơ Sở Dữ Liệu Doanh Nghiệp',
            ],
            'Tâm lý học & Phát triển Bản thân' => [
                'Thói Quen Nguyên Tử và Kỷ Luật', 'Trí Tuệ Cảm Xúc (EQ) Ứng Dụng', 'Tâm Lý Học Đám Đông và Dư Luận',
                'Nghệ Thuật Tập Trung Sâu (Deep Work)', 'Vượt Qua Vùng An Toàn', 'Khắc Phục Thói Quen Trì Hoãn',
                'Thao Túng Tâm Lý Trong Đàm Phán', 'Tư Duy Phản Biện Sắc Bén', 'Xây Dựng Sự Tự Tin Tuyệt Đối',
                'Tâm Lý Học Tích Cực', 'Nghệ Thuật Thấu Cảm', 'Quản Trị Năng Lượng Cá Nhân',
                'Định Vị Bản Thân Trong Kỷ Nguyên Số', 'Khai Sáng Tiềm Năng Vô Hạn', 'Liệu Pháp Nhận Thức Hành Vi (CBT)',
                'Tâm Lý Học Về Hạnh Phúc', 'Nghệ Thuật Giao Tiếp Đỉnh Cao', 'Nghĩ Giàu Và Làm Giàu',
            ],
            'Kinh tế số, Tài chính & Đầu tư' => [
                'Chiến Lược Đầu Tư Giá Trị', 'Tâm Lý Học Về Tiền và Thị Trường', 'Khởi Nghiệp Tinh Gọn (Lean Startup)',
                'Quản Trị Rủi Ro Danh Mục Đầu Tư', 'Kinh Tế Học Hành Vi Ứng Dụng', 'Phân Tích Kỹ Thuật Thị Trường Tài Chính',
                'Tài Chính Doanh Nghiệp Toàn Tập', 'Mô Hình Kinh Doanh Trong Nền Kinh Tế Số', 'Chiến Lược Đại Dương Xanh',
                'Xây Dựng Hệ Sinh Thái Khởi Nghiệp', 'Kinh Tế Vĩ Mô Dành Cho Nhà Đầu Tư', 'Định Giá Doanh Nghiệp',
                'Thương Mại Điện Tử & Bán Lẻ Đa Kênh', 'Chuỗi Cung Ứng Toàn Cầu', 'Đàm Phán Thương Lượng Đỉnh Cao',
                'Dòng Tiền Thông Minh', 'Tự Do Tài Chính Tuổi 30', 'Chiến Lược Cạnh Tranh Cốt Lõi',
            ],
            'Khoa học Vũ trụ & Vật lý Lượng tử' => [
                'Lỗ Đen và Không Thời Gian Uốn Cong', 'Cơ Học Lượng Tử Dành Cho Mọi Người', 'Bản Đồ Vũ Trụ và Vụ Nổ Big Bang',
                'Thuyết Tương Đối Rộng và Hẹp', 'Vật Chất Tối và Năng Lượng Tối', 'Hạt Higgs và Mô Hình Chuẩn',
                'Lý Thuyết Dây và Vũ Trụ Mười Chiều', 'Bí Ẩn Các Ngôi Sao Neutron', 'Hành Trình Tìm Kiếm Sự Sống Ngoài Trái Đất',
                'Vật Lý Thiên Văn Thực Nghiệm', 'Lịch Sử Khám Phá Không Gian', 'Sóng Hấp Dẫn và Kính Thiên Văn James Webb',
                'Nghịch Lý Thời Gian và Lỗ Sâu', 'Bức Xạ Tàn Dư Vũ Trụ (CMB)', 'Nhiệt Động Lực Học Vũ Trụ',
            ],
            'Lịch sử Thế giới, Chiến lược & Quân sự' => [
                'Binh Pháp Tôn Tử và 36 Kế', 'Lược Sử Đế Chế La Mã Hùng Cường', 'Đại Chiến Thế Giới Lần Thứ Hai',
                'Các Nền Văn Minh Cổ Đại Lưỡng Hà', 'Khảo Cổ Học Ai Cập và Kim Tự Tháp', 'Chiến Lược Địa Chính Trị Toàn Cầu',
                'Chiến Tranh Lạnh và Trật Tự Thế Giới Mới', 'Hải Quân và Quyền Lực Biển Khơi', 'Chiến Tranh Thông Tin Hiện Đại',
                'Những Vị Tướng Lừng Danh Trong Lịch Sử', 'Lịch Sử Ngoại Giao Hiện Đại', 'Nghệ Thuật Đánh Lạc Hướng Quân Sự',
                'Đại Việt Sử Ký Toàn Thư', 'Chiến Thắng Bạch Đằng Giang', 'Khởi Nghĩa Lam Sơn',
            ],
            'Triết học Đông Tây & Nhân sinh quan' => [
                'Chủ Nghĩa Khắc Kỷ (Stoicism)', 'Triết Học Hy Lạp và Socrates', 'Đạo Đức Học Nicomachean',
                'Đạo Đức Kinh và Triết Lý Vô Vi', 'Nam Hoa Kinh và Tư Tưởng Trang Tử', 'Tứ Thư Ngũ Kinh',
                'Hiện Sinh và Bản Thể Con Người', 'Ý Chí Quyền Lực và Siêu Nhân', 'Phê Phán Lý Tính Thuần Túy',
                'Nghệ Thuật Sống An Nhiên', 'Triết Học Tâm Trí Hiện Đại', 'Triết Lý Thiền Định Ứng Dụng',
                'Bản Chất Của Thực Tại', 'Đạo Trị Quốc và Pháp Gia', 'Luận Ngữ Khổng Tử',
            ],
            'Tiểu thuyết Cyberpunk, Khoa học Viễn tưởng (Sci-Fi)' => [
                'Thành Phố Neon và Linh Hồn Điện Tử', 'Ký Ức Nhân Tạo Năm 2099', 'Hành Trình Vượt Vành Đai Tiểu Hành Tinh',
                'Người Máy Sinh Học Trỗi Dậy', 'Thực Tại Ảo và Ma Trận Thần Kinh', 'Kỷ Nguyên Hậu Tận Thế',
                'Chiến Hạm Không Gian Liên Sao', 'Đột Biến Di Truyền và Loài Người Mới', 'Hệ Thống Giám Sát Toàn Cầu',
                'Kẻ Săn Tiền Thưởng Vùng Ngoại Ô', 'Thức Tỉnh Trong Giấc Ngủ Đông', 'Cuộc Chiến Cyberpunk Đẫm Máu',
                'Đế Chế Thiên Hà Đổ Vỡ', 'Hành Tinh Cát Bí Ẩn', 'Du Hành Thời Gian Nghịch Lý',
            ],
            'Y học, Sức khỏe & Dinh dưỡng' => [
                'Bí Mật Của Giấc Ngủ Sinh Học', 'Khoa Học Về Hơi Thở Đúng Cách', 'Hệ Vi Sinh Đường Ruột và Miễn Dịch',
                'Dinh Dưỡng Học Phục Hồi Cơ Thể', 'Giải Mã ADN và Y Học Cá Nhân Hóa', 'Chữa Lành Não Bộ Sau Căng Thẳng',
                'Nguyên Lý Vận Động và Cơ Xương Khớp', 'Kéo Dài Tuổi Thọ Tế Bào (Longevity)', 'Phòng Ngừa Bệnh Tim Mạch',
                'Cơ Chế Kháng Viêm Tự Nhiên', 'Sơ Cứu Ban Đầu và Cấp Cứu Y Tế', 'Dược Lý Học Lâm Sàng Cơ Bản',
                'Y Học Cổ Truyền Đông Y', 'Khoa Học Về Nước và Sự Sống', 'Chăm Sóc Sức Khỏe Toàn Diện',
            ],
            'Nghệ thuật, Kiến trúc & Thiết kế' => [
                'Lịch Sử Mỹ Thuật Thế Giới', 'Nguyên Lý Thị Giác và Bố Cục', 'Kiến Trúc Gothic và Phục Hưng',
                'Nghệ Thuật Tối Giản Minimalism', 'Lý Thuyết Màu Sắc Ứng Dụng', 'Kiến Trúc Xanh Bền Vững',
                'Thiết Kế Trải Nghiệm Người Dùng (UI/UX)', 'Bản Vẽ Kỹ Thuật và Không Gian', 'Nhiếp Ảnh Đường Phố Đương Đại',
                'Tinh Hoa Gốm Sứ và Điêu Khắc', 'Thời Trang và Phong Cách Thế Kỷ 20', 'Typography và Nghệ Thuật Chữ',
            ],
        ];

        // 4. DANH SÁCH TÁC GIẢ THEO CHUYÊN MÔN
        $categoryAuthors = [
            'Văn học & Tiểu thuyết kinh điển' => [
                'Victor Hugo', 'Leo Tolstoy', 'Fyodor Dostoevsky', 'Ernest Hemingway', 'Franz Kafka',
                'George Orwell', 'Albert Camus', 'Haruki Murakami', 'Nam Cao', 'Vũ Trọng Phụng',
                'Nguyễn Huy Thiệp', 'Bảo Ninh', 'Nguyễn Du', 'Gabriel García Márquez', 'Hermann Hesse',
            ],
            'Trí tuệ nhân tạo (AI) & Khoa học Dữ liệu' => [
                'Andrew Ng', 'Geoffrey Hinton', 'Yann LeCun', 'Yoshua Bengio', 'Stuart Russell',
                'Peter Norvig', 'Ian Goodfellow', 'François Chollet', 'Max Tegmark', 'Fei-Fei Li', 'Kai-Fu Lee',
            ],
            'Lập trình & Kỹ thuật Phần mềm' => [
                'Robert C. Martin', 'Martin Fowler', 'Kent Beck', 'Eric Evans', 'Linus Torvalds',
                'Donald Knuth', 'Bjarne Stroustrup', 'Joshua Bloch', 'Guido van Rossum', 'Chris Lattner',
            ],
            'An toàn Thông tin & Hacker Mũ Trắng' => [
                'Kevin Mitnick', 'Bruce Schneier', 'Edward Snowden', 'Mikko Hypponen', 'Chris Sanders',
                'Georgia Weidman', 'Peter Kim', 'Daniel Miessler', 'Jon Erickson', 'David Kennedy',
            ],
            'Tâm lý học & Phát triển Bản thân' => [
                'Daniel Kahneman', 'James Clear', 'Dale Carnegie', 'Stephen Covey', 'Carol Dweck',
                'Viktor Frankl', 'Jordan Peterson', 'Malcolm Gladwell', 'Sigmund Freud', 'Carl Jung',
            ],
            'Kinh tế số, Tài chính & Đầu tư' => [
                'Warren Buffett', 'Benjamin Graham', 'Ray Dalio', 'Peter Lynch', 'Adam Smith',
                'John Maynard Keynes', 'Thomas Piketty', 'Nassim Nicholas Taleb', 'Michael Lewis', 'Robert Kiyosaki',
            ],
            'Khoa học Vũ trụ & Vật lý Lượng tử' => [
                'Stephen Hawking', 'Albert Einstein', 'Carl Sagan', 'Neil deGrasse Tyson', 'Richard Feynman',
                'Kip Thorne', 'Michio Kaku', 'Brian Greene', 'Carlo Rovelli', 'Max Planck',
            ],
            'Lịch sử Thế giới, Chiến lược & Quân sự' => [
                'Tôn Tử', 'Trần Hưng Đạo', 'Carl von Clausewitz', 'Yuval Noah Harari', 'Will Durant',
                'Barbara Tuchman', 'Antony Beevor', 'John Keegan', 'Niall Ferguson', 'Sun Tzu',
            ],
            'Triết học Đông Tây & Nhân sinh quan' => [
                'Socrates', 'Plato', 'Aristotle', 'Marcus Aurelius', 'Seneca', 'Friedrich Nietzsche',
                'Immanuel Kant', 'Lão Tử', 'Trang Tử', 'Khổng Tử', 'Arthur Schopenhauer',
            ],
            'Tiểu thuyết Cyberpunk, Khoa học Viễn tưởng (Sci-Fi)' => [
                'Philip K. Dick', 'William Gibson', 'Isaac Asimov', 'Arthur C. Clarke', 'Frank Herbert',
                'Neal Stephenson', 'Cixin Liu', 'Ted Chiang', 'Ray Bradbury', 'H.G. Wells',
            ],
            'Y học, Sức khỏe & Dinh dưỡng' => [
                'Hippocrates', 'Matthew Walker', 'Peter Attia', 'Atul Gawande', 'Siddhartha Mukherjee',
                'David Sinclair', 'Michael Pollan', 'Oliver Sacks', 'Paul Kalanithi', 'Tuệ Tĩnh',
            ],
            'Nghệ thuật, Kiến trúc & Thiết kế' => [
                'Leonardo da Vinci', 'Michelangelo', 'Le Corbusier', 'Zaha Hadid', 'Don Norman',
                'Walter Gropius', 'Frank Lloyd Wright', 'John Ruskin', 'Paul Rand', 'Josef Albers',
            ],
        ];

        // 5. TIỀN TỐ, HẬU TỐ VÀ NXB
        $prefixes = [
            'Giáo Trình Chuẩn', 'Nghiên Cứu Chuyên Sâu', 'Bản Đồ Tri Thức', 'Cẩm Nang Toàn Tập',
            'Nguyên Lý Nền Tảng', 'Nghệ Thuật & Tư Duy', 'Khảo Cứu Thực Nghiệm', 'Bài Học Thực Chiến',
            'Chiến Lược Đột Phá', 'Tinh Hoa Lý Thuyết', 'Sổ Tay Kỹ Sư', 'Cẩm Nang Ứng Dụng',
            'Dẫn Luận Hiện Đại', 'Phân Tích Đa Chiều', 'Tuyển Tập Tác Phẩm', 'Hành Trình Khám Phá',
        ];

        $suffixes = [
            'Từ Cơ Bản Đến Nâng Cao', 'Dành Cho Chuyên Gia', 'Trong Kỷ Nguyên Số 4.0', 'Lý Thuyết & Thực Hành',
            'Phương Pháp Luận Mới', 'Góc Nhìn Thực Chiến', 'Định Hình Tương Lai', 'Toàn Tập',
            'Ấn Bản Tái Bản Có Bổ Sung', 'Kinh Nghiệm Thực Tiễn', 'Ứng Dụng Thực Tế', 'Chiêm Nghiệm Mới',
        ];

        $publishers = [
            'NXB Trẻ', 'NXB Kim Đồng', 'NXB Tri Thức', 'NXB Tổng Hợp TP.HCM',
            'NXB Khoa Học Kỹ Thuật', 'NXB Chính Trị Quốc Gia Sự Thật', 'NXB Thế Giới',
            'NXB Lao Động', 'NXB Văn Học', 'NXB Dân Trí', 'NXB Đại Học Quốc Gia',
        ];

        // 6. THIẾT LẬP THÔNG SỐ TỔNG SỐ LƯỢNG SÁCH CẦN SEED (6.000 BẢN GHI)
        $targetTotal = 6000;
        $booksPerCategory = (int) ceil($targetTotal / $totalCategories);
        $totalInserted = 0;
        $now = Carbon::now();

        $this->command->info("[INFO] Đang chuẩn bị tạo {$targetTotal} cuốn sách phân bổ trên {$totalCategories} thể loại (~{$booksPerCategory} cuốn/thể loại)...");

        // Sử dụng mảng kiểm tra trùng lặp tiêu đề
        $usedTitles = [];

        // BẮT BUỘC: Sử dụng Transaction và Batch Insert theo lô 500 bản ghi để tối ưu tốc độ và bộ nhớ
        DB::beginTransaction();

        try {
            $batch = [];
            $batchSize = 500;
            $globalCounter = 0;

            foreach ($categoryList as $catIdx => $category) {
                $catName = $category->name;

                // Lấy kho ảnh và từ vựng của category này (hoặc fallback nếu chưa cấu hình riêng)
                $covers = $categoryCovers[$catName] ?? $fallbackCovers;
                $topics = $categoryTopics[$catName] ?? [
                    'Chuyên Luận Nghiên Cứu', 'Nguyên Lý Cơ Bản', 'Ứng Dụng Hiện Đại',
                    'Định Hình Tương Lai', 'Kiến Thức Cốt Lõi', 'Bài Học Kinh Nghiệm',
                ];
                $authors = $categoryAuthors[$catName] ?? ['Ban Biên Soạn Tiểu Đội 2P', 'Viện Nghiên Cứu Tri Thức'];

                $totalCoversInCat = count($covers);
                $totalTopicsInCat = count($topics);
                $totalAuthorsInCat = count($authors);

                for ($i = 0; $i < $booksPerCategory; $i++) {
                    $globalCounter++;
                    if ($globalCounter > $targetTotal) {
                        break;
                    }

                    $prefix = $prefixes[$i % count($prefixes)];
                    $topic = $topics[$i % $totalTopicsInCat];
                    $suffix = $suffixes[$i % count($suffixes)];
                    $author = $authors[$i % $totalAuthorsInCat];
                    $publisher = $publishers[($i + $catIdx) % count($publishers)];
                    $publishYear = rand(1995, 2025);

                    // Tạo tiêu đề sách đa dạng phong cách học thuật
                    $pattern = $i % 4;
                    if ($pattern === 0) {
                        $title = "{$prefix}: {$topic}";
                    } elseif ($pattern === 1) {
                        $title = "{$topic} - {$suffix}";
                    } elseif ($pattern === 2) {
                        $title = "{$prefix} {$topic} ({$suffix})";
                    } else {
                        $title = "{$topic} Toàn Tập";
                    }

                    // Đảm bảo tính độc nhất 100% bằng cách gán tập/phần
                    $volumeIndex = (int) floor($i / $totalTopicsInCat) + 1;
                    if ($volumeIndex > 1) {
                        $title .= " [Tập {$volumeIndex}]";
                    }

                    if (isset($usedTitles[$title])) {
                        $title .= " (Bản In #{$globalCounter})";
                    }
                    $usedTitles[$title] = true;

                    // Ảnh bìa chính xác theo thể loại
                    $coverImage = $covers[$i % $totalCoversInCat];

                    // Số lượng tổng từ 5 đến 20 cuốn, số khả dụng từ 1 đến quantity
                    $quantity = rand(5, 20);
                    $availableCopies = rand(max(1, $quantity - 6), $quantity);

                    // Đoạn tóm tắt nội dung 2-3 câu giới thiệu súc tích
                    $description = "Tác phẩm '{$title}' của tác giả {$author} do {$publisher} xuất bản, là tài liệu học thuật giá trị thuộc chuyên ngành {$catName}. Cuốn sách cung cấp hệ thống kiến thức toàn diện, phân tích sâu sắc các nguyên lý cốt lõi và định hướng ứng dụng thực tiễn cao cho độc giả.";

                    $batch[] = [
                        'category_id' => $category->id,
                        'title' => $title,
                        'author' => $author,
                        'publisher' => $publisher,
                        'publish_year' => $publishYear,
                        'cover_image' => $coverImage,
                        'quantity' => $quantity,
                        'available_copies' => $availableCopies,
                        'description' => $description,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    // Khi đủ 500 bản ghi, chèn trực tiếp vào database và giải phóng bộ nhớ
                    if (count($batch) >= $batchSize) {
                        DB::table('books')->insert($batch);
                        $totalInserted += count($batch);
                        $this->command->line("  [OK] Đã nạp thành công {$totalInserted}/{$targetTotal} cuốn sách...");
                        $batch = []; // Giải phóng bộ nhớ RAM
                    }
                }
            }

            // Chèn số bản ghi còn lại (nếu có)
            if (! empty($batch)) {
                DB::table('books')->insert($batch);
                $totalInserted += count($batch);
                $batch = [];
            }

            DB::commit();

            $this->command->info("[SUCCESS] HOÀN THÀNH XUẤT SẮC: Đã nạp thành công tổng cộng {$totalInserted} cuốn sách mới vào hệ thống thư viện 'TIỂU ĐỘI 2P'!");

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error('[ERROR] CÓ LỖI XẢY RA TRONG QUÁ TRÌNH SEED DỮ LIỆU: '.$e->getMessage());
            throw $e;
        }
    }
}
