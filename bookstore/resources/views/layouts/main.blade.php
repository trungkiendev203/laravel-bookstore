<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Datle Bookstore')</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8f9fa;
            color: #212529;
        }
        .bg-orange {
            background-color: #ee4d2d !important;
        }
        .text-orange {
            color: #ee4d2d !important;
        }
        .btn-orange {
            background-color: #ee4d2d;
            color: white;
            border: none;
        }
        .btn-orange:hover {
            background-color: #d73c1f;
            color: white;
        }
        .btn-outline-orange {
            border: 1px solid #ee4d2d;
            color: #ee4d2d;
            background: transparent;
        }
        .btn-outline-orange:hover {
            background-color: #ee4d2d;
            color: white;
        }
        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 1020;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .book-card {
            border: none;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background: white;
        }
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }
        .book-img-wrapper {
            position: relative;
            padding-top: 133.33%; /* Tỷ lệ 3:4 */
            overflow: hidden;
            background-color: #f1f1f1;
        }
        .book-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .badge-discount {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #ee4d2d;
            color: white;
            padding: 3px 8px;
            font-size: 0.8rem;
            font-weight: 600;
            border-radius: 4px;
            z-index: 2;
        }
        .badge-outofstock {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 6px 15px;
            border-radius: 4px;
            font-size: 0.9rem;
            font-weight: 500;
            z-index: 2;
            width: 80%;
            text-align: center;
        }
        /* Toast style */
        .toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1055;
        }
    </style>
</head>
<body>

    <!-- Header / Navbar -->
    <header class="sticky-header py-2">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light p-0">
                <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                    <i class="bi bi-book-half text-orange fs-2 me-2"></i>
                    <span class="fw-bold fs-4 text-orange">Datle Shop</span>
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse ms-lg-4" id="navbarSupportedContent">
                    <!-- Search Bar -->
                    <form action="{{ route('home') }}" method="GET" class="d-flex flex-grow-1 mx-lg-4 my-2 my-lg-0">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control border-end-0" placeholder="Tìm kiếm truyện hoặc tác giả..." value="{{ request('search') }}">
                            @if(request('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif
                            @if(request('sort'))
                                <input type="hidden" name="sort" value="{{ request('sort') }}">
                            @endif
                            <button class="btn btn-orange px-4" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Links & Actions -->
                    <ul class="navbar-nav align-items-center ms-auto">
                        <!-- Category Dropdown -->
                        <li class="nav-item dropdown me-3">
                            <a class="nav-link dropdown-toggle fw-semibold" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-grid-fill me-1 text-orange"></i> Thể Loại
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-menu-item dropdown-item py-2 {{ !request('category') ? 'text-orange fw-bold' : '' }}" href="{{ route('home') }}">Tất cả thể loại</a></li>
                                @foreach(\App\Models\Category::all() as $cat)
                                    <li><a class="dropdown-menu-item dropdown-item py-2 {{ request('category') == $cat->slug ? 'text-orange fw-bold' : '' }}" href="{{ route('home', array_merge(request()->except('category', 'page'), ['category' => $cat->slug])) }}">{{ $cat->name }}</a></li>
                                @endforeach
                            </ul>
                        </li>

                        <!-- Order Track -->
                        <li class="nav-item me-3">
                            <a class="nav-link fw-semibold" href="{{ route('orders.track.form') }}">
                                <i class="bi bi-geo-alt me-1 text-orange"></i> Tra cứu đơn
                            </a>
                        </li>

                        <!-- Cart Widget -->
                        <li class="nav-item me-4 position-relative">
                            <a href="{{ route('cart.index') }}" class="btn btn-light rounded-circle p-2 position-relative" style="width: 42px; height: 42px;">
                                <i class="bi bi-cart3 fs-5 text-dark"></i>
                                @php
                                    $cart = session()->get('cart', []);
                                    $cartCount = array_sum(array_column($cart, 'quantity'));
                                @endphp
                                <span id="cart-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-orange text-white" style="font-size: 0.75rem;">
                                    {{ $cartCount }}
                                </span>
                            </a>
                        </li>

                        <!-- User Profile / Auth -->
                        @auth
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle fw-bold d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-person-circle fs-5 me-1 text-orange"></i> {{ auth()->user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Hồ sơ cá nhân</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('orders.my') }}"><i class="bi bi-bag-check me-2"></i>Lịch sử mua hàng</a></li>
                                    @if(auth()->user()->role === 'admin')
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item py-2 text-danger fw-semibold" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Trang quản trị</a></li>
                                    @endif
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item py-2 text-dark"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="btn btn-outline-orange px-3 me-2" href="{{ route('login') }}">Đăng nhập</a>
                            </li>
                            <li class="nav-item">
                                <a class="btn btn-orange px-3" href="{{ route('register') }}">Đăng ký</a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="py-4">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-top py-5 mt-5">
        <div class="container">
            <div class="row g-4 text-start">
                <div class="col-lg-4 col-md-6">
                    <a class="d-flex align-items-center text-decoration-none mb-3" href="{{ route('home') }}">
                        <i class="bi bi-book-half text-orange fs-2 me-2"></i>
                        <span class="fw-bold fs-4 text-orange">Datle Shop</span>
                    </a>
                    <p class="text-muted small">Cửa hàng truyện tranh hàng đầu Việt Nam. Cung cấp hàng ngàn đầu sách hot với chất lượng tuyệt hảo và dịch vụ giao hàng nhanh chóng.</p>
                    <p class="text-muted small mb-1"><i class="bi bi-geo-alt-fill text-orange me-2"></i> 123 Đường Láng, Đống Đa, Hà Nội</p>
                    <p class="text-muted small mb-1"><i class="bi bi-telephone-fill text-orange me-2"></i> 090 909 0909</p>
                    <p class="text-muted small"><i class="bi bi-envelope-fill text-orange me-2"></i> support@datleshop.vn</p>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h6 class="fw-bold text-uppercase mb-3">Danh Mục</h6>
                    <ul class="list-unstyled mb-0">
                        @foreach(\App\Models\Category::limit(5)->get() as $cat)
                            <li class="mb-2"><a href="{{ route('home', ['category' => $cat->slug]) }}" class="text-decoration-none text-muted small hover-orange">{{ $cat->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h6 class="fw-bold text-uppercase mb-3">Chính Sách & Hỗ Trợ</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><a href="#" class="text-decoration-none text-muted small hover-orange">Hướng dẫn mua hàng</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none text-muted small hover-orange">Chính sách bảo mật</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none text-muted small hover-orange">Chính sách đổi trả</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none text-muted small hover-orange">Điều khoản dịch vụ</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h6 class="fw-bold text-uppercase mb-3">Kết Nối Với Chúng Tôi</h6>
                    <div class="d-flex gap-3 mb-3">
                        <a href="#" class="btn btn-outline-orange btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="btn btn-outline-orange btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="btn btn-outline-orange btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-youtube"></i></a>
                    </div>
                    <p class="text-muted small">Đăng ký nhận bản tin khuyến mãi từ chúng tôi.</p>
                </div>
            </div>
            <hr class="my-4 text-muted">
            <div class="text-center">
                <p class="text-muted small mb-0">&copy; 2026 Datle Bookstore. Thiết kế và phát triển bởi AI Assistant. Dành riêng cho trải nghiệm mua sắm hoàn hảo.</p>
            </div>
        </div>
    </footer>

    <!-- Global Toast Container -->
    <div class="toast-container">
        <div id="liveToast" class="toast align-items-center text-white bg-orange border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toast-message">
                    Đã thêm vào giỏ hàng thành công!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    
    <!-- AJAX Cart Handler & Helper Scripts -->
    <script>
        function showToast(message, type = 'success') {
            const toastEl = document.getElementById('liveToast');
            const toastBody = document.getElementById('toast-message');
            toastBody.textContent = message;
            
            if (type === 'error' || type === 'danger' || type === true) {
                toastEl.classList.remove('bg-orange');
                toastEl.classList.add('bg-danger');
            } else {
                toastEl.classList.remove('bg-danger');
                toastEl.classList.add('bg-orange');
            }
            
            const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
            toast.show();
        }

        // AJAX Add to Cart handler
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.ajax-add-to-cart');
            if (!btn) return;
            
            e.preventDefault();
            const bookId = btn.getAttribute('data-book-id');
            const quantityInput = document.getElementById('quantity-' + bookId);
            const quantity = quantityInput ? quantityInput.value : 1;

            const data = {
                book_id: bookId,
                quantity: quantity
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
                    // Cập nhật badge
                    document.getElementById('cart-badge').textContent = res.body.cart_count;
                    showToast(res.body.message, false);
                } else {
                    showToast(res.body.message || 'Có lỗi xảy ra, vui lòng thử lại.', true);
                }
            })
            .catch(error => {
                console.error('Error adding to cart:', error);
                showToast('Không thể kết nối đến máy chủ.', true);
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
