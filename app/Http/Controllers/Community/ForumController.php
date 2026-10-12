<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\ForumCategory;
use App\Models\ForumComment;
use App\Models\ForumPost;
use App\Models\ForumPostLike;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ForumController extends Controller
{
    /**
     * Danh sách bài viết trong Hội Quán Tri Thức.
     * Hỗ trợ lọc theo chuyên mục, tìm kiếm và sắp xếp.
     */
    public function index(Request $request): View
    {
        $categories = ForumCategory::withCount('posts')->get();
        $selectedCategorySlug = $request->input('category');
        $search = $request->input('q');
        $sort = $request->input('sort', 'newest'); // 'newest', 'popular', 'comments'

        $selectedCategory = null;
        if ($selectedCategorySlug) {
            $selectedCategory = $categories->firstWhere('slug', $selectedCategorySlug);
        }

        $query = ForumPost::with(['user', 'category', 'book', 'likes'])
            ->withCount(['comments', 'likes']);

        // 1. Lọc theo chuyên mục
        if ($selectedCategory) {
            $query->where('category_id', $selectedCategory->id);
        }

        // 2. Tìm kiếm theo tiêu đề hoặc nội dung
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // 3. Sắp xếp (Ưu tiên bài ghim is_pinned lên đầu)
        $query->orderByDesc('is_pinned');

        match ($sort) {
            'popular' => $query->orderByDesc('likes_count')->orderByDesc('created_at'),
            'comments' => $query->orderByDesc('comments_count')->orderByDesc('created_at'),
            default => $query->orderByDesc('created_at'),
        };

        $posts = $query->paginate(10)->withQueryString();

        // Danh sách sách để phục vụ Book Tagging (Modal tạo bài)
        $books = Book::select('id', 'title', 'author', 'quantity', 'cover_image')
            ->orderBy('title')
            ->get();

        return view('forum.index', compact(
            'categories',
            'selectedCategory',
            'posts',
            'books',
            'search',
            'sort'
        ));
    }

    /**
     * Màn hình tạo bài viết mới độc lập.
     */
    public function create(): View
    {
        if (ForumCategory::count() === 0) {
            $defaultCategories = [
                ['name' => 'Góc Review Sách', 'slug' => 'review-sach', 'icon' => 'star', 'description' => 'Cảm nhận, đánh giá sách', 'color' => 'cyan'],
                ['name' => 'Hỏi Đáp & Tìm Sách', 'slug' => 'hoi-dap-tim-tai-lieu', 'icon' => 'help', 'description' => 'Hỏi đáp và tìm kiếm sách', 'color' => 'amber'],
                ['name' => 'Luận Bàn Tri Thức', 'slug' => 'luan-ban-tri-thuc', 'icon' => 'chat', 'description' => 'Thảo luận tri thức', 'color' => 'purple'],
                ['name' => 'Thông Báo Thư Viện', 'slug' => 'thong-bao-thu-vien', 'icon' => 'announcement', 'description' => 'Thông báo từ thư viện', 'color' => 'pink'],
            ];
            foreach ($defaultCategories as $cat) {
                ForumCategory::firstOrCreate(['slug' => $cat['slug']], $cat);
            }
        }

        $categories = ForumCategory::all();
        $books = Book::select('id', 'title', 'author')->orderBy('title')->get();

        return view('forum.create', compact('categories', 'books'));
    }

    /**
     * Lưu bài viết mới.
     * NGUYÊN TẮC: KHÔNG CỘNG EXP KHI ĐĂNG BÀI ĐỂ TRÁNH SPAM CÀY CẤP.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:forum_categories,id',
            'content' => 'required|string|min:5',
            'book_id' => 'nullable|exists:books,id',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề bài viết.',
            'category_id.required' => 'Vui lòng chọn chuyên mục.',
            'content.required' => 'Vui lòng nhập nội dung bài viết.',
            'content.min' => 'Nội dung bài viết tối thiểu 5 ký tự.',
        ]);

        try {
            $post = ForumPost::create([
                'user_id' => auth()->id(),
                'category_id' => $validated['category_id'],
                'book_id' => ! empty($validated['book_id']) ? $validated['book_id'] : null,
                'title' => trim($validated['title']),
                'content' => trim($validated['content']),
                'views_count' => 0,
                'likes_count' => 0,
                'is_pinned' => false,
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đăng bài viết mới thành công!',
                    'redirect' => route('forum.show', $post->id),
                ]);
            }

            return redirect()->route('forum.show', $post->id)->with('success', 'Đăng bài viết mới thành công!');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi lưu bài: '.$e->getMessage(),
                ], 500);
            }

            return back()->withInput()->with('error', 'Lỗi lưu bài: '.$e->getMessage());
        }
    }

    /**
     * Xem chi tiết bài viết và danh sách bình luận.
     */
    public function show(Request $request, ForumPost $post): View
    {
        // Tăng lượt xem (Tránh spam F5 tăng vô hạn trong cùng một session)
        $viewKey = 'viewed_forum_post_'.$post->id;
        if (! $request->session()->has($viewKey)) {
            $post->increment('views_count');
            $request->session()->put($viewKey, true);
        }

        $post->load([
            'user',
            'category',
            'book',
            'comments.user',
            'likes',
        ]);

        // Các bài viết gợi ý cùng chuyên mục
        $relatedPosts = ForumPost::where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->latest()
            ->take(4)
            ->get();

        return view('forum.show', compact('post', 'relatedPosts'));
    }

    /**
     * Màn hình chỉnh sửa bài viết (Chỉ tác giả hoặc Admin).
     */
    public function edit($id): View|RedirectResponse
    {
        $post = $id instanceof ForumPost ? $id : ForumPost::findOrFail($id);

        if (auth()->id() !== $post->user_id && auth()->user()->role !== 'admin') {
            return redirect()->route('forum.show', $post->id)
                ->with('error', 'Bạn không có quyền chỉnh sửa bài viết này!');
        }

        $categories = ForumCategory::all();
        $books = Book::select('id', 'title', 'author', 'quantity', 'cover_image')
            ->orderBy('title')
            ->get();

        return view('forum.edit', compact('post', 'categories', 'books'));
    }

    /**
     * Cập nhật bài viết trên diễn đàn (Chỉ tác giả hoặc Admin).
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $post = $id instanceof ForumPost ? $id : ForumPost::findOrFail($id);

        if (auth()->id() !== $post->user_id && auth()->user()->role !== 'admin') {
            return redirect()->route('forum.show', $post->id)
                ->with('error', 'Bạn không có quyền chỉnh sửa bài viết này!');
        }

        $request->validate([
            'title' => 'required|string|min:5|max:255',
            'category_id' => 'required|exists:forum_categories,id',
            'content' => 'required|string|min:10|max:10000',
            'book_id' => 'nullable|exists:books,id',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề bài viết.',
            'title.min' => 'Tiêu đề bài viết cần ít nhất 5 ký tự.',
            'category_id.required' => 'Vui lòng chọn chuyên mục phù hợp.',
            'content.required' => 'Vui lòng nhập nội dung bài viết.',
            'content.min' => 'Nội dung bài viết cần ít nhất 10 ký tự.',
        ]);

        $post->update([
            'title' => $request->input('title'),
            'category_id' => $request->input('category_id'),
            'content' => $request->input('content'),
            'book_id' => $request->input('book_id') ?: null,
        ]);

        return redirect()->route('forum.show', $post->id)
            ->with('success', 'Cập nhật bài viết thành công!');
    }

    /**
     * Gửi bình luận vào bài viết.
     * NGUYÊN TẮC: KHÔNG CỘNG EXP KHI BÌNH LUẬN.
     */
    public function comment(Request $request, ForumPost $post): RedirectResponse|JsonResponse
    {
        $request->validate([
            'content' => 'required|string|min:2|max:2000',
        ], [
            'content.required' => 'Vui lòng nhập nội dung bình luận.',
            'content.min' => 'Bình luận tối thiểu 2 ký tự.',
        ]);

        $comment = ForumComment::create([
            'post_id' => $post->id,
            'user_id' => $request->user()->id,
            'content' => $request->input('content'),
        ]);

        $comment->load('user');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Gửi bình luận thành công!',
                'comment' => $comment,
            ]);
        }

        return redirect()->route('forum.show', $post->id)
            ->with('success', 'Đã đăng bình luận của bạn thành công!');
    }

    /**
     * Thả tim / Bỏ tim bài viết bằng AJAX.
     */
    public function toggleLike(Request $request, ForumPost $post): JsonResponse
    {
        $user = $request->user();
        $existing = ForumPostLike::where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $post->decrement('likes_count');
            $liked = false;
        } else {
            ForumPostLike::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
            ]);
            $post->increment('likes_count');
            $liked = true;
        }

        $freshCount = $post->fresh()->likes_count;

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => $freshCount,
        ]);
    }

    /**
     * Xóa bài viết (Chỉ tác giả hoặc Admin / Thủ thư).
     */
    public function destroy(Request $request, ForumPost $post): RedirectResponse
    {
        $user = $request->user();
        if ($post->user_id !== $user->id && ! in_array($user->role, ['admin', 'librarian'])) {
            abort(403, 'Bạn không có quyền xóa bài viết này.');
        }

        $post->delete();

        return redirect()->route('forum.index')
            ->with('success', 'Đã xóa bài viết khỏi diễn đàn thành công!');
    }

    /**
     * Xóa bình luận (Chỉ người viết bình luận hoặc Admin / Thủ thư).
     */
    public function destroyComment(Request $request, ForumComment $comment): RedirectResponse
    {
        $user = $request->user();
        if ($comment->user_id !== $user->id && ! in_array($user->role, ['admin', 'librarian'])) {
            abort(403, 'Bạn không có quyền xóa bình luận này.');
        }

        $postId = $comment->post_id;
        $comment->delete();

        return redirect()->route('forum.show', $postId)
            ->with('success', 'Đã xóa bình luận thành công!');
    }

    /**
     * Ghim / Bỏ ghim bài viết (Dành cho Admin / Thủ thư).
     */
    public function togglePin(Request $request, ForumPost $post): RedirectResponse
    {
        $user = $request->user();
        if (! in_array($user->role, ['admin', 'librarian'])) {
            abort(403, 'Chỉ Quản trị viên hoặc Thủ thư mới có quyền ghim bài.');
        }

        $post->is_pinned = ! $post->is_pinned;
        $post->save();

        $status = $post->is_pinned ? 'Đã ghim bài viết lên đầu diễn đàn!' : 'Đã bỏ ghim bài viết!';

        return redirect()->back()->with('success', $status);
    }
}
