@extends('layouts.main')

@section('title', 'Giỏ hàng của bạn - Arsha Bookstore')

@section('content')
<h3 class="fw-bold mb-4"><i class="bi bi-cart3 text-orange me-2"></i> Giỏ Hàng Của Bạn</h3>

@if(empty($cart))
    <div class="card border-0 shadow-sm p-5 text-center my-4">
        <div class="mb-4">
            <i class="bi bi-cart-x text-muted" style="font-size: 6rem;"></i>
        </div>
        <h4 class="fw-bold">Giỏ hàng đang trống</h4>
        <p class="text-muted">Hãy lấp đầy giỏ hàng của bạn bằng những cuốn sách cực hay nhé!</p>
        <a href="{{ route('home') }}" class="btn btn-orange px-5 py-2.5 mt-3">Tiếp tục mua sắm</a>
    </div>
@else
    <div class="row g-4">
        <!-- Cart Items (Cột trái) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr class="text-muted small">
                                <th scope="col" style="width: 50%;">Sản phẩm</th>
                                <th scope="col" class="text-center" style="width: 15%;">Giá</th>
                                <th scope="col" class="text-center" style="width: 20%;">Số lượng</th>
                                <th scope="col" class="text-end" style="width: 15%;">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cart as $itemId => $item)
                                <tr id="cart-row-{{ $itemId }}" class="border-bottom-0">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded overflow-hidden border bg-light me-3" style="width: 60px; height: 80px; flex-shrink: 0; position: relative;">
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
                                            <div>
                                                <h6 class="fw-bold mb-1">
                                                    <a href="{{ route('books.show', $item['slug']) }}" class="text-decoration-none text-dark hover-orange">{{ $item['title'] }}</a>
                                                </h6>
                                                <form action="{{ route('cart.remove') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="book_id" value="{{ $itemId }}">
                                                    <button type="submit" class="btn btn-link text-danger p-0 border-0 small text-decoration-none" style="font-size: 0.85rem;">
                                                        <i class="bi bi-trash3 me-1"></i> Xóa khỏi giỏ
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-medium">{{ number_format($item['price'], 0, ',', '.') }} ₫</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="input-group input-group-sm mx-auto" style="width: 110px;">
                                            <button class="btn btn-outline-secondary btn-qty-change" data-id="{{ $itemId }}" data-action="minus" type="button">-</button>
                                            <input type="number" id="qty-input-{{ $itemId }}" class="form-control text-center qty-input" value="{{ $item['quantity'] }}" min="1" max="{{ $item['stock'] }}" data-id="{{ $itemId }}" readonly>
                                            <button class="btn btn-outline-secondary btn-qty-change" data-id="{{ $itemId }}" data-action="plus" type="button">+</button>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold text-orange item-subtotal" id="item-subtotal-{{ $itemId }}">
                                            {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }} ₫
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Summary (Cột phải) -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 sticky-lg-top" style="top: 75px; z-index: 10;">
                <h5 class="fw-bold mb-4">Tóm tắt đơn hàng</h5>
                
                <div class="d-flex justify-content-between mb-3 text-secondary">
                    <span>Tạm tính:</span>
                    <span id="subtotal-val" class="fw-semibold text-dark">{{ number_format($subtotal, 0, ',', '.') }} ₫</span>
                </div>
                
                <div class="d-flex justify-content-between mb-3 text-secondary">
                    <span>Phí vận chuyển:</span>
                    <span id="shipping-val" class="fw-semibold text-dark">{{ number_format($shippingFee, 0, ',', '.') }} ₫</span>
                </div>

                @php
                    $freeShippingThreshold = 300000;
                    $remainingForFreeShip = $freeShippingThreshold - $subtotal;
                @endphp

                <div id="free-ship-alert" class="alert {{ $remainingForFreeShip > 0 ? 'alert-warning text-secondary' : 'alert-success text-success' }} border-0 py-2.5 px-3 small mb-4" style="border-radius: 8px;">
                    @if($remainingForFreeShip > 0)
                        <i class="bi bi-info-circle-fill me-1 text-warning"></i> Mua thêm <strong id="remaining-val">{{ number_format($remainingForFreeShip, 0, ',', '.') }} ₫</strong> để được miễn phí vận chuyển!
                    @else
                        <i class="bi bi-check-circle-fill me-1 text-success"></i> Đơn hàng của bạn đã được <strong>Miễn phí vận chuyển</strong>!
                    @endif
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between mb-4">
                    <span class="fw-bold fs-5">Tổng cộng:</span>
                    <span id="total-val" class="fw-bold fs-5 text-orange">{{ number_format($total, 0, ',', '.') }} ₫</span>
                </div>

                <a href="{{ route('checkout.index') }}" class="btn btn-orange w-100 py-3 fw-bold fs-6" style="border-radius: 8px;">
                    Tiến hành thanh toán
                </a>
            </div>
        </div>
    </div>
@endif
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Lắng nghe sự kiện click thay đổi số lượng
        document.querySelectorAll('.btn-qty-change').forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.getAttribute('data-id');
                const action = this.getAttribute('data-action');
                const input = document.getElementById('qty-input-' + itemId);
                let quantity = parseInt(input.value) || 1;
                
                if (action === 'minus') {
                    if (quantity > 1) quantity--;
                } else if (action === 'plus') {
                    quantity++;
                }
                
                updateCartQuantity(itemId, quantity);
            });
        });

        // AJAX update cart quantity
        function updateCartQuantity(bookId, quantity) {
            fetch('{{ route('cart.update') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    book_id: bookId,
                    quantity: quantity
                })
            })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(res => {
                if (res.status === 200 && res.body.success) {
                    // Cập nhật giá trị input
                    document.getElementById('qty-input-' + bookId).value = quantity;
                    // Cập nhật badge giỏ hàng
                    document.getElementById('cart-badge').textContent = res.body.cart_count;
                    // Cập nhật thành tiền của dòng sản phẩm
                    document.getElementById('item-subtotal-' + bookId).textContent = res.body.item_subtotal.replace('đ', ' ₫');
                    // Cập nhật các trường tổng tiền
                    document.getElementById('subtotal-val').textContent = res.body.subtotal.replace('đ', ' ₫');
                    document.getElementById('shipping-val').textContent = res.body.shipping_fee.replace('đ', ' ₫');
                    document.getElementById('total-val').textContent = res.body.total.replace('đ', ' ₫');
                    
                    // Cập nhật gợi ý miễn phí ship
                    let subtotalInt = parseInt(res.body.subtotal.replace(/\D/g, '')) || 0;
                    let remaining = 300000 - subtotalInt;
                    const alertDiv = document.getElementById('free-ship-alert');
                    if (remaining > 0) {
                        alertDiv.className = 'alert alert-warning text-secondary border-0 py-2.5 px-3 small mb-4';
                        alertDiv.innerHTML = `<i class="bi bi-info-circle-fill me-1 text-warning"></i> Mua thêm <strong>${new Intl.NumberFormat('vi-VN').format(remaining)} ₫</strong> để được miễn phí vận chuyển!`;
                    } else {
                        alertDiv.className = 'alert alert-success text-success border-0 py-2.5 px-3 small mb-4';
                        alertDiv.innerHTML = `<i class="bi bi-check-circle-fill me-1 text-success"></i> Đơn hàng của bạn đã được <strong>Miễn phí vận chuyển</strong>!`;
                    }
                    
                    showToast(res.body.message, 'success');
                } else {
                    showToast(res.body.message || 'Cập nhật số lượng thất bại.', 'error');
                }
            })
            .catch(error => {
                console.error('Error updating cart:', error);
                showToast('Không thể kết nối đến máy chủ.', 'error');
            });
        }
    });
</script>
@endsection
