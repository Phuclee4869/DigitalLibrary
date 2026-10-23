@extends('layouts.admin')

@section('title', 'Chi tiết phiếu mượn')

@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Chi tiết phiếu mượn</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
        <li class="breadcrumb-item"><a href="{{ route('phieu-muon.index') }}">Phiếu mượn</a></li>
        <li class="breadcrumb-item active">Chi tiết</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-info-circle mr-1"></i>
            Thông tin phiếu mượn
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Thông tin cơ bản</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>ID phiếu mượn</th>
                            <td>{{ $phieuMuon->id }}</td>
                        </tr>
                        <tr>
                            <th>Độc giả</th>
                            <td>{{ $phieuMuon->docGia->ho_ten }}</td>
                        </tr>
                        <tr>
                            <th>Ngày mượn</th>
                            <td>{{ $phieuMuon->ngay_muon->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Ngày hẹn trả</th>
                            <td>{{ $phieuMuon->ngay_hen_tra->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Ngày trả</th>
                            <td>{{ $phieuMuon->ngay_tra ? $phieuMuon->ngay_tra->format('d/m/Y') : 'Chưa trả' }}</td>
                        </tr>
                        <tr>
                            <th>Trạng thái</th>
                            <td>
                                @if($phieuMuon->trang_thai == 'đang mượn')
                                    <span class="badge bg-primary">Đang mượn</span>
                                @elseif($phieuMuon->trang_thai == 'đã trả')
                                    <span class="badge bg-success">Đã trả</span>
                                @else
                                    <span class="badge bg-danger">Quá hạn</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Danh sách sách mượn</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tên sách</th>
                                <th>Số lượng</th>
                                <th>Tình trạng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($phieuMuon->chiTietPhieuMuons as $chiTiet)
                            <tr>
                                <td>{{ $chiTiet->sach->tieu_de }}</td>
                                <td>{{ $chiTiet->so_luong }}</td>
                                <td>{{ $chiTiet->tinh_trang_khi_muon }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mt-3">
                <a href="{{ route('phieu-muon.index') }}" class="btn btn-secondary">Quay lại</a>
                
                @if($phieuMuon->trang_thai == 'đang mượn')
                    <a href="{{ route('phieu-muon.edit', $phieuMuon->id) }}" class="btn btn-primary">Chỉnh sửa</a>
                    <a href="{{ route('phieu-muon.return', $phieuMuon->id) }}" class="btn btn-success">Trả sách</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection