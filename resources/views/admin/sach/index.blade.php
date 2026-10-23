@extends('layouts.admin')

@section('title', 'Quản lý Sách')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('sach.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Thêm Sách Mới
    </a>
    
    <form action="{{ route('sach.index') }}" method="GET" class="d-flex">
        <input type="text" name="search" class="form-control me-2" placeholder="Tìm kiếm sách..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-outline-primary">
            <i class="fas fa-search"></i>
        </button>
    </form>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Hình ảnh</th>
                        <th>Tiêu đề</th>
                        <th>Danh mục</th>
                        <th>Tác giả</th>
                        <th>NXB</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sachs as $sach)
                        <tr>
                            <td>{{ $sach->id }}</td>
                            <td>
                                <img src="{{ $sach->hinh_anh ? asset('storage/'.$sach->hinh_anh) : 'https://via.placeholder.com/50x70?text=No+Image' }}" 
                                    alt="{{ $sach->tieu_de }}" width="50">
                            </td>
                            <td>{{ Str::limit($sach->tieu_de, 30) }}</td>
                            <td>{{ $sach->danhMuc->ten_danh_muc }}</td>
                            <td>{{ $sach->tacGia->ten_tac_gia }}</td>
                            <td>{{ $sach->nhaXuatBan->ten_nxb }}</td>
                            <td>{{ $sach->so_luong_con_lai }}/{{ $sach->so_luong }}</td>
                            <td>{{ number_format($sach->gia) }} VNĐ</td>
                            <td>
                                <div class="d-flex">
                                <a href="{{ route('sach.detail', $sach->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('sach.edit', $sach->id) }}" class="btn btn-sm btn-warning me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('sach.destroy', $sach->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sách này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection