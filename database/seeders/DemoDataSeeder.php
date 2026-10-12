<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BorrowRecord;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 0. Đảm bảo có tài khoản Độc giả để gán phiếu mượn demo
        $reader = User::firstOrCreate(
            ['email' => 'docgia@gmail.com'],
            [
                'name' => 'Độc Giả Mẫu',
                'password' => Hash::make('tieudoi2p'),
                'role' => 'reader',
                'email_verified_at' => now(),
            ]
        );

        // 1. Mở rộng 12 Thể loại sách phong phú chuẩn Thư viện Hiện đại & Cyberpunk
        $categoriesData = [
            [
                'name' => 'Văn học & Tiểu thuyết kinh điển',
                'description' => 'Các kiệt tác văn chương trường tồn, khắc họa chiều sâu tâm hồn con người và các giá trị nhân đạo vĩnh cửu.',
            ],
            [
                'name' => 'Tâm lý & Kỹ năng sống (Self-Help)',
                'description' => 'Nghệ thuật thấu hiểu tâm lý, xây dựng thói quen tích cực, quản lý cảm xúc và bứt phá giới hạn bản thân.',
            ],
            [
                'name' => 'Khoa học vũ trụ & Vật lý thiên văn',
                'description' => 'Khám phá bí ẩn vụ nổ Big Bang, thuyết tương đối, lỗ đen, cơ học lượng tử và bức tranh kỳ vĩ của vũ trụ.',
            ],
            [
                'name' => 'Trí tuệ nhân tạo (AI) & Khoa học dữ liệu',
                'description' => 'Mô hình học máy (Machine Learning), học sâu (Deep Learning), AI tạo sinh và kiến trúc dữ liệu tương lai.',
            ],
            [
                'name' => 'Lập trình & Kỹ thuật phần mềm',
                'description' => 'Mã sạch (Clean Code), thiết kế kiến trúc hệ thống, mẫu thiết kế (Design Patterns) và tư duy kỹ sư thực chiến.',
            ],
            [
                'name' => 'Kinh tế số & Khởi nghiệp kinh doanh',
                'description' => 'Tư duy tài chính thông minh, chiến lược kinh doanh đột phá, khởi nghiệp tinh gọn và xây dựng doanh nghiệp vĩ đại.',
            ],
            [
                'name' => 'Lịch sử thế giới & Khảo cổ học',
                'description' => 'Lược sử các nền văn minh nhân loại, những bước ngoặt lịch sử và sự tiến hóa của tổ chức xã hội loài người.',
            ],
            [
                'name' => 'Triết học & Tư duy phản biện',
                'description' => 'Tư tưởng triết học Đông - Tây, phép biện chứng, chủ nghĩa khắc kỷ và nghệ thuật tư duy phản biện sắc bén.',
            ],
            [
                'name' => 'Nghệ thuật, Thiết kế & UI/UX',
                'description' => 'Tư duy thiết kế lấy người dùng làm trung tâm, tâm lý học thị giác, màu sắc và mỹ thuật đương đại.',
            ],
            [
                'name' => 'Y học, Sức khỏe & Dinh dưỡng',
                'description' => 'Khoa học về giấc ngủ, hô hấp, hệ miễn dịch và các phương pháp dưỡng sinh, kéo dài tuổi thọ tự nhiên.',
            ],
            [
                'name' => 'Khoa học thần kinh & Não bộ',
                'description' => 'Giải mã cấu trúc nơ-ron, tính mềm dẻo của não bộ (Neuroplasticity) và nguồn gốc của ý thức con người.',
            ],
            [
                'name' => 'Môi trường & Khoa học Trái đất',
                'description' => 'Hệ sinh thái toàn cầu, biến đổi khí hậu, phát triển bền vững và bảo vệ hành tinh xanh.',
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['name']] = Category::updateOrCreate(
                ['name' => $cat['name']],
                ['description' => $cat['description']]
            );
        }

        // 2. Danh mục 50+ đầu sách kinh điển nổi tiếng với ảnh bìa thật 100% chuẩn tỉ lệ
        $booksData = [
            // ==========================================
            // THỂ LOẠI 1: Văn học & Tiểu thuyết kinh điển
            // ==========================================
            [
                'category' => 'Văn học & Tiểu thuyết kinh điển',
                'title' => 'Nhà Giả Kim (The Alchemist)',
                'author' => 'Paulo Coelho',
                'description' => 'Hành trình vượt sa mạc đi tìm kho báu của chàng chăn cừu Santiago, gửi gắm triết lý sâu sắc: Khi bạn khao khát một điều gì, cả vũ trụ sẽ hợp lực giúp bạn đạt được.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/10522591-L.jpg',
                'quantity' => 8,
            ],
            [
                'category' => 'Văn học & Tiểu thuyết kinh điển',
                'title' => 'Tội Ác và Hình Phạt (Crime and Punishment)',
                'author' => 'Fyodor Dostoevsky',
                'description' => 'Kiệt tác tâm lý vĩ đại mổ xẻ cuộc giằng xé lương tâm và hành trình cứu rỗi tâm hồn của chàng cựu sinh viên nghèo Raskolnikov tại nước Nga thế kỷ 19.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/12833521-L.jpg',
                'quantity' => 6,
            ],
            [
                'category' => 'Văn học & Tiểu thuyết kinh điển',
                'title' => 'Trăm Năm Cô Đơn (One Hundred Years of Solitude)',
                'author' => 'Gabriel García Márquez',
                'description' => 'Đỉnh cao của chủ nghĩa hiện thực huyền ảo, kể về bảy thế hệ dòng họ Buendía tại ngôi làng Macondo huyền thoại, soi tỏ bản ngã cô đơn của con người.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/10582964-L.jpg',
                'quantity' => 5,
            ],
            [
                'category' => 'Văn học & Tiểu thuyết kinh điển',
                'title' => 'Hoàng Tử Bé (The Little Prince)',
                'author' => 'Antoine de Saint-Exupéry',
                'description' => 'Câu chuyện ngụ ngôn tuyệt mỹ về tình yêu, trách nhiệm và vẻ đẹp của sự giản đơn: Người ta chỉ thấy rõ bằng trái tim, điều cốt lõi vô hình trong mắt trần.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/10543227-L.jpg',
                'quantity' => 10,
            ],
            [
                'category' => 'Văn học & Tiểu thuyết kinh điển',
                'title' => 'Những Người Khốn Khổ (Les Misérables)',
                'author' => 'Victor Hugo',
                'description' => 'Bản trường ca bất hủ về lòng bao dung, sự hy sinh và khát vọng tự do của Jean Valjean giữa những biến cố lịch sử khắc nghiệt của nước Pháp.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/12539710-L.jpg',
                'quantity' => 7,
            ],

            // ==========================================
            // THỂ LOẠI 2: Tâm lý & Kỹ năng sống (Self-Help)
            // ==========================================
            [
                'category' => 'Tâm lý & Kỹ năng sống (Self-Help)',
                'title' => 'Đắc Nhân Tâm (How to Win Friends and Influence People)',
                'author' => 'Dale Carnegie',
                'description' => 'Cuốn sách nghệ thuật giao tiếp kinh điển nhất mọi thời đại, hướng dẫn chân thành và thực tế để thấu hiểu, kết nối và cảm hóa lòng người.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/8231856-L.jpg',
                'quantity' => 12,
            ],
            [
                'category' => 'Tâm lý & Kỹ năng sống (Self-Help)',
                'title' => 'Thói Quen Nguyên Tử (Atomic Habits)',
                'author' => 'James Clear',
                'description' => 'Phương pháp khoa học đột phá: Thay đổi 1% mỗi ngày để tạo nên bước nhảy vọt vĩ đại, giải mã quy trình 4 bước hình thành và duy trì thói quen bền vững.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/12843513-L.jpg',
                'quantity' => 9,
            ],
            [
                'category' => 'Tâm lý & Kỹ năng sống (Self-Help)',
                'title' => 'Tư Duy Nhanh và Chậm (Thinking, Fast and Slow)',
                'author' => 'Daniel Kahneman',
                'description' => 'Công trình đoạt giải Nobel Kinh tế giải mã hai hệ thống tư duy: Hệ thống 1 trực giác, bộc phát và Hệ thống 2 chậm rãi, logic nhưng lười biếng.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/8302521-L.jpg',
                'quantity' => 6,
            ],
            [
                'category' => 'Tâm lý & Kỹ năng sống (Self-Help)',
                'title' => '7 Thói Quen Của Người Thành Đạt (The 7 Habits of Highly Effective People)',
                'author' => 'Stephen R. Covey',
                'description' => 'Hệ thống nguyên lý trường tồn rèn luyện tính chủ động, tư duy cùng thắng và rèn giũa bản thân để đạt hiệu quả tối ưu trong cuộc sống.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/8235113-L.jpg',
                'quantity' => 8,
            ],
            [
                'category' => 'Tâm lý & Kỹ năng sống (Self-Help)',
                'title' => 'Đi Tìm Lẽ Sống (Man\'s Search for Meaning)',
                'author' => 'Viktor E. Frankl',
                'description' => 'Trải nghiệm sinh tử trong trại tập trung Auschwitz và liệu pháp ý nghĩa: Người có một lý do để sống có thể chịu đựng hầu hết mọi nghịch cảnh.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/12832569-L.jpg',
                'quantity' => 7,
            ],

            // ==========================================
            // THỂ LOẠI 3: Khoa học vũ trụ & Vật lý thiên văn
            // ==========================================
            [
                'category' => 'Khoa học vũ trụ & Vật lý thiên văn',
                'title' => 'Lược Sử Thời Gian (A Brief History of Time)',
                'author' => 'Stephen Hawking',
                'description' => 'Bức tranh toàn cảnh về nguồn gốc và số phận của vũ trụ, giải thích hố đen, thời gian và không gian dưới góc nhìn vật lý lý thuyết hiện đại.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/8739161-L.jpg',
                'quantity' => 7,
            ],
            [
                'category' => 'Khoa học vũ trụ & Vật lý thiên văn',
                'title' => 'Vũ Trụ (Cosmos)',
                'author' => 'Carl Sagan',
                'description' => 'Hành trình tráng lệ khám phá 15 tỷ năm tiến hóa của vũ trụ và khoa học, khơi dậy niềm say mê tri thức và vị thế của con người trong đại dương ngân hà.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/8739162-L.jpg',
                'quantity' => 6,
            ],
            [
                'category' => 'Khoa học vũ trụ & Vật lý thiên văn',
                'title' => 'Vật Lý Thiên Văn Cho Người Vội Vã',
                'author' => 'Neil deGrasse Tyson',
                'description' => 'Những khái niệm vật lý thiên văn cốt lõi được đúc kết ngắn gọn, dí dỏm, từ năng lượng tối, vật chất tối đến thuyết lượng tử trong lòng vũ trụ.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/10574883-L.jpg',
                'quantity' => 8,
            ],
            [
                'category' => 'Khoa học vũ trụ & Vật lý thiên văn',
                'title' => 'Vũ Trụ Trong Vỏ Hạt Dẻ (The Universe in a Nutshell)',
                'author' => 'Stephen Hawking',
                'description' => 'Cuốn sách khám phá các biên giới kỳ thú nhất của vật lý hiện đại: Lý thuyết dây, không thời gian đa chiều và tương lai của loài người.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/12046830-L.jpg',
                'quantity' => 5,
            ],

            // ==========================================
            // THỂ LOẠI 4: Trí tuệ nhân tạo (AI) & Khoa học dữ liệu
            // ==========================================
            [
                'category' => 'Trí tuệ nhân tạo (AI) & Khoa học dữ liệu',
                'title' => 'Artificial Intelligence: A Modern Approach',
                'author' => 'Stuart Russell & Peter Norvig',
                'description' => 'Kinh thánh ngành Trí tuệ Nhân tạo, bao phủ toàn diện từ tác tử thông minh, logic tìm kiếm, mạng nơ-ron đến xử lý ngôn ngữ tự nhiên.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/8739163-L.jpg',
                'quantity' => 5,
            ],
            [
                'category' => 'Trí tuệ nhân tạo (AI) & Khoa học dữ liệu',
                'title' => 'Deep Learning (Sách Bìa Đen Học Sâu)',
                'author' => 'Ian Goodfellow, Yoshua Bengio, Aaron Courville',
                'description' => 'Tài liệu nền tảng chuẩn mực về toán học và thuật toán học sâu: Tối ưu hóa gradient, CNN, RNN và các mô hình sinh (Generative Models).',
                'cover_image' => 'https://covers.openlibrary.org/b/id/8292882-L.jpg',
                'quantity' => 4,
            ],
            [
                'category' => 'Trí tuệ nhân tạo (AI) & Khoa học dữ liệu',
                'title' => 'Hands-On Machine Learning with Scikit-Learn and TensorFlow',
                'author' => 'Aurélien Géron',
                'description' => 'Cẩm nang thực chiến số 1 về xây dựng hệ thống trí tuệ nhân tạo hoàn chỉnh từ dữ liệu thô, triển khai mô hình học máy đến huấn luyện mạng nơ-ron sâu.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/10515152-L.jpg',
                'quantity' => 6,
            ],
            [
                'category' => 'Trí tuệ nhân tạo (AI) & Khoa học dữ liệu',
                'title' => 'The Master Algorithm: Thuật Toán Tối Thượng',
                'author' => 'Pedro Domingos',
                'description' => 'Cuộc truy tìm cội nguồn thuật toán học máy vĩ đại có thể rút ra mọi tri thức từ dữ liệu để thay đổi tương lai kinh tế và văn minh nhân loại.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/8231991-L.jpg',
                'quantity' => 7,
            ],

            // ==========================================
            // THỂ LOẠI 5: Lập trình & Kỹ thuật phần mềm
            // ==========================================
            [
                'category' => 'Lập trình & Kỹ thuật phần mềm',
                'title' => 'Clean Code: Mã Sạch Và Tác Phẩm Nghệ Thuật',
                'author' => 'Robert C. Martin (Uncle Bob)',
                'description' => 'Cuốn sách gối đầu giường của mọi kỹ sư phần mềm, định hình tư duy viết mã dễ đọc, dễ kiểm thử, tinh gọn và trường tồn với thời gian.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/12648719-L.jpg',
                'quantity' => 9,
            ],
            [
                'category' => 'Lập trình & Kỹ thuật phần mềm',
                'title' => 'Design Patterns: Elements of Reusable Object-Oriented Software',
                'author' => 'Erich Gamma, Richard Helm, Ralph Johnson, John Vlissides',
                'description' => 'Kiệt tác kinh điển của nhóm Gang of Four (GoF), tổng hợp 23 mẫu thiết kế hướng đối tượng chuẩn mực giúp giải quyết bài toán kiến trúc hệ thống.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/12716388-L.jpg',
                'quantity' => 6,
            ],
            [
                'category' => 'Lập trình & Kỹ thuật phần mềm',
                'title' => 'The Pragmatic Programmer: Người Lập Trình Thực Dụng',
                'author' => 'Andrew Hunt & David Thomas',
                'description' => 'Tổng hợp các triết lý thực chiến về phát triển phần mềm, làm chủ công cụ, tránh cạm bẫy kỹ thuật và nâng cao phẩm chất nghề nghiệp lập trình viên.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/10515153-L.jpg',
                'quantity' => 8,
            ],
            [
                'category' => 'Lập trình & Kỹ thuật phần mềm',
                'title' => 'Refactoring: Cải Tiến Thiết Kế Mã Nguồn Hiện Có',
                'author' => 'Martin Fowler',
                'description' => 'Nghệ thuật tái cấu trúc mã nguồn một cách an toàn và có kiểm soát, biến những khối mã rườm rà thành hệ thống thanh thoát, dễ bảo trì.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/8292883-L.jpg',
                'quantity' => 5,
            ],
            [
                'category' => 'Lập trình & Kỹ thuật phần mềm',
                'title' => 'Introduction to Algorithms (CLRS)',
                'author' => 'Thomas H. Cormen, Charles E. Leiserson, Ronald L. Rivest',
                'description' => 'Bách khoa toàn thư thuật toán và cấu trúc dữ liệu kinh điển nhất thế giới giảng dạy tại MIT, Stanford và các trường đại học hàng đầu.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/12539712-L.jpg',
                'quantity' => 4,
            ],

            // ==========================================
            // THỂ LOẠI 6: Kinh tế số & Khởi nghiệp kinh doanh
            // ==========================================
            [
                'category' => 'Kinh tế số & Khởi nghiệp kinh doanh',
                'title' => 'Từ Số 0 Đến Số 1 (Zero to One)',
                'author' => 'Peter Thiel & Blake Masters',
                'description' => 'Bí quyết khởi nghiệp đột phá từ nhà sáng lập PayPal và nhà đầu tư đầu tiên của Facebook: Đừng cạnh tranh sao chép, hãy tạo ra giá trị độc quyền mới.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/8231992-L.jpg',
                'quantity' => 8,
            ],
            [
                'category' => 'Kinh tế số & Khởi nghiệp kinh doanh',
                'title' => 'Khởi Nghiệp Tinh Gọn (The Lean Startup)',
                'author' => 'Eric Ries',
                'description' => 'Mô hình phát triển sản phẩm linh hoạt qua vòng lặp Xây dựng - Đo lường - Học hỏi, giúp startup kiểm chứng ý tưởng với chi phí và rủi ro tối thiểu.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/8278278-L.jpg',
                'quantity' => 10,
            ],
            [
                'category' => 'Kinh tế số & Khởi nghiệp kinh doanh',
                'title' => 'Từ Tốt Đến Vĩ Đại (Good to Great)',
                'author' => 'Jim Collins',
                'description' => 'Nghiên cứu công phu suốt nhiều thập kỷ giải mã lý do tại sao một số công ty tạo ra bước nhảy vọt phi thường trong khi các đối thủ khác dậm chân tại chỗ.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/8231993-L.jpg',
                'quantity' => 7,
            ],
            [
                'category' => 'Kinh tế số & Khởi nghiệp kinh doanh',
                'title' => 'Tâm Lý Học Về Tiền (The Psychology of Money)',
                'author' => 'Morgan Housel',
                'description' => 'Những câu chuyện sâu sắc hé lộ rằng việc quản lý tiền bạc và làm giàu phụ thuộc vào cách bạn hành xử nhiều hơn là sự thông minh về mặt số học.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/12833522-L.jpg',
                'quantity' => 11,
            ],

            // ==========================================
            // THỂ LOẠI 7: Lịch sử thế giới & Khảo cổ học
            // ==========================================
            [
                'category' => 'Lịch sử thế giới & Khảo cổ học',
                'title' => 'Sapiens: Lược Sử Loài Người',
                'author' => 'Yuval Noah Harari',
                'description' => 'Cuốn sách lịch sử hiện tượng toàn cầu, truy vết hành trình của một loài vượn người vô danh vươn lên thống trị hành tinh nhờ năng lực tưởng tượng tập thể.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/8739164-L.jpg',
                'quantity' => 10,
            ],
            [
                'category' => 'Lịch sử thế giới & Khảo cổ học',
                'title' => 'Homo Deus: Lược Sử Tương Lai',
                'author' => 'Yuval Noah Harari',
                'description' => 'Tiếp nối Sapiens, cuốn sách vẽ nên tương lai của loài người khi công nghệ sinh học và trí tuệ nhân tạo đưa con người tiệm cận sức mạnh thần thánh.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/8739165-L.jpg',
                'quantity' => 8,
            ],
            [
                'category' => 'Lịch sử thế giới & Khảo cổ học',
                'title' => 'Súng, Vi Trùng và Thép (Guns, Germs, and Steel)',
                'author' => 'Jared Diamond',
                'description' => 'Tác phẩm đoạt giải Pulitzer lý giải nguồn gốc sâu xa của sự bất bình đẳng xã hội loài người qua yếu tố địa lý, môi trường tự nhiên và thuần hóa sinh vật.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/8231995-L.jpg',
                'quantity' => 6,
            ],
            [
                'category' => 'Lịch sử thế giới & Khảo cổ học',
                'title' => 'Con Đường Tơ Lụa (The Silk Roads)',
                'author' => 'Peter Frankopan',
                'description' => 'Một thiên sử thi viết lại dòng chảy thương mại, tôn giáo và quyền lực toàn cầu dịch chuyển qua chiếc nôi Trung Á suốt hàng ngàn năm.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/10574884-L.jpg',
                'quantity' => 7,
            ],

            // ==========================================
            // THỂ LOẠI 8: Triết học & Tư duy phản biện
            // ==========================================
            [
                'category' => 'Triết học & Tư duy phản biện',
                'title' => 'Suy Tưởng (Meditations)',
                'author' => 'Marcus Aurelius',
                'description' => 'Những ghi chép tự vấn sâu kín của vị hoàng đế La Mã vĩ đại, đại diện tiêu biểu nhất cho Chủ nghĩa Khắc kỷ (Stoicism) về sự điềm tĩnh và kỷ luật nội tâm.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/12832570-L.jpg',
                'quantity' => 9,
            ],
            [
                'category' => 'Triết học & Tư duy phản biện',
                'title' => 'Cộng Hòa (The Republic)',
                'author' => 'Plato',
                'description' => 'Đối thoại triết học nền tảng của văn minh phương Tây, bàn về công lý, trật tự xã hội lý tưởng và câu chuyện ngụ ngôn nổi tiếng về cái hang.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/12539704-L.jpg',
                'quantity' => 5,
            ],
            [
                'category' => 'Triết học & Tư duy phản biện',
                'title' => 'Zarathustra Đã Nói Như Thế (Thus Spoke Zarathustra)',
                'author' => 'Friedrich Nietzsche',
                'description' => 'Tác phẩm triết học kinh điển đề xướng khái niệm Siêu nhân (Übermensch), ý chí hùng cường và lời kêu gọi vượt lên trên những giáo điều tầm thường.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/12539706-L.jpg',
                'quantity' => 6,
            ],
            [
                'category' => 'Triết học & Tư duy phản biện',
                'title' => 'Bàn Về Tự Do (On Liberty)',
                'author' => 'John Stuart Mill',
                'description' => 'Khảo luận mẫu mực đặt nền móng cho tư tưởng tự do cá nhân, quyền tự do ngôn luận và giới hạn đạo đức của quyền lực nhà nước.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/12539705-L.jpg',
                'quantity' => 5,
            ],

            // ==========================================
            // THỂ LOẠI 9: Nghệ thuật, Thiết kế & UI/UX
            // ==========================================
            [
                'category' => 'Nghệ thuật, Thiết kế & UI/UX',
                'title' => 'The Design of Everyday Things: Thiết Kế Đồ Vật Hàng Ngày',
                'author' => 'Don Norman',
                'description' => 'Kinh thánh của ngành thiết kế trải nghiệm người dùng (UX), phân tích tâm lý nhận thức và cách tạo ra những sản phẩm thông minh, trực quan.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/8231996-L.jpg',
                'quantity' => 7,
            ],
            [
                'category' => 'Nghệ thuật, Thiết kế & UI/UX',
                'title' => 'Don\'t Make Me Think: Đừng Bắt Tôi Phải Suy Nghĩ',
                'author' => 'Steve Krug',
                'description' => 'Cẩm nang thiết kế giao diện web thực dụng và dí dỏm nhất: Một giao diện thành công là giao diện người dùng hiểu ngay mà không cần dừng lại suy nghĩ.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/8231997-L.jpg',
                'quantity' => 8,
            ],
            [
                'category' => 'Nghệ thuật, Thiết kế & UI/UX',
                'title' => 'Câu Chuyện Nghệ Thuật (The Story of Art)',
                'author' => 'E.H. Gombrich',
                'description' => 'Cuốn sách lịch sử mỹ thuật nổi tiếng nhất thế giới, dẫn dắt độc giả qua từng thời kỳ từ hội họa hang động tiền sử đến nghệ thuật đương đại.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/10574885-L.jpg',
                'quantity' => 6,
            ],

            // ==========================================
            // THỂ LOẠI 10: Y học, Sức khỏe & Dinh dưỡng
            // ==========================================
            [
                'category' => 'Y học, Sức khỏe & Dinh dưỡng',
                'title' => 'Tại Sao Chúng Ta Ngủ (Why We Sleep)',
                'author' => 'Matthew Walker',
                'description' => 'Nghiên cứu khoa học đột phá của giáo sư thần kinh học giải mã sức mạnh phục hồi kỳ diệu của giấc ngủ đối với trí nhớ, tuổi thọ và sức khỏe tinh thần.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/10574886-L.jpg',
                'quantity' => 9,
            ],
            [
                'category' => 'Y học, Sức khỏe & Dinh dưỡng',
                'title' => 'Hơi Thở: Khoa Học Mới Về Nghệ Thuật Bị Quên Lãng (Breath)',
                'author' => 'James Nestor',
                'description' => 'Hành trình y khoa thực nghiệm chứng minh rằng cách chúng ta hít thở có thể đảo ngược nhiều căn bệnh mãn tính và phục hồi thể trạng đỉnh cao.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/12833523-L.jpg',
                'quantity' => 7,
            ],
            [
                'category' => 'Y học, Sức khỏe & Dinh dưỡng',
                'title' => 'Bác Sĩ Tốt Nhất Là Chính Mình (Anticancer)',
                'author' => 'David Servan-Schreiber',
                'description' => 'Phương pháp củng cố hệ miễn dịch tự nhiên, chế độ dinh dưỡng lành mạnh và lối sống thanh lọc tế bào dựa trên các bằng chứng y khoa hiện đại.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/12833524-L.jpg',
                'quantity' => 8,
            ],

            // ==========================================
            // THỂ LOẠI 11: Khoa học thần kinh & Não bộ
            // ==========================================
            [
                'category' => 'Khoa học thần kinh & Não bộ',
                'title' => 'Bộ Não Tự Chữa Lành (The Brain That Changes Itself)',
                'author' => 'Norman Doidge',
                'description' => 'Cuộc cách mạng thần kinh học chứng minh não bộ con người có khả năng tái cấu trúc và chữa lành liên tục suốt cuộc đời thông qua tư duy.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/10574887-L.jpg',
                'quantity' => 6,
            ],
            [
                'category' => 'Khoa học thần kinh & Não bộ',
                'title' => 'Kẻ Khác Trong Bạn (The Tell-Tale Brain)',
                'author' => 'V.S. Ramachandran',
                'description' => 'Khám phá thế giới kỳ bí của tâm trí qua các ca bệnh thần kinh hiếm gặp, hé lộ nguồn gốc của ngôn ngữ, đồng cảm và sự sáng tạo của con người.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/10574888-L.jpg',
                'quantity' => 5,
            ],

            // ==========================================
            // THỂ LOẠI 12: Môi trường & Khoa học Trái đất
            // ==========================================
            [
                'category' => 'Môi trường & Khoa học Trái đất',
                'title' => 'Mùa Xuân Vắng Lặng (Silent Spring)',
                'author' => 'Rachel Carson',
                'description' => 'Cuốn sách chấn động khai sinh phong trào bảo vệ môi trường toàn cầu, cảnh báo nguy cơ hủy diệt hệ sinh thái do hóa chất độc hại.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/12539707-L.jpg',
                'quantity' => 6,
            ],
            [
                'category' => 'Môi trường & Khoa học Trái đất',
                'title' => 'Trái Đất Không Thể Sinh Sống (The Uninhabitable Earth)',
                'author' => 'David Wallace-Wells',
                'description' => 'Bản báo cáo cảnh tỉnh sâu sắc về tương lai khí hậu Trái đất nếu nhân loại không hành động ngay hôm nay để bảo vệ hành tinh.',
                'cover_image' => 'https://covers.openlibrary.org/b/id/10574889-L.jpg',
                'quantity' => 7,
            ],
        ];

        $books = [];
        foreach ($booksData as $b) {
            $catModel = $categories[$b['category']] ?? null;
            if (! $catModel) {
                continue;
            }

            $createdBook = Book::updateOrCreate(
                ['title' => $b['title']],
                [
                    'category_id' => $catModel->id,
                    'author' => $b['author'],
                    'description' => $b['description'],
                    'cover_image' => $b['cover_image'],
                    'quantity' => $b['quantity'],
                ]
            );

            $books[$b['title']] = $createdBook;
        }

        // 3. Khởi tạo 6 Phiếu mượn mẫu đại diện cho đầy đủ các trạng thái thực tế
        $today = Carbon::now();

        // 3.1. Phiếu 1: ĐANG MƯỢN HỢP LỆ (Mượn 3 ngày trước, còn 11 ngày hạn)
        if (isset($books['Nhà Giả Kim (The Alchemist)'])) {
            $book1 = $books['Nhà Giả Kim (The Alchemist)'];
            BorrowRecord::updateOrCreate(
                [
                    'user_id' => $reader->id,
                    'book_id' => $book1->id,
                    'borrow_date' => $today->copy()->subDays(3)->toDateString(),
                ],
                [
                    'borrower_name' => $reader->name,
                    'borrower_phone' => '0912345678',
                    'borrower_email' => $reader->email,
                    'due_date' => $today->copy()->addDays(11)->toDateString(),
                    'return_date' => null,
                    'status' => 'borrowing',
                    'fine_amount' => 0,
                    'notes' => 'Sách bìa mới đẹp, độc giả bảo quản cẩn thận.',
                ]
            );
        }

        // 3.2. Phiếu 2: ĐANG CHỜ DUYỆT (Mới gửi yêu cầu hôm nay)
        if (isset($books['Thói Quen Nguyên Tử (Atomic Habits)'])) {
            $book2 = $books['Thói Quen Nguyên Tử (Atomic Habits)'];
            BorrowRecord::updateOrCreate(
                [
                    'user_id' => $reader->id,
                    'book_id' => $book2->id,
                    'status' => 'pending',
                ],
                [
                    'borrower_name' => $reader->name,
                    'borrower_phone' => '0912345678',
                    'borrower_email' => $reader->email,
                    'borrow_date' => $today->toDateString(),
                    'due_date' => $today->copy()->addDays(14)->toDateString(),
                    'return_date' => null,
                    'fine_amount' => 0,
                    'notes' => 'Độc giả gửi yêu cầu mượn trực tuyến.',
                ]
            );
        }

        // 3.3. Phiếu 3: ĐÃ TRẢ ĐÚNG HẠN (Mượn 20 ngày trước, trả 8 ngày trước)
        if (isset($books['Lược Sử Thời Gian (A Brief History of Time)'])) {
            $book3 = $books['Lược Sử Thời Gian (A Brief History of Time)'];
            BorrowRecord::updateOrCreate(
                [
                    'user_id' => $reader->id,
                    'book_id' => $book3->id,
                    'borrow_date' => $today->copy()->subDays(20)->toDateString(),
                ],
                [
                    'borrower_name' => $reader->name,
                    'borrower_phone' => '0912345678',
                    'borrower_email' => $reader->email,
                    'due_date' => $today->copy()->subDays(6)->toDateString(),
                    'return_date' => $today->copy()->subDays(8)->toDateString(),
                    'status' => 'returned',
                    'fine_amount' => 0,
                    'notes' => 'Đã hoàn trả sách nguyên vẹn đúng hạn.',
                ]
            );
        }

        // 3.4. Phiếu 4: ĐÃ TRẢ TRỄ HẠN (Mượn 30 ngày trước, hạn 16 ngày trước, trả 10 ngày trước => trễ 6 ngày, phạt 30.000đ)
        if (isset($books['Clean Code: Mã Sạch Và Tác Phẩm Nghệ Thuật'])) {
            $book4 = $books['Clean Code: Mã Sạch Và Tác Phẩm Nghệ Thuật'];
            BorrowRecord::updateOrCreate(
                [
                    'user_id' => $reader->id,
                    'book_id' => $book4->id,
                    'borrow_date' => $today->copy()->subDays(30)->toDateString(),
                ],
                [
                    'borrower_name' => $reader->name,
                    'borrower_phone' => '0912345678',
                    'borrower_email' => $reader->email,
                    'due_date' => $today->copy()->subDays(16)->toDateString(),
                    'return_date' => $today->copy()->subDays(10)->toDateString(),
                    'status' => 'returned',
                    'fine_amount' => 30000,
                    'notes' => 'Trả trễ 6 ngày. Độc giả đã thanh toán đủ 30.000 VNĐ tiền phạt.',
                ]
            );
        }

        // 3.5. Phiếu 5: QUÁ HẠN (Mượn 25 ngày trước, hạn 11 ngày trước, chưa trả => Quá hạn 11 ngày)
        if (isset($books['Sapiens: Lược Sử Loài Người'])) {
            $book5 = $books['Sapiens: Lược Sử Loài Người'];
            BorrowRecord::updateOrCreate(
                [
                    'user_id' => $reader->id,
                    'book_id' => $book5->id,
                    'borrow_date' => $today->copy()->subDays(25)->toDateString(),
                ],
                [
                    'borrower_name' => $reader->name,
                    'borrower_phone' => '0912345678',
                    'borrower_email' => $reader->email,
                    'due_date' => $today->copy()->subDays(11)->toDateString(),
                    'return_date' => null,
                    'status' => 'borrowing',
                    'fine_amount' => 0,
                    'notes' => 'Đã gửi email nhắc hạn trả lần 2.',
                ]
            );
        }

        // 3.6. Phiếu 6: QUÁ HẠN (Mượn 18 ngày trước, hạn 4 ngày trước, chưa trả => Quá hạn 4 ngày)
        if (isset($books['Artificial Intelligence: A Modern Approach'])) {
            $book6 = $books['Artificial Intelligence: A Modern Approach'];
            BorrowRecord::updateOrCreate(
                [
                    'user_id' => $reader->id,
                    'book_id' => $book6->id,
                    'borrow_date' => $today->copy()->subDays(18)->toDateString(),
                ],
                [
                    'borrower_name' => $reader->name,
                    'borrower_phone' => '0912345678',
                    'borrower_email' => $reader->email,
                    'due_date' => $today->copy()->subDays(4)->toDateString(),
                    'return_date' => null,
                    'status' => 'borrowing',
                    'fine_amount' => 0,
                    'notes' => 'Phiếu mượn quá hạn 4 ngày.',
                ]
            );
        }
    }
}
