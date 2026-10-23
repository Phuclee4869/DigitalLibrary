@extends('layouts.admin')

@section('title', 'Chỉnh sửa phiếu mượn')

@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Chỉnh sửa phiếu mượn</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
        <li class="breadcrumb-item"><a href="{{ route('phieu-muon.index') }}">Phiếu mượn</a></li>
        <li class="breadcrumb-item active">Chỉnh sửa</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit mr-1"></i>
            Chỉnh sửa thông tin phiếu mượn
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('phieu-muon.update', $phieuMuon->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group mb-3">
                    <label for="ngay_hen_tra">Ngày hẹn trả</label>
                    <input type="date" class="form-control @error('ngay_hen_tra') is-invalid @enderror" id="ngay_hen_tra" name="ngay_hen_tra" value="{{ old('ngay_hen_tra', $phieuMuon->ngay_hen_tra->format('Y-m-d')) }}" required>
                    @error('ngay_hen_tra')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group mb-3">
                    <label for="ghi_chu">Ghi chú</label>
                    <textarea class="form-control" id="ghi_chu" name="ghi_chu" rows="3">{{ old('ghi_chu', $phieuMuon->ghi_chu) }}</textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="{{ route('phieu-muon.index') }}" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>
@endsection