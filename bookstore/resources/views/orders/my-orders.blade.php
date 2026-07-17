@extends('layouts.main')

@section('title', 'Lịch sử mua hàng - Arsha Bookstore')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <h3 class="fw-bold mb-4"><i class="bi bi-bag-check text-orange me-2"></i> Lịch Sử Mua Hàng</h3>

        @if($orders->isEmpty())
            <div class="card border-0 shadow-sm p-5 text-center">
                <i class="bi bi-calendar-x text-muted" style="font-size: 5rem;"></i>
                <h4 class="mt-4 fw-bold">Bạn chưa mua đơn hàng nào</h4>
                <p class="text-muted">Hãy lấp đầy lịch sử mua sắm của bạn bằng cách chọn mua những cuốn truyện hay nhé!</p>
                <a href="{{ route('home') }}" class="btn btn-orange px-4 py-2 mt-3">Tiếp tục mua sắm</a>
            </div>
        @else
            <!-- Orders List -->
            <div class="d-flex flex-column gap-4">
                @foreach($orders as $order)
                    <div class="card border-0 shadow-sm overflow-hidden">
                        <!-- Card Header -->
                        <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div>
                                <a href="{{ route('orders.show', $order->order_code) }}" class="fw-bold text-orange me-2 text-decoration-none hover-orange">#{{ $order->order_code }}</a>
                                <span class="text-muted small">Đặt ngày: {{ $order->created_at->format('H:i d/m/Y') }}</span>
                            </div>
                            <div class="d-flex gap-2">
                                <!-- Order status badge -->
                                @if($order->status === 'processing')
                                    <span class="badge bg-warning text-dark py-2 px-3">Đang xử lý</span>
                                @elseif($order->status === 'shipping')
                                    <span class="badge bg-primary py-2 px-3">Đang giao hàng</span>
                                @elseif($order->status === 'completed')
                                    <span class="badge bg-success py-2 px-3">Đã giao thành công</span>
                                @else
                                    <span class="badge bg-danger py-2 px-3">Đã hủy</span>
                                @endif

                                <!-- Payment status badge -->
                                @if($order->payment_status === 'paid')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle py-2 px-3">Đã thanh toán</span>
                                @elseif($order->payment_status === 'failed')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle py-2 px-3">Thanh toán lỗi</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle py-2 px-3">Chờ thanh toán</span>
                                @endif
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body p-4">
                            <!-- Items List -->
                            <div class="table-responsive">
                                <table class="table align-middle table-sm border-0 mb-0">
                                    <tbody>
                                        @foreach($order->items as $item)
                                            <tr>
                                                <td class="border-0 py-2" style="width: 60%;">
                                                    <span class="fw-semibold text-dark">{{ $item->book_title }}</span>
                                                    <span class="text-muted small ms-2">x{{ $item->quantity }}</span>
                                                </td>
                                                <td class="text-end border-0 py-2 fw-medium" style="width: 40%;">
                                                    {{ number_format($item->price, 0, ',', '.') }} ₫
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <hr class="my-3">

                            <!-- Delivery Address & Actions -->
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                                <div class="text-muted small">
                                    <span class="d-block"><i class="bi bi-geo-alt me-1"></i> Giao đến: <strong>{{ $order->customer_name }}</strong> ({{ $order->customer_phone }})</span>
                                    <span class="d-block mt-1 text-truncate" style="max-width: 400px;">{{ $order->customer_address }}</span>
                                </div>
                                <div class="d-flex align-items-baseline gap-2">
                                    <span class="text-secondary small">Tổng tiền:</span>
                                    <span class="fw-bold fs-5 text-orange">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer Actions -->
                        <div class="card-footer bg-white border-top py-3 d-flex justify-content-end gap-2">
                            <!-- VNPay Retry option -->
                            @if($order->payment_method === 'vnpay' && $order->payment_status !== 'paid' && $order->status === 'processing')
                                <a href="{{ route('vnpay.retry', $order->order_code) }}" class="btn btn-sm btn-orange py-2 px-3 fw-semibold">
                                    <i class="bi bi-credit-card me-1"></i> Thanh toán VNPay
                                </a>
                            @endif

                            <!-- Cancel button -->
                            @if($order->status === 'processing')
                                <form action="{{ route('orders.cancel', $order->order_code) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-2 px-3">
                                        <i class="bi bi-x-circle me-1"></i> Hủy đơn hàng
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
