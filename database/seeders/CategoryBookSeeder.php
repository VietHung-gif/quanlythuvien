<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryBookSeeder extends Seeder
{
    /**
     * Bổ sung các thể loại chuyên ngành và các đầu sách tiêu biểu theo 5 nhóm ngành nghề:
     * 1. Công Nghệ Thông Tin & Kỹ Thuật Phần Mềm (IT & Software Engineering)
     * 2. Kinh Tế, Tài Chính & Marketing (Economics & Business)
     * 3. Y Tế & Chăm Sóc Sức Khỏe (Medicine & Healthcare)
     * 4. Thiết Kế Đồ Họa & Đa Phương Tiện (Design & Multimedia)
     * 5. Xây Dựng & Kiến Trúc (Architecture & Civil Engineering)
     */
    public function run(): void
    {
        $this->command->info('📚 [TIỂU ĐỘI 2P] BẮT ĐẦU NẠP CÁC ĐẦU SÁCH CHUYÊN NGÀNH TIÊU BIỂU...');

        $industries = [
            // =========================================================================
            // NGÀNH 1: CÔNG NGHỆ THÔNG TIN & KỸ THUẬT PHẦN MỀM
            // =========================================================================
            [
                'category' => [
                    'name' => 'Công Nghệ Thông Tin & Kỹ Thuật Phần Mềm',
                    'description' => 'Kho tàng tri thức về kỹ thuật lập trình, kiến trúc hệ thống dữ liệu lớn, quy trình Agile/DevOps và ứng dụng Trí tuệ Nhân tạo thực chiến.',
                ],
                'books' => [
                    [
                        'title' => 'Clean Code: A Handbook of Agile Software Craftsmanship',
                        'author' => 'Robert C. Martin',
                        'publisher' => 'Prentice Hall / NXB Thông Tin & Truyền Thông',
                        'publish_year' => 2008,
                        'description' => 'Cẩm nang kinh điển của Uncle Bob về triết lý viết mã sạch, dễ đọc, dễ bảo trì và mở rộng. Hướng dẫn chi tiết kỹ thuật đặt tên, tách hàm nhỏ, xử lý lỗi an toàn và tái cấu trúc mã nguồn chuyên nghiệp. [Giá bìa: 285.000 VNĐ]',
                        'quantity' => 12,
                        'available_copies' => 12,
                        'cover_image' => 'https://covers.openlibrary.org/b/id/12539710-L.jpg',
                    ],
                    [
                        'title' => 'Thiết Kế Hệ Thống Phân Tán (Designing Data-Intensive Applications)',
                        'author' => 'Martin Kleppmann',
                        'publisher' => 'O\'Reilly Media / NXB Dân Trí',
                        'publish_year' => 2017,
                        'description' => 'Cuốn sách "gối đầu giường" của các kỹ sư phần mềm cao cấp và kiến trúc sư hệ thống. Khám phá sâu về công nghệ cơ sở dữ liệu, mô hình nhân bản (Replication), phân mảnh (Partitioning), tính nhất quán (Consistency) và xử lý luồng sự kiện phân tán. [Giá bìa: 360.000 VNĐ]',
                        'quantity' => 10,
                        'available_copies' => 10,
                        'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781449373320-L.jpg',
                    ],
                    [
                        'title' => 'Lập Trình Web Hiện Đại Với Laravel & Vue.js',
                        'author' => 'Taylor Otwell & Evan You',
                        'publisher' => 'Packt Publishing / NXB Bách Khoa',
                        'publish_year' => 2023,
                        'description' => 'Giáo trình thực chiến xây dựng ứng dụng Web Single-Page (SPA) và Full-stack hiện đại với Laravel Framework phía Backend kết hợp cùng Vue.js Reactive phía Frontend. Tối ưu kiến trúc RESTful API, bảo mật và triển khai liên tục CI/CD. [Giá bìa: 220.000 VNĐ]',
                        'quantity' => 15,
                        'available_copies' => 14,
                        'cover_image' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=600&q=80',
                    ],
                    [
                        'title' => 'Nhập Môn Trí Tuệ Nhân Tạo & Học Máy (Machine Learning Yearning)',
                        'author' => 'Andrew Ng',
                        'publisher' => 'DeepLearning.AI / NXB Khoa Học & Kỹ Thuật',
                        'publish_year' => 2021,
                        'description' => 'Chiến lược và tư duy thực chiến xây dựng các dự án Học máy (Machine Learning) từ chuyên gia AI hàng đầu Andrew Ng. Phân tích phương pháp chẩn đoán lỗi mô hình, xây dựng tập dữ liệu huấn luyện và tối ưu hiệu suất mô hình thực tế. [Giá bìa: 250.000 VNĐ]',
                        'quantity' => 14,
                        'available_copies' => 13,
                        'cover_image' => 'https://covers.openlibrary.org/b/id/8739161-L.jpg',
                    ],
                ],
            ],

            // =========================================================================
            // NGÀNH 2: KINH TẾ, TÀI CHÍNH & MARKETING
            // =========================================================================
            [
                'category' => [
                    'name' => 'Kinh Tế, Tài Chính & Marketing',
                    'description' => 'Tài liệu quản trị kinh doanh, đầu tư chứng khoán giá trị, khởi nghiệp đổi mới sáng tạo, tâm lý học hành vi tài chính và chiến lược marketing toàn cầu.',
                ],
                'books' => [
                    [
                        'title' => 'Nhà Đầu Tư Thông Minh (The Intelligent Investor)',
                        'author' => 'Benjamin Graham',
                        'publisher' => 'Harper Business / NXB Trẻ',
                        'publish_year' => 2006,
                        'description' => 'Tác phẩm vĩ đại nhất về trường phái đầu tư giá trị, cuốn sách dẫn lối cho huyền thoại Warren Buffett. Giúp nhà đầu tư rèn luyện tâm lý vững vàng trước biến động thị trường, quản trị rủi ro và xác định biên an toàn (Margin of Safety). [Giá bìa: 240.000 VNĐ]',
                        'quantity' => 10,
                        'available_copies' => 9,
                        'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780060555665-L.jpg',
                    ],
                    [
                        'title' => 'Nguyên Lý Tiếp Thị (Principles of Marketing)',
                        'author' => 'Philip Kotler',
                        'publisher' => 'Pearson Education / NXB Lao Động - Xã Hội',
                        'publish_year' => 2020,
                        'description' => 'Kinh thánh marketing toàn cầu được giảng dạy tại hầu hết các trường đại học kinh tế danh tiếng. Phân tích toàn diện chiến lược định vị thương hiệu, hành vi người tiêu dùng, Marketing 4P và tiếp thị số trong kỷ nguyên đa nền tảng. [Giá bìa: 395.000 VNĐ]',
                        'quantity' => 8,
                        'available_copies' => 8,
                        'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780134492513-L.jpg',
                    ],
                    [
                        'title' => 'Khởi Nghiệp Tinh Gọn (The Lean Startup)',
                        'author' => 'Eric Ries',
                        'publisher' => 'Crown Business / NXB Tổng Hợp TP.HCM',
                        'publish_year' => 2011,
                        'description' => 'Mô hình khởi nghiệp thực chiến với chu trình Xây dựng - Đo lường - Học hỏi (Build-Measure-Learn). Giúp doanh nhân và nhà sáng lập thử nghiệm sản phẩm khả dụng tối thiểu (MVP) với chi phí thấp nhất và thích ứng linh hoạt. [Giá bìa: 195.000 VNĐ]',
                        'quantity' => 11,
                        'available_copies' => 10,
                        'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780307887894-L.jpg',
                    ],
                    [
                        'title' => 'Tâm Lý Học Về Tiền (The Psychology of Money)',
                        'author' => 'Morgan Housel',
                        'publisher' => 'Harriman House / NXB Công Thương',
                        'publish_year' => 2020,
                        'description' => '19 câu chuyện sâu sắc lý giải cách con người tư duy và đưa ra quyết định với tiền bạc. Khẳng định thành công tài chính cá nhân không phụ thuộc vào chỉ số IQ hay công thức phức tạp mà quyết định bởi sự kiên nhẫn, khiêm tốn và kỷ luật hành vi. [Giá bìa: 175.000 VNĐ]',
                        'quantity' => 15,
                        'available_copies' => 15,
                        'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780857197689-L.jpg',
                    ],
                ],
            ],

            // =========================================================================
            // NGÀNH 3: Y TẾ & CHĂM SÓC SỨC KHỎE
            // =========================================================================
            [
                'category' => [
                    'name' => 'Y Tế & Chăm Sóc Sức Khỏe',
                    'description' => 'Bộ sách y khoa chuẩn mực về giải phẫu học người, dược lý lâm sàng, kỹ năng kê đơn an toàn và lịch sử các phát kiến y học cứu rỗi nhân loại.',
                ],
                'books' => [
                    [
                        'title' => 'Giải Phẫu Học Người Dành Cho Sinh Viên Y Khoa',
                        'author' => 'GS. Trịnh Văn Minh',
                        'publisher' => 'NXB Y Học Hà Nội',
                        'publish_year' => 2019,
                        'description' => 'Bộ giáo trình kinh điển của trường Đại học Y Hà Nội, trang bị hệ thống kiến thức chuẩn xác về cấu trúc cơ thể người, hệ vận động, tuần hoàn, thần kinh và các cơ quan nội tạng kèm hình ảnh minh họa giải phẫu chi tiết. [Giá bìa: 320.000 VNĐ]',
                        'quantity' => 9,
                        'available_copies' => 8,
                        'cover_image' => 'https://images.unsplash.com/photo-1532938911079-1b06ac7ceec7?auto=format&fit=crop&w=600&q=80',
                    ],
                    [
                        'title' => 'Dược Lý Học Thực Hành & Kê Đơn An Toàn',
                        'author' => 'ĐH Y Dược',
                        'publisher' => 'NXB Y Học TP.HCM',
                        'publish_year' => 2022,
                        'description' => 'Cẩm nang chuyên sâu về cơ chế tác dụng, dược động học, chỉ định, chống chỉ định, tương tác thuốc và các nguyên tắc kê đơn an toàn, hợp lý cho bác sĩ thực hành lâm sàng và sinh viên Y - Dược. [Giá bìa: 280.000 VNĐ]',
                        'quantity' => 7,
                        'available_copies' => 7,
                        'cover_image' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?auto=format&fit=crop&w=600&q=80',
                    ],
                    [
                        'title' => 'Lược Sử Y Học Nhân Loại',
                        'author' => 'Siddhartha Mukherjee',
                        'publisher' => 'Scribner / NXB Thế Giới',
                        'publish_year' => 2016,
                        'description' => 'Bức tranh toàn cảnh đầy cuốn hút về cuộc chiến chống lại dịch bệnh và cái chết của nhân loại qua các thời kỳ: từ những khám phá thô sơ thời cổ đại đến các cuộc cách mạng kháng sinh, vắc-xin, giải mã bộ gen và liệu pháp miễn dịch hiện đại. [Giá bìa: 210.000 VNĐ]',
                        'quantity' => 12,
                        'available_copies' => 11,
                        'cover_image' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=600&q=80',
                    ],
                ],
            ],

            // =========================================================================
            // NGÀNH 4: THIẾT KẾ ĐỒ HỌA & ĐA PHƯƠNG TIỆN
            // =========================================================================
            [
                'category' => [
                    'name' => 'Thiết Kế Đồ Họa & Đa Phương Tiện',
                    'description' => 'Tài liệu nền tảng về tư duy thiết kế lấy người dùng làm trung tâm (User-Centered Design), nghệ thuật bố cục thị giác, hệ thống màu sắc và Typography.',
                ],
                'books' => [
                    [
                        'title' => 'Tư Duy Thiết Kế Thực Dụng (The Design of Everyday Things)',
                        'author' => 'Don Norman',
                        'publisher' => 'Basic Books / NXB Dân Trí',
                        'publish_year' => 2013,
                        'description' => 'Tác phẩm nền tảng khai sinh ngành Thiết kế lấy người dùng làm trung tâm (User-Centered Design). Phân tích cách thiết kế đồ vật và giao diện thông minh, trực quan, giải quyết triệt để sự lúng túng và thất vọng của người sử dụng. [Giá bìa: 190.000 VNĐ]',
                        'quantity' => 13,
                        'available_copies' => 13,
                        'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780465050659-L.jpg',
                    ],
                    [
                        'title' => 'Nghệ Thuật Bố Cục & Màu Sắc Trong Thiết Kế UI/UX',
                        'author' => 'Paul Rand',
                        'publisher' => 'Yale University Press / NXB Mỹ Thuật',
                        'publish_year' => 2018,
                        'description' => 'Tổng hợp các nguyên lý thị giác đỉnh cao về tương phản, tỷ lệ vàng, nhịp điệu và phân cấp thị giác (Visual Hierarchy). Nền tảng vững chắc cho mọi nhà thiết kế giao diện ứng dụng và đồ họa nhận diện thương hiệu. [Giá bìa: 260.000 VNĐ]',
                        'quantity' => 8,
                        'available_copies' => 8,
                        'cover_image' => 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?auto=format&fit=crop&w=600&q=80',
                    ],
                    [
                        'title' => 'Typography Cơ Bản: Nghệ Thuật Chữ Trong Truyền Thông Thị Giác',
                        'author' => 'Ellen Lupton',
                        'publisher' => 'Princeton Architectural Press / NXB Mỹ Thuật',
                        'publish_year' => 2014,
                        'description' => 'Hướng dẫn thực hành trực quan và sáng tạo về cấu trúc chữ, phân cấp văn bản, tỷ lệ khoảng cách Kerning, Tracking, Leading và nghệ thuật dàn trang tạo sức truyền cảm hứng mạnh mẽ cho thông điệp thị giác. [Giá bìa: 235.000 VNĐ]',
                        'quantity' => 11,
                        'available_copies' => 10,
                        'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781568989693-L.jpg',
                    ],
                ],
            ],

            // =========================================================================
            // NGÀNH 5: XÂY DỰNG & KIẾN TRÚC
            // =========================================================================
            [
                'category' => [
                    'name' => 'Xây Dựng & Kiến Trúc',
                    'description' => 'Kho tàng kiến thức kỹ thuật kết cấu công trình dân dụng, cơ học vật liệu, quy hoạch không gian và lịch sử tiến hóa kiến trúc nhân loại.',
                ],
                'books' => [
                    [
                        'title' => 'Sức Bền Vật Liệu & Cơ Học Kết Cấu Công Trình',
                        'author' => 'NXB Xây Dựng',
                        'publisher' => 'NXB Xây Dựng',
                        'publish_year' => 2021,
                        'description' => 'Giáo trình nền tảng không thể thiếu của kỹ sư xây dựng: phân tích trạng thái ứng suất, biến dạng đàn hồi, nội lực dầm, cột, giàn và kết cấu khung không gian chịu tải trọng tĩnh cũng như động lực học công trình. [Giá bìa: 295.000 VNĐ]',
                        'quantity' => 10,
                        'available_copies' => 10,
                        'cover_image' => 'https://images.unsplash.com/photo-1541888946425-d0fbb186f5f7?auto=format&fit=crop&w=600&q=80',
                    ],
                    [
                        'title' => 'Lược Sử Kiến Trúc Thế Giới',
                        'author' => 'Sir Banister Fletcher',
                        'publisher' => 'Architectural Press / NXB Mỹ Thuật',
                        'publish_year' => 2019,
                        'description' => 'Bản trường ca đồ sộ về các phong cách và trường phái kiến trúc vĩ đại qua hàng ngàn năm lịch sử: từ Kim tự tháp Ai Cập, đền đài Hy Lạp - La Mã, nhà thờ Gothic trung cổ cho đến các tòa nhà chọc trời của kỷ nguyên kiến trúc hiện đại. [Giá bìa: 450.000 VNĐ]',
                        'quantity' => 6,
                        'available_copies' => 5,
                        'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780750622677-L.jpg',
                    ],
                ],
            ],
        ];

        DB::transaction(function () use ($industries) {
            $totalSeeded = 0;

            foreach ($industries as $industry) {
                // 1. Tạo hoặc lấy Thể loại
                $category = Category::firstOrCreate(
                    ['name' => $industry['category']['name']],
                    ['description' => $industry['category']['description']]
                );

                $this->command->line("  📁 Chuyên ngành: <info>{$category->name}</info>");

                // 2. Tạo hoặc cập nhật từng đầu sách
                foreach ($industry['books'] as $bookData) {
                    $book = Book::updateOrCreate(
                        ['title' => $bookData['title']],
                        [
                            'category_id' => $category->id,
                            'author' => $bookData['author'],
                            'publisher' => $bookData['publisher'],
                            'publish_year' => $bookData['publish_year'],
                            'description' => $bookData['description'],
                            'quantity' => $bookData['quantity'],
                            'available_copies' => $bookData['available_copies'],
                            'cover_image' => $bookData['cover_image'],
                        ]
                    );

                    $this->command->line("     ✓ [{$book->id}] {$book->title} - {$book->author} (Kho: {$book->quantity} cuốn)");
                    $totalSeeded++;
                }
            }

            $this->command->info("🎉 HOÀN THÀNH! Đã nạp thành công {$totalSeeded} đầu sách chuyên ngành thuộc 5 nhóm ngành nghề!");
        });
    }
}
