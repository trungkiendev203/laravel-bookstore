@extends('layouts.main')

@section('title', 'Đặt hàng thành công - Arsha Bookstore')

@section('content')
<div class="max-w-3xl mx-auto my-5">
    <!-- Success Status Header -->
    <div class="card border-0 shadow-sm p-4 text-center mb-4">
        @if($order->payment_status === 'paid')
            <div class="text-success mb-3">
                <i class="bi bi-check-circle-fill" style="font-size: 5rem;"></i>
            </div>
            <h3 class="fw-bold">Thanh Toán Thành Công!</h3>
            <p class="text-muted">Cảm ơn bạn đã mua hàng. Đơn hàng của bạn đang được xử lý.</p>
        @elseif($order->payment_status === 'failed')
            <div class="text-danger mb-3">
                <i class="bi bi-x-circle-fill" style="font-size: 5rem;"></i>
            </div>
            <h3 class="fw-bold text-danger">Thanh Toán Thất Bại!</h3>
            <p class="text-muted">Giao dịch thanh toán online của bạn không thành công.</p>
            @if($order->status === 'processing')
                <div class="mt-4">
                    <a href="{{ route('vnpay.retry', $order->order_code) }}" class="btn btn-orange px-4 py-2 fw-semibold">
                        <i class="bi bi-arrow-clockwise me-1"></i> Thử lại thanh toán VNPay
                    </a>
                </div>
            @endif
        @else
            <div class="text-orange mb-3">
                <i class="bi bi-check-circle-fill" style="font-size: 5rem;"></i>
            </div>
            <h3 class="fw-bold text-orange">Đặt Hàng Thành Công!</h3>
            <p class="text-muted">Cảm ơn bạn đã tin tưởng Arsha Bookstore.</p>
            @if($order->payment_method === 'vnpay' && $order->status === 'processing')
                <div class="alert alert-warning border-0 d-inline-block px-4 py-3 my-2 small">
                    <i class="bi bi-exclamation-circle-fill me-1"></i> Đơn hàng chưa được thanh toán. 
                    <a href="{{ route('vnpay.retry', $order->order_code) }}" class="alert-link text-orange fw-bold text-decoration-underline ms-1">Thanh toán ngay bằng VNPay</a>.
                </div>
            @endif
        @endif
    </div>

    <!-- Order Details -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4 h-100">
                <h5 class="fw-bold mb-4 pb-2 border-bottom">Thông tin đơn hàng</h5>
                <table class="table table-borderless align-middle mb-0 small">
                    <tr>
                        <td class="text-muted py-2">Mã đơn hàng:</td>
                        <td class="fw-bold text-dark text-end py-2">{{ $order->order_code }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted py-2">Ngày đặt:</td>
                        <td class="fw-semibold text-dark text-end py-2">{{ $order->created_at->format('H:i d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted py-2">Trạng thái đơn:</td>
                        <td class="text-end py-2">
                            @if($order->status === 'processing')
                                <span class="badge bg-warning text-dark">Đang xử lý</span>
                            @elseif($order->status === 'shipping')
                                <span class="badge bg-primary">Đang giao</span>
                            @elseif($order->status === 'completed')
                                <span class="badge bg-success">Đã hoàn tất</span>
                            @else
                                <span class="badge bg-danger">Đã hủy</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted py-2">Phương thức TT:</td>
                        <td class="fw-semibold text-dark text-end py-2">{{ strtoupper($order->payment_method) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted py-2">Trạng thái TT:</td>
                        <td class="text-end py-2">
                            @if($order->payment_status === 'paid')
                                <span class="badge bg-success">Đã thanh toán</span>
                            @elseif($order->payment_status === 'failed')
                                <span class="badge bg-danger">Thanh toán lỗi</span>
                            @else
                                <span class="badge bg-warning text-dark">Chờ thanh toán</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4 h-100">
                <h5 class="fw-bold mb-4 pb-2 border-bottom">Thông tin giao nhận</h5>
                <div class="small">
                    <p class="mb-2">Người nhận: <strong class="text-dark">{{ $order->customer_name }}</strong></p>
                    <p class="mb-2">Số điện thoại: <strong class="text-dark">{{ $order->customer_phone }}</strong></p>
                    <p class="mb-2">Địa chỉ: <span class="text-secondary">{{ $order->customer_address }}</span></p>
                    @if($order->note)
                        <p class="mb-0 text-muted">Ghi chú: <em>"{{ $order->note }}"</em></p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold mb-4 pb-2 border-bottom">Sản phẩm đã đặt</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr class="text-muted small">
                                <th>Tên sản phẩm</th>
                                <th class="text-center">Đơn giá</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <span class="fw-semibold text-dark">{{ $item->book_title }}</span>
                                    </td>
                                    <td class="text-center">{{ number_format($item->price, 0, ',', '.') }} ₫</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end fw-bold">{{ number_format($item->subtotal, 0, ',', '.') }} ₫</td>
                                </tr>
                            @endforeach
                            <!-- Pricing rows -->
                            <tr class="border-bottom-0">
                                <td colspan="2" class="border-0"></td>
                                <td class="text-end text-muted small border-0 py-1">Tạm tính:</td>
                                <td class="text-end fw-semibold border-0 py-1">{{ number_format($order->total_amount - $order->shipping_fee, 0, ',', '.') }} ₫</td>
                            </tr>
                            <tr class="border-bottom-0">
                                <td colspan="2" class="border-0"></td>
                                <td class="text-end text-muted small border-0 py-1">Phí vận chuyển:</td>
                                <td class="text-end fw-semibold border-0 py-1">{{ number_format($order->shipping_fee, 0, ',', '.') }} ₫</td>
                            </tr>
                            <tr class="border-bottom-0">
                                <td colspan="2" class="border-0"></td>
                                <td class="text-end text-dark fw-bold border-0 py-2">Tổng tiền:</td>
                                <td class="text-end text-orange fw-bold fs-5 border-0 py-2">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="d-flex justify-content-center gap-3 mt-4">
        <a href="{{ route('home') }}" class="btn btn-outline-orange px-4 py-2.5">
            Tiếp tục mua hàng
        </a>
        @auth
            <a href="{{ route('orders.my') }}" class="btn btn-orange px-4 py-2.5">
                Xem lịch sử mua hàng
            </a>
        @else
            <a href="{{ route('orders.track.form') }}" class="btn btn-orange px-4 py-2.5">
                Tra cứu đơn hàng
            </a>
        @endauth
    </div>
</div>
@endsection
