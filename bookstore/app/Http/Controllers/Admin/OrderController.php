<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Danh sách tất cả đơn hàng (cho Admin)
     */
    public function index(Request $request)
    {
        $query = Order::with('user')->orderBy('created_at', 'desc');

        // Lọc theo trạng thái đơn
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Lọc theo phương thức thanh toán
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        // Tìm kiếm theo mã đơn hoặc tên khách
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Chi tiết một đơn hàng
     */
    public function show($id)
    {
        $order = Order::with('items.book', 'user')->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Cập nhật trạng thái đơn hàng và thanh toán
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => 'required|in:processing,shipping,completed,cancelled',
            'payment_status' => 'required|in:pending,paid,failed',
        ]);

        $currentStatus = $order->status;
        $newStatus = $request->input('status');
        $newPaymentStatus = $request->input('payment_status');

        // Kiểm tra quy tắc chuyển trạng thái
        if ($currentStatus !== $newStatus) {
            if ($currentStatus === 'completed' || $currentStatus === 'cancelled') {
                return redirect()->back()->with('error', 'Đơn hàng đã hoàn tất hoặc đã hủy thì không thể thay đổi trạng thái nữa.');
            }

            if ($currentStatus === 'processing' && !in_array($newStatus, ['shipping', 'cancelled'])) {
                return redirect()->back()->with('error', 'Đơn hàng đang xử lý chỉ có thể chuyển sang "đang giao hàng" hoặc "đã hủy".');
            }

            if ($currentStatus === 'shipping' && !in_array($newStatus, ['completed', 'cancelled'])) {
                return redirect()->back()->with('error', 'Đơn hàng đang giao hàng chỉ có thể chuyển sang "hoàn tất" hoặc "đã hủy".');
            }
        }

        // Chạy Transaction để xử lý thay đổi trạng thái
        try {
            DB::transaction(function () use ($order, $newStatus, $newPaymentStatus) {
                // Nếu đơn chuyển sang trạng thái Hủy (cancelled) và trước đó chưa bị hủy
                if ($newStatus === 'cancelled' && $order->status !== 'cancelled') {
                    // Cộng lại kho hàng và giảm sold_count cho từng quyển truyện
                    foreach ($order->items as $item) {
                        $book = Book::find($item->book_id);
                        if ($book) {
                            $book->stock += $item->quantity;
                            $book->sold_count = max(0, $book->sold_count - $item->quantity);
                            $book->save();
                        }
                    }
                }

                $order->update([
                    'status' => $newStatus,
                    'payment_status' => $newPaymentStatus,
                ]);
            });

            return redirect()->back()->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
