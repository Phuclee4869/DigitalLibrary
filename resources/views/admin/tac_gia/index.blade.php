@extends('layouts.admin')

@section('title', 'Quản lý Tác giả')

@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Quản lý Tác giả</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
        <li class="breadcrumb-item active">Tác giả</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-table mr-1"></i> Danh sách tác giả</div>
            <a href="{{ route('tac-gia.create') }}" class="btn btn-primary">Thêm tác giả</a>
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
                            <th>Tên tác giả</th>
                            <th>Tiểu sử</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tacGias as $tacGia)
                        <tr>
                            <td>{{ $tacGia->id }}</td>
                            <td>{{ $tacGia->ten_tac_gia }}</td>
                            <td>{{ Str::limit($tacGia->tieu_su, 50) }}</td>
                            <td>
                                <a href="{{ route('tac-gia.edit', $tacGia->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                                <form action="{{ route('tac-gia.destroy', $tacGia->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa tác giả này?')">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection