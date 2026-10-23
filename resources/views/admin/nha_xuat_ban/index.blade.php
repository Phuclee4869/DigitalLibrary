@extends('layouts.admin')

@section('title', 'Quản lý Nhà xuất bản')

@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Quản lý Nhà xuất bản</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
        <li class="breadcrumb-item active">Nhà xuất bản</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-table mr-1"></i> Danh sách nhà xuất bản</div>
            <a href="{{ route('nha-xuat-ban.create') }}" class="btn btn-primary">Thêm nhà xuất bản</a>
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
                            <th>Tên nhà xuất bản</th>
                            <th>Địa chỉ</th>
                            <th>Email</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($nhaXuatBans as $nhaXuatBan)
                        <tr>
                            <td>{{ $nhaXuatBan->id }}</td>
                            <td>{{ $nhaXuatBan->ten_nxb }}</td>
                            <td>{{ $nhaXuatBan->dia_chi }}</td>
                            <td>{{ $nhaXuatBan->email }}</td>
                            <td>
                                <a href="{{ route('nha-xuat-ban.edit', $nhaXuatBan->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                                <form action="{{ route('nha-xuat-ban.destroy', $nhaXuatBan->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa nhà xuất bản này?')">
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
                {{ $nhaXuatBans->links() }}
            </div>
        </div>
    </div>
</div>
@endsection