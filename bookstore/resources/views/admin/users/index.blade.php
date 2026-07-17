@extends('admin.layout')

@section('title', 'Quản lý người dùng - Bookstore')
@section('page_title', 'Danh Sách Thành Viên')

@section('admin_content')
<div class="card border-0 shadow-sm p-4">
    <h5 class="fw-bold mb-4">Danh sách thành viên đăng ký hệ thống</h5>

    <!-- Search & Filter Panel -->
    <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3 mb-4 p-3 bg-light rounded border">
        <!-- Search Keyword -->
        <div class="col-md-5">
            <label class="form-label small fw-semibold text-secondary">Tìm kiếm</label>
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Tìm tên, email, số điện thoại..." value="{{ request('search') }}">
        </div>

        <!-- Role Filter -->
        <div class="col-md-4">
            <label class="form-label small fw-semibold text-secondary">Vai trò (Quyền hạn)</label>
            <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Tất cả vai trò</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Quản trị viên (Admin)</option>
                <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Khách hàng (Customer)</option>
            </select>
        </div>

        <!-- Submit & Reset Buttons -->
        <div class="col-md-3 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Lọc</button>
            @if(request('search') || request('role'))
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm flex-grow-1">Reset</a>
            @endif
        </div>
    </form>

    <!-- Users Table -->
    @if($users->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
            <h5 class="mt-3">Không tìm thấy người dùng nào</h5>
            <p class="text-muted">Thử thay đổi bộ lọc hoặc kiểm tra từ khóa.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr class="text-muted small">
                        <th style="width: 5%;">ID</th>
                        <th style="width: 30%;">Họ tên / Email</th>
                        <th style="width: 20%;">Số điện thoại</th>
                        <th style="width: 15%;">Vai trò</th>
                        <th style="width: 20%;">Ngày đăng ký</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                <span class="fw-bold text-dark d-block">{{ $user->name }}</span>
                                <span class="text-muted small">{{ $user->email }}</span>
                            </td>
                            <td>{{ $user->phone ?? 'Chưa cập nhật' }}</td>
                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle py-1.5 px-2.5">Quản trị (Admin)</span>
                                @else
                                    <span class="badge bg-info-subtle text-info border border-info-subtle py-1.5 px-2.5">Khách hàng</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('H:i d/m/Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
