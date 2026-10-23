@extends('layouts.admin')

@section('title', 'Thêm Nhà xuất bản mới')

@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Thêm Nhà xuất bản mới</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
        <li class="breadcrumb-item"><a href="{{ route('nha-xuat-ban.index') }}">Nhà xuất bản</a></li>
        <li class="breadcrumb-item active">Thêm mới</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-building mr-1"></i>
            Thông tin nhà xuất bản
        </div>
        <div class="card-body">
            <form action="{{ route('nha-xuat-ban.store') }}" method="POST">
                @csrf
                
                <div class="form-group mb-3">
                    <label for="ten_nxb">Tên nhà xuất bản <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('ten_nxb') is-invalid @enderror" id="ten_nxb" name="ten_nxb" value="{{ old('ten_nxb') }}" required>
                    @error('ten_nxb')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group mb-3">
                    <label for="dia_chi">Địa chỉ</label>
                    <input type="text" class="form-control @error('dia_chi') is-invalid @enderror" id="dia_chi" name="dia_chi" value="{{ old('dia_chi') }}">
                    @error('dia_chi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group mb-3">
                    <label for="email">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group mb-3">
                    <label for="so_dien_thoai">Số điện thoại</label>
                    <input type="text" class="form-control @error('so_dien_thoai') is-invalid @enderror" id="so_dien_thoai" name="so_dien_thoai" value="{{ old('so_dien_thoai') }}">
                    @error('so_dien_thoai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <button type="submit" class="btn btn-primary">Thêm nhà xuất bản</button>
                <a href="{{ route('nha-xuat-ban.index') }}" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>
@endsection