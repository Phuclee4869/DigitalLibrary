@extends('layouts.admin')

@section('title', 'Quản lý Phiếu Mượn')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('phieu-muon.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tạo Phiếu Mượn Mới
    </a>
    
    <form action="{{ route('phieu-muon.index') }}" method="GET" class="d-flex">
        <select name="status" class="form-select me-2" onchange="this.form.submit()">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="đang mượn" {{ request('status') == 'đang mượn' ? 'selected' : '' }}>Đang mượn</option>
            <option value="đã trả" {{ request('status') == 'đã trả' ? 'selected' : '' }}>Đã trả</option>
        </select>
        <input type="text" name="search" class="form-control me-2" placeholder="Tìm kiếm độc giả..." value="{{ request('search') }}">
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
                        <th>Độc giả</th>
                        <th>Người tạo</th>
                        <th>Ngày mượn</th>
                        <th>Ngày hẹn trả</th>
                        <th>Ngày trả</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($phieuMuons as $phieuMuon)
                        <tr>
                            <td>{{ $phieuMuon->id }}</td>
                            <td>{{ $phieuMuon->docGia->ho_ten }}</td>
                            <td>{{ $phieuMuon->user->name }}</td>
                            <td>{{ $phieuMuon->ngay_muon->format('d/m/Y') }}</td>
                            <td>{{ $phieuMuon->ngay_hen_tra->format('d/m/Y') }}</td>
                            <td>{{ $phieuMuon->ngay_tra ? $phieuMuon->ngay_tra->format('d/m/Y') : 'Chưa trả' }}</td>
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
                                <div class="d-flex">
                                    <a href="{{ route('phieu-muon.show', $phieuMuon->id) }}" class="btn btn-sm btn-info me-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($phieuMuon->trang_thai == 'đang mượn')
                                        <a href="{{ route('phieu-muon.edit', $phieuMuon->id) }}" class="btn btn-sm btn-warning me-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('phieu-muon.return', $phieuMuon->id) }}" class="btn btn-sm btn-success me-1">
                                            <i class="fas fa-undo"></i> Trả
                                        </a>
                                    @endif
                                    
                                    @if($phieuMuon->trang_thai != 'đang mượn')
                                        <form action="{{ route('phieu-muon.destroy', $phieuMuon->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa phiếu mượn này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $phieuMuons->links() }}
    </div>
</div>
@endsection