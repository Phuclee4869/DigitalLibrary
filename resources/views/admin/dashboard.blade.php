@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Tổng số sách</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBooks }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-book fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Độc giả</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalReaders }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Phiếu mượn</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBorrowings }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Người dùng hệ thống</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-cog fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Phiếu mượn gần đây</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Độc giả</th>
                                <th>Ngày mượn</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($latestBorrowings as $phieuMuon)
                                <tr>
                                    <td>{{ $phieuMuon->id }}</td>
                                    <td>{{ $phieuMuon->docGia->ho_ten }}</td>
                                    <td>{{ $phieuMuon->ngay_muon->format('d/m/Y') }}</td>
                                    <td>
                                        @if($phieuMuon->trang_thai == 'đang mượn')
                                            <span class="badge bg-warning">Đang mượn</span>
                                        @elseif($phieuMuon->trang_thai == 'đã trả')
                                            <span class="badge bg-success">Đã trả</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $phieuMuon->trang_thai }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('phieu-muon.show', $phieuMuon->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Sách được mượn nhiều nhất</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sách</th>
                                <th>Tác giả</th>
                                <th>Số lượt mượn</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($popularBooks as $book)
                                <tr>
                                    <td>{{ $book->sach->tieu_de }}</td>
                                    <td>{{ $book->sach->tacGia->ten_tac_gia }}</td>
                                    <td>{{ $book->total }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection