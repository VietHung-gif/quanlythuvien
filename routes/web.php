<?php

// 1. Quản trị hệ thống (Admin)
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\FinancialReportController;
// 2. Nghiệp vụ sách & Mượn trả (Library)
use App\Http\Controllers\Community\ChatbotController;
use App\Http\Controllers\Community\EntertainmentController;
use App\Http\Controllers\Community\ForumController;
use App\Http\Controllers\Library\BookController;
// 3. Cộng đồng & Tương tác (Community)
use App\Http\Controllers\Library\BorrowController;
use App\Http\Controllers\Library\CartController;
use App\Http\Controllers\Library\CategoryController;
// 4. Tài khoản & Cá nhân (User)
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\WalletController;
use App\Models\Book;
use App\Models\BorrowRecord;
use App\Models\Category;
use App\Models\ForumPost;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// =========================================================================
// ROUTE CÔNG KHAI (PUBLIC ACCESS)
// =========================================================================
Route::get('/', function () {
    $featuredBooks = Book::with('category')->latest()->take(8)->get();
    $categories = Category::withCount('books')->get();
    $totalBooks = Book::count();
    $totalCopies = Book::sum('quantity');
    $totalBorrowRecords = BorrowRecord::count();
    $totalUsers = User::count();
    $announcements = ForumPost::with('user')
        ->whereHas('category', function ($q) {
            $q->where('slug', 'thong-bao-thu-vien');
        })
        ->latest()
        ->take(3)
        ->get();

    return view('welcome', compact('featuredBooks', 'categories', 'totalBooks', 'totalCopies', 'totalBorrowRecords', 'totalUsers', 'announcements'));
})->name('home');
Route::redirect('/home', '/');

// Khám phá thể loại sách (Công khai cho mọi độc giả & khách vãng lai)
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->whereNumber('category')->name('categories.show');

Route::post('/chatbot/message', [ChatbotController::class, 'handle'])->middleware('throttle:30,1')->name('chatbot.message');
Route::post('/chatbot/clear', [ChatbotController::class, 'clearHistory'])->name('chatbot.clear');

// =========================================================================
// PHÂN HỆ: KHU GIẢI TRÍ TRI THỨC & THỬ THÁCH ĐỌC SÁCH (READING QUESTS HUB)
// =========================================================================
Route::prefix('entertainment')->name('entertainment.')->group(function () {
    // 1. Chuỗi thử thách đọc sách & Hệ thống nhiệm vụ tuần (Trang chính)
    Route::get('/', [EntertainmentController::class, 'index'])->name('index');

    // 2. Nhận thưởng nhiệm vụ & Điểm danh hàng ngày
    Route::middleware('auth')->group(function () {
        Route::post('/checkin', [EntertainmentController::class, 'checkin'])->name('checkin');
        Route::post('/claim-reward', [EntertainmentController::class, 'claimReward'])->name('claim-reward');
    });

    // 3. Bảng Phong Thần "Mọt Sách Chiến Tướng" (Leaderboard)
    Route::get('/leaderboard', [EntertainmentController::class, 'leaderboard'])->name('leaderboard');
});

// =========================================================================
// PHÂN HỆ: GIỎ SÁCH & ĐĂNG KÝ PHIẾU MƯỢN (BOOK CART SYSTEM)
// =========================================================================
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/update-quantity', [CartController::class, 'updateQuantity'])->name('cart.update-quantity');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/borrow/create', [BorrowController::class, 'createBorrowFromCart'])->name('borrow.create');
});

// =========================================================================
// PHÂN HỆ: HỘI QUÁN TRI THỨC // DIỄN ĐÀN (COMMUNITY FORUM)
// =========================================================================
Route::prefix('forum')->name('forum.')->group(function () {
    // Xem danh sách và xem chi tiết bài viết (Công khai)
    Route::get('/', [ForumController::class, 'index'])->name('index');
    Route::get('/create', [ForumController::class, 'create'])->middleware('auth')->name('create');
    Route::post('/', [ForumController::class, 'store'])->middleware('auth')->name('store');
    Route::get('/{post}', [ForumController::class, 'show'])->whereNumber('post')->name('show');

    // Thao tác tương tác (Bình luận, Thả tim, Ghim, Xóa, Sửa) - Yêu cầu đăng nhập
    Route::middleware('auth')->group(function () {
        Route::get('/{post}/edit', [ForumController::class, 'edit'])->whereNumber('post')->name('edit');
        Route::put('/{post}', [ForumController::class, 'update'])->whereNumber('post')->name('update');
        Route::post('/{post}/comment', [ForumController::class, 'comment'])->whereNumber('post')->name('comment');
        Route::post('/{post}/like', [ForumController::class, 'toggleLike'])->whereNumber('post')->name('like');
        Route::delete('/{post}', [ForumController::class, 'destroy'])->whereNumber('post')->name('destroy');
        Route::delete('/comment/{comment}', [ForumController::class, 'destroyComment'])->whereNumber('comment')->name('comment.destroy');
        Route::post('/{post}/pin', [ForumController::class, 'togglePin'])->whereNumber('post')->name('pin');
    });
});

// =========================================================================
// NHÓM 1: DÀNH RIÊNG CHO ADMIN (QUẢN TRỊ VIÊN CAO CẤP)
// Middleware: ['auth', 'role:admin']
// =========================================================================
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Quản lý thành viên / phân quyền hệ thống (RBAC) & Nhật ký kiểm toán (Audit Logs)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/role', [AdminUserController::class, 'updateRole'])->whereNumber('user')->name('users.update-role');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->whereNumber('user')->name('users.destroy');
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });

    // Quyền xóa vĩnh viễn sách và thể loại (Chỉ Admin mới có quyền xóa)
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->whereNumber('book')->name('books.destroy');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->whereNumber('category')->name('categories.destroy');
});

// =========================================================================
// NHÓM 2: DÀNH CHO ADMIN & THỦ THƯ (LIBRARIAN)
// Middleware: ['auth', 'role:admin,librarian']
// =========================================================================
Route::middleware(['auth', 'role:admin,librarian'])->group(function () {
    // Bảng điều khiển quản trị (Dashboard / Control Panel)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Quản lý kho sách: Thêm mới, chỉnh sửa thông tin, cập nhật tồn kho (trừ xóa)
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::get('/books/{book}/edit', [BookController::class, 'edit'])->whereNumber('book')->name('books.edit');
    Route::put('/books/{book}', [BookController::class, 'update'])->whereNumber('book')->name('books.update');
    Route::patch('/books/{book}/stock', [BookController::class, 'updateStock'])->whereNumber('book')->name('books.update-stock');

    // Quản lý Thể loại sách: Thêm, sửa (Chỉ dành cho Admin & Thủ thư)
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->whereNumber('category')->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->whereNumber('category')->name('categories.update');

    // Quản lý Mượn / Trả sách, Phê duyệt, Tính tiền phạt & Thu tiền phạt tại quầy
    Route::resource('borrows', BorrowController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::post('borrows/{borrow}/return', [BorrowController::class, 'returnBook'])->name('borrows.return');
    Route::post('borrows/{borrow}/approve', [BorrowController::class, 'approveRequest'])->name('borrows.approve');
    Route::post('borrows/{borrow}/reject', [BorrowController::class, 'rejectRequest'])->name('borrows.reject');
    Route::post('borrows/{borrow}/pay-fine', [BorrowController::class, 'payFine'])->name('borrows.payFine');
    Route::post('/admin/borrows/{borrow}/approve-renewal', [BorrowController::class, 'approveRenewal'])->whereNumber('borrow')->name('admin.borrows.approve-renewal');
    Route::post('/admin/borrows/{borrow}/reject-renewal', [BorrowController::class, 'rejectRenewal'])->whereNumber('borrow')->name('admin.borrows.reject-renewal');

    // Báo cáo Tài chính & Ngân sách Thư viện (Financial & Fund Management)
    Route::get('/admin/finances', [FinancialReportController::class, 'index'])->name('admin.finances.index');
    Route::post('/admin/finances/sponsorship', [FinancialReportController::class, 'storeSponsorship'])->name('admin.finances.store-sponsorship');
});

// =========================================================================
// NHÓM 3: DÀNH CHO ĐỘC GIẢ & TẤT CẢ USER ĐÃ ĐĂNG NHẬP
// Middleware: ['auth']
// =========================================================================
Route::middleware('auth')->group(function () {
    // Ví số dư điện tử & Gói hội viên (Digital Wallet & Membership)
    Route::get('/membership', [WalletController::class, 'membership'])->name('membership.index');
    Route::post('/membership/upgrade', [WalletController::class, 'upgradeVip'])->name('membership.upgrade');
    Route::get('/wallet/info', [WalletController::class, 'info'])->name('wallet.info');
    Route::get('/wallet/deposit', [WalletController::class, 'deposit'])->name('wallet.deposit');
    Route::post('/wallet/deposit', [WalletController::class, 'processDeposit'])->name('wallet.deposit.process');
    Route::post('/wallet/deposit-mock', [WalletController::class, 'processDeposit'])->name('wallet.deposit-mock');
    Route::post('/wallet/upgrade-vip', [WalletController::class, 'upgradeVip'])->name('wallet.upgrade-vip');
    Route::post('/wallet/pay-fine/{borrow}', [WalletController::class, 'payFineFromWallet'])->name('wallet.pay-fine');

    // Hồ sơ cá nhân (Profile)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Tra cứu danh mục sách & xem chi tiết
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::get('/books/{book}', [BookController::class, 'show'])->whereNumber('book')->name('books.show');

    // Độc giả: Xem danh sách sách cá nhân đang mượn & gửi yêu cầu mượn trực tuyến / xin gia hạn
    Route::get('/my-borrows', [BorrowController::class, 'myBorrows'])->name('borrows.my');
    Route::post('/books/{book}/request-borrow', [BorrowController::class, 'requestBorrow'])->name('borrows.request');
    Route::delete('/my-borrows/{borrow}/cancel', [BorrowController::class, 'cancelRequest'])->name('borrows.cancel');
    Route::post('/borrows/{borrow}/request-renewal', [BorrowController::class, 'requestRenewal'])->whereNumber('borrow')->name('borrows.request-renewal');

    // Theo dõi phiếu đăng ký mượn cá nhân (Theo phiếu & Theo đầu sách)
    Route::get('/tracking-borrows', [BorrowController::class, 'tracking'])->name('borrows.tracking');
    Route::get('/borrows-detail/{borrow}', [BorrowController::class, 'slipDetail'])->name('borrows.slip-detail');
});

require __DIR__.'/auth.php';
