<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateBookAuthorsAndDescriptionsSeeder extends Seeder
{
    /**
     * Cập nhật thông tin tác giả và bản tóm tắt nội dung chuyên nghiệp cho toàn bộ 100 cuốn sách.
     */
    public function run(): void
    {
        $bookDetails = [
            // ==========================================
            // I. CÔNG NGHỆ THÔNG TIN (25 CUỐN)
            // ==========================================
            'Clean Code: A Handbook of Agile Software Craftsmanship' => [
                'author' => 'Robert C. Martin (Uncle Bob)',
                'description' => 'Tác phẩm kinh điển định hình tư duy viết mã sạch, dễ đọc và dễ bảo trì cho kỹ sư phần mềm. Cuốn sách cung cấp các nguyên tắc thực chiến giúp chuyển hóa mã lộn xộn thành mã nguồn thanh lịch, nâng tầm chất lượng dự án dài hạn.',
            ],
            'The Clean Coder' => [
                'author' => 'Robert C. Martin (Uncle Bob)',
                'description' => 'Cẩm nang đạo đức nghề nghiệp và kỹ năng ứng xử chuẩn mực dành cho lập trình viên chuyên nghiệp. Bạn sẽ học được cách ước lượng thời gian chính xác, kiểm soát áp lực tiến độ và bảo vệ tiêu chuẩn kỹ thuật trước các thỏa hiệp tạm thời.',
            ],
            'Clean Architecture' => [
                'author' => 'Robert C. Martin (Uncle Bob)',
                'description' => 'Hướng dẫn xây dựng kiến trúc hệ thống bền vững, độc lập với framework, cơ sở dữ liệu và giao diện người dùng. Tác phẩm trang bị tư duy thiết kế phần mềm linh hoạt, dễ kiểm thử và trường tồn trước các biến động công nghệ.',
            ],
            'Clean Agile: Back to Basics' => [
                'author' => 'Robert C. Martin (Uncle Bob)',
                'description' => 'Đưa triết lý Agile trở về đúng bản chất tinh gọn nguyên bản, loại bỏ các biến tướng hình thức trong quy trình phát triển. Cuốn sách giúp các đội ngũ tối ưu hóa vòng lặp phản hồi ngắn và liên tục chuyển giao giá trị thực cho khách hàng.',
            ],
            'The Pragmatic Programmer' => [
                'author' => 'Andrew Hunt & David Thomas',
                'description' => 'Tuyển tập những lời khuyên thực tế vô giá giúp lập trình viên rèn luyện tư duy tự chủ, thích ứng nhanh và nâng cao tay nghề. Bạn sẽ nắm vững kỹ năng kiểm thử tự động, tránh lặp lại mã nguồn và trở thành một chuyên gia công nghệ toàn diện.',
            ],
            'Design Patterns: Elements of Reusable Object-Oriented Software' => [
                'author' => 'Erich Gamma, Richard Helm, Ralph Johnson, John Vlissides (GoF)',
                'description' => 'Kinh thánh về 23 mẫu thiết kế hướng đối tượng kinh điển, đặt nền móng cho kiến trúc phần mềm hiện đại. Tác phẩm giúp lập trình viên giải quyết triệt để bài toán tái sử dụng mã nguồn và xây dựng hệ thống linh hoạt, dễ mở rộng.',
            ],
            'Refactoring: Improving the Design of Existing Code' => [
                'author' => 'Martin Fowler',
                'description' => 'Hướng dẫn chuyên sâu phương pháp cải tiến cấu trúc mã nguồn bên trong mà không làm thay đổi hành vi bên ngoài của hệ thống. Cuốn sách giúp bạn nhận diện các "mùi mã xấu" (code smells) và từng bước nâng cao độ mượt mà, dễ bảo trì của ứng dụng.',
            ],
            'Code Complete' => [
                'author' => 'Steve McConnell',
                'description' => 'Bách khoa toàn thư toàn diện nhất về nghệ thuật xây dựng phần mềm chất lượng cao từ cấp độ dòng lệnh đến module. Độc giả sẽ học được cách thiết kế, lập trình, kiểm thử và gỡ lỗi theo các tiêu chuẩn kỹ thuật nghiêm ngặt hàng đầu thế giới.',
            ],
            'Head First Design Patterns' => [
                'author' => 'Eric Freeman & Elisabeth Robson',
                'description' => 'Phương pháp tiếp cận trực quan, sinh động và đầy cuốn hút về các mẫu thiết kế hướng đối tượng phổ biến nhất. Cuốn sách biến những khái niệm trừu tượng thành kiến thức dễ hiểu, dễ nhớ và có thể ứng dụng ngay vào dự án thực tế.',
            ],
            'Cracking the Coding Interview' => [
                'author' => 'Gayle Laakmann McDowell',
                'description' => 'Cẩm nang phỏng vấn kỹ thuật lập trình kinh điển với 189 bài toán thuật toán và cấu trúc dữ liệu sát với thực tế của các tập đoàn công nghệ lớn. Cuốn sách rèn luyện tư duy giải quyết vấn đề logic và chiến lược gây ấn tượng với nhà tuyển dụng hàng đầu.',
            ],
            'Introduction to Algorithms' => [
                'author' => 'Thomas H. Cormen, Charles E. Leiserson, Ronald L. Rivest, Clifford Stein',
                'description' => 'Giáo trình thuật toán chuẩn mực toàn cầu, bao quát từ cấu trúc dữ liệu nền tảng đến các thuật toán đồ thị và quy hoạch động phức tạp. Tác phẩm rèn luyện tư duy tính toán chặt chẽ và khả năng phân tích độ phức tạp tối ưu cho mọi bài toán kỹ thuật.',
            ],
            'Structure and Interpretation of Computer Programs' => [
                'author' => 'Harold Abelson & Gerald Jay Sussman',
                'description' => 'Tuyệt tác kinh điển khai mở bản chất sâu sắc của khoa học máy tính thông qua lập trình hàm và trừu tượng hóa dữ liệu. Độc giả sẽ nắm bắt được tư duy kiểm soát độ phức tạp phần mềm và sức mạnh của việc thiết kế ngôn ngữ lập trình.',
            ],
            "Don't Make Me Think" => [
                'author' => 'Steve Krug',
                'description' => 'Nguyên lý cốt lõi về trải nghiệm người dùng (UX) và tính khả dụng của giao diện web, khẳng định mọi thao tác phải trực quan đến mức người dùng không cần suy nghĩ. Tác phẩm là kim chỉ nam giúp bạn tạo ra những sản phẩm số thân thiện, hiệu quả.',
            ],
            'The Mythical Man-Month' => [
                'author' => 'Frederick P. Brooks Jr.',
                'description' => 'Khảo luận kinh điển phân tích các cạm bẫy trong quản lý dự án phần mềm quy mô lớn, nổi tiếng với định luật Brooks về việc thêm nhân sự vào dự án trễ hạn. Tác phẩm cung cấp cái nhìn thực tế về giao tiếp, tổ chức đội ngũ và tính toàn vẹn hệ thống.',
            ],
            'Eloquent JavaScript' => [
                'author' => 'Marijn Haverbeke',
                'description' => 'Hành trình học lập trình JavaScript hiện đại đầy mê hoặc, kết hợp nhuần nhuyễn giữa lý thuyết ngôn ngữ sâu sắc và các dự án thực tế. Cuốn sách giúp bạn làm chủ từ cú pháp cơ bản đến lập trình bất đồng bộ và xử lý DOM trên trình duyệt một cách thanh lịch.',
            ],
            "You Don't Know JS" => [
                'author' => 'Kyle Simpson (getify)',
                'description' => 'Đào sâu vào tầng bản chất vi mô của JavaScript, làm sáng tỏ cơ chế hoạt động của phạm vi biến (scope), hoisting và closures. Tác phẩm giúp bạn làm chủ hoàn toàn hành vi của ngôn ngữ, xóa bỏ mọi hiểu lầm và viết code tự tin, chính xác.',
            ],
            'Python Crash Course' => [
                'author' => 'Eric Matthes',
                'description' => 'Khởi đầu hoàn hảo với ngôn ngữ Python thông qua các bài học ngắn gọn, thực tiễn cùng 3 dự án lớn về game, trực quan hóa dữ liệu và web app. Cuốn sách mang đến trải nghiệm học tập hào hứng, giúp bạn nhanh chóng tự tay xây dựng ứng dụng hữu ích.',
            ],
            'Effective Java' => [
                'author' => 'Joshua Bloch',
                'description' => 'Bộ sưu tập 90 nguyên tắc vàng và thực hành tốt nhất từ kiến trúc sư trưởng nền tảng Java, giúp viết mã an toàn, rõ ràng và hiệu năng cao. Cuốn sách là tài liệu bắt buộc phải đọc đối với bất kỳ kỹ sư nào muốn làm chủ Java ở cấp độ chuyên sâu.',
            ],
            'Designing Data-Intensive Applications' => [
                'author' => 'Martin Kleppmann',
                'description' => 'Tuyệt tác kinh điển phân tích sâu sắc kiến trúc của các hệ thống dữ liệu phân tán quy mô lớn, bao quát từ lưu trữ, lập chỉ mục đến đồng thuận và tính nhất quán. Tác phẩm là chiếc la bàn không thể thiếu cho các kỹ sư backend và chuyên gia dữ liệu hiện đại.',
            ],
            'System Design Interview' => [
                'author' => 'Alex Xu',
                'description' => 'Hướng dẫn từng bước cách thiết kế các hệ thống phân tán quy mô hàng triệu người dùng như YouTube, Twitter hay sàn thương mại điện tử. Cuốn sách cung cấp các mô hình kiến trúc trực quan, rõ ràng, giúp bạn tự tin vượt qua các vòng phỏng vấn thiết kế hệ thống cấp cao.',
            ],
            'Patterns of Enterprise Application Architecture' => [
                'author' => 'Martin Fowler',
                'description' => 'Tổng hợp các giải pháp kiến trúc mẫu mực cho những bài toán phức tạp trong phần mềm doanh nghiệp, từ kiến trúc tầng đến truy cập cơ sở dữ liệu. Tác phẩm đặt nền móng kỹ thuật vững chắc, giúp hệ thống vận hành ổn định và dễ dàng mở rộng quy mô.',
            ],
            "Soft Skills: The software developer's life manual" => [
                'author' => 'John Sonmez',
                'description' => 'Hướng dẫn toàn diện và thực tế về phát triển sự nghiệp, xây dựng thương hiệu cá nhân, quản lý tài chính và chăm sóc sức khỏe cho lập trình viên. Cuốn sách truyền cảm hứng mạnh mẽ giúp bạn không chỉ giỏi kỹ thuật mà còn đạt được cuộc sống viên mãn, cân bằng.',
            ],
            'Continuous Delivery' => [
                'author' => 'Jez Humble & David Farley',
                'description' => 'Nền tảng tư duy và thực hành của văn hóa DevOps, hướng dẫn tự động hóa toàn bộ quy trình từ kiểm thử, đóng gói đến phát hành phần mềm an toàn. Tác phẩm giúp doanh nghiệp rút ngắn vòng đời triển khai, giảm thiểu rủi ro và tăng tốc độ đưa sản phẩm ra thị trường.',
            ],
            'Building Microservices' => [
                'author' => 'Sam Newman',
                'description' => 'Cẩm nang toàn diện về thiết kế, triển khai và vận hành kiến trúc vi dịch vụ (microservices) trong môi trường thực tế. Cuốn sách giải quyết các thách thức then chốt về tích hợp, phân tách cơ sở dữ liệu, giám sát và bảo mật hệ thống phân tán.',
            ],
            'Domain-Driven Design' => [
                'author' => 'Eric Evans',
                'description' => 'Khởi nguồn của phương pháp thiết kế hướng miền, tập trung vào việc mô hình hóa sâu sắc nghiệp vụ cốt lõi và gắn kết chặt chẽ mã nguồn với ngôn ngữ chung. Tác phẩm giúp các nhóm phần mềm kiểm soát độ phức tạp trong các dự án nghiệp vụ quy mô lớn.',
            ],

            // ==========================================
            // II. KINH DOANH & KHỞI NGHIỆP (25 CUỐN)
            // ==========================================
            'The Personal MBA' => [
                'author' => 'Josh Kaufman',
                'description' => 'Tinh hoa quản trị kinh doanh được đúc kết ngắn gọn, bao quát từ tiếp thị, bán hàng, vận hành đến tài chính mà không cần bằng cấp đắt đỏ. Tác phẩm trang bị tư duy kinh doanh thực chiến và cách vận hành một cỗ máy sinh lời hiệu quả, bền vững.',
            ],
            'The Lean Startup' => [
                'author' => 'Eric Ries',
                'description' => 'Phương pháp khởi nghiệp tinh gọn mang tính cách mạng, nhấn mạnh vào việc kiểm chứng giả thuyết bằng sản phẩm khả dụng tối thiểu (MVP) và vòng lặp học hỏi nhanh. Cuốn sách giúp các nhà sáng lập hạn chế tối đa lãng phí tài nguyên và thích ứng tức thì với thị trường.',
            ],
            'The Mom Test' => [
                'author' => 'Rob Fitzpatrick',
                'description' => 'Nghệ thuật phỏng vấn khách hàng thực tế để tìm ra sự thật mà không bị đánh lừa bởi những lời khen ngợi xã giao. Cuốn sách cung cấp các câu hỏi đắt giá giúp bạn thẩm định chính xác nhu cầu thị trường trước khi đổ tiền xây dựng sản phẩm.',
            ],
            'The E-Myth Revisited' => [
                'author' => 'Michael E. Gerber',
                'description' => 'Đập tan ảo tưởng khởi nghiệp và chỉ ra sự khác biệt cốt tử giữa một người thợ kỹ thuật giỏi và một chủ doanh nghiệp đích thực. Tác phẩm hướng dẫn cách chuẩn hóa quy trình và xây dựng hệ thống tự vận hành như một mô hình nhượng quyền thành công.',
            ],
            'The $100 Startup' => [
                'author' => 'Chris Guillebeau',
                'description' => 'Kế hoạch hành động cụ thể để biến đam mê cá nhân thành doanh nghiệp sinh lời với số vốn khởi nghiệp siêu nhỏ. Bạn sẽ học được cách tìm kiếm khách hàng, đóng gói giá trị và đạt được tự do tài chính mà không cần phụ thuộc vào nguồn vốn ngoài.',
            ],
            'Thinking in Bets' => [
                'author' => 'Annie Duke',
                'description' => 'Vận dụng tư duy của một nhà vô địch poker để đưa ra quyết định sắc bén trong môi trường kinh doanh đầy biến số và bất định. Tác phẩm giúp bạn tách bạch chất lượng quyết định khỏi kết quả may rủi, từ đó rèn luyện bản lĩnh bình tĩnh trước mọi ngã rẽ.',
            ],
            'Quit' => [
                'author' => 'Annie Duke',
                'description' => 'Phân tích khoa học và nghệ thuật của việc từ bỏ đúng lúc, coi việc dừng lại là một chiến lược khôn ngoan thay vì thất bại. Cuốn sách giúp bạn dũng cảm cắt lỗ những dự án kém hiệu quả để tập trung nguồn lực vào cơ hội tăng trưởng lớn hơn.',
                'cover_image' => 'books/quit-annie-duke-fixed.jpg',
            ],
            'The Hard Thing About Hard Things' => [
                'author' => 'Ben Horowitz',
                'description' => 'Những góc khuất trần trụi và bài học xương máu của một CEO thung lũng Silicon khi đối mặt với bờ vực phá sản và áp lực sa thải nhân sự. Tác phẩm mang đến cái nhìn chân thực về sự cô độc của người lãnh đạo và bản lĩnh sinh tồn trong giông bão kinh doanh.',
            ],
            'Super Thinking' => [
                'author' => 'Gabriel Weinberg & Lauren McCann',
                'description' => 'Bộ công cụ tư duy đa ngành mạnh mẽ gồm các mô hình nhận thức kinh điển từ kinh tế, tâm lý, xác suất đến vật lý. Cuốn sách giúp bạn nâng cấp khả năng suy luận, nhìn thấu bản chất vấn đề và giải quyết các tình huống hóc búa một cách thông minh.',
            ],
            'Risk' => [
                'author' => 'Stanley McChrystal & Anna Butrico',
                'description' => 'Khung quản trị rủi ro thực chiến từ góc nhìn của cựu Đại tướng quân đội Hoa Kỳ, chuyển trọng tâm từ dự báo nguy cơ sang tăng cường sức đề kháng nội tại. Cuốn sách giúp tổ chức xây dựng hệ miễn dịch vững vàng để chủ động vượt qua mọi khủng hoảng bất ngờ.',
                'cover_image' => 'books/risk-stanley-mcchrystal-fixed.jpg',
            ],
            'Zero to One' => [
                'author' => 'Peter Thiel & Blake Masters',
                'description' => 'Tư duy đột phá về đổi mới sáng tạo, khuyến khích các nhà sáng lập tạo ra giá trị hoàn toàn mới để độc quyền thị trường thay vì cạnh tranh khốc liệt. Tác phẩm mở ra nhãn quan khác biệt về công nghệ, tương lai và cách thức định hình lại thế giới.',
            ],
            "The Startup Owner's Manual" => [
                'author' => 'Steve Blank & Bob Dorf',
                'description' => 'Cẩm nang hướng dẫn chi tiết từng bước áp dụng quy trình phát triển khách hàng (Customer Development) vào việc xây dựng doanh nghiệp mới. Cuốn sách là tài liệu kinh điển giúp các nhà sáng lập biến ý tưởng mơ hồ thành mô hình kinh doanh có khả năng mở rộng quy mô.',
            ],
            'Blue Ocean Strategy' => [
                'author' => 'W. Chan Kim & Renée Mauborgne',
                'description' => 'Chiến lược đại dương xanh hướng dẫn doanh nghiệp thoát khỏi vùng biển đỏ cạnh tranh đẫm máu để khai phá khoảng trống thị trường hoàn toàn mới. Tác phẩm cung cấp các công cụ phân tích sắc bén nhằm gia tăng giá trị vượt trội cho khách hàng đồng thời tối ưu hóa chi phí.',
                'cover_image' => 'books/blue-ocean-strategy-fixed.jpg',
            ],
            'Traction' => [
                'author' => 'Gabriel Weinberg & Justin Mares',
                'description' => 'Khung phương pháp Bulls-eye thực tiễn giúp doanh nghiệp xác định và thử nghiệm 19 kênh thu hút khách hàng tiềm năng hiệu quả nhất. Tác phẩm nhấn mạnh tầm quan trọng của lực kéo thị trường (traction), yếu tố sống còn quyết định sự thành bại của mọi dự án khởi nghiệp.',
            ],
            'Profit First' => [
                'author' => 'Mike Michalowicz',
                'description' => 'Đảo ngược công thức kế toán truyền thống bằng nguyên tắc "Lợi nhuận trước, chi phí sau", buộc doanh nghiệp phải vận hành kỷ luật và tinh gọn. Cuốn sách giúp các chủ doanh nghiệp chấm dứt tình trạng kiệt quệ dòng tiền và xây dựng nền tảng tài chính lành mạnh, sinh lời ổn định.',
                'cover_image' => 'books/profit-first-fixed.jpg',
            ],
            'Good to Great' => [
                'author' => 'Jim Collins',
                'description' => 'Nghiên cứu công phu chỉ ra các yếu tố then chốt giúp các công ty bình thường bứt phá trở thành vĩ đại và duy trì vị thế hàng thập kỷ. Độc giả sẽ tiếp cận các khái niệm kinh điển như Lãnh đạo cấp độ 5, Khái niệm Con nhím và Bánh đà tăng trưởng bền vững.',
            ],
            'The Innovator\'s Dilemma' => [
                'author' => 'Clayton M. Christensen',
                'description' => 'Khám phá nghịch lý đau đớn khi các tập đoàn hùng mạnh bị sụp đổ chỉ vì làm đúng mọi việc nhưng lại bỏ lỡ làn sóng công nghệ đột phá. Tác phẩm là hồi chuông cảnh tỉnh sâu sắc về cách thức đổi mới mô hình kinh doanh để tồn tại trước các đối thủ mới nổi.',
            ],
            'Built to Sell' => [
                'author' => 'John Warrillow',
                'description' => 'Câu chuyện hấp dẫn về cách tái cấu trúc một công ty dịch vụ phụ thuộc vào người sáng lập thành một doanh nghiệp có quy trình chuẩn hóa và giá trị chuyển nhượng cao. Tác phẩm hướng dẫn cách tạo ra dòng tiền định kỳ và giải phóng hoàn toàn thời gian cho chủ sở hữu.',
            ],
            'Rework' => [
                'author' => 'Jason Fried & David Heinemeier Hansson',
                'description' => 'Tuyên ngôn phá vỡ các quy tắc kinh doanh sáo rỗng, khuyến khích làm việc thông minh hơn, từ bỏ các cuộc họp vô bổ và tập trung vào việc tạo ra sản phẩm thực. Cuốn sách mang đến luồng gió mới đầy thực tế, thúc đẩy bạn hành động ngay thay vì lập kế hoạch viển vông.',
            ],
            'Shoe Dog' => [
                'author' => 'Phil Knight',
                'description' => 'Hồi ký xúc động và chân thực của nhà sáng lập Nike về hành trình từ chàng thanh niên bán giày nhập khẩu đến đế chế thể thao toàn cầu. Cuốn sách khắc họa sâu sắc lòng kiên trì phi thường, sự liều lĩnh có tính toán và vẻ đẹp của niềm tin không lay chuyển.',
            ],
            'Atomic Habits' => [
                'author' => 'James Clear',
                'description' => 'Phương pháp khoa học đã được kiểm chứng về sức mạnh của những cải tiến nhỏ 1% mỗi ngày để tạo nên sự biến đổi phi thường trong dài hạn. Cuốn sách cung cấp 4 quy luật đơn giản giúp bạn thiết lập thói quen tốt, từ bỏ thói quen xấu và làm chủ vận mệnh cá nhân.',
            ],
            'Deep Work' => [
                'author' => 'Cal Newport',
                'description' => 'Rèn luyện năng lực tập trung sâu độ vào các nhiệm vụ phức tạp, thứ kỹ năng ngày càng quý hiếm và giá trị trong nền kinh tế số đầy xao nhãng. Tác phẩm cung cấp các chiến lược cụ thể giúp bạn làm việc năng suất vượt bậc và tạo ra những kết quả đột phá.',
            ],
            'Start with Why' => [
                'author' => 'Simon Sinek',
                'description' => 'Khám phá mô hình Vòng tròn Vàng kinh điển, giải thích lý do tại sao những nhà lãnh đạo và thương hiệu vĩ đại luôn bắt đầu từ câu hỏi "Tại sao". Tác phẩm truyền cảm hứng mạnh mẽ về việc xây dựng sự gắn kết sâu sắc và khơi dậy lòng trung thành từ bên trong.',
            ],
            'Measure What Matters' => [
                'author' => 'John Doerr',
                'description' => 'Hướng dẫn triển khai phương pháp Mục tiêu và Kết quả Then chốt (OKRs) từng giúp Google và các tập đoàn hàng đầu thế giới tăng trưởng bùng nổ. Cuốn sách giúp tổ chức đồng bộ hóa mục tiêu, duy trì sự tập trung cao độ và đo lường minh bạch những điều thực sự quan trọng.',
            ],
            'Hooked: How to Build Habit-Forming Products' => [
                'author' => 'Nir Eyal',
                'description' => 'Giải mã mô hình 4 bước tạo thói quen (Kích hoạt - Hành động - Phần thưởng biến đổi - Đầu tư) giúp sản phẩm công nghệ thu hút và giữ chân người dùng tự nhiên. Cuốn sách là kim chỉ nam cho các nhà thiết kế và quản trị sản phẩm muốn tạo ra ứng dụng khiến khách hàng gắn bó dài lâu.',
                'cover_image' => 'books/hooked-nir-eyal-fixed.jpg',
            ],

            // ==========================================
            // III. CHỨNG KHOÁN & ĐẦU TƯ TÀI CHÍNH (25 CUỐN)
            // ==========================================
            'The Intelligent Investor' => [
                'author' => 'Benjamin Graham',
                'description' => 'Kinh thánh bất hủ về trường phái đầu tư giá trị, trang bị cho nhà đầu tư tâm lý vững vàng trước biến động và khái niệm cốt lõi "Biên an toàn". Tác phẩm dạy bạn cách tư duy như một chủ doanh nghiệp thay vì một kẻ đầu cơ cảm tính theo đám đông.',
                'cover_image' => 'books/the-intelligent-investor-fixed.jpg',
            ],
            'Reminiscences of a Stock Operator' => [
                'author' => 'Edwin Lefèvre',
                'description' => 'Tác phẩm bán tự truyện kinh điển tái hiện cuộc đời thăng trầm của huyền thoại đầu cơ Jesse Livermore trên sàn giao dịch Phố Wall. Cuốn sách là bài học vô giá về kỷ luật giao dịch, tâm lý bầy đàn và nghệ thuật quản trị vốn trong thị trường khốc liệt.',
            ],
            'How to Make Money in Stocks' => [
                'author' => 'William J. O\'Neil',
                'description' => 'Giới thiệu hệ thống đầu tư CANSLIM lừng danh kết hợp giữa phân tích cơ bản vượt trội và thời điểm mua theo phân tích kỹ thuật chuẩn xác. Độc giả sẽ học được cách chọn lọc siêu cổ phiếu dẫn dắt thị trường và nguyên tắc cắt lỗ nghiêm ngặt để bảo toàn vốn.',
            ],
            'One Up On Wall Street' => [
                'author' => 'Peter Lynch & John Rothchild',
                'description' => 'Chiến lược đầu tư giản dị nhưng hiệu quả phi thường của nhà quản lý quỹ huyền thoại Fidelity Magellan, khuyến khích tìm kiếm cơ hội từ trải nghiệm đời thường. Cuốn sách giúp nhà đầu tư cá nhân tự tin tìm ra những cổ phiếu tăng trưởng mười lần (tenbaggers) ngay trước mắt.',
                'cover_image' => 'books/one-up-on-wall-street-fixed.jpg',
            ],
            'Beating the Street' => [
                'author' => 'Peter Lynch & John Rothchild',
                'description' => 'Hành trình thực tế từng bước lựa chọn cổ phiếu và quản lý danh mục đầu tư từ một trong những nhà đầu tư thành công nhất lịch sử. Tác phẩm đúc kết 21 nguyên tắc vàng giúp bạn kiên định với nghiên cứu độc lập và vượt qua sự ồn ào của thị trường.',
            ],
            'Common Stocks and Uncommon Profits' => [
                'author' => 'Philip A. Fisher',
                'description' => 'Nền tảng tư duy đầu tư tăng trưởng hiện đại với phương pháp "Lời đồn đại" (Scuttlebutt) và 15 tiêu chí đánh giá tiềm năng doanh nghiệp dài hạn. Tác phẩm là nguồn cảm hứng lớn định hình phong cách đầu tư của Warren Buffett khi tìm kiếm những công ty phi thường.',
                'cover_image' => 'books/common-stocks-uncommon-profits-fixed.jpg',
            ],
            'A Random Walk Down Wall Street' => [
                'author' => 'Burton G. Malkiel',
                'description' => 'Khám phá lý thuyết bước đi ngẫu nhiên và hiệu quả thị trường, chứng minh chiến lược nắm giữ quỹ chỉ số chi phí thấp luôn đánh bại các chuyên gia chủ động. Cuốn sách cung cấp kế hoạch phân bổ tài sản thực tế và bền vững cho mọi giai đoạn cuộc đời.',
            ],
            'The Little Book That Still Beats the Market' => [
                'author' => 'Joel Greenblatt',
                'description' => 'Công thức Kỳ diệu (Magic Formula) kết hợp giữa lợi suất sinh lời cao và định giá hấp dẫn để lựa chọn cổ phiếu vượt trội thị trường một cách có hệ thống. Cuốn sách trình bày súc tích, hóm hỉnh nhưng mang lại giá trị ứng dụng thực tiễn cao cho cả người mới bắt đầu.',
            ],
            'Security Analysis' => [
                'author' => 'Benjamin Graham & David Dodd',
                'description' => 'Tác phẩm kinh điển đồ sộ đặt nền móng khoa học cho việc phân tích báo cáo tài chính và định giá chứng khoán chuyên sâu. Cuốn sách trang bị cho các nhà phân tích phương pháp thẩm định tài sản, nợ và dòng tiền chặt chẽ để bảo vệ vốn đầu tư an toàn tuyệt đối.',
            ],
            'Market Wizards' => [
                'author' => 'Jack D. Schwager',
                'description' => 'Phỏng vấn sâu với các phù thủy tài chính hàng đầu thế giới, hé lộ những chiến lược giao dịch đa dạng từ hợp đồng tương lai đến cổ phiếu và tiền tệ. Điểm chung đúc kết là tầm quan trọng tối thượng của kỷ luật cá nhân, kiểm soát cảm xúc và quản trị rủi ro sinh tử.',
            ],
            'The New Market Wizards' => [
                'author' => 'Jack D. Schwager',
                'description' => 'Phần tiếp theo đầy cuốn hút khám phá tư duy và phong cách sống của thế hệ phù thủy thị trường mới trong kỷ nguyên tài chính hiện đại. Tác phẩm khẳng định thành công phi thường trên thị trường không phụ thuộc vào công thức bí mật mà vào sự kiên định tuân thủ kế hoạch riêng.',
            ],
            'Unknown Market Wizards' => [
                'author' => 'Jack D. Schwager',
                'description' => 'Những câu chuyện phi thường về các nhà giao dịch cá nhân độc lập đạt tỷ suất lợi nhuận hàng nghìn phần trăm từ tài khoản khiêm tốn của chính mình. Cuốn sách truyền cảm hứng mãnh liệt và chứng minh bất kỳ ai cũng có thể chiến thắng nếu làm chủ tâm lý và hệ thống giao dịch chuẩn mực.',
            ],
            'Trade Like a Stock Market Wizard' => [
                'author' => 'Mark Minervini',
                'description' => 'Bật mí phương pháp SEPA độc quyền giúp xác định các điểm bùng nổ giá với tỷ lệ lợi nhuận/rủi ro tối ưu nhất trên biểu đồ kỹ thuật. Tác phẩm là giáo trình thực chiến xuất sắc cho những ai khao khát đạt hiệu suất sinh lời siêu đẳng trên thị trường chứng khoán.',
            ],
            'The Psychology of Money' => [
                'author' => 'Morgan Housel',
                'description' => 'Tuyển tập 19 câu chuyện ngắn sâu sắc chỉ ra rằng cách bạn hành xử với tiền bạc quan trọng hơn nhiều so với việc bạn thông minh ra sao. Cuốn sách giúp bạn định hình lại mối quan hệ giữa sự giàu có, lòng tham và hạnh phúc đích thực trong cuộc sống.',
            ],
            'Rich Dad Poor Dad' => [
                'author' => 'Robert T. Kiyosaki',
                'description' => 'Cuốn sách tài chính cá nhân bán chạy nhất mọi thời đại, làm thay đổi hoàn toàn tư duy của hàng triệu người về tài sản, tiêu sản và dòng tiền thụ động. Tác phẩm thức tỉnh độc giả thoát khỏi vòng luẩn quẩn đi làm kiếm tiền và bắt đầu xây dựng con đường tự do tài chính.',
            ],
            'Think and Grow Rich' => [
                'author' => 'Napoleon Hill',
                'description' => 'Kiệt tác kinh điển đúc kết 13 nguyên tắc thành công từ hơn 500 nhân vật kiệt xuất nhất nước Mỹ như Andrew Carnegie, Thomas Edison và Henry Ford. Cuốn sách rèn luyện niềm tin mãnh liệt, mục tiêu rõ ràng và sức mạnh của trí tuệ tập thể để biến ước mơ thành hiện thực.',
            ],
            "The Bogleheads' Guide to Investing" => [
                'author' => 'Mel Lindauer, Taylor Larimore, Michael LeBoeuf',
                'description' => 'Triết lý đầu tư thụ động thông minh theo tinh thần của John Bogle, tập trung vào quỹ chỉ số, đa dạng hóa danh mục và tiết kiệm tối đa chi phí. Cuốn sách là bản hướng dẫn tài chính an tâm, giúp bạn làm giàu bền vững theo năm tháng.',
            ],
            'Principles: Life and Work' => [
                'author' => 'Ray Dalio',
                'description' => 'Bộ nguyên tắc sống và làm việc độc đáo được tôi luyện qua các chu kỳ kinh tế bởi người đứng đầu quỹ phòng hộ lớn nhất hành tinh. Cuốn sách hướng dẫn cách đối diện sự thật trần trụi, học hỏi từ sai lầm và xây dựng văn hóa minh bạch triệt để trong tổ chức.',
                'cover_image' => 'books/principles-life-and-work-fixed.jpg',
            ],
            'Fooled by Randomness' => [
                'author' => 'Nassim Nicholas Taleb',
                'description' => 'Phân tích sắc sảo về vai trò tiềm ẩn của tính ngẫu nhiên và sự may mắn trong sự nghiệp cũng như trên các thị trường tài chính toàn cầu. Tác phẩm cảnh báo về cạm bẫy ngụy biện hồi tưởng, giúp bạn tỉnh táo phân biệt giữa năng lực thực sự và sự may mắn ngẫu nhiên.',
            ],
            'The Black Swan' => [
                'author' => 'Nassim Nicholas Taleb',
                'description' => 'Khảo luận chấn động về các biến cố Thiên Nga Đen – những sự kiện cực hiếm, tác động khủng khiếp nhưng luôn bị con người hợp lý hóa sau khi xảy ra. Tác phẩm rèn luyện tư duy phản biện sắc bén và khả năng sinh tồn vững chãi trước những cú sốc khó lường của thời đại.',
            ],
            'Margin of Safety' => [
                'author' => 'Seth A. Klarman',
                'description' => 'Tác phẩm đầu tư giá trị quý hiếm bậc nhất, tập trung cao độ vào việc bảo toàn vốn và quản trị rủi ro trước khi tìm kiếm lợi nhuận. Tác giả chỉ ra sự nguy hiểm của tâm lý ngắn hạn trên Phố Wall và tầm quan trọng sống còn của việc kiên nhẫn chờ đợi cơ hội định giá sai.',
            ],
            'Mastering the Market Cycle' => [
                'author' => 'Howard Marks',
                'description' => 'Khám phá nhịp điệu của các chu kỳ kinh tế, tín dụng và tâm lý thị trường để nhận biết vị thế hiện tại và nghiêng xác suất thắng về phía mình. Cuốn sách là tài liệu gối đầu giường giúp nhà đầu tư làm chủ cảm xúc khi thị trường chuyển biến giữa lòng tham và nỗi sợ hãi.',
                'cover_image' => 'books/mastering-the-market-cycle-4k-ultra.jpg',
            ],
            'Value Investing: From Graham to Buffett' => [
                'author' => 'Bruce C. N. Greenwald et al.',
                'description' => 'Giáo trình hiện đại hóa trường phái đầu tư giá trị từ Đại học Columbia, cung cấp khung định giá 3 bước thực tế từ giá trị tài sản đến năng lực sinh lời và con hào kinh tế. Cuốn sách là cây cầu hoàn hảo kết nối giữa học thuyết hàn lâm và nghệ thuật đầu tư thực chiến.',
                'cover_image' => 'books/value-investing-from-graham-to-buffett-4k-ultra.jpg',
            ],
            'The Alchemy of Finance' => [
                'author' => 'George Soros',
                'description' => 'Công bố lý thuyết phản xạ (Reflexivity) chấn động của nhà đầu cơ huyền thoại, chứng minh nhận thức của con người luôn làm méo mó các yếu tố cơ bản của thị trường. Tác phẩm là cái nhìn sâu sắc và độc nhất vô nhị vào tâm trí của người từng làm khuynh đảo Ngân hàng Trung ương Anh.',
                'cover_image' => 'books/the-alchemy-of-finance-4k-ultra.jpg',
            ],
            'Trading for a Living' => [
                'author' => 'Dr. Alexander Elder',
                'description' => 'Bộ ba chìa khóa vàng gồm Tâm lý giao dịch, Chiến thuật kỹ thuật và Quản lý dòng tiền giúp nhà đầu tư xây dựng sự nghiệp giao dịch chuyên nghiệp. Tác phẩm cung cấp các chỉ báo thực chiến và hệ thống kỷ luật sắt đá để tồn tại và thịnh vượng trên thị trường tài chính.',
                'cover_image' => 'books/trading-for-a-living-4k-ultra.jpg',
            ],

            // ==========================================
            // IV. TRUYỆN ANIME & MANGA (25 BỘ)
            // ==========================================
            'Manga One Piece Vol 1' => [
                'author' => 'Eiichiro Oda',
                'description' => 'Mở đầu chuyến hải trình vĩ đại của cậu bé người cao su Monkey D. Luffy cùng ước mơ cháy bỏng trở thành Vua Hải Tặc trên biển lớn. Tác phẩm là khúc ca tráng lệ về tình bạn keo sơn, lòng dũng cảm bất khuất và khát vọng tự do cháy bỏng của tuổi trẻ.',
            ],
            'Manga Naruto Vol 1' => [
                'author' => 'Masashi Kishimoto',
                'description' => 'Khởi đầu hành trình đầy nghị lực của cậu bé mồ côi Naruto mang trong mình Cửu Vĩ hồ ly, quyết tâm vượt qua định kiến để trở thành Hokage làng Lá. Cuốn truyện truyền cảm hứng mạnh mẽ về tinh thần không bao giờ bỏ cuộc và giá trị của sự thấu hiểu, sẻ chia.',
            ],
            'Manga Dragon Ball Vol 1' => [
                'author' => 'Akira Toriyama',
                'description' => 'Cuộc gặp gỡ định mệnh giữa cậu bé đuôi khỉ Son Goku và cô nàng Bulma, mở ra chuyến phiêu lưu kỳ thú đi tìm 7 viên ngọc rồng quyền năng. Tác phẩm kinh điển vượt thời gian đưa độc giả vào thế giới võ thuật đầy ắp tiếng cười, lòng chính nghĩa và sự thuần khiết.',
            ],
            'Manga Bleach Vol 1' => [
                'author' => 'Tite Kubo',
                'description' => 'Nam sinh trung học Kurosaki Ichigo bất ngờ nhận được sức mạnh Tử thần từ Rukia để bảo vệ gia đình và thị trấn khỏi những linh hồn tà ác Hollow. Cuốn truyện cuốn hút độc giả bởi những trận chiến trảm hồn đao mãn nhãn, phong cách thời thượng và trách nhiệm gánh vác định mệnh.',
            ],
            'Manga Detective Conan Vol 1' => [
                'author' => 'Gosho Aoyama',
                'description' => 'Thám tử học sinh tài ba Kudo Shinichi bị Tổ chức Áo đen đầu độc teo nhỏ thành Edogawa Conan, mở ra hành trình phá án thiên tài dưới vỏ bọc học sinh tiểu học. Tác phẩm rèn luyện tư duy suy luận logic sắc bén và tôn vinh chân lý: Sự thật luôn luôn chỉ có một!',
                'cover_image' => 'books/manga-detective-conan-vol-1-fixed.jpg',
            ],
            'Manga Doraemon Vol 1' => [
                'author' => 'Fujiko F. Fujio',
                'description' => 'Chú mèo máy thông minh Doraemon đến từ thế kỷ 22 cùng chiếc túi bảo bối thần kỳ bước vào cuộc đời của cậu bé vụng về Nobita. Tác phẩm nuôi dưỡng trí tưởng tượng phong phú, lòng nhân ái và mang đến những tiếng cười tuổi thơ ấm áp cho nhiều thế hệ độc giả.',
                'cover_image' => 'books/manga-doraemon-vol-1-fixed.jpg',
            ],
            'Manga Attack on Titan Vol 1' => [
                'author' => 'Hajime Isayama',
                'description' => 'Nhân loại sống co cụm sau những bức tường thành khổng lồ bỗng chốc sụp đổ trước sự tàn phá của loài Titan ăn thịt người khát máu. Tác phẩm là thiên sử thi bi tráng về cuộc đấu tranh sinh tồn tuyệt vọng, bản chất tàn khốc của chiến tranh và khát khao tự do tột cùng.',
            ],
            'Manga Demon Slayer Vol 1' => [
                'author' => 'Koyoharu Gotouge',
                'description' => 'Sau khi gia đình bị quỷ tàn sát và em gái Nezuko biến thành quỷ, chàng thiếu niên giàu lòng trắc ẩn Tanjiro gia nhập Sát Quỷ Đội để tìm thuốc chữa. Cuốn truyện lay động triệu con tim bởi tình anh em thiêng liêng, lòng kiên trì phi thường và những đường kiếm hơi thở rực rỡ.',
            ],
            'Manga Jujutsu Kaisen Vol 1' => [
                'author' => 'Gege Akutami',
                'description' => 'Thiếu niên sở hữu thể chất phi phàm Itadori Yuji nuốt ngón tay nguyền vương Sukuna để cứu bạn bè, chính thức dấn thân vào thế giới nguyền sư nguy hiểm. Tác phẩm mang đến những trận chiến chú thuật nghẹt thở, góc nhìn sâu sắc về ranh giới sinh tử và ý nghĩa của sự hy sinh.',
            ],
            'Manga My Hero Academia Vol 1' => [
                'author' => 'Kohei Horikoshi',
                'description' => 'Cậu bé vô năng Midoriya Izuku nhận được ngọn lửa kế thừa từ anh hùng số một All Might để bước chân vào học viện đào tạo siêu anh hùng U.A. Cuốn truyện thắp sáng niềm tin rằng một anh hùng đích thực được định nghĩa bởi trái tim quả cảm luôn sẵn sàng cứu giúp người khác.',
            ],
            'Manga Hunter x Hunter Vol 1' => [
                'author' => 'Yoshihiro Togashi',
                'description' => 'Chàng thiếu niên hoang dã Gon Freecss lên đường tham dự kỳ thi tuyển chọn Thợ săn khắc nghiệt để tìm kiếm tung tích người cha huyền thoại. Tác phẩm nổi tiếng với hệ thống chiến đấu Niệm tinh vi, cốt truyện khó lường và chiều sâu tâm lý phức tạp giữa các nhân vật.',
            ],
            'Manga Fullmetal Alchemist Vol 1' => [
                'author' => 'Hiromu Arakawa',
                'description' => 'Hai anh em Edward và Alphonse Elric phạm phải điều cấm kỵ của thuật giả kim và phải dấn thân vào hành trình tìm kiếm Hòn đá Triết gia để chuộc lại lỗi lầm. Kiệt tác hoàn hảo về mặt nội dung, truyền tải bài học thấm thía về quy luật trao đổi đồng giá và giá trị bất diệt của gia đình.',
            ],
            'Manga Death Note Vol 1' => [
                'author' => 'Tsugumi Ohba & Takeshi Obata',
                'description' => 'Học sinh ưu tú Yagami Light nhặt được quyển sổ Thần chết có khả năng tước đoạt sinh mạng, tự xưng là vị thần thanh trừng tội phạm dưới sự truy lùng của thám tử L. Tác phẩm là cuộc đấu trí cân não nghẹt thở về công lý, đạo đức và sự tha hóa của quyền lực tuyệt đối.',
            ],
            'Manga Tokyo Ghoul Vol 1' => [
                'author' => 'Sui Ishida',
                'description' => 'Sau một tai nạn bất ngờ, sinh viên đại học Kaneki Ken bị cấy ghép nội tạng ngạ quỷ và buộc phải học cách sinh tồn giữa ranh giới mong manh giữa người và quỷ. Cuốn truyện xoáy sâu vào sự cô đơn, nỗi đau giằng xé nội tâm và khát vọng tìm kiếm sự thấu hiểu trong một thế giới đầy định kiến.',
            ],
            'Manga One Punch Man Vol 1' => [
                'author' => 'ONE & Yusuke Murata',
                'description' => 'Anh hùng Saitama luyện tập đến mức hói đầu và có thể hạ gục mọi quái vật vũ trụ chỉ bằng một cú đấm duy nhất, rơi vào nỗi chán chường vì quá vô địch. Tác phẩm châm biếm hài hước dòng truyện siêu anh hùng nhưng sở hữu nét vẽ hành động chi tiết và đỉnh cao bậc nhất giới manga.',
            ],
            'Manga Haikyuu!! Vol 1' => [
                'author' => 'Haruichi Furudate',
                'description' => 'Chàng trai nhỏ thon Hinata Shoyo với khả năng bật nhảy phi thường kết hợp cùng chuyền hai thiên tài Kageyama đưa đội bóng chuyền trường Karasuno trở lại đỉnh cao. Tác phẩm bùng nổ nhiệt huyết tuổi trẻ, tinh thần đồng đội keo sơn và niềm say mê thể thao thuần khiết, rực lửa.',
            ],
            'Manga Spy x Family Vol 1' => [
                'author' => 'Tatsuya Endo',
                'description' => 'Điệp viên lừng danh Twilight phải lập một gia đình giả tạo gồm một nữ sát thủ và cô bé có khả năng đọc suy nghĩ để thực hiện nhiệm vụ hòa bình thế giới. Cuốn truyện kết hợp duyên dáng giữa hành động kịch tính và những khoảnh khắc gia đình ấm áp, mang lại tiếng cười sảng khoái cho độc giả.',
            ],
            'Manga Chainsaw Man Vol 1' => [
                'author' => 'Tatsuki Fujimoto',
                'description' => 'Chàng trai nghèo khổ Denji hồi sinh nhờ hợp thể với quỷ cưa Pochita, trở thành thợ săn quỷ cho tổ chức an ninh công cộng để theo đuổi những ước mơ giản dị đời thường. Phong cách kể chuyện điên rồ, bạo liệt nhưng thấm đượm chất điện ảnh và nỗi niềm trăn trở về bản ngã con người.',
            ],
            'Manga Black Clover Vol 1' => [
                'author' => 'Yuki Tabata',
                'description' => 'Cậu bé không sở hữu chút ma pháp nào tên Asta sở hữu cuốn ma đạo thư năm lá phản ma pháp, quyết tâm thách thức số phận để trở thành Ma Pháp Vương. Câu chuyện tiếp thêm ngọn lửa nhiệt huyết về tinh thần kiên cường không chịu khuất phục trước nghịch cảnh và số phận định sẵn.',
            ],
            'Manga Fairy Tail Vol 1' => [
                'author' => 'Hiro Mashima',
                'description' => 'Natsu Dragneel – chàng pháp sư mang sức mạnh Hỏa Long cùng chú mèo Happy dắt mối cô bạn Lucy gia nhập hội pháp sư ồn ào và lẫy lừng nhất vương quốc: Fairy Tail. Tác phẩm rực rỡ sắc màu phiêu lưu ma thuật, tôn vinh tình bằng hữu xem nhau như máu thịt gia đình.',
                'cover_image' => 'books/manga-fairy-tail-vol-1-fixed.jpg',
            ],
            'Manga Gintama Vol 1' => [
                'author' => 'Hideaki Sorachi',
                'description' => 'Tại thành Edo thời kỳ bị người ngoài hành tinh Amanto xâm chiếm, chàng samurai tóc bạc lười biếng Sakata Gintoki mở tiệm Vạn Năng để kiếm sống qua ngày. Kiệt tác hài hước lầy lội bậc nhất lịch sử manga nhưng ẩn chứa linh hồn võ sĩ đạo bất khuất và tình người sâu nặng trong thời loạn.',
            ],
            'Manga JoJo\'s Bizarre Adventure Vol 1' => [
                'author' => 'Hirohiko Araki',
                'description' => 'Mở đầu biên niên sử kỳ ảo truyền qua nhiều thế hệ của dòng họ Joestar trong cuộc chiến chống lại kẻ phản diện mưu mô Dio Brando với chiếc mặt nạ đá bí ẩn. Tác phẩm ghi dấu ấn với phong cách hội họa độc dị, tư thế tạo hình kinh điển và ngợi ca vẻ đẹp của lòng dũng cảm con người.',
            ],
            'Manga Sailor Moon Vol 1' => [
                'author' => 'Naoko Takeuchi',
                'description' => 'Nữ sinh mít ướt Usagi Tsukino thức tỉnh thành chiến binh Thủy Thủ Mặt Trăng, cùng các đồng đội bảo vệ Trái Đất và tìm kiếm công chúa Mặt Trăng thất lạc. Biểu tượng văn hóa toàn cầu của thể loại thiếu nữ phép thuật, ngợi ca tình yêu, hòa bình và sức mạnh tự chủ của phái nữ.',
            ],
            'Manga Slam Dunk Vol 1' => [
                'author' => 'Takehiko Inoue',
                'description' => 'Cậu học sinh cá biệt tóc đỏ Hanamichi Sakuragi ban đầu gia nhập đội bóng rổ Shohoku chỉ để cưa cẩm bạn gái, nhưng dần tìm thấy niềm đam mê đích thực của cuộc đời. Kiệt tác thể thao kinh điển lay động hàng triệu trái tim với những trận cầu nghẹt thở và khát vọng chiến thắng cháy bỏng.',
            ],
            'Manga Yu-Gi-Oh! Vol 1' => [
                'author' => 'Kazuki Takahashi',
                'description' => 'Cậu bé nhút nhát Yugi Muto giải thành công Trò Chơi Ngàn Năm, đánh thức nhân cách thứ hai là vị Pharaoh cổ đại tài ba chuyên trừng phạt kẻ xấu qua các trò chơi bóng tối. Tác phẩm đặt nền móng cho hiện tượng văn hóa toàn cầu về đấu bài ma thuật, đề cao lòng dũng cảm và niềm tin vào bạn bè.',
                'cover_image' => 'books/manga-yu-gi-oh-vol-1-fixed.jpg',
            ],
        ];

        $updated = 0;
        foreach ($bookDetails as $title => $info) {
            $updateData = [
                'author' => $info['author'],
                'description' => $info['description'],
                'updated_at' => now(),
            ];
            if (! empty($info['cover_image'])) {
                $updateData['cover_image'] = $info['cover_image'];
            }
            $count = DB::table('books')
                ->where('title', $title)
                ->update($updateData);

            if ($count > 0) {
                $updated += $count;
            }
        }

        $this->command->info("[OK] Đã cập nhật thành công {$updated}/100 cuốn sách với tác giả và tóm tắt chuẩn xác!");
    }
}
