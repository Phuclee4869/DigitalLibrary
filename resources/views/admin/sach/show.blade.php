@extends('layouts.admin')

@section('title', 'Chi tiết sách')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 text-center">
                <img src="{{ $sach->hinh_anh ? asset('storage/'.$sach->hinh_anh) : 'https://via.placeholder.com/300x450?text=No+Image' }}" 
                    alt="{{ $sach->tieu_de }}" class="img-fluid rounded mb-3" style="max-height: 400px;">
                
                <div class="d-flex justify-content-center mt-3">
                    <a href="{{ route('sach.edit', $sach->id) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit"></i> Chỉnh sửa
                    </a>
                    <a href="{{ route('sach.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
            
            <div class="col-md-8">
                <h3>{{ $sach->tieu_de }}</h3>
                
                <div class="mb-3">
                    <span class="badge bg-primary">{{ $sach->danhMuc->ten_danh_muc }}</span>
                    <span class="badge bg-secondary">{{ $sach->nam_xuat_ban }}</span>
                    <span class="badge {{ $sach->so_luong_con_lai > 0 ? 'bg-success' : 'bg-danger' }}">
                        {{ $sach->so_luong_con_lai > 0 ? 'Còn sách' : 'Hết sách' }}
                    </span>
                </div>
                
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <th style="width: 150px;">Tác giả:</th>
                            <td>{{ $sach->tacGia->ten_tac_gia }}</td>
                        </tr>
                        <tr>
                            <th>Nhà xuất bản:</th>
                            <td>{{ $sach->nhaXuatBan ? $sach->nhaXuatBan->ten_nxb : 'Không có' }}</td>
                        </tr>
                        <tr>
                            <th>ISBN:</th>
                            <td>{{ $sach->isbn ?: 'Chưa có thông tin' }}</td>
                        </tr>
                        <tr>
                            <th>Số trang:</th>
                            <td>{{ $sach->so_trang ?: 'Chưa có thông tin' }}</td>
                        </tr>
                        <tr>
                            <th>Năm xuất bản:</th>
                            <td>{{ $sach->nam_xuat_ban ?: 'Chưa có thông tin' }}</td>
                        </tr>
                        <tr>
                            <th>Số lượng:</th>
                            <td>{{ $sach->so_luong_con_lai }}/{{ $sach->so_luong }}</td>
                        </tr>
                        <tr>
                            <th>Giá:</th>
                            <td>{{ number_format($sach->gia) }} VNĐ</td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="mt-4">
                    <h5>Mô tả:</h5>
                    <p>{{ $sach->mo_ta ?: 'Chưa có mô tả' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection