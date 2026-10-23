@extends('layouts.app')

@section('title', $sach->tieu_de)

@section('content')
<div class="row">
    <div class="col-md-4">
        <img src="{{ $sach->hinh_anh ? asset('storage/'.$sach->hinh_anh) : 'https://via.placeholder.com/300x450?text=No+Image' }}" 
            alt="{{ $sach->tieu_de }}" class="img-fluid rounded mb-3 shadow-sm" style="max-height: 400px; width: 100%; object-fit: contain;">
    </div>
    
    <div class="col-md-8">
        <h2 class="mb-3">{{ $sach->tieu_de }}</h2>
        <p class="lead">Tác giả: <strong>{{ $sach->tacGia->ten_tac_gia }}</strong></p>
        
        <div class="mb-3">
            <span class="badge bg-primary">{{ $sach->danhMuc->ten_danh_muc }}</span>
            <span class="badge bg-secondary">{{ $sach->nam_xuat_ban }}</span>
            <span class="badge {{ $sach->so_luong_con_lai > 0 ? 'bg-success' : 'bg-danger' }}">
                {{ $sach->so_luong_con_lai > 0 ? 'Còn sách' : 'Hết sách' }}
            </span>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Thông tin chi tiết</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped mb-0">
                            <tr>
                                <th style="width: 35%;">Nhà xuất bản:</th>
                                <td>{{ $sach->nhaXuatBan->ten_nxb }}</td>
                            </tr>
                            <tr>
                                <th>Số trang:</th>
                                <td>{{ $sach->so_trang }}</td>
                            </tr>
                            <tr>
                                <th>ISBN:</th>
                                <td>{{ $sach->isbn ?: 'Chưa có thông tin' }}</td>
                            </tr>
                            <tr>
                                <th>Số lượng còn:</th>
                                <td>{{ $sach->so_luong_con_lai }}/{{ $sach->so_luong }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mb-4">
            <h4>Giới thiệu sách</h4>
            <div class="card">
                <div class="card-body bg-light">
                    @if($sach->mo_ta)
                        <p class="mb-0">{{ $sach->mo_ta }}</p>
                    @else
                        <p class="text-muted mb-0 fst-italic">Chưa có mô tả cho sách này.</p>
                    @endif
                </div>
            </div>
        </div>
        
        @auth
            @if(auth()->user()->isAdmin() || auth()->user()->isThuThu())
                <div class="mb-4">
                    <a href="{{ route('sach.edit', $sach->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Chỉnh sửa
                    </a>
                </div>
            @endif
        @endauth
    </div>
</div>

<div class="mt-5">
    <h3>Sách cùng danh mục</h3>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mt-2">
        @foreach($relatedBooks as $relatedBook)
            <div class="col">
                <div class="card h-100 book-card">
                    <img src="{{ $relatedBook->hinh_anh ? asset('storage/'.$relatedBook->hinh_anh) : 'https://via.placeholder.com/300x450?text=No+Image' }}" class="card-img-top book-img" alt="{{ $relatedBook->tieu_de }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ Str::limit($relatedBook->tieu_de, 30) }}</h5>
                        <p class="card-text text-muted mb-2">{{ $relatedBook->tacGia->ten_tac_gia }}</p>
                        <span class="badge bg-dark text-truncate mw-100 align-middle" title="{{ $relatedBook->danhMuc->ten_danh_muc }}">
                            {{ Str::limit($relatedBook->danhMuc->ten_danh_muc, 20) }}
                        </span>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <div class="d-grid gap-2">
                            <a href="{{ route('sach.detail', $relatedBook->id) }}" class="btn btn-outline-primary">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection