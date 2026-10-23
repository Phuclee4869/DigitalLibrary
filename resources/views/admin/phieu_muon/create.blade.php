@extends('layouts.admin')

@section('title', 'Tạo Phiếu Mượn Mới')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('phieu-muon.store') }}" method="POST" id="phieuMuonForm">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="doc_gia_id" class="form-label">Độc giả <span class="text-danger">*</span></label>
                        <select class="form-select select2 @error('doc_gia_id') is-invalid @enderror" id="doc_gia_id" name="doc_gia_id" required>
                            <option value="">-- Chọn độc giả --</option>
                            @foreach($docGias as $docGia)
                                <option value="{{ $docGia->id }}" {{ old('doc_gia_id') == $docGia->id ? 'selected' : '' }}>
                                    {{ $docGia->ho_ten }} - {{ $docGia->so_dien_thoai }}
                                </option>
                            @endforeach
                        </select>
                        @error('doc_gia_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="ngay_muon" class="form-label">Ngày mượn <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('ngay_muon') is-invalid @enderror" id="ngay_muon" name="ngay_muon" value="{{ old('ngay_muon', date('Y-m-d')) }}" required>
                        @error('ngay_muon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="ngay_hen_tra" class="form-label">Ngày hẹn trả <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('ngay_hen_tra') is-invalid @enderror" id="ngay_hen_tra" name="ngay_hen_tra" value="{{ old('ngay_hen_tra', date('Y-m-d', strtotime('+7 days'))) }}" required>
                        @error('ngay_hen_tra')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="ghi_chu" class="form-label">Ghi chú</label>
                <textarea class="form-control @error('ghi_chu') is-invalid @enderror" id="ghi_chu" name="ghi_chu" rows="2">{{ old('ghi_chu') }}</textarea>
                @error('ghi_chu')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <hr>
            
            <h5 class="mb-3">Danh sách sách mượn</h5>
            
            <div id="sach-container">
                <div class="sach-item row mb-3">
                    <div class="col-md-5">
                        <select class="form-select select2-sach @error('sach_ids.0') is-invalid @enderror" name="sach_ids[]" required>
                            <option value="">-- Chọn sách --</option>
                            @foreach($sachs as $sach)
                                <option value="{{ $sach->id }}" {{ old('sach_ids.0') == $sach->id ? 'selected' : '' }}>
                                    {{ $sach->tieu_de }} (Còn: {{ $sach->so_luong_con_lai }})
                                </option>
                            @endforeach
                        </select>
                        @error('sach_ids.0')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-2">
                        <input type="number" class="form-control @error('so_luongs.0') is-invalid @enderror" name="so_luongs[]" placeholder="Số lượng" min="1" value="{{ old('so_luongs.0', 1) }}" required>
                        @error('so_luongs.0')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <input type="text" class="form-control @error('tinh_trang_khi_muons.0') is-invalid @enderror" name="tinh_trang_khi_muons[]" placeholder="Tình trạng khi mượn" value="{{ old('tinh_trang_khi_muons.0', 'Sách còn tốt') }}" required>
                        @error('tinh_trang_khi_muons.0')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-remove-sach" disabled>
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <button type="button" id="btn-add-sach" class="btn btn-success">
                    <i class="fas fa-plus"></i> Thêm sách
                </button>
            </div>
            
            <div class="d-flex justify-content-end mt-4">
                <a href="{{ route('phieu-muon.index') }}" class="btn btn-secondary me-2">Hủy</a>
                <button type="submit" class="btn btn-primary">Tạo phiếu mượn</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap-5'
        });
        
        $('.select2-sach').select2({
            theme: 'bootstrap-5'
        });
        
        // Add new book row
        $('#btn-add-sach').click(function() {
            var index = $('.sach-item').length;
            var newRow = `
                <div class="sach-item row mb-3">
                    <div class="col-md-5">
                        <select class="form-select select2-sach" name="sach_ids[]" required>
                            <option value="">-- Chọn sách --</option>
                            @foreach($sachs as $sach)
                                <option value="{{ $sach->id }}">
                                    {{ $sach->tieu_de }} (Còn: {{ $sach->so_luong_con_lai }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <input type="number" class="form-control" name="so_luongs[]" placeholder="Số lượng" min="1" value="1" required>
                    </div>
                    
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="tinh_trang_khi_muons[]" placeholder="Tình trạng khi mượn" value="Sách còn tốt" required>
                    </div>
                    
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-remove-sach">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
            
            $('#sach-container').append(newRow);
            $('.select2-sach').select2({
                theme: 'bootstrap-5'
            });
            
            // Enable all remove buttons if there are more than one book
            if ($('.sach-item').length > 1) {
                $('.btn-remove-sach').prop('disabled', false);
            }
        });
        
        // Remove book row
        $(document).on('click', '.btn-remove-sach', function() {
            $(this).closest('.sach-item').remove();
            
            // Disable remove button if only one book left
            if ($('.sach-item').length === 1) {
                $('.btn-remove-sach').prop('disabled', true);
            }
        });
        
        // Form validation
        $('#phieuMuonForm').submit(function(e) {
            var isValid = true;
            
            // Check if at least one book is selected
            if ($('.sach-item').length === 0) {
                alert('Vui lòng chọn ít nhất 1 quyển sách để mượn');
                isValid = false;
            }
            
            // Check for duplicate books
            var selectedBooks = [];
            $('select[name="sach_ids[]"]').each(function() {
                var bookId = $(this).val();
                if (bookId && selectedBooks.includes(bookId)) {
                    alert('Một quyển sách không thể được chọn nhiều lần. Vui lòng tăng số lượng nếu muốn mượn nhiều.');
                    isValid = false;
                    return false;
                }
                selectedBooks.push(bookId);
            });
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    });
</script>
@endsection