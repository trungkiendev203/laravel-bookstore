<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('books')->orderBy('name')->paginate(15);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ], [
            'name.required' => 'Tên thể loại không được để trống.',
            'name.unique' => 'Thể loại này đã tồn tại.',
        ]);

        Category::create([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Thêm thể loại thành công.');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
        ], [
            'name.required' => 'Tên thể loại không được để trống.',
            'name.unique' => 'Thể loại này đã tồn tại.',
        ]);

        $category->update([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật thể loại thành công.');
    }

    public function destroy($id)
    {
        $category = Category::withCount('books')->findOrFail($id);

        if ($category->books_count > 0) {
            return redirect()->back()->with('error', "Không thể xóa thể loại '{$category->name}' vì đang có {$category->books_count} truyện thuộc thể loại này.");
        }

        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Xóa thể loại thành công.');
    }
}
