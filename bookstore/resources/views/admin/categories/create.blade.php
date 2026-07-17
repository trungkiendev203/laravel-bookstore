@extends('admin.layout')

@section('title', 'Thêm thể loại - Bookstore')
@section('page_title', 'Thêm Thể Loại Mới')

@section('admin_content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold mb-4 pb-2 border-bottom">Thông tin thể loại</h5>
            
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="name" class="form-label fw-semibold">Tên thể loại <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control py-2.5 @error('name') is-invalid @enderror" placeholder="Nhập tên thể loại (Ví dụ: Manga, Trinh thám...)" value="{{ old('name') }}">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary py-2 px-4">Hủy</a>
                    <button type="submit" class="btn btn-primary py-2 px-4">Lưu lại</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
