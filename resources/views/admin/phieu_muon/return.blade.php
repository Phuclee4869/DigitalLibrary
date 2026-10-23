@extends('layouts.admin')

@section('title', 'Xử lý trả sách')

@section('content')
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Thông tin phiếu mượn</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <th style="width: 150px;">Mã phiếu:</th>
                        <td>{{ $phieuMuon->id }}</td>
                    </tr>
                    <tr>
                        <th>Độc giả:</th>
                        <td>{{ $phieuMuon->docGia->ho_ten }}</td>
                    </tr>
                    <tr>
                        <th>Ngày mượn:</th>
                        <td>{{ $phieuMuon->ngay_muon->format('d/m/Y') }}</td>
                    </tr>
                </table>
            </div>
            
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <th style="width: 150px;">Ngày hẹn trả:</th>
                        <td>{{ $phieuMuon->ngay_hen_tra->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Số ngày đã mượn:</th>
                        <td>{{ $phieuMuon->ngay_muon->diffInDays(now()) }} ngày</td>
                    </tr>
                    <tr>
                        <th>Trạng thái:</th>
                        <td>
                            @if(now() > $phieuMuon->ngay_hen_tra)
                                <span class="badge bg-danger">Trễ hạn {{ $phieuMuon->ngay_hen_tra->diffInDays(now()) }} ngày</span>
                            @else
                                <span class="badge bg-success">Đúng hạn</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('phieu-muon.process-return', $phieuMuon->id) }}" method="POST">
    @csrf
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Thông tin trả sách</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="ngay_tra" class="form-label">Ngày trả <span class="text-danger">*</span></label>
                <input type="date" class="form-control @error('ngay_tra') is-invalid @enderror" id="ngay_tra" name="ngay_tra" value="{{ old('ngay_tra', date('Y-m-d')) }}" required>
                @error('ngay_tra')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Danh sách sách trả</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tên sách</th>
                            <th>Tình trạng khi mượn</th>
                            <th>Tình trạng khi trả <span class="text-danger">*</span></th>
                            <th>Tiền phạt (VNĐ)</th>
                            <th>Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($phieuMuon->chiTietPhieuMuons as $index => $chiTiet)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $chiTiet->sach->tieu_de }}</td>
                                <td>{{ $chiTiet->tinh_trang_khi_muon }}</td>
                                <td>
                                    <input type="text" class="form-control @error('tinh_trang_khi_tras.'.$chiTiet->id) is-invalid @enderror" 
                                        name="tinh_trang_khi_tras[{{ $chiTiet->id }}]" 
                                        value="{{ old('tinh_trang_khi_tras.'.$chiTiet->id, 'Sách còn tốt') }}" required>
                                    @error('tinh_trang_khi_tras.'.$chiTiet->id)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <input type="number" class="form-control @error('tien_phats.'.$chiTiet->id) is-invalid @enderror" 
                                        name="tien_phats[{{ $chiTiet->id }}]" 
                                        value="{{ old('tien_phats.'.$chiTiet->id, 0) }}" min="0">
                                    @error('tien_phats.'.$chiTiet->id)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <input type="text" class="form-control" 
                                        name="ghi_chus[{{ $chiTiet->id }}]" 
                                        value="{{ old('ghi_chus.'.$chiTiet->id) }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-between">
                <a href="{{ route('phieu-muon.show', $phieuMuon->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check"></i> Xác nhận trả sách
                </button>
            </div>
        </div>
    </div>
</form>
@endsection