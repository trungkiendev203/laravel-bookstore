@extends('layouts.main')

@section('title', 'Thông tin tài khoản - Arsha Bookstore')

@section('content')
<div class="row g-4 my-4">
    <!-- Cột trái: Menu tài khoản -->
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm p-3" style="border-radius: 12px;">
            <div class="text-center py-3 border-bottom mb-3">
                <div class="rounded-circle bg-light p-3 mx-auto text-orange mb-2" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-person-fill fs-1"></i>
                </div>
                <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                <p class="text-muted small mb-0">{{ $user->email }}</p>
            </div>
            
            <div class="d-flex flex-column gap-2">
                <a href="{{ route('profile.edit') }}" class="btn btn-sm text-start py-2.5 px-3 border-0 bg-orange text-white fw-bold" style="border-radius: 8px;">
                    <i class="bi bi-person-fill-gear me-2"></i> Thông tin tài khoản
                </a>
                <a href="{{ route('orders.my') }}" class="btn btn-sm text-start py-2.5 px-3 border-0 btn-light text-dark" style="border-radius: 8px;">
                    <i class="bi bi-box-seam-fill me-2"></i> Đơn hàng của tôi
                </a>
                
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger w-100 py-2" style="border-radius: 8px;">
                        <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Cột phải: Form thông tin -->
    <div class="col-lg-9">
        @if (session('status') === 'profile-updated')
            <div class="alert alert-success border-0 py-2.5 px-3 mb-4 small" style="border-radius: 8px;">
                <i class="bi bi-check-circle-fill me-2"></i> Đã cập nhật thông tin cá nhân thành công.
            </div>
        @endif

        @if (session('status') === 'password-updated')
            <div class="alert alert-success border-0 py-2.5 px-3 mb-4 small" style="border-radius: 8px;">
                <i class="bi bi-check-circle-fill me-2"></i> Đã thay đổi mật khẩu thành công.
            </div>
        @endif

        <!-- Card 1: Thông tin cá nhân -->
        <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 12px;">
            <h5 class="fw-bold mb-4"><i class="bi bi-person-card-text text-orange me-2"></i> Thông Tin Cá Nhân</h5>
            
            <form method="post" action="{{ route('profile.update') }}">
                @csrf
                @method('patch')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold">Họ và tên</label>
                        <input id="name" name="name" type="text" class="form-control py-2.5 @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required style="border-radius: 8px;">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label fw-semibold">Địa chỉ Email</label>
                        <input id="email" name="email" type="email" class="form-control py-2.5 @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required style="border-radius: 8px;">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-semibold">Số điện thoại</label>
                        <input id="phone" name="phone" type="text" class="form-control py-2.5 @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" style="border-radius: 8px;">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="address" class="form-label fw-semibold">Địa chỉ giao nhận hàng</label>
                        <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror" rows="3" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố..." style="border-radius: 8px;">{{ old('address', $user->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-orange px-4 py-2" style="border-radius: 8px;">Lưu thay đổi</button>
                </div>
            </form>
        </div>

        <!-- Card 2: Thay đổi mật khẩu -->
        <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 12px;">
            <h5 class="fw-bold mb-4"><i class="bi bi-shield-lock text-orange me-2"></i> Đổi Mật Khẩu</h5>
            
            <form method="post" action="{{ route('password.update') }}">
                @csrf
                @method('put')

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="update_password_current_password" class="form-label fw-semibold">Mật khẩu hiện tại</label>
                        <input id="update_password_current_password" name="current_password" type="password" class="form-control py-2.5 @error('current_password', 'updatePassword') is-invalid @enderror" style="border-radius: 8px;">
                        @error('current_password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="update_password_password" class="form-label fw-semibold">Mật khẩu mới</label>
                        <input id="update_password_password" name="password" type="password" class="form-control py-2.5 @error('password', 'updatePassword') is-invalid @enderror" style="border-radius: 8px;">
                        @error('password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="update_password_password_confirmation" class="form-label fw-semibold">Xác nhận mật khẩu</label>
                        <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control py-2.5 @error('password_confirmation', 'updatePassword') is-invalid @enderror" style="border-radius: 8px;">
                        @error('password_confirmation', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-orange px-4 py-2" style="border-radius: 8px;">Cập nhật mật khẩu</button>
                </div>
            </form>
        </div>

        <!-- Card 3: Xóa tài khoản (Nguy hiểm) -->
        <div class="card border-0 shadow-sm p-4 border-start border-danger border-4" style="border-radius: 12px;">
            <h5 class="fw-bold text-danger mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i> Khu Vực Nguy Hiểm</h5>
            <p class="text-muted small mb-4">Một khi bạn xóa tài khoản, tất cả dữ liệu đơn hàng và thông tin cá nhân của bạn sẽ bị xóa vĩnh viễn.</p>
            
            <button class="btn btn-danger px-4 py-2" data-bs-toggle="modal" data-bs-target="#deleteAccountModal" style="border-radius: 8px;">Xóa tài khoản</button>
        </div>
    </div>
</div>

<!-- Modal xác nhận xóa tài khoản -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')
                
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger" id="deleteAccountModalLabel">Xóa tài khoản vĩnh viễn?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <p class="text-secondary small">Vui lòng nhập mật khẩu của bạn để xác nhận hành động xóa tài khoản vĩnh viễn.</p>
                    <div class="mb-3">
                        <label for="delete_password" class="form-label fw-semibold">Mật khẩu của bạn</label>
                        <input id="delete_password" name="password" type="password" class="form-control py-2.5 @error('password', 'userDeletion') is-invalid @enderror" required placeholder="Nhập mật khẩu..." style="border-radius: 8px;">
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius: 8px;">Hủy bỏ</button>
                    <button type="submit" class="btn btn-danger btn-sm" style="border-radius: 8px;">Xác nhận xóa tài khoản</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
