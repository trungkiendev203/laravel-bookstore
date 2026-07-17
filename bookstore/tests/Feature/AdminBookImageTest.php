<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBookImageTest extends TestCase
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

    public function test_admin_can_create_book_with_image_url(): void
    {
        $imageUrl = 'https://example.com/images/doraemon.jpg';

        $response = $this->actingAs($this->admin)->post('/admin/books', [
            'title' => 'Doraemon 2',
            'author' => 'Fujiko F. Fujio',
            'category_id' => $this->category->id,
            'price' => 35000,
            'stock' => 50,
            'cover_image_url' => $imageUrl,
            'is_active' => 1,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('books', [
            'title' => 'Doraemon 2',
            'cover_image' => $imageUrl,
        ]);

        $book = Book::where('title', 'Doraemon 2')->first();
        $this->assertEquals($imageUrl, $book->cover_image_url);
    }

    public function test_admin_can_update_book_with_image_url(): void
    {
        $book = Book::create([
            'category_id' => $this->category->id,
            'title' => 'Conan 1',
            'slug' => 'conan-1',
            'price' => 30000,
            'stock' => 20,
            'is_active' => true,
        ]);

        $imageUrl = 'https://example.com/images/conan.jpg';

        $response = $this->actingAs($this->admin)->put("/admin/books/{$book->id}", [
            'title' => 'Conan 1 Updated',
            'author' => 'Gosho Aoyama',
            'category_id' => $this->category->id,
            'price' => 32000,
            'stock' => 25,
            'cover_image_url' => $imageUrl,
            'is_active' => 1,
        ]);

        $response->assertStatus(302);
        $book->refresh();
        $this->assertEquals('Conan 1 Updated', $book->title);
        $this->assertEquals($imageUrl, $book->cover_image);
        $this->assertEquals($imageUrl, $book->cover_image_url);
    }
}
