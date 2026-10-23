@extends('layouts.admin')

@section('title', 'Thêm Sách Mới')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('sach.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tieu_de" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('tieu_de') is-invalid @enderror" id="tieu_de" name="tieu_de" value="{{ old('tieu_de') }}" required>
                        @error('tieu_de')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="isbn" class="form-label">ISBN</label>
                        <input type="text" class="form-control @error('isbn') is-invalid @enderror" id="isbn" name="isbn" value="{{ old('isbn') }}">
                        @error('isbn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="danh_muc_id" class="form-label">Danh mục <span class="text-danger">*</span></label>
                        <select class="form-select @error('danh_muc_id') is-invalid @enderror" id="danh_muc_id" name="danh_muc_id" required>
                            <option value="">-- Chọn danh mục --</option>
                            @foreach($danhMucs as $danhMuc)
                                <option value="{{ $danhMuc->id }}" {{ old('danh_muc_id') == $danhMuc->id ? 'selected' : '' }}>
                                    {{ $danhMuc->ten_danh_muc }}
                                </option>
                            @endforeach
                        </select>
                        @error('danh_muc_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="tac_gia_id" class="form-label">Tác giả <span class="text-danger">*</span></label>
                        <select class="form-select @error('tac_gia_id') is-invalid @enderror" id="tac_gia_id" name="tac_gia_id" required>
                            <option value="">-- Chọn tác giả --</option>
                            @foreach($tacGias as $tacGia)
                                <option value="{{ $tacGia->id }}" {{ old('tac_gia_id') == $tacGia->id ? 'selected' : '' }}>
                                    {{ $tacGia->ten_tac_gia }}
                                </option>
                            @endforeach
                        </select>
                        @error('tac_gia_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="nha_xuat_ban_id" class="form-label">Nhà xuất bản <span class="text-danger">*</span></label>
                        <select class="form-select @error('nha_xuat_ban_id') is-invalid @enderror" id="nha_xuat_ban_id" name="nha_xuat_ban_id" required>
                            <option value="">-- Chọn nhà xuất bản --</option>
                            @foreach($nhaXuatBans as $nhaXuatBan)
                                <option value="{{ $nhaXuatBan->id }}" {{ old('nha_xuat_ban_id') == $nhaXuatBan->id ? 'selected' : '' }}>
                                    {{ $nhaXuatBan->ten_nxb }}
                                </option>
                            @endforeach
                        </select>
                        @error('nha_xuat_ban_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="so_trang" class="form-label">Số trang</label>
                        <input type="number" class="form-control @error('so_trang') is-invalid @enderror" id="so_trang" name="so_trang" value="{{ old('so_trang') }}" min="1">
                        @error('so_trang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="nam_xuat_ban" class="form-label">Năm xuất bản</label>
                        <input type="number" class="form-control @error('nam_xuat_ban') is-invalid @enderror" id="nam_xuat_ban" name="nam_xuat_ban" value="{{ old('nam_xuat_ban') }}" min="1900" max="{{ date('Y') + 1 }}">
                        @error('nam_xuat_ban')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="gia" class="form-label">Giá (VNĐ)</label>
                        <input type="number" class="form-control @error('gia') is-invalid @enderror" id="gia" name="gia" value="{{ old('gia') }}" min="0">
                        @error('gia')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="so_luong" class="form-label">Số lượng <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('so_luong') is-invalid @enderror" id="so_luong" name="so_luong" value="{{ old('so_luong') }}" min="0" required>
                        @error('so_luong')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="hinh_anh" class="form-label">Hình ảnh</label>
                        <input type="file" class="form-control @error('hinh_anh') is-invalid @enderror" id="hinh_anh" name="hinh_anh" accept="image/*">
                        @error('hinh_anh')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="mo_ta" class="form-label">Mô tả</label>
                <textarea class="form-control @error('mo_ta') is-invalid @enderror" id="mo_ta" name="mo_ta" rows="5">{{ old('mo_ta') }}</textarea>
                @error('mo_ta')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-end mt-4">
                <a href="{{ route('sach.index') }}" class="btn btn-secondary me-2">Hủy</a>
                <button type="submit" class="btn btn-primary">Thêm sách</button>
            </div>
        </form>
    </div>
</div>
@endsection