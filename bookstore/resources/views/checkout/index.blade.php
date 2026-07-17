@extends('layouts.main')

@section('title', 'Thanh toán đơn hàng - Arsha Bookstore')

@section('content')
<h3 class="fw-bold mb-4"><i class="bi bi-wallet2 text-orange me-2"></i> Thanh Toán</h3>

<!-- Checkout Form -->
<form action="{{ route('checkout.process') }}" method="POST">
    @csrf
    <div class="row g-4">
        <!-- Delivery info (Cột trái) -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-4 pb-2 border-bottom"><i class="bi bi-geo-alt-fill text-orange me-2"></i> Thông tin giao hàng</h5>
                
                @if(!auth()->check())
                    <div class="alert alert-light border small text-muted mb-4 py-2 px-3">
                        <i class="bi bi-info-circle me-1 text-orange"></i> Bạn chưa đăng nhập. Bạn vẫn có thể mua hàng, hoặc <a href="{{ route('login') }}" class="text-orange fw-bold">Đăng nhập</a> để tự động điền thông tin và lưu lịch sử mua hàng.
                    </div>
                @endif

                <div class="mb-3">
                    <label for="customer_name" class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" class="form-control py-2.5 @error('customer_name') is-invalid @enderror" id="customer_name" name="customer_name" placeholder="Nhập đầy đủ họ tên..." value="{{ old('customer_name', $user->name ?? '') }}">
                    @error('customer_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="customer_phone" class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="text" class="form-control py-2.5 @error('customer_phone') is-invalid @enderror" id="customer_phone" name="customer_phone" placeholder="Nhập số điện thoại nhận hàng..." value="{{ old('customer_phone', $user->phone ?? '') }}">
                        @error('customer_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="customer_address" class="form-label fw-semibold">Địa chỉ nhận hàng <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('customer_address') is-invalid @enderror" id="customer_address" name="customer_address" rows="3" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố...">{{ old('customer_address', $user->address ?? '') }}</textarea>
                    @error('customer_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="note" class="form-label fw-semibold">Ghi chú (Tùy chọn)</label>
                    <textarea class="form-control" id="note" name="note" rows="2" placeholder="Ví dụ: Giao giờ hành chính, gọi điện trước khi giao...">{{ old('note') }}</textarea>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold mb-4 pb-2 border-bottom"><i class="bi bi-credit-card-2-front-fill text-orange me-2"></i> Phương thức thanh toán</h5>
                
                <div class="d-flex flex-column gap-3">
                    <!-- COD -->
                    <label class="d-flex align-items-center p-3 border rounded-3 cursor-pointer @error('payment_method') border-danger @enderror" style="cursor: pointer;">
                        <input type="radio" name="payment_method" value="cod" class="form-check-input me-3" {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }}>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-truck text-success fs-3 me-3"></i>
                            <div>
                                <span class="fw-bold d-block">Thanh toán khi nhận hàng (COD)</span>
                                <span class="text-muted small">Nhận hàng rồi thanh toán bằng tiền mặt tại nhà.</span>
                            </div>
                        </div>
                    </label>

                    <!-- VNPay -->
                    <label class="d-flex align-items-center p-3 border rounded-3 cursor-pointer @error('payment_method') border-danger @enderror" style="cursor: pointer;">
                        <input type="radio" name="payment_method" value="vnpay" class="form-check-input me-3" {{ old('payment_method') === 'vnpay' ? 'checked' : '' }}>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-qr-code-scan text-primary fs-3 me-3"></i>
                            <div>
                                <span class="fw-bold d-block">Thanh toán online qua cổng VNPay</span>
                                <span class="text-muted small">Thanh toán nhanh chóng bằng thẻ nội địa, thẻ quốc tế hoặc quét mã QR.</span>
                            </div>
                        </div>
                    </label>

                    @error('payment_method')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Order Summary (Cột phải) -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm p-4 sticky-lg-top" style="top: 75px; z-index: 10;">
                <h5 class="fw-bold mb-4 pb-2 border-bottom">Tóm tắt đơn hàng</h5>
                
                <!-- Items list -->
                <div class="mb-4" style="max-height: 250px; overflow-y: auto;">
                    @foreach($cart as $item)
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <div class="d-flex align-items-center" style="width: 70%;">
                                <div class="rounded overflow-hidden border bg-light me-2.5" style="width: 40px; height: 55px; flex-shrink: 0;">
                                    @if($item['cover_image'])
                                        @php
                                            $isUrl = str_starts_with($item['cover_image'], 'http://') || str_starts_with($item['cover_image'], 'https://');
                                            $imgSrc = $isUrl ? $item['cover_image'] : asset('storage/' . $item['cover_image']);
                                        @endphp
                                        <img src="{{ $imgSrc }}" alt="{{ $item['title'] }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                            <i class="bi bi-book"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="text-truncate">
                                    <span class="fw-semibold text-dark d-block text-truncate">{{ $item['title'] }}</span>
                                    <span class="text-muted small">Số lượng: {{ $item['quantity'] }}</span>
                                </div>
                            </div>
                            <span class="fw-medium text-dark">{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }} ₫</span>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-between mb-2 text-secondary">
                    <span>Tạm tính:</span>
                    <span class="fw-semibold text-dark">{{ number_format($subtotal, 0, ',', '.') }} ₫</span>
                </div>
                
                <div class="d-flex justify-content-between mb-3 text-secondary">
                    <span>Phí vận chuyển:</span>
                    <span class="fw-semibold text-dark">{{ number_format($shippingFee, 0, ',', '.') }} ₫</span>
                </div>

                <hr class="my-3">

                <div class="d-flex justify-content-between mb-4">
                    <span class="fw-bold fs-5">Tổng cộng:</span>
                    <span class="fw-bold fs-5 text-orange">{{ number_format($total, 0, ',', '.') }} ₫</span>
                </div>

                <button type="submit" class="btn btn-orange w-100 py-3 fw-bold fs-6">
                    <i class="bi bi-bag-check-fill me-1"></i> Xác nhận Đặt hàng
                </button>
            </div>
        </div>
    </div>
</form>
@endsection
