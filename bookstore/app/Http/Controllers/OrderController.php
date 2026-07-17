<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Lịch sử đơn hàng của khách đã đăng nhập
     */
    public function myOrders()
    {
        $orders = Order::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.my-orders', compact('orders'));
    }

    /**
     * Trang tra cứu đơn hàng cho guest
     */
    public function trackForm()
    {
        return view('orders.track');
    }

    /**
     * Xử lý tra cứu đơn hàng guest (khớp mã đơn + SĐT)
     */
    public function track(Request $request)
    {
        $request->validate([
            'order_code' => 'required|string',
            'customer_phone' => 'required|string',
        ]);

        $order = Order::with('items')
            ->where('order_code', $request->input('order_code'))
            ->where('customer_phone', $request->input('customer_phone'))
            ->first();

        if (!$order) {
            return redirect()->back()
                ->with('error', 'Không tìm thấy đơn hàng. Vui lòng kiểm tra lại mã đơn và số điện thoại.')
                ->withInput();
        }

        return view('orders.track-result', compact('order'));
    }

    /**
     * Chi tiết đơn hàng của thành viên
     */
    public function show($code)
    {
        $order = Order::with('items')
            ->where('order_code', $code)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('orders.track-result', compact('order'));
    }

    /**
     * Khách hàng tự hủy đơn khi đơn đang ở trạng thái processing
     */
    public function cancel($code)
    {
        $order = Order::where('order_code', $code)->firstOrFail();

        // Kiểm tra quyền: chỉ chủ đơn mới được hủy
        if ($order->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Bạn không có quyền hủy đơn hàng này.');
        }

        // Chỉ cho hủy khi đơn đang ở trạng thái "processing"
        if ($order->status !== 'processing') {
            return redirect()->back()->with('error', 'Không thể hủy đơn hàng đã được xử lý (đang giao/đã giao).');
        }

        // Hủy đơn và hoàn kho trong transaction
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $book = \App\Models\Book::find($item->book_id);
                if ($book) {
                    $book->stock += $item->quantity;
                    $book->sold_count = max(0, $book->sold_count - $item->quantity);
                    $book->save();
                }
            }

            $order->update([
                'status' => 'cancelled',
                'payment_status' => $order->payment_method === 'cod' ? 'failed' : $order->payment_status,
            ]);
        });

        return redirect()->back()->with('success', 'Đã hủy đơn hàng thành công. Tồn kho đã được hoàn lại.');
    }
}
