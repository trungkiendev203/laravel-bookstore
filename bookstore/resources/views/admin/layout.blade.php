<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard - Arsha')</title>
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
            background-color: #f4f6f9;
            color: #333;
        }
        .admin-sidebar {
            width: 260px;
            background: white;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            box-shadow: 2px 0 15px rgba(0,0,0,0.05);
            padding-top: 1.5rem;
        }
        .admin-content {
            margin-left: 260px;
            padding: 2rem;
            min-height: 100vh;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.8rem 1.5rem;
            color: #555;
            text-decoration: none;
            font-weight: 500;
            border-left: 4px solid transparent;
            transition: all 0.2s ease;
        }
        .sidebar-link:hover {
            background-color: #f8f9fa;
            color: #0d6efd;
            border-left-color: #0d6efd;
        }
        .sidebar-link.active {
            background-color: #eef5ff;
            color: #0d6efd;
            border-left-color: #0d6efd;
            font-weight: 600;
        }
        .sidebar-link i {
            font-size: 1.2rem;
            margin-right: 0.75rem;
        }
        .navbar-admin {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            padding: 1rem 2rem;
            margin-bottom: 2rem;
            border-radius: 8px;
        }
        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            background: white;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
        }
        @media (max-width: 991.98px) {
            .admin-sidebar {
                width: 70px;
            }
            .sidebar-link span {
                display: none;
            }
            .sidebar-link i {
                margin-right: 0;
            }
            .admin-content {
                margin-left: 70px;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <aside class="admin-sidebar d-flex flex-column justify-content-between">
        <div>
            <div class="px-4 mb-4 text-center">
                <a href="{{ route('home') }}" class="d-flex align-items-center justify-content-center text-decoration-none">
                    <i class="bi bi-book-half text-primary fs-3 me-2"></i>
                    <span class="fw-bold fs-5 text-dark">Arsha Admin</span>
                </a>
            </div>
            
            <nav class="d-flex flex-column gap-1">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="bi bi-grid"></i>
                    <span>Thể loại</span>
                </a>
                <a href="{{ route('admin.books.index') }}" class="sidebar-link {{ request()->routeIs('admin.books.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i>
                    <span>Truyện</span>
                </a>
                <a href="{{ route('admin.orders.index') }}" class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="bi bi-bag-check"></i>
                    <span>Đơn hàng</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Người dùng</span>
                </a>
            </nav>
        </div>

        <div class="pb-4">
            <a href="{{ route('home') }}" class="sidebar-link text-danger border-0">
                <i class="bi bi-box-arrow-left"></i>
                <span>Xem Cửa Hàng</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="admin-content">
        <!-- Top header -->
        <div class="navbar-admin d-flex justify-content-between align-items-center">
            <h4 class="fw-bold m-0">@yield('page_title', 'Dashboard')</h4>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle fw-bold" type="button" id="adminUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-badge text-primary me-1"></i> Admin: {{ auth()->user()->name }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow" aria-labelledby="adminUserDropdown">
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Hồ sơ</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

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

        <!-- Yield main panel -->
        @yield('admin_content')
    </main>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
