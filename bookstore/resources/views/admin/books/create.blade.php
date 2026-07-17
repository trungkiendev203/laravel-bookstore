@extends('admin.layout')

@section('title', 'Thêm truyện mới - Bookstore')
@section('page_title', 'Thêm Truyện Mới')

@section('admin_content')
<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-8">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold mb-4 pb-2 border-bottom">Thông tin truyện</h5>
            
            <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <!-- Title -->
                    <div class="col-md-6 mb-3">
                        <label for="title" class="form-label fw-semibold">Tên truyện <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" placeholder="Nhập tên truyện..." value="{{ old('title') }}">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Author -->
                    <div class="col-md-6 mb-3">
                        <label for="author" class="form-label fw-semibold">Tác giả</label>
                        <input type="text" name="author" id="author" class="form-control" placeholder="Nhập tên tác giả..." value="{{ old('author') }}">
                    </div>
                </div>

                <div class="row">
                    <!-- Category -->
                    <div class="col-md-6 mb-3">
                        <label for="category_id" class="form-label fw-semibold">Thể loại <span class="text-danger">*</span></label>
                        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
                            <option value="">-- Chọn thể loại --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Stock -->
                    <div class="col-md-6 mb-3">
                        <label for="stock" class="form-label fw-semibold">Số lượng trong kho <span class="text-danger">*</span></label>
                        <input type="number" name="stock" id="stock" class="form-control @error('stock') is-invalid @enderror" placeholder="Ví dụ: 100..." value="{{ old('stock', 10) }}" min="0">
                        @error('stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <!-- Price -->
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label fw-semibold">Giá niêm yết (₫) <span class="text-danger">*</span></label>
                        <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror" placeholder="Ví dụ: 55000..." value="{{ old('price') }}" min="0">
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Sale Price -->
                    <div class="col-md-6 mb-3">
                        <label for="sale_price" class="form-label fw-semibold">Giá khuyến mãi (₫) (Nếu có)</label>
                        <input type="number" name="sale_price" id="sale_price" class="form-control @error('sale_price') is-invalid @enderror" placeholder="Để trống nếu không có..." value="{{ old('sale_price') }}" min="0">
                        @error('sale_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Cover Image -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="cover_image" class="form-label fw-semibold">Ảnh bìa truyện (Tải tệp lên)</label>
                        <input type="file" name="cover_image" id="cover_image" class="form-control @error('cover_image') is-invalid @enderror" onchange="previewImage(event)">
                        <span class="text-muted small d-block mb-2">Định dạng hỗ trợ: JPG, JPEG, PNG, WEBP. Kích thước tối đa 2MB.</span>
                        @error('cover_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="cover_image_url" class="form-label fw-semibold">Hoặc Đường dẫn ảnh (URL)</label>
                        <input type="text" name="cover_image_url" id="cover_image_url" class="form-control @error('cover_image_url') is-invalid @enderror" placeholder="Ví dụ: https://example.com/image.jpg" value="{{ old('cover_image_url') }}" oninput="previewImageUrl(this.value)">
                        <span class="text-muted small d-block mb-2">Nhập link trực tiếp từ Internet nếu không tải tệp lên.</span>
                        @error('cover_image_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 mt-3">
                        <div id="image-preview-wrapper" class="d-none">
                            <span class="text-muted small d-block mb-1">Xem trước ảnh:</span>
                            <img id="image-preview" src="#" alt="Preview" style="max-height: 200px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label for="description" class="form-label fw-semibold">Mô tả chi tiết truyện</label>
                    <textarea name="description" id="description" class="form-control" rows="5" placeholder="Nhập tóm tắt nội dung, thông tin truyện...">{{ old('description') }}</textarea>
                </div>

                <!-- Status Checkbox -->
                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" checked>
                        <label class="form-check-label fw-semibold" for="is_active">Hoạt động (Hiển thị ra ngoài cửa hàng)</label>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.books.index') }}" class="btn btn-secondary py-2 px-4">Hủy</a>
                    <button type="submit" class="btn btn-primary py-2 px-4">Lưu truyện mới</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const output = document.getElementById('image-preview');
        output.src = reader.result;
        document.getElementById('image-preview-wrapper').classList.remove('d-none');
    };
    if(event.target.files[0]) {
        document.getElementById('cover_image_url').value = '';
        reader.readAsDataURL(event.target.files[0]);
    }
}

function previewImageUrl(url) {
    const output = document.getElementById('image-preview');
    if (url.trim() !== '') {
        output.src = url;
        document.getElementById('image-preview-wrapper').classList.remove('d-none');
        document.getElementById('cover_image').value = '';
    } else {
        document.getElementById('image-preview-wrapper').classList.add('d-none');
    }
}
</script>
@endsection
