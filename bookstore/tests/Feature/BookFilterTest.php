<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookFilterTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'name' => 'Manga',
            'slug' => 'manga',
        ]);

        // Quyển 1: Giá rẻ (15k)
        Book::create([
            'category_id' => $this->category->id,
            'title' => 'Book Cheap',
            'slug' => 'book-cheap',
            'price' => 15000,
            'stock' => 10,
            'is_active' => true,
        ]);

        // Quyển 2: Giá trung bình (Gốc 50k, sale còn 45k)
        Book::create([
            'category_id' => $this->category->id,
            'title' => 'Book Medium',
            'slug' => 'book-medium',
            'price' => 50000,
            'sale_price' => 45000,
            'stock' => 10,
            'is_active' => true,
        ]);

        // Quyển 3: Giá cao (100k)
        Book::create([
            'category_id' => $this->category->id,
            'title' => 'Book Expensive',
            'slug' => 'book-expensive',
            'price' => 100000,
            'stock' => 10,
            'is_active' => true,
        ]);
    }

    public function test_price_range_filtering(): void
    {
        // 1. Chỉ lọc min_price = 30000 -> Phải thấy Medium và Expensive, không thấy Cheap
        $response = $this->get('/books?min_price=30000');
        $response->assertStatus(200);
        $response->assertSee('Book Medium');
        $response->assertSee('Book Expensive');
        $response->assertDontSee('Book Cheap');

        // 2. Chỉ lọc max_price = 40000 -> Phải thấy Cheap, không thấy Medium (45k) hay Expensive (100k)
        $response = $this->get('/books?max_price=40000');
        $response->assertStatus(200);
        $response->assertSee('Book Cheap');
        $response->assertDontSee('Book Medium');
        $response->assertDontSee('Book Expensive');

        // 3. Lọc trong khoảng min_price = 20000 và max_price = 80000 -> Phải thấy Medium, không thấy Cheap và Expensive
        $response = $this->get('/books?min_price=20000&max_price=80000');
        $response->assertStatus(200);
        $response->assertSee('Book Medium');
        $response->assertDontSee('Book Cheap');
        $response->assertDontSee('Book Expensive');
    }
}
