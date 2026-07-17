<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookVolumeTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->category = Category::create([
            'name' => 'Manga',
            'slug' => 'manga',
        ]);
    }

    public function test_only_parent_books_shown_on_shop_listing(): void
    {
        // Tạo truyện cha
        $parentBook = Book::create([
            'category_id' => $this->category->id,
            'title' => 'One Piece',
            'slug' => 'one-piece',
            'price' => 30000,
            'stock' => 10,
            'is_active' => true,
        ]);

        // Tạo tập con
        $childBook = Book::create([
            'category_id' => $this->category->id,
            'parent_id' => $parentBook->id,
            'title' => 'One Piece - Tập 1',
            'slug' => 'one-piece-tap-1',
            'volume_number' => '1',
            'price' => 30000,
            'stock' => 5,
            'is_active' => true,
        ]);

        // Truy cập trang danh sách truyện
        $response = $this->get('/books');
        
        $response->assertStatus(200);
        $response->assertSee('One Piece');
        $response->assertDontSee('One Piece - Tập 1'); // Không hiện truyện con
    }

    public function test_visiting_child_volume_redirects_to_parent(): void
    {
        $parentBook = Book::create([
            'category_id' => $this->category->id,
            'title' => 'Naruto',
            'slug' => 'naruto',
            'price' => 25000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $childBook = Book::create([
            'category_id' => $this->category->id,
            'parent_id' => $parentBook->id,
            'title' => 'Naruto - Tập 1',
            'slug' => 'naruto-tap-1',
            'volume_number' => '1',
            'price' => 25000,
            'stock' => 5,
            'is_active' => true,
        ]);

        $response = $this->get("/books/{$childBook->slug}");

        $response->assertStatus(302);
        $response->assertRedirect("/books/{$parentBook->slug}");
        $response->assertSessionHas('selected_volume_id', $childBook->id);
    }

    public function test_admin_can_bulk_generate_volumes(): void
    {
        $parentBook = Book::create([
            'category_id' => $this->category->id,
            'title' => 'Bleach',
            'slug' => 'bleach',
            'price' => 30000,
            'stock' => 0,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/books/{$parentBook->id}/generate-volumes", [
            'volume_start' => 1,
            'volume_end' => 5,
            'price' => 28000,
            'stock' => 15,
            'cover_image_url' => 'https://example.com/bleach-cover.jpg',
        ]);

        $response->assertStatus(302);
        
        // Kiểm tra xem đã tạo 5 tập truyện chưa
        $this->assertEquals(5, Book::where('parent_id', $parentBook->id)->count());

        $this->assertDatabaseHas('books', [
            'parent_id' => $parentBook->id,
            'title' => 'Bleach - Tập 1',
            'volume_number' => '1',
            'price' => 28000,
            'stock' => 15,
            'cover_image' => 'https://example.com/bleach-cover.jpg',
        ]);

        $this->assertDatabaseHas('books', [
            'parent_id' => $parentBook->id,
            'title' => 'Bleach - Tập 5',
            'volume_number' => '5',
            'price' => 28000,
            'stock' => 15,
            'cover_image' => 'https://example.com/bleach-cover.jpg',
        ]);
    }

    public function test_admin_updating_parent_category_syncs_to_children(): void
    {
        $newCategory = Category::create([
            'name' => 'Tiểu thuyết',
            'slug' => 'tieu-thuyet',
        ]);

        $parentBook = Book::create([
            'category_id' => $this->category->id,
            'title' => 'Bleach',
            'slug' => 'bleach',
            'price' => 30000,
            'stock' => 0,
            'is_active' => true,
        ]);

        $childBook = Book::create([
            'category_id' => $this->category->id,
            'parent_id' => $parentBook->id,
            'title' => 'Bleach - Tập 1',
            'slug' => 'bleach-tap-1',
            'volume_number' => '1',
            'price' => 30000,
            'stock' => 5,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/books/{$parentBook->id}", [
            'title' => 'Bleach Updated',
            'category_id' => $newCategory->id,
            'price' => 30000,
            'stock' => 0,
            'is_active' => 1,
        ]);

        $response->assertStatus(302);
        
        // Kiểm tra xem thể loại của cả truyện cha và tập con đều được chuyển sang newCategory
        $parentBook->refresh();
        $childBook->refresh();

        $this->assertEquals($newCategory->id, $parentBook->category_id);
        $this->assertEquals($newCategory->id, $childBook->category_id);
    }

    public function test_user_can_submit_comment(): void
    {
        $book = Book::create([
            'category_id' => $this->category->id,
            'title' => 'Bleach',
            'slug' => 'bleach',
            'price' => 30000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post("/books/{$book->id}/comments", [
            'content' => 'Truyện đọc rất hay và kịch tính!',
            'rating' => 5,
        ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'content' => 'Truyện đọc rất hay và kịch tính!',
            'rating' => 5,
        ]);
    }
}
