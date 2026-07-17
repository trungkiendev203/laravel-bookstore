@extends('admin.layout')

@section('title', 'Sửa truyện - Bookstore')
@section('page_title', 'Sửa Thông Tin Truyện')

@section('admin_content')
<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-8">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold mb-4 pb-2 border-bottom">Thông tin truyện</h5>
            
            <form action="{{ route('admin.books.update', $book->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Title -->
                    <div class="col-md-6 mb-3">
                        <label for="title" class="form-label fw-semibold">Tên truyện <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" placeholder="Nhập tên truyện..." value="{{ old('title', $book->title) }}">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Author -->
                    <div class="col-md-6 mb-3">
                        <label for="author" class="form-label fw-semibold">Tác giả</label>
                        <input type="text" name="author" id="author" class="form-control" placeholder="Nhập tên tác giả..." value="{{ old('author', $book->author) }}">
                    </div>
                </div>

                <div class="row">
                    <!-- Category -->
                    <div class="col-md-6 mb-3">
                        <label for="category_id" class="form-label fw-semibold">Thể loại <span class="text-danger">*</span></label>
                        @if($book->parent_id)
                            <select class="form-select" disabled>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $book->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="category_id" value="{{ $book->category_id }}">
                            <span class="text-muted small mt-1 d-block"><i class="bi bi-info-circle me-1"></i>Tập con tự động kế thừa thể loại của truyện cha.</span>
                        @else
                            <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                <option value="">-- Chọn thể loại --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $book->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        @endif
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Stock -->
                    <div class="col-md-6 mb-3">
                        <label for="stock" class="form-label fw-semibold">Số lượng trong kho <span class="text-danger">*</span></label>
                        <input type="number" name="stock" id="stock" class="form-control @error('stock') is-invalid @enderror" placeholder="Ví dụ: 100..." value="{{ old('stock', $book->stock) }}" min="0">
                        @error('stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <!-- Price -->
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label fw-semibold">Giá niêm yết (₫) <span class="text-danger">*</span></label>
                        <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror" placeholder="Ví dụ: 55000..." value="{{ old('price', (int)$book->price) }}" min="0">
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Sale Price -->
                    <div class="col-md-6 mb-3">
                        <label for="sale_price" class="form-label fw-semibold">Giá khuyến mãi (₫) (Nếu có)</label>
                        <input type="number" name="sale_price" id="sale_price" class="form-control @error('sale_price') is-invalid @enderror" placeholder="Để trống nếu không có..." value="{{ old('sale_price', $book->sale_price ? (int)$book->sale_price : '') }}" min="0">
                        @error('sale_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Cover Image -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="cover_image" class="form-label fw-semibold">Ảnh bìa truyện (Tải tệp mới lên)</label>
                        <input type="file" name="cover_image" id="cover_image" class="form-control @error('cover_image') is-invalid @enderror mb-2" onchange="previewImage(event)">
                        <span class="text-muted small d-block">Định dạng hỗ trợ: JPG, JPEG, PNG, WEBP. Kích thước tối đa 2MB.</span>
                        @error('cover_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="cover_image_url" class="form-label fw-semibold">Hoặc Đường dẫn ảnh mới (URL)</label>
                        @php
                            $currentUrlVal = ($book->cover_image && (str_starts_with($book->cover_image, 'http://') || str_starts_with($book->cover_image, 'https://'))) ? $book->cover_image : '';
                        @endphp
                        <input type="text" name="cover_image_url" id="cover_image_url" class="form-control @error('cover_image_url') is-invalid @enderror" placeholder="Ví dụ: https://example.com/image.jpg" value="{{ old('cover_image_url', $currentUrlVal) }}" oninput="previewImageUrl(this.value)">
                        <span class="text-muted small d-block">Nhập link trực tiếp từ Internet nếu không tải tệp lên.</span>
                        @error('cover_image_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 mt-3">
                        <div id="image-preview-wrapper" class="d-none mb-3">
                            <span class="text-muted small d-block mb-1">Ảnh mới chọn để thay thế:</span>
                            <img id="image-preview" src="#" alt="Preview" style="max-height: 200px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                        </div>

                        @if($book->cover_image)
                            <div class="mb-2" id="current-image-wrapper">
                                <span class="text-muted small d-block mb-1">Ảnh hiện tại:</span>
                                <div class="rounded overflow-hidden border bg-light" style="width: 100px; height: 133px; position: relative;">
                                    <img src="{{ $book->cover_image_url }}" alt="{{ $book->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label for="description" class="form-label fw-semibold">Mô tả chi tiết truyện</label>
                    <textarea name="description" id="description" class="form-control" rows="5" placeholder="Nhập tóm tắt nội dung, thông tin truyện...">{{ old('description', $book->description) }}</textarea>
                </div>

                <!-- Status Checkbox -->
                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $book->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">Hoạt động (Hiển thị ra ngoài cửa hàng)</label>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.books.index') }}" class="btn btn-secondary py-2 px-4">Hủy</a>
                    <button type="submit" class="btn btn-primary py-2 px-4">Cập nhật truyện</button>
                </div>
            </form>
        </div>
    </div>

    @if(is_null($book->parent_id))
    <!-- Quản lý tập truyện (Volumes) -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-layers me-2 text-primary"></i>Quản lý danh sách tập truyện</h5>
        </div>
        <div class="card-body p-4">
            <div class="row">
                <!-- Form tạo nhanh tập truyện -->
                <div class="col-lg-4 border-end">
                    <h6 class="fw-bold mb-3 text-secondary">Tạo nhanh tập truyện mới</h6>
                    <form action="{{ route('admin.books.generateVolumes', $book->id) }}" method="POST">
                        @csrf
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label for="volume_start" class="form-label small fw-semibold">Tập bắt đầu</label>
                                <input type="number" name="volume_start" id="volume_start" class="form-control" value="1" min="1" required>
                            </div>
                            <div class="col-6">
                                <label for="volume_end" class="form-label small fw-semibold">Tập kết thúc</label>
                                <input type="number" name="volume_end" id="volume_end" class="form-control" value="10" min="1" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="vol_price" class="form-label small fw-semibold">Giá bán chung cho các tập (đ)</label>
                            <input type="number" name="price" id="vol_price" class="form-control" value="{{ intval($book->price) }}" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label for="vol_stock" class="form-label small fw-semibold">Số lượng tồn kho chung</label>
                            <input type="number" name="stock" id="vol_stock" class="form-control" value="20" min="0" required>
                        </div>

                        <div class="mb-4">
                            <label for="vol_cover_image_url" class="form-label small fw-semibold">Ảnh bìa riêng (URL) nếu có</label>
                            <input type="text" name="cover_image_url" id="vol_cover_image_url" class="form-control" placeholder="Để trống nếu muốn kế thừa ảnh bìa cha">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                            <i class="bi bi-plus-circle me-1"></i> Tạo nhanh danh sách tập
                        </button>
                    </form>
                </div>

                <!-- Danh sách các tập hiện tại -->
                <div class="col-lg-8 ps-lg-4 mt-4 mt-lg-0">
                    <h6 class="fw-bold mb-3 text-secondary">Danh sách tập truyện hiện tại ({{ $book->volumes->count() }} tập)</h6>
                    
                    @if($book->volumes->count() > 0)
                        <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                            <table class="table table-hover align-middle border">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th style="width: 80px;">Số tập</th>
                                        <th>Ảnh</th>
                                        <th>Tên tập</th>
                                        <th>Giá</th>
                                        <th>Kho</th>
                                        <th class="text-end">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($book->volumes as $vol)
                                        <tr>
                                            <td class="fw-bold text-primary">#{{ $vol->volume_number }}</td>
                                            <td>
                                                <div class="rounded overflow-hidden border bg-light" style="width: 35px; height: 48px; position: relative;">
                                                    <img src="{{ $vol->cover_image_url }}" alt="{{ $vol->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                </div>
                                            </td>
                                            <td>
                                                <span class="d-block fw-semibold text-dark">{{ $vol->title }}</span>
                                                <span class="text-muted small">Slug: {{ $vol->slug }}</span>
                                            </td>
                                            <td>{{ number_format($vol->price, 0, ',', '.') }} ₫</td>
                                            <td>
                                                @if($vol->stock <= 5)
                                                    <span class="badge bg-danger text-white">{{ $vol->stock }}</span>
                                                @else
                                                    <span class="badge bg-success text-white">{{ $vol->stock }}</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="d-inline-flex gap-2">
                                                    <a href="{{ route('admin.books.edit', $vol->id) }}" class="btn btn-sm btn-outline-primary" title="Sửa tập">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('admin.books.destroy', $vol->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tập {{ $vol->volume_number }}?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa tập">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center py-4">
                            <i class="bi bi-info-circle display-6 d-block mb-2 text-info"></i>
                            <span>Truyện cha này chưa được tạo tập truyện nào. Nhập dải số tập bên trái để tạo nhanh!</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
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
        const currentWrapper = document.getElementById('current-image-wrapper');
        if (currentWrapper) {
            currentWrapper.classList.add('d-none');
        }
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
        const currentWrapper = document.getElementById('current-image-wrapper');
        if (currentWrapper) {
            currentWrapper.classList.add('d-none');
        }
    } else {
        document.getElementById('image-preview-wrapper').classList.add('d-none');
        const currentWrapper = document.getElementById('current-image-wrapper');
        if (currentWrapper) {
            currentWrapper.classList.remove('d-none');
        }
    }
}
</script>
@endsection
