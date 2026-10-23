@extends('layouts.admin')

@section('title', 'Chi tiết Nhà xuất bản')

@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Chi tiết Nhà xuất bản</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
        <li class="breadcrumb-item"><a href="{{ route('nha-xuat-ban.index') }}">Nhà xuất bản</a></li>
        <li class="breadcrumb-item active">Chi tiết</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-building mr-1"></i>
            Thông tin nhà xuất bản
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 150px;">ID:</th>
                            <td>{{ $nhaXuatBan->id }}</td>
                        </tr>
                        <tr>
                            <th>Tên nhà xuất bản:</th>
                            <td>{{ $nhaXuatBan->ten_nxb }}</td>
                        </tr>
                        <tr>
                            <th>Địa chỉ:</th>
                            <td>{{ $nhaXuatBan->dia_chi ?? 'Không có thông tin' }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $nhaXuatBan->email ?? 'Không có thông tin' }}</td>
                        </tr>
                        <tr>
                            <th>Số điện thoại:</th>
                            <td>{{ $nhaXuatBan->so_dien_thoai ?? 'Không có thông tin' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">Thống kê</div>
                        <div class="card-body">
                            <p><strong>Số sách đã xuất bản:</strong> {{ $nhaXuatBan->sachs->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <h5 class="mt-4">Danh sách sách của nhà xuất bản</h5>
            @if($nhaXuatBan->sachs->isEmpty())
                <div class="alert alert-info">Nhà xuất bản này chưa có sách nào.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên sách</th>
                                <th>Tác giả</th>
                                <th>Danh mục</th>
                                <th>Năm xuất bản</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($nhaXuatBan->sachs as $sach)
                            <tr>
                                <td>{{ $sach->id }}</td>
                                <td>{{ $sach->tieu_de }}</td>
                                <td>{{ $sach->tacGia->ten_tac_gia ?? 'Không có' }}</td>
                                <td>{{ $sach->danhMuc->ten_danh_muc ?? 'Không có' }}</td>
                                <td>{{ $sach->nam_xuat_ban ?? 'Không có' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            
            <div class="mt-3">
                <a href="{{ route('nha-xuat-ban.edit', $nhaXuatBan->id) }}" class="btn btn-primary">Chỉnh sửa</a>
                <a href="{{ route('nha-xuat-ban.index') }}" class="btn btn-secondary">Quay lại</a>
            </div>
        </div>
    </div>
</div>
@endsection