@extends('layouts.admin')

@section('content')
<div class="container">
    <h2 class="mb-4">Thống kê mượn sách tháng {{ $thang }}/{{ $nam }}</h2>
    <a href="{{ route('thong-ke.xuat-pdf', ['thang' => $thang, 'nam' => $nam]) }}" 
           class="btn btn-primary">
            <i class="fas fa-file-pdf"></i> Xuất PDF
        </a>

    <!-- Form chọn tháng năm -->
    <div class="card mb-4">
        <!-- ... giữ nguyên phần form ... -->
    </div>

    <!-- Thống kê tổng quan -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Tổng phiếu mượn</h6>
                    <p class="h3">{{ $thongKeChiTiet['tong_phieu'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Tổng sách được mượn</h6>
                    <p class="h3">{{ $thongKeChiTiet['tong_sach_muon'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title">Phiếu quá hạn</h6>
                    <p class="h3">{{ $thongKeChiTiet['phieu_qua_han'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Tổng tiền phạt</h6>
                    <p class="h3">{{ number_format($thongKeChiTiet['tong_tien_phat']) }}đ</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê theo danh mục -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Thống kê theo danh mục</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Danh mục</th>
                                <th>Số lượt mượn</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($thongKeChiTiet['theo_danh_muc'] as $danhMuc => $soLuot)
                            <tr>
                                <td>{{ $danhMuc }}</td>
                                <td>{{ $soLuot }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top độc giả mượn nhiều -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Top độc giả mượn nhiều nhất</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Độc giả</th>
                                <th>Số lượt mượn</th>
                                <th>Tổng sách</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($thongKeChiTiet['doc_gia_muon_nhieu'] as $docGia)
                            <tr>
                                <td>{{ $docGia['ten'] }}</td>
                                <td>{{ $docGia['so_luot_muon'] }}</td>
                                <td>{{ $docGia['tong_sach'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top sách mượn nhiều -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Top sách được mượn nhiều nhất</h5>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tên sách</th>
                        <th>Số lượt mượn</th>
                        <th>Tổng số lượng</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($thongKeChiTiet['sach_muon_nhieu'] as $sach)
                    <tr>
                        <td>{{ $sach['tieu_de'] }}</td>
                        <td>{{ $sach['so_luot_muon'] }}</td>
                        <td>{{ $sach['tong_so_luong'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Chi tiết phiếu mượn -->
    <div class="card">
        <div class="card-header">
            <h5>Chi tiết phiếu mượn</h5>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Ngày mượn</th>
                        <th>Độc giả</th>
                        <th>Sách mượn</th>
                        <th>Thủ thư</th>
                        <th>Trạng thái</th>
                        <th>Tiền phạt</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($thongKe as $phieuMuon)
                    <tr>
                        <td>{{ date('d/m/Y', strtotime($phieuMuon->ngay_muon)) }}</td>
                        <td>{{ $phieuMuon->docGia->ho_ten }}</td>
                        <td>
                            @foreach($phieuMuon->chiTietPhieuMuons as $chiTiet)
                                - {{ $chiTiet->sach->tieu_de }} (SL: {{ $chiTiet->so_luong }})<br>
                            @endforeach
                        </td>
                        <td>{{ $phieuMuon->user->name }}</td>
                        <td>
                            <span class="badge {{ $phieuMuon->trang_thai == 'quá hạn' ? 'bg-danger' : 'bg-success' }}">
                                {{ $phieuMuon->trang_thai }}
                            </span>
                        </td>
                        <td>{{ number_format($phieuMuon->chiTietPhieuMuons->sum('tien_phat')) }}đ</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection