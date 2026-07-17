<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Book $book;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->category = Category::create([
            'name' => 'Manga',
            'slug' => 'manga',
        ]);

        $this->book = Book::create([
            'category_id' => $this->category->id,
            'title' => 'Doraemon 1',
            'slug' => 'doraemon-1',
            'price' => 20000,
            'stock' => 10,
            'is_active' => true,
        ]);
    }

    public function test_checkout_validation_requires_fields_with_vietnamese_messages(): void
    {
        $response = $this->post('/checkout', []);

        $response->assertSessionHasErrors([
            'customer_name' => 'Họ và tên người nhận không được để trống.',
            'customer_phone' => 'Số điện thoại nhận hàng không được để trống.',
            'customer_address' => 'Địa chỉ nhận hàng không được để trống.',
            'payment_method' => 'Vui lòng chọn phương thức thanh toán.',
        ]);
    }

    public function test_checkout_validation_payment_method_must_be_valid(): void
    {
        $response = $this->post('/checkout', [
            'customer_name' => 'Nguyen Van A',
            'customer_phone' => '0987654321',
            'customer_address' => 'Hanoi',
            'payment_method' => 'invalid-method',
        ]);

        $response->assertSessionHasErrors([
            'payment_method' => 'Phương thức thanh toán đã chọn không hợp lệ.',
        ]);
    }

    public function test_user_can_view_order_details_by_code(): void
    {
        $order = Order::create([
            'order_code' => 'ORD-123456',
            'user_id' => $this->user->id,
            'customer_name' => 'Nguyen Van A',
            'customer_phone' => '0987654321',
            'customer_address' => 'Hanoi',
            'total_amount' => 50000,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'status' => 'processing',
        ]);

        $response = $this->actingAs($this->user)->get("/my-orders/{$order->order_code}");

        $response->assertStatus(200);
        $response->assertSee('ORD-123456');
    }

    public function test_user_can_cancel_processing_order(): void
    {
        $order = Order::create([
            'order_code' => 'ORD-123456',
            'user_id' => $this->user->id,
            'customer_name' => 'Nguyen Van A',
            'customer_phone' => '0987654321',
            'customer_address' => 'Hanoi',
            'total_amount' => 20000,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'status' => 'processing',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'book_id' => $this->book->id,
            'book_title' => $this->book->title,
            'price' => $this->book->price,
            'quantity' => 2,
            'subtotal' => 40000,
        ]);

        $response = $this->actingAs($this->user)->post("/my-orders/{$order->order_code}/cancel");

        $response->assertStatus(302);
        
        $order->refresh();
        $this->assertEquals('cancelled', $order->status);
        
        $this->book->refresh();
        $this->assertEquals(12, $this->book->stock); // Initial 10 + returned 2
    }

    public function test_admin_order_status_transitions(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $order = Order::create([
            'order_code' => 'ORD-999999',
            'user_id' => $this->user->id,
            'customer_name' => 'Nguyen Van A',
            'customer_phone' => '0987654321',
            'customer_address' => 'Hanoi',
            'total_amount' => 20000,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'status' => 'processing',
        ]);

        // 1. Chuyển thẳng từ processing -> completed: Phải bị từ chối
        $response = $this->actingAs($admin)->post("/admin/orders/{$order->id}/status", [
            'status' => 'completed',
            'payment_status' => 'pending',
        ]);
        $response->assertSessionHas('error');
        $order->refresh();
        $this->assertEquals('processing', $order->status);

        // 2. Chuyển từ processing -> shipping: Hợp lệ
        $response = $this->actingAs($admin)->post("/admin/orders/{$order->id}/status", [
            'status' => 'shipping',
            'payment_status' => 'pending',
        ]);
        $order->refresh();
        $this->assertEquals('shipping', $order->status);

        // 3. Chuyển từ shipping -> processing: Phải bị từ chối
        $response = $this->actingAs($admin)->post("/admin/orders/{$order->id}/status", [
            'status' => 'processing',
            'payment_status' => 'pending',
        ]);
        $response->assertSessionHas('error');
        $order->refresh();
        $this->assertEquals('shipping', $order->status);

        // 4. Chuyển từ shipping -> completed: Hợp lệ
        $response = $this->actingAs($admin)->post("/admin/orders/{$order->id}/status", [
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);
        $order->refresh();
        $this->assertEquals('completed', $order->status);

        // 5. Chuyển từ trạng thái cuối completed -> cancelled: Phải bị từ chối
        $response = $this->actingAs($admin)->post("/admin/orders/{$order->id}/status", [
            'status' => 'cancelled',
            'payment_status' => 'paid',
        ]);
        $response->assertSessionHas('error');
        $order->refresh();
        $this->assertEquals('completed', $order->status);
    }
}
