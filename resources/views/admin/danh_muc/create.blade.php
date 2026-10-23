@extends('layouts.admin')

@section('title', 'Thêm Danh Mục Mới')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('danh-muc.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="ten_danh_muc" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('ten_danh_muc') is-invalid @enderror" id="ten_danh_muc" name="ten_danh_muc" value="{{ old('ten_danh_muc') }}" required>
                @error('ten_danh_muc')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="mo_ta" class="form-label">Mô tả</label>
                <textarea class="form-control @error('mo_ta') is-invalid @enderror" id="mo_ta" name="mo_ta" rows="3">{{ old('mo_ta') }}</textarea>
                @error('mo_ta')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-end mt-4">
                <a href="{{ route('danh-muc.index') }}" class="btn btn-secondary me-2">Hủy</a>
                <button type="submit" class="btn btn-primary">Thêm danh mục</button>
            </div>
        </form>
    </div>
</div>
@endsection