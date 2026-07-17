@extends('admin.layout')

@section('title', 'Quản lý truyện - Bookstore')
@section('page_title', 'Danh Sách Truyện')

@section('admin_content')
<div class="card border-0 shadow-sm p-4 mb-4">
    <!-- Top Actions & Filters -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <h5 class="fw-bold m-0">Quản lý truyện</h5>
        <a href="{{ route('admin.books.create') }}" class="btn btn-primary btn-sm px-3">
            <i class="bi bi-plus-lg me-1"></i> Thêm Truyện Mới
        </a>
    </div>

    <!-- Filters Panel -->
    <form action="{{ route('admin.books.index') }}" method="GET" class="row g-3 mb-4 p-3 bg-light rounded border">
        <!-- Search Keyword -->
        <div class="col-md-5">
            <label class="form-label small fw-semibold text-secondary">Từ khóa</label>
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Tìm tên truyện, tác giả..." value="{{ request('search') }}">
        </div>
        
        <!-- Category Filter -->
        <div class="col-md-4">
            <label class="form-label small fw-semibold text-secondary">Thể loại</label>
            <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Tất cả thể loại</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Submit & Reset Buttons -->
        <div class="col-md-3 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                <i class="bi bi-search me-1"></i> Lọc
            </button>
            @if(request('search') || request('category_id'))
                <a href="{{ route('admin.books.index') }}" class="btn btn-secondary btn-sm flex-grow-1">
                    Reset
                </a>
            @endif
        </div>
    </form>

    <!-- Books Table -->
    @if($books->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-journal-x text-muted" style="font-size: 4rem;"></i>
            <h5 class="mt-3">Không tìm thấy truyện nào</h5>
            <p class="text-muted">Thử thay đổi bộ lọc hoặc thêm truyện mới.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr class="text-muted small">
                        <th style="width: 5%;">ID</th>
                        <th style="width: 8%;">Ảnh</th>
                        <th style="width: 32%;">Tên truyện / Tác giả</th>
                        <th style="width: 15%;">Thể loại</th>
                        <th style="width: 15%;">Giá bán</th>
                        <th class="text-center" style="width: 8%;">Kho</th>
                        <th class="text-center" style="width: 7%;">Đã bán</th>
                        <th class="text-center" style="width: 10%;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($books as $b)
                        <tr>
                            <td>{{ $b->id }}</td>
                            <td>
                                <div class="rounded overflow-hidden border bg-light" style="width: 45px; height: 60px; position: relative;">
                                    @if($b->cover_image)
                                        <img src="{{ $b->cover_image_url }}" alt="{{ $b->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                            <i class="bi bi-book"></i>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-dark d-block">{{ $b->title }}</span>
                                @if($b->volumes->count() > 0)
                                    <span class="badge bg-info text-white small mt-1">{{ $b->volumes->count() }} tập</span>
                                @endif
                                <span class="text-muted small d-block">Tác giả: {{ $b->author ?? 'Chưa rõ' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border">{{ $b->category->name }}</span>
                            </td>
                            <td>
                                @if($b->sale_price)
                                    <span class="text-orange fw-bold d-block">{{ number_format($b->sale_price, 0, ',', '.') }} ₫</span>
                                    <span class="text-muted text-decoration-line-through text-xs d-block">{{ number_format($b->price, 0, ',', '.') }} ₫</span>
                                @else
                                    <span class="fw-bold text-dark">{{ number_format($b->price, 0, ',', '.') }} ₫</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($b->stock > 0)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">{{ $b->stock }}</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Hết</span>
                                @endif
                            </td>
                            <td class="text-center fw-semibold">{{ $b->sold_count }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.books.edit', $b->id) }}" class="btn btn-sm btn-outline-primary py-1 px-2">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.books.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn ẩn/xóa truyện này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2">
                                            <i class="bi bi-eye-slash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $books->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
