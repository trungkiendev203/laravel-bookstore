@extends('admin.layout')

@section('title', 'Quản lý đơn hàng - Bookstore')
@section('page_title', 'Danh Sách Đơn Hàng')

@section('admin_content')
<div class="card border-0 shadow-sm p-4">
    <h5 class="fw-bold mb-4">Danh sách tất cả đơn hàng</h5>

    <!-- Search & Filter Panel -->
    <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-3 mb-4 p-3 bg-light rounded border">
        <!-- Search Keyword -->
        <div class="col-md-4">
            <label class="form-label small fw-semibold text-secondary">Tìm kiếm</label>
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Mã đơn, tên, sđt khách..." value="{{ request('search') }}">
        </div>

        <!-- Status Filter -->
        <div class="col-md-3">
            <label class="form-label small fw-semibold text-secondary">Trạng thái đơn</label>
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Tất cả trạng thái</option>
                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                <option value="shipping" {{ request('status') === 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Đã hoàn tất</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
            </select>
        </div>

        <!-- Payment Method Filter -->
        <div class="col-md-3">
            <label class="form-label small fw-semibold text-secondary">Phương thức TT</label>
            <select name="payment_method" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Tất cả phương thức</option>
                <option value="cod" {{ request('payment_method') === 'cod' ? 'selected' : '' }}>COD</option>
                <option value="vnpay" {{ request('payment_method') === 'vnpay' ? 'selected' : '' }}>VNPay</option>
            </select>
        </div>

        <!-- Filter & Reset Buttons -->
        <div class="col-md-2 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Lọc</button>
            @if(request('search') || request('status') || request('payment_method'))
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm flex-grow-1">Reset</a>
            @endif
        </div>
    </form>

    <!-- Orders Table -->
    @if($orders->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
            <h5 class="mt-3">Không tìm thấy đơn hàng nào</h5>
            <p class="text-muted">Thử thay đổi bộ lọc hoặc kiểm tra từ khóa.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr class="text-muted small">
                        <th>Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Khách hàng / SĐT</th>
                        <th>Hình thức TT</th>
                        <th>Trạng thái TT</th>
                        <th>Trạng thái đơn</th>
                        <th class="text-end">Tổng tiền</th>
                        <th class="text-center">Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td class="fw-bold text-dark">{{ $order->order_code }}</td>
                            <td>{{ $order->created_at->format('H:i d/m/Y') }}</td>
                            <td>
                                <span class="d-block fw-semibold text-dark">{{ $order->customer_name }}</span>
                                <span class="text-muted small">{{ $order->customer_phone }}</span>
                            </td>
                            <td><span class="badge bg-light text-secondary border">{{ strtoupper($order->payment_method) }}</span></td>
                            <td>
                                @if($order->payment_status === 'paid')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Đã thanh toán</span>
                                @elseif($order->payment_status === 'failed')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Thanh toán lỗi</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Chờ thanh toán</span>
                                @endif
                            </td>
                            <td>
                                @if($order->status === 'processing')
                                    <span class="badge bg-warning text-dark">Đang xử lý</span>
                                @elseif($order->status === 'shipping')
                                    <span class="badge bg-primary">Đang giao</span>
                                @elseif($order->status === 'completed')
                                    <span class="badge bg-success">Hoàn tất</span>
                                @else
                                    <span class="badge bg-danger">Đã hủy</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-orange">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</td>
                            <td class="text-center">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary py-1 px-2">
                                    <i class="bi bi-eye-fill"></i> Xem
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
