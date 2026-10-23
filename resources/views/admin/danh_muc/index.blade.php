@extends('layouts.admin')

@section('title', 'Quản lý Danh Mục')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('danh-muc.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Thêm Danh Mục Mới
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên danh mục</th>
                        <th>Mô tả</th>
                        <th>Số lượng sách</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($danhMucs as $danhMuc)
                        <tr>
                            <td>{{ $danhMuc->id }}</td>
                            <td>{{ $danhMuc->ten_danh_muc }}</td>
                            <td>{{ Str::limit($danhMuc->mo_ta, 50) }}</td>
                            <td>{{ $danhMuc->sachs->count() }}</td>
                            <td>
                                <div class="d-flex">
                                    <a href="{{ route('danh-muc.edit', $danhMuc->id) }}" class="btn btn-sm btn-warning me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('danh-muc.destroy', $danhMuc->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $danhMucs->links() }}
    </div>
</div>
@endsection