@extends('layouts.main')

@section('title', $book->title . ' - Arsha Bookstore')

@section('content')
<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4 bg-white p-3 rounded shadow-sm">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Trang chủ</a></li>
        <li class="breadcrumb-item"><a href="{{ route('home', ['category' => $book->category->slug]) }}" class="text-decoration-none text-muted">{{ $book->category->name }}</a></li>
        <li class="breadcrumb-item active text-orange" aria-current="page">{{ $book->title }}</li>
    </ol>
</nav>

<!-- Book Details Card -->
<div class="card border-0 shadow-sm p-4 mb-5">
    <div class="row g-5">
        <!-- Book Cover (Cột trái) -->
        <div class="col-md-5 col-lg-4">
            <div class="book-img-wrapper rounded shadow-sm">
                @if($book->sale_price)
                    @php
                        $discount = round((($book->price - $book->sale_price) / $book->price) * 100);
                    @endphp
                    <span class="badge-discount fs-6">-{{ $discount }}%</span>
                @endif

                @if($book->stock <= 0)
                    <span class="badge-outofstock fs-5">Hết hàng</span>
                @endif

                @if($book->cover_image)
                    <img src="{{ $book->cover_image_url }}" class="book-img" alt="{{ $book->title }}">
                @else
                    <div class="book-img d-flex align-items-center justify-content-center bg-light text-muted">
                        <i class="bi bi-book" style="font-size: 5rem;"></i>
                    </div>
                @endif
            </div>
        </div>

        <!-- Book Info (Cột phải) -->
        <div class="col-md-7 col-lg-8 d-flex flex-column">
            <span class="badge bg-light text-secondary border align-self-start mb-2 py-2 px-3">{{ $book->category->name }}</span>
            <h2 class="fw-bold mb-2">{{ $book->title }}</h2>
            <p class="text-muted fs-5 mb-4">Tác giả: <strong class="text-dark">{{ $book->author ?? 'Đang cập nhật' }}</strong></p>
            
            <!-- Price Info -->
            <div id="price-wrapper" class="bg-light p-4 rounded-3 mb-4 d-flex align-items-baseline gap-3">
                @if($book->sale_price)
                    <span class="text-orange fw-bold fs-2">{{ number_format($book->sale_price, 0, ',', '.') }} ₫</span>
                    <span class="text-muted text-decoration-line-through fs-5">{{ number_format($book->price, 0, ',', '.') }} ₫</span>
                @else
                    <span class="text-orange fw-bold fs-2">{{ number_format($book->price, 0, ',', '.') }} ₫</span>
                @endif
            </div>

            <!-- Stock status -->
            <div class="mb-4">
                <p class="mb-2">Tình trạng: 
                    <span id="stock-wrapper">
                        @if($book->stock > 0)
                            <span class="badge bg-success py-1.5 px-2.5">Còn hàng ({{ $book->stock }} cuốn)</span>
                        @else
                            <span class="badge bg-danger py-1.5 px-2.5">Hết hàng</span>
                        @endif
                    </span>
                </p>
                <p class="text-muted small mb-0"><i class="bi bi-tag-fill me-1 text-orange"></i> Đã bán: <strong>{{ $book->sold_count }}</strong> cuốn</p>
            </div>

            @if($book->volumes->count() > 0)
                <!-- Selector tập truyện -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark"><i class="bi bi-collection me-1 text-primary"></i>Chọn tập truyện:</label>
                    <div class="d-flex flex-wrap gap-2 volume-selector-container">
                        @foreach($book->volumes as $vol)
                            @php
                                $priceText = $vol->sale_price ? number_format($vol->sale_price, 0, ',', '.') : number_format($vol->price, 0, ',', '.');
                                $isOutOfStock = $vol->stock <= 0;
                            @endphp
                            <button type="button" 
                                    class="btn btn-outline-dark volume-select-btn py-2 px-3 {{ $isOutOfStock ? 'opacity-50' : '' }}" 
                                    data-vol-id="{{ $vol->id }}"
                                    data-vol-title="{{ $vol->title }}"
                                    data-vol-price="{{ $vol->price }}"
                                    data-vol-sale-price="{{ $vol->sale_price }}"
                                    data-vol-stock="{{ $vol->stock }}"
                                    data-vol-cover="{{ $vol->cover_image_url }}">
                                Tập {{ $vol->volume_number }}
                                @if($isOutOfStock)
                                    <span class="text-xs d-block text-danger mt-0.5">(Hết hàng)</span>
                                @else
                                    <span class="text-xs d-block text-muted mt-0.5">{{ $priceText }} ₫</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($book->stock > 0 || $book->volumes->count() > 0)
                <!-- Quantity & Actions -->
                <form action="{{ route('cart.add') }}" method="POST" id="buy-now-form" class="mb-4">
                    @csrf
                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                    <input type="hidden" name="buy_now" value="0" id="buy_now_flag">
                    
                    <div class="row align-items-center g-3 mb-4">
                        <div class="col-auto">
                            <label for="quantity-{{ $book->id }}" class="form-label mb-0 fw-semibold">Số lượng:</label>
                        </div>
                        <div class="col-auto">
                            <div class="input-group" style="width: 140px;">
                                <button class="btn btn-outline-secondary btn-qty-minus" type="button">-</button>
                                <input type="number" id="quantity-{{ $book->id }}" name="quantity" class="form-control text-center py-2" value="1" min="1" max="{{ $book->stock }}">
                                <button class="btn btn-outline-secondary btn-qty-plus" type="button">+</button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <!-- Add to Cart AJAX Button -->
                        <button type="button" class="btn btn-lg btn-outline-orange px-4 py-3 flex-grow-1 ajax-add-to-cart" data-book-id="{{ $book->id }}">
                            <i class="bi bi-cart-plus-fill me-2"></i> Thêm vào giỏ hàng
                        </button>
                        <!-- Buy Now Button -->
                        <button type="button" class="btn btn-lg btn-orange px-4 py-3 flex-grow-1" id="btn-buy-now">
                            Mua ngay
                        </button>
                    </div>
                </form>
            @else
                <div class="alert alert-secondary border-0 p-3 mb-4">
                    <i class="bi bi-info-circle-fill me-2"></i> Truyện hiện tại đã hết hàng. Vui lòng quay lại sau!
                </div>
            @endif

            <!-- Dịch vụ & Cam kết -->
            <div class="row g-3 mt-4 pt-3 border-top">
                <div class="col-6 col-sm-3 text-center text-sm-start d-flex flex-column flex-sm-row align-items-center gap-2">
                    <div class="rounded-circle bg-light p-2.5 text-orange d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; min-width: 45px;">
                        <i class="bi bi-truck fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size: 0.85rem;">Giao hàng siêu tốc</h6>
                        <span class="text-muted" style="font-size: 0.75rem;">Freeship từ 300k</span>
                    </div>
                </div>
                <div class="col-6 col-sm-3 text-center text-sm-start d-flex flex-column flex-sm-row align-items-center gap-2">
                    <div class="rounded-circle bg-light p-2.5 text-orange d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; min-width: 45px;">
                        <i class="bi bi-patch-check fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size: 0.85rem;">Sách chính hãng</h6>
                        <span class="text-muted" style="font-size: 0.75rem;">Mới 100% nguyên seal</span>
                    </div>
                </div>
                <div class="col-6 col-sm-3 text-center text-sm-start d-flex flex-column flex-sm-row align-items-center gap-2">
                    <div class="rounded-circle bg-light p-2.5 text-orange d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; min-width: 45px;">
                        <i class="bi bi-arrow-counterclockwise fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size: 0.85rem;">Đổi trả dễ dàng</h6>
                        <span class="text-muted" style="font-size: 0.75rem;">Trong vòng 7 ngày</span>
                    </div>
                </div>
                <div class="col-6 col-sm-3 text-center text-sm-start d-flex flex-column flex-sm-row align-items-center gap-2">
                    <div class="rounded-circle bg-light p-2.5 text-orange d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; min-width: 45px;">
                        <i class="bi bi-headset fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size: 0.85rem;">Hỗ trợ 24/7</h6>
                        <span class="text-muted" style="font-size: 0.75rem;">Giải đáp tận tâm</span>
                    </div>
                </div>
            </div>

            <!-- Thông số chi tiết -->
            <div class="mt-4 p-3 bg-light rounded-3">
                <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-info-circle-fill text-secondary me-2"></i>Thông tin chi tiết</h6>
                <div class="row g-2">
                    <div class="col-sm-6">
                        <div class="d-flex justify-content-between border-bottom pb-1.5" style="font-size: 0.9rem;">
                            <span class="text-muted">Tác giả:</span>
                            <span class="fw-semibold text-dark">{{ $book->author ?? 'Đang cập nhật' }}</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex justify-content-between border-bottom pb-1.5" style="font-size: 0.9rem;">
                            <span class="text-muted">Thể loại:</span>
                            <span class="fw-semibold text-dark">{{ $book->category->name }}</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex justify-content-between border-bottom pb-1.5" style="font-size: 0.9rem;">
                            <span class="text-muted">Hình thức:</span>
                            <span class="fw-semibold text-dark">Bìa mềm</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex justify-content-between border-bottom pb-1.5" style="font-size: 0.9rem;">
                            <span class="text-muted">Ngôn ngữ:</span>
                            <span class="fw-semibold text-dark">Tiếng Việt</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Description -->
    @if($book->description)
        <hr class="my-5">
        <h4 class="fw-bold mb-4">Mô tả sản phẩm</h4>
        <div class="lh-lg text-secondary" style="white-space: pre-line;">
            {{ $book->description }}
        </div>
    @endif

    <!-- Comments & Reviews Section -->
    <hr class="my-5">
    <div class="row g-4" id="comments-section">
        <div class="col-lg-8">
            <h4 class="fw-bold mb-4 d-flex align-items-center">
                <i class="bi bi-chat-left-text-fill text-orange me-2"></i> Bình luận & Đánh giá ({{ $book->comments->count() }})
            </h4>

            <!-- List Comments -->
            @if($book->comments->isEmpty())
                <div class="card border-0 bg-light p-4 text-center text-muted mb-4" style="border-radius: 12px;">
                    <i class="bi bi-chat-dots fs-1 mb-2 text-secondary"></i>
                    <p class="mb-0">Chưa có bình luận nào. Hãy là người đầu tiên chia sẻ cảm nhận!</p>
                </div>
            @else
                <div class="d-flex flex-column gap-3 mb-4">
                    @foreach($book->comments as $comment)
                        <div class="card border-0 shadow-sm p-3" style="border-radius: 12px; background-color: #fafafa;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-orange text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 35px; height: 35px; font-size: 0.9rem;">
                                        {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0" style="font-size: 0.95rem;">{{ $comment->user->name }}</h6>
                                        <div class="text-warning small" style="font-size: 0.8rem;">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $comment->rating)
                                                    <i class="bi bi-star-fill"></i>
                                                @else
                                                    <i class="bi bi-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <span class="text-muted small" style="font-size: 0.8rem;">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="mb-0 text-secondary" style="font-size: 0.9rem; white-space: pre-line; padding-left: 43px;">{{ $comment->content }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Write a comment form -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 bg-white sticky-lg-top" style="top: 100px; border-radius: 16px;">
                <h5 class="fw-bold mb-3">Viết đánh giá</h5>
                
                @auth
                    <form action="{{ route('books.comments.store', $book->id) }}" method="POST">
                        @csrf
                        <!-- Rating Selector -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted d-block">Đánh giá của bạn:</label>
                            <div class="d-flex gap-1 rating-select-container fs-3 text-secondary" style="cursor: pointer;">
                                <i class="bi bi-star-fill star-select text-warning" data-value="1"></i>
                                <i class="bi bi-star-fill star-select text-warning" data-value="2"></i>
                                <i class="bi bi-star-fill star-select text-warning" data-value="3"></i>
                                <i class="bi bi-star-fill star-select text-warning" data-value="4"></i>
                                <i class="bi bi-star-fill star-select text-warning" data-value="5"></i>
                            </div>
                            <input type="hidden" name="rating" id="rating-input" value="5">
                        </div>

                        <!-- Content Textarea -->
                        <div class="mb-3">
                            <label for="comment-content" class="form-label fw-semibold small text-muted">Nội dung bình luận:</label>
                            <textarea name="content" id="comment-content" rows="4" class="form-control @error('content') is-invalid @enderror" placeholder="Nhập cảm nhận của bạn về cuốn truyện này..." style="border-radius: 8px; resize: none;"></textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-orange w-100 py-2 fw-semibold" style="border-radius: 8px;">
                            Gửi đánh giá <i class="bi bi-send ms-1"></i>
                        </button>
                    </form>
                @else
                    <div class="text-center py-3">
                        <i class="bi bi-lock text-muted fs-1 mb-2 d-block"></i>
                        <p class="text-muted small">Vui lòng đăng nhập để gửi đánh giá và nhận xét của bạn.</p>
                        <a href="{{ route('login') }}" class="btn btn-outline-orange btn-sm px-4 fw-semibold" style="border-radius: 20px;">
                            Đăng nhập ngay
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedBooks->isNotEmpty())
        <hr class="my-5">
        <h4 class="fw-bold mb-4"><i class="bi bi-bookmark-star text-orange me-2"></i>Truyện Cùng Thể Loại</h4>
        <div class="row row-cols-2 row-cols-md-4 g-4">
            @foreach($relatedBooks as $rel)
                <div class="col">
                    @include('partials.book-card', ['book' => $rel])
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const qtyInput = document.querySelector('input[name="quantity"]');
        const btnMinus = document.querySelector('.btn-qty-minus');
        const btnPlus = document.querySelector('.btn-qty-plus');
        const buyNowForm = document.getElementById('buy-now-form');
        const btnBuyNow = document.getElementById('btn-buy-now');
        const bookIdInput = document.querySelector('input[name="book_id"]');
        const addToCartBtn = document.querySelector('.ajax-add-to-cart');
        const volumeButtons = document.querySelectorAll('.volume-select-btn');

        if (qtyInput) {
            btnMinus.addEventListener('click', function() {
                let current = parseInt(qtyInput.value) || 1;
                if (current > 1) {
                    qtyInput.value = current - 1;
                }
            });

            btnPlus.addEventListener('click', function() {
                let current = parseInt(qtyInput.value) || 1;
                let max = parseInt(qtyInput.getAttribute('max')) || 999;
                if (current < max) {
                    qtyInput.value = current + 1;
                }
            });

            qtyInput.addEventListener('change', function() {
                let current = parseInt(qtyInput.value) || 1;
                let max = parseInt(qtyInput.getAttribute('max')) || 999;
                if (current < 1) qtyInput.value = 1;
                if (current > max) qtyInput.value = max;
            });
        }

        // Volume Selector logic
        if (volumeButtons.length > 0) {
            let defaultSelectedBtn = null;
            @if(session('selected_volume_id'))
                defaultSelectedBtn = document.querySelector('.volume-select-btn[data-vol-id="{{ session('selected_volume_id') }}"]');
            @endif
            
            if (!defaultSelectedBtn) {
                // Find first with stock
                for (let btn of volumeButtons) {
                    if (parseInt(btn.getAttribute('data-vol-stock')) > 0) {
                        defaultSelectedBtn = btn;
                        break;
                    }
                }
                // Fallback to first if all out of stock
                if (!defaultSelectedBtn) {
                    defaultSelectedBtn = volumeButtons[0];
                }
            }

            if (defaultSelectedBtn) {
                selectVolume(defaultSelectedBtn);
            }

            volumeButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    selectVolume(btn);
                });
            });
        }

        function selectVolume(btn) {
            volumeButtons.forEach(b => {
                b.classList.remove('active', 'btn-dark', 'text-white');
                b.classList.add('btn-outline-dark');
            });

            btn.classList.remove('btn-outline-dark');
            btn.classList.add('active', 'btn-dark', 'text-white');

            const volId = btn.getAttribute('data-vol-id');
            const volPrice = parseFloat(btn.getAttribute('data-vol-price'));
            const volSalePrice = btn.getAttribute('data-vol-sale-price') ? parseFloat(btn.getAttribute('data-vol-sale-price')) : null;
            const volStock = parseInt(btn.getAttribute('data-vol-stock'));
            const volCover = btn.getAttribute('data-vol-cover');

            // Update form and Ajax button
            if (bookIdInput) bookIdInput.value = volId;
            if (addToCartBtn) addToCartBtn.setAttribute('data-book-id', volId);
            if (qtyInput) {
                qtyInput.id = 'quantity-' + volId;
                qtyInput.max = volStock;
                qtyInput.value = Math.min(parseInt(qtyInput.value) || 1, volStock);
            }

            // Update Cover image
            const mainImg = document.querySelector('.book-img');
            if (mainImg && volCover) {
                mainImg.src = volCover;
            }

            // Update Price
            const priceWrapper = document.getElementById('price-wrapper');
            if (priceWrapper) {
                let html = '';
                if (volSalePrice) {
                    html = `<span class="text-orange fw-bold fs-2">${formatPrice(volSalePrice)} ₫</span>` +
                           `<span class="text-muted text-decoration-line-through fs-5">${formatPrice(volPrice)} ₫</span>`;
                } else {
                    html = `<span class="text-orange fw-bold fs-2">${formatPrice(volPrice)} ₫</span>`;
                }
                priceWrapper.innerHTML = html;
            }

            // Update Stock badge
            const stockWrapper = document.getElementById('stock-wrapper');
            if (stockWrapper) {
                if (volStock > 0) {
                    stockWrapper.innerHTML = `<span class="badge bg-success py-1.5 px-2.5">Còn hàng (${volStock} cuốn)</span>`;
                    if (addToCartBtn) addToCartBtn.disabled = false;
                    if (btnBuyNow) btnBuyNow.disabled = false;
                } else {
                    stockWrapper.innerHTML = `<span class="badge bg-danger py-1.5 px-2.5">Hết hàng</span>`;
                    if (addToCartBtn) addToCartBtn.disabled = true;
                    if (btnBuyNow) btnBuyNow.disabled = true;
                    if (qtyInput) qtyInput.value = 0;
                }
            }
        }

        function formatPrice(val) {
            return new Intl.NumberFormat('vi-VN').format(Math.round(val));
        }

        if (btnBuyNow) {
            btnBuyNow.addEventListener('click', function() {
                const activeBookId = bookIdInput ? bookIdInput.value : '{{ $book->id }}';
                const data = {
                    book_id: activeBookId,
                    quantity: qtyInput.value
                };

                fetch('{{ route('cart.add') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json().then(data => ({ status: response.status, body: data })))
                .then(res => {
                    if (res.status === 200 && res.body.success) {
                        window.location.href = '{{ route('checkout.index') }}';
                    } else {
                        showToast(res.body.message || 'Có lỗi xảy ra, vui lòng thử lại.', true);
                    }
                })
                .catch(error => {
                    console.error('Error in buy now:', error);
                    showToast('Không thể kết nối đến máy chủ.', true);
                });
            });
        }

        // Interactive star rating select
        const stars = document.querySelectorAll('.star-select');
        const ratingInput = document.getElementById('rating-input');
        
        if (stars.length > 0 && ratingInput) {
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const val = parseInt(this.getAttribute('data-value'));
                    ratingInput.value = val;
                    
                    // Highlight stars up to clicked value
                    stars.forEach(s => {
                        const sVal = parseInt(s.getAttribute('data-value'));
                        if (sVal <= val) {
                            s.classList.remove('text-secondary');
                            s.classList.add('text-warning');
                            s.classList.remove('bi-star');
                            s.classList.add('bi-star-fill');
                        } else {
                            s.classList.remove('text-warning');
                            s.classList.add('text-secondary');
                            s.classList.remove('bi-star-fill');
                            s.classList.add('bi-star');
                        }
                    });
                });
            });
        }
    });
</script>
@endsection
