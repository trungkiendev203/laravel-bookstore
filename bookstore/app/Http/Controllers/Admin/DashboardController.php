<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Book;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Trang dashboard thống kê cho Admin
     */
    public function index()
    {
        // Thống kê tổng quan
        $totalOrders = Order::count();
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_amount');
        $totalBooks = Book::count();
        $totalUsers = User::where('role', 'customer')->count();

        // Đơn hàng theo trạng thái
        $ordersByStatus = [
            'processing' => Order::where('status', 'processing')->count(),
            'shipping' => Order::where('status', 'shipping')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        // 5 đơn hàng gần nhất
        $recentOrders = Order::with('user')->orderBy('created_at', 'desc')->limit(5)->get();

        // Top 5 truyện bán chạy
        $topBooks = Book::orderBy('sold_count', 'desc')->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalOrders', 'totalRevenue', 'totalBooks', 'totalUsers',
            'ordersByStatus', 'recentOrders', 'topBooks'
        ));
    }
}
