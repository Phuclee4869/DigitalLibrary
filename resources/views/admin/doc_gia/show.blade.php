@extends('layouts.admin')

@section('title', 'Chi tiết Độc giả')

@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Chi tiết Độc giả</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
        <li class="breadcrumb-item"><a href="{{ route('doc-gia.index') }}">Độc giả</a></li>
        <li class="breadcrumb-item active">Chi tiết</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user mr-1"></i>
            Thông tin độc giả
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 150px;">ID:</th>
                            <td>{{ $docGia->id }}</td>
                        </tr>
                        <tr>
                            <th>Họ tên:</th>
                            <td>{{ $docGia->ho_ten }}</td>
                        </tr>
                        <tr>
                            <th>Ngày sinh:</th>
                            <td>{{ $docGia->ngay_sinh ? $docGia->ngay_sinh->format('d/m/Y') : 'Không có thông tin' }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $docGia->email ?? 'Không có thông tin' }}</td>
                        </tr>
                        <tr>
                            <th>Số điện thoại:</th>
                            <td>{{ $docGia->so_dien_thoai ?? 'Không có thông tin' }}</td>
                        </tr>
                        <tr>
                            <th>Địa chỉ:</th>
                            <td>{{ $docGia->dia_chi ?? 'Không có thông tin' }}</td>
                        </tr>
                        <tr>
                            <th>Ngày đăng ký:</th>
                            <td>{{ $docGia->ngay_dang_ky ? $docGia->ngay_dang_ky->format('d/m/Y') : 'Không có thông tin' }}</td>
                        </tr>
                        <tr>
                            <th>Ngày hết hạn:</th>
                            <td>{{ $docGia->ngay_het_han ? $docGia->ngay_het_han->format('d/m/Y') : 'Không có thông tin' }}</td>
                        </tr>
                        <tr>
                            <th>Trạng thái:</th>
                            <td>
                                @if($docGia->trang_thai)
                                    <span class="badge bg-success">Đang hoạt động</span>
                                @else
                                    <span class="badge bg-danger">Đã khóa</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">Thống kê</div>
                        <div class="card-body">
                            <p><strong>Số phiếu mượn:</strong> {{ $docGia->phieuMuons->count() }}</p>
                            <p><strong>Số sách đang mượn:</strong> {{ $docGia->phieuMuons->where('trang_thai', 'đang mượn')->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <h5 class="mt-4">Lịch sử mượn sách</h5>
            @if($docGia->phieuMuons->isEmpty())
                <div class="alert alert-info">Độc giả này chưa mượn sách nào.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Ngày mượn</th>
                                <th>Ngày hẹn trả</th>
                                <th>Ngày trả</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($docGia->phieuMuons as $phieuMuon)
                            <tr>
                                <td>{{ $phieuMuon->id }}</td>
                                <td>{{ $phieuMuon->ngay_muon->format('d/m/Y') }}</td>
                                <td>{{ $phieuMuon->ngay_hen_tra->format('d/m/Y') }}</td>
                                <td>{{ $phieuMuon->ngay_tra ? $phieuMuon->ngay_tra->format('d/m/Y') : 'Chưa trả' }}</td>
                                <td>
                                    @if($phieuMuon->trang_thai == 'đang mượn')
                                        <span class="badge bg-primary">Đang mượn</span>
                                    @elseif($phieuMuon->trang_thai == 'đã trả')
                                        <span class="badge bg-success">Đã trả</span>
                                    @else
                                        <span class="badge bg-danger">Quá hạn</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('phieu-muon.show', $phieuMuon->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Chi tiết
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            
            <div class="mt-3">
                <a href="{{ route('doc-gia.edit', $docGia->id) }}" class="btn btn-primary">Chỉnh sửa</a>
                <a href="{{ route('doc-gia.index') }}" class="btn btn-secondary">Quay lại</a>
            </div>
        </div>
    </div>
</div>
@endsection