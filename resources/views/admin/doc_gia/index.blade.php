@extends('layouts.admin')

@section('title', 'Quản lý Độc giả')

@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Quản lý Độc giả</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
        <li class="breadcrumb-item active">Độc giả</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-table mr-1"></i> Danh sách độc giả</div>
            <a href="{{ route('doc-gia.create') }}" class="btn btn-primary">Thêm độc giả</a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Ngày đăng ký</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($docGias as $docGia)
                        <tr>
                            <td>{{ $docGia->id }}</td>
                            <td>{{ $docGia->ho_ten }}</td>
                            <td>{{ $docGia->email }}</td>
                            <td>{{ $docGia->so_dien_thoai }}</td>
                            <td>{{ $docGia->ngay_dang_ky }}</td>
                            <td>
                                @if($docGia->trang_thai)
                                    <span class="badge bg-success">Đang hoạt động</span>
                                @else
                                    <span class="badge bg-danger">Đã khóa</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('doc-gia.edit', $docGia->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                                <form action="{{ route('doc-gia.destroy', $docGia->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa độc giả này?')">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $docGias->links() }}
            </div>
        </div>
    </div>
</div>
@endsection