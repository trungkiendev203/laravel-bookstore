@extends('layouts.main')

@section('title', 'Đăng nhập - Arsha Bookstore')

@section('content')
<div class="row justify-content-center my-5">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm p-4" style="border-radius: 12px;">
            <div class="text-center mb-4">
                <i class="bi bi-person-circle text-orange fs-1"></i>
                <h4 class="fw-bold mt-2">Đăng Nhập Tài Khoản</h4>
                <p class="text-muted small">Chào mừng bạn quay trở lại với Arsha Shop</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="alert alert-success border-0 small py-2 px-3 mb-3">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Địa chỉ Email</label>
                    <input id="email" type="email" name="email" class="form-control py-2.5 @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus placeholder="name@example.com" style="border-radius: 8px;">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label for="password" class="form-label fw-semibold mb-0">Mật khẩu</label>
                        @if (Route::has('password.request'))
                            <a class="text-orange small text-decoration-none" href="{{ route('password.request') }}">
                                Quên mật khẩu?
                            </a>
                        @endif
                    </div>
                    <input id="password" type="password" name="password" class="form-control py-2.5 mt-2 @error('password') is-invalid @enderror" required placeholder="Nhập mật khẩu..." style="border-radius: 8px;">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="mb-3 form-check">
                    <input id="remember_me" type="checkbox" name="remember" class="form-check-input">
                    <label for="remember_me" class="form-check-label text-muted small">Ghi nhớ đăng nhập</label>
                </div>

                <button type="submit" class="btn btn-orange w-100 py-2.5 fw-bold fs-6 mb-3" style="border-radius: 8px;">
                    Đăng nhập
                </button>

                <div class="text-center">
                    <span class="text-muted small">Chưa có tài khoản? <a href="{{ route('register') }}" class="text-orange fw-bold text-decoration-none">Đăng ký ngay</a></span>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
