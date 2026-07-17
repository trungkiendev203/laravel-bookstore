<div class="card h-100 book-card shadow-sm border-0 position-relative">
    <a href="{{ route('books.show', $book->slug) }}" class="text-decoration-none text-dark">
        <div class="book-img-wrapper" style="position: relative; padding-top: 133.33%; overflow: hidden; background-color: #f8f9fa;">
            @if($book->sale_price && $book->sale_price < $book->price)
                @php
                    $discount = round((($book->price - $book->sale_price) / $book->price) * 100);
                @endphp
                <span class="badge-discount" style="position: absolute; top: 10px; right: 10px; background-color: #ee4d2d; color: white; padding: 4px 8px; font-size: 0.8rem; font-weight: 600; border-radius: 4px; z-index: 2;">-{{ $discount }}%</span>
            @endif

            @if(!$book->isAvailable())
                <span class="badge-outofstock" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: rgba(0, 0, 0, 0.7); color: white; padding: 6px 15px; border-radius: 4px; font-size: 0.9rem; font-weight: 500; z-index: 2; width: 80%; text-align: center;">Hết hàng</span>
            @endif

            @if($book->cover_image)
                <img src="{{ $book->cover_image_url }}" class="book-img" alt="{{ $book->title }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
            @else
                <div class="book-img d-flex align-items-center justify-content-center bg-light text-muted" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                    <i class="bi bi-book fs-1 text-secondary"></i>
                </div>
            @endif
        </div>
    </a>
    <div class="card-body p-3 d-flex flex-column">
        <span class="badge bg-light text-secondary border mb-2 align-self-start">{{ $book->category->name }}</span>
        <h6 class="card-title fw-bold text-truncate-2 mb-1" style="height: 40px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; font-size: 0.95rem; line-height: 1.3;">
            <a href="{{ route('books.show', $book->slug) }}" class="text-decoration-none text-dark hover-orange">{{ $book->title }}</a>
        </h6>
        <p class="card-text text-muted small mb-2 text-truncate">{{ $book->author ?? 'Đang cập nhật' }}</p>
        
        <div class="mt-auto">
            <div class="d-flex align-items-baseline gap-2 mb-3">
                @if($book->sale_price && $book->sale_price < $book->price)
                    <span class="text-orange fw-bold fs-5">{{ number_format($book->sale_price, 0, ',', '.') }} ₫</span>
                    <span class="text-muted text-decoration-line-through small">{{ number_format($book->price, 0, ',', '.') }} ₫</span>
                @else
                    <span class="text-orange fw-bold fs-5">{{ number_format($book->price, 0, ',', '.') }} ₫</span>
                @endif
            </div>
            
            @if($book->volumes->count() > 0)
                <a href="{{ route('books.show', $book->slug) }}" class="btn btn-sm btn-outline-orange w-100 py-2">
                    <i class="bi bi-collection me-1"></i> Chọn tập
                </a>
            @elseif($book->isAvailable())
                <button class="btn btn-sm btn-orange w-100 ajax-add-to-cart py-2" data-book-id="{{ $book->id }}">
                    <i class="bi bi-cart-plus me-1"></i> Thêm giỏ hàng
                </button>
            @else
                <button class="btn btn-sm btn-secondary w-100 py-2" disabled>
                    Hết hàng
                </button>
            @endif
        </div>
    </div>
</div>
