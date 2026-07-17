@extends('layouts.main')

@section('title', 'Tra cứu đơn hàng - Arsha Bookstore')

@section('content')
<div class="row justify-content-center my-5">
    <div class="col-md-6 col-lg-5">
        <div class="card border-0 shadow-sm p-4">
            <div class="text-center mb-4">
                <i class="bi bi-search text-orange" style="font-size: 3rem;"></i>
                <h4 class="fw-bold mt-3">Tra Cứu Đơn Hàng</h4>
                <p class="text-muted small">Dành cho khách hàng mua hàng không cần tài khoản</p>
            </div>

            <form action="{{ route('orders.track') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="order_code" class="form-label fw-semibold">Mã đơn hàng <span class="text-danger">*</span></label>
                    <input type="text" name="order_code" id="order_code" class="form-control py-2.5 @error('order_code') is-invalid @enderror" placeholder="Ví dụ: ORD-20260716-101" value="{{ old('order_code') }}">
                    @error('order_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="customer_phone" class="form-label fw-semibold">Số điện thoại mua hàng <span class="text-danger">*</span></label>
                    <input type="text" name="customer_phone" id="customer_phone" class="form-control py-2.5 @error('customer_phone') is-invalid @enderror" placeholder="Số điện thoại nhận hàng..." value="{{ old('customer_phone') }}">
                    @error('customer_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-orange w-100 py-3 fw-bold fs-6">
                    <i class="bi bi-search me-1"></i> Tra cứu ngay
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
