@extends('layouts.admin')

@section('title', 'Chi tiết Tác giả')

@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Chi tiết Tác giả</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
        <li class="breadcrumb-item"><a href="{{ route('tac-gia.index') }}">Tác giả</a></li>
        <li class="breadcrumb-item active">Chi tiết</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user mr-1"></i>
            Thông tin tác giả
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 150px;">ID:</th>
                            <td>{{ $tacGia->id }}</td>
                        </tr>
                        <tr>
                            <th>Tên tác giả:</th>
                            <td>{{ $tacGia->ten_tac_gia }}</td>
                        </tr>
                        <tr>
                            <th>Ngày sinh:</th>
                            <td>{{ $tacGia->ngay_sinh ? $tacGia->ngay_sinh->format('d/m/Y') : 'Không có thông tin' }}</td>
                        </tr>
                        <tr>
                            <th>Tiểu sử:</th>
                            <td>{{ $tacGia->tieu_su ?? 'Không có thông tin' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">Thống kê</div>
                        <div class="card-body">
                            <p><strong>Số sách đã xuất bản:</strong> {{ $tacGia->sachs->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <h5 class="mt-4">Danh sách sách của tác giả</h5>
            @if($tacGia->sachs->isEmpty())
                <div class="alert alert-info">Tác giả này chưa có sách nào.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên sách</th>
                                <th>Danh mục</th>
                                <th>Năm xuất bản</th>
                                <th>Số lượng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tacGia->sachs as $sach)
                            <tr>
                                <td>{{ $sach->id }}</td>
                                <td>{{ $sach->tieu_de }}</td>
                                <td>{{ $sach->danhMuc->ten_danh_muc ?? 'Không có' }}</td>
                                <td>{{ $sach->nam_xuat_ban ?? 'Không có' }}</td>
                                <td>{{ $sach->so_luong }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            
            <div class="mt-3">
                <a href="{{ route('tac-gia.edit', $tacGia->id) }}" class="btn btn-primary">Chỉnh sửa</a>
                <a href="{{ route('tac-gia.index') }}" class="btn btn-secondary">Quay lại</a>
            </div>
        </div>
    </div>
</div>
@endsection