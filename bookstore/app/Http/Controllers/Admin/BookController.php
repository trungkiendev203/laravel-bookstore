<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with(['category', 'volumes'])->whereNull('parent_id');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $books = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $categories = Category::all();

        return view('admin.books.index', compact('books', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.books.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'cover_image_url' => 'nullable|url|max:2048',
            'is_active' => 'nullable|boolean',
        ], [
            'title.required' => 'Tên truyện không được để trống.',
            'category_id.required' => 'Vui lòng chọn thể loại.',
            'category_id.exists' => 'Thể loại không hợp lệ.',
            'price.required' => 'Giá bán không được để trống.',
            'price.min' => 'Giá bán phải lớn hơn hoặc bằng 0.',
            'stock.required' => 'Số lượng tồn kho không được để trống.',
            'cover_image.image' => 'File tải lên phải là ảnh.',
            'cover_image.max' => 'Kích thước ảnh tối đa 2MB.',
            'cover_image_url.url' => 'Đường dẫn ảnh phải là một URL hợp lệ.',
            'cover_image_url.max' => 'Đường dẫn ảnh tối đa 2048 ký tự.',
        ]);

        $data = $request->only(['title', 'author', 'category_id', 'price', 'sale_price', 'stock', 'description']);
        $data['slug'] = Str::slug($request->input('title')) . '-' . rand(100, 999);
        $data['is_active'] = $request->has('is_active') ? true : true; // Mặc định active

        // Upload ảnh bìa vào storage/app/public/covers hoặc lưu URL
        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('covers', 'public');
            $data['cover_image'] = $path;
        } elseif ($request->filled('cover_image_url')) {
            $data['cover_image'] = $request->input('cover_image_url');
        }

        Book::create($data);

        return redirect()->route('admin.books.index')->with('success', 'Thêm truyện mới thành công.');
    }

    public function edit($id)
    {
        $book = Book::with('volumes')->findOrFail($id);
        $categories = Category::all();
        return view('admin.books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'cover_image_url' => 'nullable|url|max:2048',
            'is_active' => 'nullable|boolean',
        ], [
            'title.required' => 'Tên truyện không được để trống.',
            'category_id.required' => 'Vui lòng chọn thể loại.',
            'price.required' => 'Giá bán không được để trống.',
            'cover_image_url.url' => 'Đường dẫn ảnh phải là một URL hợp lệ.',
            'cover_image_url.max' => 'Đường dẫn ảnh tối đa 2048 ký tự.',
        ]);

        $data = $request->only(['title', 'author', 'category_id', 'price', 'sale_price', 'stock', 'description']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('cover_image')) {
            // Xóa ảnh cũ nếu có và là ảnh cục bộ
            if ($book->cover_image && !str_starts_with($book->cover_image, 'http://') && !str_starts_with($book->cover_image, 'https://') && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $path = $request->file('cover_image')->store('covers', 'public');
            $data['cover_image'] = $path;
        } elseif ($request->filled('cover_image_url')) {
            // Xóa ảnh cũ nếu có và là ảnh cục bộ
            if ($book->cover_image && !str_starts_with($book->cover_image, 'http://') && !str_starts_with($book->cover_image, 'https://') && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $data['cover_image'] = $request->input('cover_image_url');
        } else {
            // Nếu không upload file mới và URL bị trống
            // Trường hợp trước đó là URL mà nay bị xóa trống -> tức là muốn xóa ảnh
            if ($book->cover_image && (str_starts_with($book->cover_image, 'http://') || str_starts_with($book->cover_image, 'https://'))) {
                $data['cover_image'] = null;
            }
        }

        $book->update($data);

        // Nếu là truyện cha, tự động đồng bộ thể loại (category_id) cho các tập con
        if (is_null($book->parent_id)) {
            Book::where('parent_id', $book->id)->update([
                'category_id' => $book->category_id,
            ]);
        }

        return redirect()->route('admin.books.index')->with('success', 'Cập nhật truyện thành công.');
    }

    /**
     * Soft Delete — KHÔNG hard delete theo yêu cầu PRD
     */
    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $book->delete(); // SoftDeletes — chỉ set deleted_at, không xóa thật
        return redirect()->route('admin.books.index')->with('success', "Đã ẩn truyện '{$book->title}' (soft delete).");
    }

    /**
     * Khôi phục truyện đã soft delete
     */
    public function restore($id)
    {
        $book = Book::withTrashed()->findOrFail($id);
        $book->restore();
        return redirect()->route('admin.books.index')->with('success', "Đã khôi phục truyện '{$book->title}'.");
    }

    /**
     * Tạo nhanh nhiều tập truyện cho truyện cha
     */
    public function generateVolumes(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $request->validate([
            'volume_start' => 'required|integer|min:1',
            'volume_end' => 'required|integer|min:1|gte:volume_start',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'cover_image_url' => 'nullable|url|max:2048',
        ], [
            'volume_start.required' => 'Số tập bắt đầu không được để trống.',
            'volume_start.integer' => 'Số tập bắt đầu phải là số nguyên.',
            'volume_end.required' => 'Số tập kết thúc không được để trống.',
            'volume_end.integer' => 'Số tập kết thúc phải là số nguyên.',
            'volume_end.gte' => 'Số tập kết thúc phải lớn hơn hoặc bằng tập bắt đầu.',
            'price.required' => 'Giá bán không được để trống.',
            'stock.required' => 'Số lượng tồn kho không được để trống.',
        ]);

        $start = (int)$request->input('volume_start');
        $end = (int)$request->input('volume_end');
        $price = $request->input('price');
        $stock = $request->input('stock');
        $coverImageUrl = $request->input('cover_image_url');

        if (($end - $start) > 150) {
            return redirect()->back()->with('error', 'Chỉ được phép tạo tối đa 150 tập một lúc để tránh quá tải hệ thống.');
        }

        $createdCount = 0;
        for ($i = $start; $i <= $end; $i++) {
            // Kiểm tra xem tập này đã tồn tại chưa
            $exists = Book::where('parent_id', $book->id)
                ->where('volume_number', (string)$i)
                ->exists();

            if (!$exists) {
                Book::create([
                    'category_id' => $book->category_id,
                    'parent_id' => $book->id,
                    'title' => $book->title . ' - Tập ' . $i,
                    'author' => $book->author,
                    'slug' => Str::slug($book->title . ' - Tap ' . $i) . '-' . rand(100, 999),
                    'volume_number' => (string)$i,
                    'price' => $price,
                    'stock' => $stock,
                    'cover_image' => $coverImageUrl ?: $book->cover_image, // Lấy ảnh bìa truyện cha nếu không nhập ảnh riêng
                    'is_active' => true,
                ]);
                $createdCount++;
            }
        }

        return redirect()->back()->with('success', "Đã tạo thành công {$createdCount} tập mới (từ Tập {$start} đến Tập {$end}).");
    }
}
