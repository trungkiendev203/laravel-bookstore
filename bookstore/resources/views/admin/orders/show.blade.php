@extends('admin.layout')

@section('title', 'Chi tiết đơn hàng - Bookstore')
@section('page_title', 'Chi Tiết Đơn Hàng #' . $order->order_code)

@section('admin_content')
<div class="row g-4">
    <!-- Order info, status update (Cột trái) -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4 mb-4">
            <h5 class="fw-bold mb-4 pb-2 border-bottom">Sản phẩm đã đặt</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="text-muted small">
                            <th>Tên sản phẩm</th>
                            <th class="text-center">Giá bán</th>
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
                                <td class="text-end fw-bold text-dark">{{ number_format($item->subtotal, 0, ',', '.') }} ₫</td>
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

        <!-- Customer & Delivery info -->
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold mb-4 pb-2 border-bottom">Thông tin giao nhận</h5>
            <table class="table table-borderless align-middle mb-0 small">
                <tr>
                    <td class="text-muted" style="width: 25%;">Họ tên khách hàng:</td>
                    <td class="fw-semibold text-dark">{{ $order->customer_name }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Số điện thoại:</td>
                    <td class="fw-semibold text-dark">{{ $order->customer_phone }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Địa chỉ nhận hàng:</td>
                    <td class="text-dark">{{ $order->customer_address }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Ghi chú từ khách:</td>
                    <td class="text-secondary"><em>"{{ $order->note ?? 'Không có ghi chú' }}"</em></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Status control panel (Cột phải) -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 sticky-lg-top" style="top: 75px; z-index: 10;">
            <h5 class="fw-bold mb-4 pb-2 border-bottom">Xử lý đơn hàng</h5>
            
            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                @csrf

                <!-- Order Status -->
                <div class="mb-3">
                    <label for="status" class="form-label fw-semibold">Trạng thái đơn hàng</label>
                    <select name="status" id="status" class="form-select">
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Đang xử lý (Chờ gói)</option>
                        <option value="shipping" {{ $order->status === 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Đã hoàn tất (Đã nhận hàng)</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>

                <!-- Payment Status -->
                <div class="mb-4">
                    <label for="payment_status" class="form-label fw-semibold">Trạng thái thanh toán</label>
                    <select name="payment_status" id="payment_status" class="form-select">
                        <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Chờ thanh toán</option>
                        <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                        <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Thanh toán lỗi / Thất bại</option>
                    </select>
                </div>

                <!-- Info summary -->
                <div class="bg-light p-3 rounded mb-4 small">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Phương thức TT:</span>
                        <strong class="text-dark">{{ strtoupper($order->payment_method) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Đăng bởi thành viên:</span>
                        <strong class="text-dark">{{ $order->user ? $order->user->name : 'Khách vãng lai' }}</strong>
                    </div>
                </div>

                <!-- Action Button -->
                <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold mb-2">
                    Cập nhật trạng thái
                </button>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary w-100 py-2">
                    Quay lại danh sách
                </a>
            </form>
        </div>
    </div>
</div>
@endsection
