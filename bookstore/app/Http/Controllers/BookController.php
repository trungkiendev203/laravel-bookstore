<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Danh sách truyện (có tìm kiếm, lọc thể loại)
     */
    public function index(Request $request)
    {
        $query = Book::with(['category', 'volumes'])->where('is_active', true)->whereNull('parent_id');

        // Tìm kiếm theo tên hoặc tác giả
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
            });
        }

        // Lọc theo thể loại
        if ($request->filled('category')) {
            $categorySlug = $request->input('category');
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Lọc theo khoảng giá (min_price, max_price)
        if ($request->filled('min_price')) {
            $minPrice = $request->input('min_price');
            $query->where(function ($q) use ($minPrice) {
                $q->where(function ($sub) use ($minPrice) {
                    $sub->whereNotNull('sale_price')
                        ->where('sale_price', '>=', $minPrice);
                })->orWhere(function ($sub) use ($minPrice) {
                    $sub->whereNull('sale_price')
                        ->where('price', '>=', $minPrice);
                });
            });
        }

        if ($request->filled('max_price')) {
            $maxPrice = $request->input('max_price');
            $query->where(function ($q) use ($maxPrice) {
                $q->where(function ($sub) use ($maxPrice) {
                    $sub->whereNotNull('sale_price')
                        ->where('sale_price', '<=', $maxPrice);
                })->orWhere(function ($sub) use ($maxPrice) {
                    $sub->whereNull('sale_price')
                        ->where('price', '<=', $maxPrice);
                });
            });
        }

        // Sắp xếp
        $sort = $request->input('sort', 'newest');
        if ($sort === 'price_asc') {
            $query->orderByRaw('COALESCE(sale_price, price) ASC');
        } elseif ($sort === 'price_desc') {
            $query->orderByRaw('COALESCE(sale_price, price) DESC');
        } elseif ($sort === 'best_seller') {
            $query->orderBy('sold_count', 'desc');
        } else {
            $query->orderBy('created_at', 'desc'); // Mặc định truyện mới nhất
        }

        $isHomepage = ($request->path() === '/' || $request->routeIs('home')) && !$request->filled('search') && !$request->filled('category') && !$request->filled('min_price') && !$request->filled('max_price') && !$request->filled('sort');

        $newArrivals = [];
        $bestSellers = [];
        $featuredCategories = [];

        if ($isHomepage) {
            $newArrivals = Book::with(['category', 'volumes'])->where('is_active', true)->whereNull('parent_id')->orderBy('created_at', 'desc')->limit(4)->get();
            $bestSellers = Book::with(['category', 'volumes'])->where('is_active', true)->whereNull('parent_id')->orderBy('sold_count', 'desc')->limit(4)->get();
            $featuredCategories = Category::with(['books' => function ($q) {
                $q->where('is_active', true)->whereNull('parent_id')->with('volumes')->limit(4);
            }])->get()->filter(function ($cat) {
                return $cat->books->count() > 0;
            });
        }

        $books = $query->paginate(12)->withQueryString();
        $categories = Category::all();

        return view('books.index', compact('books', 'categories', 'isHomepage', 'newArrivals', 'bestSellers', 'featuredCategories'));
    }

    /**
     * Chi tiết một cuốn truyện
     */
    public function show($slug)
    {
        $book = Book::with(['volumes', 'comments.user'])->where('slug', $slug)->where('is_active', true)->firstOrFail();
        
        if ($book->parent_id) {
            $parent = Book::findOrFail($book->parent_id);
            return redirect()->route('books.show', $parent->slug)->with('selected_volume_id', $book->id);
        }
        
        // Lấy truyện cùng thể loại liên quan (giới hạn 4 cuốn)
        $relatedBooks = Book::where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->with('volumes')
            ->limit(4)
            ->get();

        return view('books.show', compact('book', 'relatedBooks'));
    }

    /**
     * Lưu bình luận/đánh giá của khách hàng
     */
    public function storeComment(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $request->validate([
            'content' => 'required|string|min:3|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ], [
            'content.required' => 'Nội dung bình luận không được để trống.',
            'content.min' => 'Nội dung bình luận phải có ít nhất 3 ký tự.',
            'content.max' => 'Nội dung bình luận không được quá 1000 ký tự.',
            'rating.required' => 'Vui lòng chọn số sao đánh giá.',
            'rating.integer' => 'Đánh giá không hợp lệ.',
            'rating.min' => 'Đánh giá tối thiểu là 1 sao.',
            'rating.max' => 'Đánh giá tối đa là 5 sao.',
        ]);

        \App\Models\Comment::create([
            'user_id' => auth()->id(),
            'book_id' => $book->id,
            'content' => $request->input('content'),
            'rating' => $request->input('rating'),
        ]);

        return redirect()->back()->with('success', 'Gửi đánh giá thành công.');
    }

    /**
     * Truyện theo thể loại
     */
    public function category(Request $request, $slug)
    {
        Category::where('slug', $slug)->firstOrFail();
        $request->merge(['category' => $slug]);
        return $this->index($request);
    }
}
