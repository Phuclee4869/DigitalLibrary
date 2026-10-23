@extends('layouts.admin')

@section('title', 'Thêm Độc giả mới')

@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Thêm Độc giả mới</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
        <li class="breadcrumb-item"><a href="{{ route('doc-gia.index') }}">Độc giả</a></li>
        <li class="breadcrumb-item active">Thêm mới</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-plus mr-1"></i>
            Thông tin độc giả
        </div>
        <div class="card-body">
            <form action="{{ route('doc-gia.store') }}" method="POST">
                @csrf
                
                <div class="form-group mb-3">
                    <label for="ho_ten">Họ tên <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('ho_ten') is-invalid @enderror" id="ho_ten" name="ho_ten" value="{{ old('ho_ten') }}" required>
                    @error('ho_ten')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="ngay_sinh">Ngày sinh</label>
                            <input type="date" class="form-control @error('ngay_sinh') is-invalid @enderror" id="ngay_sinh" name="ngay_sinh" value="{{ old('ngay_sinh') }}">
                            @error('ngay_sinh')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="ngay_dang_ky">Ngày đăng ký <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('ngay_dang_ky') is-invalid @enderror" id="ngay_dang_ky" name="ngay_dang_ky" value="{{ old('ngay_dang_ky', date('Y-m-d')) }}" required>
                            @error('ngay_dang_ky')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="email">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="so_dien_thoai">Số điện thoại</label>
                            <input type="text" class="form-control @error('so_dien_thoai') is-invalid @enderror" id="so_dien_thoai" name="so_dien_thoai" value="{{ old('so_dien_thoai') }}">
                            @error('so_dien_thoai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="dia_chi">Địa chỉ</label>
                            <input type="text" class="form-control @error('dia_chi') is-invalid @enderror" id="dia_chi" name="dia_chi" value="{{ old('dia_chi') }}">
                            @error('dia_chi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="ngay_het_han">Ngày hết hạn</label>
                            <input type="date" class="form-control @error('ngay_het_han') is-invalid @enderror" id="ngay_het_han" name="ngay_het_han" value="{{ old('ngay_het_han', date('Y-m-d', strtotime('+1 year'))) }}">
                            @error('ngay_het_han')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="trang_thai" name="trang_thai" value="1" {{ old('trang_thai', '1') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="trang_thai">
                            Đang hoạt động
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Thêm độc giả</button>
                <a href="{{ route('doc-gia.index') }}" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>
@endsection