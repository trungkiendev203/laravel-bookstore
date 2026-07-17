@extends('layouts.main')

@section('title', $isHomepage ? 'Cửa hàng truyện tranh - Arsha Bookstore' : 'Danh sách truyện - Arsha Bookstore')

@section('content')

@if($isHomepage)
    <style>
        .hero-banner-card {
            background-blend-mode: multiply;
            transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.4s ease;
        }
        .hero-banner-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(238, 77, 45, 0.3) !important;
        }
        .btn-hero-primary {
            background-color: #ffc107 !important;
            border-color: #ffc107 !important;
            color: #212529 !important;
            box-shadow: 0 4px 14px rgba(255, 193, 7, 0.3);
        }
        .btn-hero-primary:hover {
            background-color: #e0a800 !important;
            border-color: #d39e00 !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 193, 7, 0.5);
        }
        .btn-hero-secondary:hover {
            background-color: rgba(255, 255, 255, 0.15) !important;
            color: white !important;
            transform: translateY(-2px);
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        .feature-card {
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important;
        }
        .text-shadow {
            text-shadow: 0 3px 6px rgba(0,0,0,0.3);
        }
        .text-shadow-sm {
            text-shadow: 0 2px 4px rgba(0,0,0,0.25);
        }
    </style>

    <!-- Banner/Slider Tĩnh Đầu Trang -->
    <div class="row mb-5">
        <div class="col-lg-8 mb-4 mb-lg-0">
            <div class="p-4 p-md-5 rounded-4 text-white shadow-lg position-relative overflow-hidden h-100 d-flex flex-column justify-content-center hero-banner-card" style="border-radius: 20px !important; background-image: linear-gradient(135deg, rgba(238, 77, 45, 0.95) 0%, rgba(20, 20, 30, 0.65) 100%), url('{{ asset('hero_banner_bg.png') }}'); background-size: cover; background-position: center; min-height: 380px;">
                <div class="col-md-9 px-0 position-relative" style="z-index: 2;">
                    <span class="badge bg-white bg-opacity-20 text-white fw-bold mb-3 px-3 py-2 text-uppercase fs-7 border border-white border-opacity-25 shadow-sm" style="border-radius: 30px; backdrop-filter: blur(8px);">
                        <i class="bi bi-stars me-1 text-warning animate-pulse"></i> Chào mừng tới Arsha Shop
                    </span>
                    <h1 class="display-4 fw-extrabold tracking-tight text-shadow" style="font-family: 'Outfit', 'Inter', sans-serif; font-weight: 800; letter-spacing: -1px; text-shadow: 0 4px 15px rgba(0,0,0,0.35);">
                        Khám Phá Thế Giới <br class="d-none d-md-block"><span class="text-warning">Truyện Tranh</span>
                    </h1>
                    <p class="lead my-3 opacity-90 fs-5 text-shadow-sm" style="text-shadow: 0 2px 8px rgba(0,0,0,0.25);">
                        Tuyển tập những bộ truyện hot nhất, cập nhật liên tục mỗi ngày với mức giá cực kỳ ưu đãi.
                    </p>
                    <div class="d-flex flex-wrap gap-3 mt-4">
                        <a href="#new-books" class="btn btn-warning text-dark fw-bold px-4 py-2.5 shadow-lg btn-hero-primary" style="border-radius: 30px; transition: all 0.3s ease;">
                            <i class="bi bi-cart-fill me-1"></i> Mua Sắm Ngay
                        </a>
                        <a href="{{ route('home', ['sort' => 'best_seller']) }}" class="btn btn-outline-light fw-semibold px-4 py-2.5 btn-hero-secondary" style="border-radius: 30px; backdrop-filter: blur(4px); transition: all 0.3s ease;">
                            <i class="bi bi-fire me-1 text-warning"></i> Bán Chạy Nhất
                        </a>
                    </div>
                </div>
                <!-- Ambient glow effect decoration -->
                <div class="position-absolute rounded-circle" style="width: 250px; height: 250px; background: radial-gradient(circle, rgba(255,193,7,0.25) 0%, rgba(255,193,7,0) 70%); right: -50px; top: -50px; z-index: 1;"></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4 text-center d-flex flex-column justify-content-center align-items-center h-100 feature-card" style="border-radius: 20px !important;">
                <div class="rounded-circle bg-light p-3 mb-3 text-orange d-flex align-items-center justify-content-center shadow-sm" style="width: 70px; height: 70px;">
                    <i class="bi bi-truck fs-2"></i>
                </div>
                <h5 class="fw-bold">Giao Hàng Siêu Tốc</h5>
                <p class="text-muted small px-3">Miễn phí vận chuyển cho mọi đơn hàng từ 300.000 ₫ trên toàn quốc.</p>
                <hr class="w-75 my-3 opacity-25">
                <div class="rounded-circle bg-light p-3 mb-3 text-orange d-flex align-items-center justify-content-center shadow-sm" style="width: 70px; height: 70px;">
                    <i class="bi bi-shield-check fs-2"></i>
                </div>
                <h5 class="fw-bold">Đảm Bảo Chất Lượng</h5>
                <p class="text-muted small px-3">Cam kết truyện chính hãng, chất lượng giấy tốt nhất đến tay bạn đọc.</p>
            </div>
        </div>
    </div>

    <!-- Truyện Mới -->
    <section class="mb-5" id="new-books">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold m-0 position-relative pb-2" style="border-bottom: 3px solid #ee4d2d; font-size: 1.5rem;">
                <i class="bi bi-sparkles text-orange me-2"></i>Truyện Mới Xuất Bản
            </h3>
            <a href="{{ route('home', ['sort' => 'newest']) }}" class="text-decoration-none text-orange fw-semibold hover-underline">Xem tất cả <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row row-cols-2 row-cols-md-4 g-4">
            @foreach($newArrivals as $book)
                <div class="col">
                    @include('partials.book-card', ['book' => $book])
                </div>
            @endforeach
        </div>
    </section>

    <!-- Bán Chạy -->
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold m-0 position-relative pb-2" style="border-bottom: 3px solid #ee4d2d; font-size: 1.5rem;">
                <i class="bi bi-fire text-orange me-2"></i>Bán Chạy Nhất
            </h3>
            <a href="{{ route('home', ['sort' => 'best_seller']) }}" class="text-decoration-none text-orange fw-semibold hover-underline">Xem tất cả <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row row-cols-2 row-cols-md-4 g-4">
            @foreach($bestSellers as $book)
                <div class="col">
                    @include('partials.book-card', ['book' => $book])
                </div>
            @endforeach
        </div>
    </section>

    <!-- Theo Thể Loại -->
    @foreach($featuredCategories as $featCat)
        <section class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold m-0 position-relative pb-2" style="border-bottom: 3px solid #ee4d2d; font-size: 1.5rem;">
                    <i class="bi bi-bookmark-star text-orange me-2"></i>{{ $featCat->name }} Nổi Bật
                </h3>
                <a href="{{ route('home', ['category' => $featCat->slug]) }}" class="text-decoration-none text-orange fw-semibold hover-underline">Xem tất cả <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="row row-cols-2 row-cols-md-4 g-4">
                @foreach($featCat->books as $book)
                    <div class="col">
                        @include('partials.book-card', ['book' => $book])
                    </div>
                @endforeach
            </div>
        </section>
    @endforeach

@else
    <!-- Danh sách truyện có bộ lọc -->
    <div class="row g-4" id="books-list">
        <!-- Filter Sidebar (Cột trái) -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm p-4 sticky-lg-top" style="top: 85px; z-index: 10; border-radius: 12px;">
                <h5 class="fw-bold mb-4 d-flex align-items-center">
                    <i class="bi bi-funnel-fill text-orange me-2"></i> Bộ Lọc Tìm Kiếm
                </h5>
                
                <form action="{{ route('home') }}" method="GET">
                    <!-- Giữ lại search keyword if any -->
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif

                    <!-- Thể loại -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted small text-uppercase">Thể loại</label>
                        <div class="d-flex flex-column gap-2 mt-2">
                            <a href="{{ route('home', request()->except('category', 'page')) }}" class="btn btn-sm text-start py-2 px-3 border-0 {{ !request('category') ? 'bg-orange text-white fw-bold' : 'btn-light' }}" style="border-radius: 8px;">
                                Tất cả thể loại
                            </a>
                            @foreach($categories as $cat)
                                <a href="{{ route('home', array_merge(request()->except('category', 'page'), ['category' => $cat->slug])) }}" class="btn btn-sm text-start py-2 px-3 border-0 {{ request('category') == $cat->slug ? 'bg-orange text-white fw-bold' : 'btn-light' }}" style="border-radius: 8px;">
                                    {{ $cat->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Khoảng giá -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted small text-uppercase">Khoảng giá (₫)</label>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Từ" value="{{ request('min_price') }}">
                            <span class="text-muted">-</span>
                            <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Đến" value="{{ request('max_price') }}">
                        </div>
                        <button type="submit" class="btn btn-sm btn-orange w-100 mt-3 py-2" style="border-radius: 8px;">
                            Áp dụng khoảng giá
                        </button>
                    </div>

                    <!-- Sắp xếp -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted small text-uppercase">Sắp xếp theo</label>
                        <select name="sort" class="form-select form-select-sm mt-2" onchange="this.form.submit()" style="border-radius: 8px; padding: 8px 12px;">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                            <option value="best_seller" {{ request('sort') == 'best_seller' ? 'selected' : '' }}>Bán chạy</option>
                        </select>
                    </div>

                    <!-- Nút Clear filters -->
                    @if(request('search') || request('category') || request('sort') || request('min_price') || request('max_price'))
                        <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary w-100 py-2" style="border-radius: 8px;">
                            <i class="bi bi-x-circle me-1"></i> Xóa bộ lọc
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Books Grid (Cột phải) -->
        <div class="col-lg-9">
            <!-- Breadcrumb & Total -->
            <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-3 shadow-sm">
                <nav aria-label="breadcrumb" class="mb-0">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Trang chủ</a></li>
                        @if(request('category'))
                            @php
                                $selectedCat = $categories->firstWhere('slug', request('category'));
                            @endphp
                            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Danh mục</a></li>
                            <li class="breadcrumb-item active text-orange" aria-current="page">{{ $selectedCat ? $selectedCat->name : request('category') }}</li>
                        @else
                            <li class="breadcrumb-item active text-orange" aria-current="page">Tất cả truyện</li>
                        @endif
                    </ol>
                </nav>
                <span class="text-muted small">Tìm thấy <strong>{{ $books->total() }}</strong> kết quả</span>
            </div>

            @if($books->isEmpty())
                <div class="card border-0 shadow-sm p-5 text-center my-4" style="border-radius: 12px;">
                    <i class="bi bi-journal-x text-muted" style="font-size: 5rem;"></i>
                    <h4 class="mt-4 fw-bold">Không tìm thấy truyện nào</h4>
                    <p class="text-muted">Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm của bạn.</p>
                    <a href="{{ route('home') }}" class="btn btn-orange mx-auto px-4 mt-3" style="border-radius: 8px;">Quay lại trang chủ</a>
                </div>
            @else
                <!-- Grid -->
                <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-4 mb-4">
                    @foreach($books as $book)
                        <div class="col">
                            @include('partials.book-card', ['book' => $book])
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-5">
                    {{ $books->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endif

@endsection
