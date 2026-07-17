<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Book;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\VnpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    protected VnpayService $vnpayService;

    public function __construct(VnpayService $vnpayService)
    {
        $this->vnpayService = $vnpayService;
    }

    /**
     * Hiển thị trang thanh toán
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        // Tính toán tổng tiền
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        $shippingFee = $subtotal >= 300000 ? 0 : 30000;
        $total = $subtotal + $shippingFee;

        // Lấy thông tin user đăng nhập để tự động điền form
        $user = auth()->user();

        return view('checkout.index', compact('cart', 'subtotal', 'shippingFee', 'total', 'user'));
    }

    /**
     * Xử lý đặt hàng
     */
    public function process(CheckoutRequest $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        // 1. Tính toán tổng tiền đơn hàng
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        $shippingFee = $subtotal >= 300000 ? 0 : 30000;
        $totalAmount = $subtotal + $shippingFee;

        $orderCode = 'ORD-' . date('YmdHis') . '-' . rand(100, 999);

        // 2. Chạy Database Transaction để đảm bảo tính toàn vẹn dữ liệu
        try {
            $order = DB::transaction(function () use ($request, $cart, $shippingFee, $totalAmount, $orderCode) {
                // Kiểm tra và trừ kho hàng của từng sản phẩm
                foreach ($cart as $itemId => $item) {
                    // Sử dụng sharedLock hoặc lockForUpdate để tránh race condition khi nhiều người mua cùng lúc
                    $book = Book::lockForUpdate()->find($itemId);

                    if (!$book || !$book->is_active) {
                        throw new \Exception("Truyện '{$item['title']}' hiện tại không hoạt động hoặc không tồn tại.");
                    }

                    if ($book->stock < $item['quantity']) {
                        throw new \Exception("Truyện '{$item['title']}' không đủ số lượng trong kho. Hiện tại kho chỉ còn {$book->stock} cuốn.");
                    }

                    // Bước 2: Trừ tồn kho và tăng sold_count
                    $book->stock -= $item['quantity'];
                    $book->sold_count += $item['quantity'];
                    $book->save();
                }

                // Tạo bản ghi Order
                $order = Order::create([
                    'order_code' => $orderCode,
                    'user_id' => auth()->id(),
                    'customer_name' => $request->input('customer_name'),
                    'customer_phone' => $request->input('customer_phone'),
                    'customer_address' => $request->input('customer_address'),
                    'note' => $request->input('note'),
                    'shipping_fee' => $shippingFee,
                    'total_amount' => $totalAmount,
                    'payment_method' => $request->input('payment_method'),
                    'payment_status' => 'pending',
                    'status' => 'processing',
                ]);

                // Tạo các OrderItem
                foreach ($cart as $itemId => $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'book_id' => $itemId,
                        'book_title' => $item['title'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'subtotal' => $item['price'] * $item['quantity'],
                    ]);
                }

                return $order;
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }

        // 3. Xử lý theo phương thức thanh toán
        if ($request->input('payment_method') === 'cod') {
            // Thanh toán khi nhận hàng -> xóa giỏ hàng ngay và redirect về trang thành công
            session()->forget('cart');
            return redirect()->route('checkout.success', ['code' => $order->order_code])
                             ->with('success', 'Đặt hàng thành công! Đơn hàng của bạn đang được xử lý.');
        } else {
            // Thanh toán VNPay -> tạo URL thanh toán và redirect sang VNPay sandbox
            try {
                $paymentUrl = $this->vnpayService->createPaymentUrl(
                    $order->order_code,
                    $order->total_amount,
                    "Thanh toan don hang " . $order->order_code
                );
                return redirect()->away($paymentUrl);
            } catch (\Exception $e) {
                // Nếu tạo link VNPay lỗi, rollback thủ công đơn hàng (hoàn kho)
                $this->cancelOrderAndRestoreStock($order);
                return redirect()->back()->with('error', 'Có lỗi xảy ra khi tạo link thanh toán VNPay: ' . $e->getMessage())->withInput();
            }
        }
    }

    /**
     * Trang hiển thị thông báo đặt hàng thành công
     */
    public function success($code)
    {
        $order = Order::with('items')->where('order_code', $code)->firstOrFail();
        return view('checkout.success', compact('order'));
    }

    /**
     * Nhận phản hồi redirect từ VNPay
     */
    public function vnpayReturn(Request $request)
    {
        $vnpData = $request->all();
        
        if (empty($vnpData) || !isset($vnpData['vnp_SecureHash'])) {
            return redirect('/')->with('error', 'Dữ liệu phản hồi từ VNPay không hợp lệ.');
        }

        // 1. Kiểm tra chữ ký bảo mật
        $isValidSignature = $this->vnpayService->validateSignature($vnpData);
        if (!$isValidSignature) {
            return redirect('/')->with('error', 'Chữ ký VNPay không hợp lệ. Giao dịch bị nghi ngờ gian lận.');
        }

        $txnRef = $vnpData['vnp_TxnRef'];
        $responseCode = $vnpData['vnp_ResponseCode'];

        // Xử lý retry: TxnRef có thể dạng ORD-xxx-Rn, cần tách lấy order_code gốc
        $orderCode = preg_replace('/-R\d+$/', '', $txnRef);

        $order = Order::where('order_code', $orderCode)->first();
        if (!$order) {
            return redirect('/')->with('error', "Không tìm thấy đơn hàng {$orderCode} trong hệ thống.");
        }

        // 2. Kiểm tra ResponseCode từ VNPay
        if ($responseCode === '00') {
            // Thanh toán thành công
            if ($order->payment_status !== 'paid') {
                $order->update([
                    'payment_status' => 'paid',
                ]);
            }
            
            // Xóa giỏ hàng vì đã thanh toán thành công
            session()->forget('cart');

            return redirect()->route('checkout.success', ['code' => $order->order_code])
                             ->with('success', 'Thanh toán qua VNPay thành công!');
        } else {
            // Thanh toán thất bại — GIỮ ĐƠN HÀNG (giữ chỗ kho) để cho phép thanh toán lại
            // Chỉ cập nhật payment_status = failed, KHÔNG hủy đơn, KHÔNG hoàn kho
            $order->update(['payment_status' => 'failed']);

            return redirect()->route('checkout.success', ['code' => $order->order_code])
                             ->with('error', 'Giao dịch VNPay không thành công. Bạn có thể thử thanh toán lại.');
        }
    }

    /**
     * Cho phép người dùng retry thanh toán VNPay cho đơn hàng đã failed
     * Tạo vnp_TxnRef mới (thêm suffix lần thử) để VNPay chấp nhận
     */
    public function vnpayRetry($orderCode)
    {
        $order = Order::where('order_code', $orderCode)->firstOrFail();

        // Chỉ cho retry khi đơn đang ở processing + payment_status = failed hoặc pending
        if ($order->status !== 'processing' || $order->payment_method !== 'vnpay') {
            return redirect()->back()->with('error', 'Đơn hàng này không hợp lệ để thanh toán lại.');
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.success', ['code' => $order->order_code])
                             ->with('success', 'Đơn hàng này đã được thanh toán rồi.');
        }

        // Tạo TxnRef mới bằng cách thêm suffix số lần retry
        // VNPay yêu cầu vnp_TxnRef phải duy nhất mỗi lần gửi
        $retryCount = $order->retry_count ?? 0;
        $retryCount++;

        // Lưu lại retry count (dùng note hoặc thêm cột nếu cần, ở đây dùng cách đơn giản)
        $txnRef = $order->order_code . '-R' . $retryCount;

        try {
            $paymentUrl = $this->vnpayService->createPaymentUrl(
                $txnRef,
                $order->total_amount,
                "Thanh toan lai don hang " . $order->order_code
            );

            // Reset payment_status về pending khi thử lại và tăng số lần retry
            $order->update([
                'payment_status' => 'pending',
                'retry_count' => $retryCount,
            ]);

            return redirect()->away($paymentUrl);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi khi tạo link thanh toán: ' . $e->getMessage());
        }
    }

    /**
     * Hủy đơn hàng và cộng trả lại tồn kho
     */
    protected function cancelOrderAndRestoreStock(Order $order)
    {
        DB::transaction(function () use ($order) {
            // Chỉ hoàn kho khi đơn chưa bị hủy/chưa được hoàn kho để tránh cộng lặp
            if ($order->status !== 'cancelled') {
                foreach ($order->items as $item) {
                    $book = Book::find($item->book_id);
                    if ($book) {
                        $book->stock += $item->quantity;
                        // Đảm bảo sold_count không âm
                        $book->sold_count = max(0, $book->sold_count - $item->quantity);
                        $book->save();
                    }
                }

                $order->update([
                    'payment_status' => 'failed',
                    'status' => 'cancelled',
                ]);
            }
        });
    }
}
