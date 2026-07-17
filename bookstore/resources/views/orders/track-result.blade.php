@extends('layouts.main')

@section('title', 'Kết quả tra cứu đơn hàng - Arsha Bookstore')

@section('content')
<div class="max-w-3xl mx-auto my-4">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 bg-white p-3 rounded shadow-sm">
        <nav aria-label="breadcrumb" class="mb-0">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('orders.track.form') }}" class="text-decoration-none text-muted">Tra cứu đơn</a></li>
                <li class="breadcrumb-item active text-orange" aria-current="page">{{ $order->order_code }}</li>
            </ol>
        </nav>
        <span class="badge bg-orange text-white py-2 px-3 fw-bold">{{ $order->order_code }}</span>
    </div>

    <!-- Info columns -->
    <div class="row g-4 mb-4">
        <!-- Order details -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4 h-100">
                <h5 class="fw-bold mb-4 pb-2 border-bottom">Trạng thái đơn hàng</h5>
                <table class="table table-borderless align-middle mb-0 small">
                    <tr>
                        <td class="text-muted py-2">Trạng thái đơn:</td>
                        <td class="text-end py-2">
                            @if($order->status === 'processing')
                                <span class="badge bg-warning text-dark">Đang xử lý</span>
                            @elseif($order->status === 'shipping')
                                <span class="badge bg-primary">Đang giao hàng</span>
                            @elseif($order->status === 'completed')
                                <span class="badge bg-success">Đã hoàn tất</span>
                            @else
                                <span class="badge bg-danger">Đã hủy</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted py-2">Phương thức thanh toán:</td>
                        <td class="fw-semibold text-dark text-end py-2">{{ strtoupper($order->payment_method) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted py-2">Trạng thái thanh toán:</td>
                        <td class="text-end py-2">
                            @if($order->payment_status === 'paid')
                                <span class="badge bg-success">Đã thanh toán</span>
                            @elseif($order->payment_status === 'failed')
                                <span class="badge bg-danger">Thanh toán thất bại</span>
                            @else
                                <span class="badge bg-warning text-dark">Chờ thanh toán</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted py-2">Tổng giá trị đơn hàng:</td>
                        <td class="fw-bold text-orange text-end py-2">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</td>
                    </tr>
                </table>

                <!-- VNPay Retry option -->
                @if($order->payment_method === 'vnpay' && $order->payment_status !== 'paid' && $order->status === 'processing')
                    <div class="mt-4 pt-3 border-top text-center">
                        <a href="{{ route('vnpay.retry', $order->order_code) }}" class="btn btn-orange btn-sm w-100 py-2 fw-semibold">
                            <i class="bi bi-arrow-clockwise me-1"></i> Thanh toán lại qua VNPay
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Shipping info -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4 h-100">
                <h5 class="fw-bold mb-4 pb-2 border-bottom">Địa chỉ nhận hàng</h5>
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
    </div>

    <!-- Items list -->
    <div class="card border-0 shadow-sm p-4 mb-4">
        <h5 class="fw-bold mb-4 pb-2 border-bottom">Chi tiết sản phẩm</h5>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
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
                        <td class="text-end text-dark fw-bold border-0 py-2">Tổng cộng:</td>
                        <td class="text-end text-orange fw-bold fs-5 border-0 py-2">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Back button -->
    <div class="text-center">
        <a href="{{ route('orders.track.form') }}" class="btn btn-secondary px-4 py-2.5">
            <i class="bi bi-arrow-left me-1"></i> Tra cứu đơn hàng khác
        </a>
    </div>
</div>
@endsection
