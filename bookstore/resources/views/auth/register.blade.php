@extends('layouts.main')

@section('title', 'Đăng ký tài khoản - Arsha Bookstore')

@section('content')
<div class="row justify-content-center my-5">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-4" style="border-radius: 12px;">
            <div class="text-center mb-4">
                <i class="bi bi-person-plus-fill text-orange fs-1"></i>
                <h4 class="fw-bold mt-2">Đăng Ký Tài Khoản</h4>
                <p class="text-muted small">Tạo tài khoản mới để mua sắm và theo dõi đơn hàng dễ dàng</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Họ và tên</label>
                    <input id="name" type="text" name="name" class="form-control py-2.5 @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus placeholder="Nhập họ và tên..." style="border-radius: 8px;">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email Address -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Địa chỉ Email</label>
                    <input id="email" type="email" name="email" class="form-control py-2.5 @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="name@example.com" style="border-radius: 8px;">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Mật khẩu</label>
                    <input id="password" type="password" name="password" class="form-control py-2.5 @error('password') is-invalid @enderror" required placeholder="Nhập mật khẩu (tối thiểu 8 ký tự)..." style="border-radius: 8px;">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mb-4">
                    <label for="password_confirmation" class="form-label fw-semibold">Xác nhận mật khẩu</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control py-2.5 @error('password_confirmation') is-invalid @enderror" required placeholder="Nhập lại mật khẩu..." style="border-radius: 8px;">
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-orange w-100 py-2.5 fw-bold fs-6 mb-3" style="border-radius: 8px;">
                    Đăng ký tài khoản
                </button>

                <div class="text-center">
                    <span class="text-muted small">Đã có tài khoản? <a href="{{ route('login') }}" class="text-orange fw-bold text-decoration-none">Đăng nhập ngay</a></span>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
