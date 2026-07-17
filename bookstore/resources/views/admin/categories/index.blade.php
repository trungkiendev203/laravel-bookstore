@extends('admin.layout')

@section('title', 'Quản lý thể loại - Bookstore')
@section('page_title', 'Danh Sách Thể Loại')

@section('admin_content')
<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold m-0">Danh sách các thể loại truyện</h5>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm px-3">
            <i class="bi bi-plus-lg me-1"></i> Thêm Thể Loại
        </a>
    </div>

    @if($categories->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-tag text-muted" style="font-size: 4rem;"></i>
            <h5 class="mt-3">Chưa có thể loại nào</h5>
            <p class="text-muted">Bấm Thêm Thể Loại ở trên để bắt đầu.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr class="text-muted small">
                        <th style="width: 10%;">ID</th>
                        <th style="width: 40%;">Tên thể loại</th>
                        <th style="width: 20%;">Slug</th>
                        <th class="text-center" style="width: 15%;">Số lượng truyện</th>
                        <th class="text-center" style="width: 15%;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $cat)
                        <tr>
                            <td>{{ $cat->id }}</td>
                            <td class="fw-bold text-dark">{{ $cat->name }}</td>
                            <td>{{ $cat->slug }}</td>
                            <td class="text-center fw-semibold text-primary">{{ $cat->books_count }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.categories.edit', $cat->id) }}" class="btn btn-sm btn-outline-primary py-1 px-2">
                                        <i class="bi bi-pencil-square"></i> Sửa
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thể loại này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2">
                                            <i class="bi bi-trash3"></i> Xóa
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
            {{ $categories->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
