@extends('layouts.main')

@section('title', 'Quên mật khẩu - Arsha Bookstore')

@section('content')
<div class="row justify-content-center my-5">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm p-4" style="border-radius: 12px;">
            <div class="text-center mb-4">
                <i class="bi bi-key-fill text-orange fs-1"></i>
                <h4 class="fw-bold mt-2">Quên Mật Khẩu?</h4>
                <p class="text-muted small px-2">Nhập email của bạn và chúng tôi sẽ gửi liên kết để đặt lại mật khẩu mới.</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="alert alert-success border-0 small py-2 px-3 mb-3">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Address -->
                <div class="mb-4">
                    <label for="email" class="form-label fw-semibold">Địa chỉ Email</label>
                    <input id="email" type="email" name="email" class="form-control py-2.5 @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus placeholder="name@example.com" style="border-radius: 8px;">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-orange w-100 py-2.5 fw-bold fs-6 mb-3" style="border-radius: 8px;">
                    Gửi liên kết đặt lại mật khẩu
                </button>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-orange fw-semibold small text-decoration-none"><i class="bi bi-arrow-left me-1"></i> Quay lại Đăng nhập</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
