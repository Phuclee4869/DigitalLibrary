@extends('layouts.admin')

@section('title', 'Chỉnh sửa Tác giả')

@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Chỉnh sửa Tác giả</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
        <li class="breadcrumb-item"><a href="{{ route('tac-gia.index') }}">Tác giả</a></li>
        <li class="breadcrumb-item active">Chỉnh sửa</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-edit mr-1"></i>
            Chỉnh sửa thông tin tác giả
        </div>
        <div class="card-body">
        <form action="{{ url('/admin/tac-gia/' . $tacGia->id) }}" method="POST">

                @csrf
                @method('PUT')
                
                <div class="form-group mb-3">
                    <label for="ten_tac_gia">Tên tác giả <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('ten_tac_gia') is-invalid @enderror" id="ten_tac_gia" name="ten_tac_gia" value="{{ old('ten_tac_gia', $tacGia->ten_tac_gia) }}" required>
                    @error('ten_tac_gia')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group mb-3">
                    <label for="ngay_sinh">Ngày sinh</label>
                    <input type="date" class="form-control @error('ngay_sinh') is-invalid @enderror" id="ngay_sinh" name="ngay_sinh" value="{{ old('ngay_sinh', $tacGia->ngay_sinh ? $tacGia->ngay_sinh->format('Y-m-d') : '') }}">
                    @error('ngay_sinh')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group mb-3">
                    <label for="tieu_su">Tiểu sử</label>
                    <textarea class="form-control @error('tieu_su') is-invalid @enderror" id="tieu_su" name="tieu_su" rows="5">{{ old('tieu_su', $tacGia->tieu_su) }}</textarea>
                    @error('tieu_su')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <button type="submit" class="btn btn-primary">Cập nhật tác giả</button>
                <a href="{{ route('tac-gia.index') }}" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>
@endsection