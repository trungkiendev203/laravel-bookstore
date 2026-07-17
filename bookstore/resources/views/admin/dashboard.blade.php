@extends('admin.layout')

@section('title', 'Admin Dashboard - Bookstore')
@section('page_title', 'Tổng Quan Báo Cáo')

@section('admin_content')
<!-- Stats Cards Row -->
<div class="row g-4 mb-4">
    <!-- Revenue -->
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold uppercase d-block mb-1">Doanh thu thật</span>
                    <h3 class="fw-bold m-0 text-success">{{ number_format($totalRevenue, 0, ',', '.') }} ₫</h3>
                </div>
                <div class="bg-success-subtle text-success rounded p-3">
                    <i class="bi bi-currency-dollar fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders -->
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold uppercase d-block mb-1">Tổng đơn hàng</span>
                    <h3 class="fw-bold m-0 text-primary">{{ $totalOrders }}</h3>
                </div>
                <div class="bg-primary-subtle text-primary rounded p-3">
                    <i class="bi bi-cart-check fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Books -->
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold uppercase d-block mb-1">Số lượng đầu sách</span>
                    <h3 class="fw-bold m-0 text-warning">{{ $totalBooks }}</h3>
                </div>
                <div class="bg-warning-subtle text-warning rounded p-3">
                    <i class="bi bi-book fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Users -->
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold uppercase d-block mb-1">Khách hàng đăng ký</span>
                    <h3 class="fw-bold m-0 text-info">{{ $totalUsers }}</h3>
                </div>
                <div class="bg-info-subtle text-info rounded p-3">
                    <i class="bi bi-people fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Status Breakdowns -->
<div class="card border-0 shadow-sm p-4 mb-4">
    <h5 class="fw-bold mb-3">Tình trạng đơn hàng</h5>
    <div class="row g-3 text-center">
        <div class="col-6 col-md-3">
            <div class="p-3 border rounded bg-light">
                <span class="d-block text-warning fw-bold fs-3">{{ $ordersByStatus['processing'] }}</span>
                <span class="text-muted small">Chờ xử lý</span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 border rounded bg-light">
                <span class="d-block text-primary fw-bold fs-3">{{ $ordersByStatus['shipping'] }}</span>
                <span class="text-muted small">Đang giao</span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 border rounded bg-light">
                <span class="d-block text-success fw-bold fs-3">{{ $ordersByStatus['completed'] }}</span>
                <span class="text-muted small">Đã hoàn tất</span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 border rounded bg-light">
                <span class="d-block text-danger fw-bold fs-3">{{ $ordersByStatus['cancelled'] }}</span>
                <span class="text-muted small">Đã hủy</span>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders & Best Sellers -->
<div class="row g-4">
    <!-- Recent Orders (Cột trái) -->
    <div class="col-xl-7">
        <div class="card border-0 shadow-sm p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0">Đơn hàng mới nhận</h5>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">Tất cả đơn hàng</a>
            </div>

            <div class="table-responsive">
                <table class="table align-middle table-hover small">
                    <thead>
                        <tr class="text-muted">
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Tổng tiền</th>
                            <th class="text-center">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                            <tr>
                                <td class="fw-bold text-dark">{{ $order->order_code }}</td>
                                <td>
                                    <span class="d-block fw-semibold">{{ $order->customer_name }}</span>
                                    <span class="text-muted text-xs">{{ $order->customer_phone }}</span>
                                </td>
                                <td>
                                    @if($order->status === 'processing')
                                        <span class="badge bg-warning text-dark">Chờ xử lý</span>
                                    @elseif($order->status === 'shipping')
                                        <span class="badge bg-primary">Đang giao</span>
                                    @elseif($order->status === 'completed')
                                        <span class="badge bg-success">Hoàn tất</span>
                                    @else
                                        <span class="badge bg-danger">Đã hủy</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-dark">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-light p-1 px-2 border">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Best Seller Books (Cột phải) -->
    <div class="col-xl-5">
        <div class="card border-0 shadow-sm p-4 h-100">
            <h5 class="fw-bold mb-4">Truyện bán chạy nhất</h5>
            
            <div class="d-flex flex-column gap-3">
                @foreach($topBooks as $b)
                    <div class="d-flex align-items-center justify-content-between p-2 border-bottom">
                        <div class="d-flex align-items-center" style="width: 75%;">
                            <div class="rounded overflow-hidden border bg-light me-2.5" style="width: 40px; height: 55px; flex-shrink: 0;">
                                @if($b->cover_image)
                                    <img src="{{ $b->cover_image_url }}" alt="{{ $b->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                        <i class="bi bi-book"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="text-truncate">
                                <span class="fw-bold text-dark d-block text-truncate">{{ $b->title }}</span>
                                <span class="text-muted small">Thể loại: {{ $b->category->name }}</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="d-block fw-bold text-orange">{{ $b->sold_count }}</span>
                            <span class="text-muted small">Đã bán</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
