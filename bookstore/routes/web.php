<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use Illuminate\Support\Facades\Route;

// ========================
// 1. PUBLIC ROUTES
// ========================

// Trang chủ = danh sách truyện
Route::get('/', [BookController::class, 'index'])->name('home');
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/category/{slug}', [BookController::class, 'category'])->name('categories.show');
Route::get('/books/{slug}', [BookController::class, 'show'])->name('books.show');

// Giỏ hàng (Session-based, dùng được cho cả guest)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

// Thanh toán và Đặt hàng
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/order/success/{code}', [CheckoutController::class, 'success'])->name('checkout.success');

// VNPay
Route::get('/vnpay/return', [CheckoutController::class, 'vnpayReturn'])->name('vnpay.return');
Route::get('/vnpay/retry/{order_code}', [CheckoutController::class, 'vnpayRetry'])->name('vnpay.retry');

// Tra cứu đơn hàng (guest)
Route::get('/order/track', [OrderController::class, 'trackForm'])->name('orders.track.form');
Route::post('/order/track', [OrderController::class, 'track'])->name('orders.track');

// ========================
// 2. AUTH ROUTES (đã đăng nhập)
// ========================

Route::get('/dashboard', function () {
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile (Breeze mặc định + hỗ trợ PUT/PATCH)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::match(['put', 'patch'], '/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Lịch sử đơn hàng + Chi tiết đơn + Hủy đơn
    Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('orders.my');
    Route::get('/my-orders/{code}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/my-orders/{code}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Bình luận truyện
    Route::post('/books/{id}/comments', [BookController::class, 'storeComment'])->name('books.comments.store');
});

// ========================
// 3. ADMIN ROUTES
// ========================

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // CRUD Categories
        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{id}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{id}', [AdminCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

        // CRUD Books (soft delete)
        Route::get('/books', [AdminBookController::class, 'index'])->name('books.index');
        Route::get('/books/create', [AdminBookController::class, 'create'])->name('books.create');
        Route::post('/books', [AdminBookController::class, 'store'])->name('books.store');
        Route::get('/books/{id}/edit', [AdminBookController::class, 'edit'])->name('books.edit');
        Route::put('/books/{id}', [AdminBookController::class, 'update'])->name('books.update');
        Route::delete('/books/{id}', [AdminBookController::class, 'destroy'])->name('books.destroy');
        Route::post('/books/{id}/restore', [AdminBookController::class, 'restore'])->name('books.restore');
        Route::post('/books/{id}/generate-volumes', [AdminBookController::class, 'generateVolumes'])->name('books.generateVolumes');

        // Orders management
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');

        // Users list
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    });

require __DIR__.'/auth.php';
