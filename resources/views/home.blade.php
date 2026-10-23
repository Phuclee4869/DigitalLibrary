@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- Sidebar bên trái: Danh mục và thống kê -->
        <div class="col-md-3">
            <!-- Danh mục sách -->
            <div class="card mb-4 shadow-sm rounded-0">
                <div class="card-header bg-dark text-white rounded-0">
                    <h5 class="mb-0">Danh mục sách</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($danhMucs as $dm)
                            <li class="list-group-item">
                                <a href="{{ route('danh-muc.filter', $dm->id) }}" class="text-decoration-none w-100 d-flex justify-content-between align-items-center">
                                    <span>{{ $dm->ten_danh_muc }}</span>
                                    <!-- Hiện lại con số tự nhiên, không bị bo tròn -->
                                    <span class="fw-bold text-dark">{{ $dm->sachs_count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <!-- Nút xem tất cả góc vuông hình chữ nhật, không có icon -->
                <div class="card-footer bg-white text-center rounded-0">
                    <a href="{{ route('home') }}" class="btn btn-outline-dark btn-sm w-100 rounded-0">Tất cả sách</a>
                </div>
            </div>

            <!-- Thống kê -->
            <div class="card mb-4 shadow-sm rounded-0">
                <div class="card-header bg-dark text-white rounded-0">
                    <h5 class="mb-0">Thống kê</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Tổng số danh mục
                            <span class="fw-bold">{{ count($danhMucs) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Tổng số sách
                            <span class="fw-bold">{{ $totalBooks ?? $sachs->total() }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Nội dung chính bên phải: Danh sách sách -->
        <div class="col-md-9">
            <div class="card mb-4 shadow-sm rounded-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center rounded-0">
                    <div>
                        @if(isset($danhMuc))
                            <h4 class="mb-0">Danh mục sách: {{ $danhMuc->ten_danh_muc }}</h4>
                        @elseif(isset($query))
                            <h4 class="mb-0">Kết quả tìm kiếm cho: "{{ $query }}"</h4>
                        @else
                            <h4 class="mb-0"></h4>
                        @endif
                    </div>
                    @if(isset($danhMuc))
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-0">
                            <i class="fas fa-arrow-left me-1"></i>Quay lại
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                        @foreach($sachs as $sach)
                            <div class="col">
                                <div class="card h-100 book-card shadow-sm rounded-0">
                                    <img src="{{ $sach->hinh_anh ? asset('storage/'.$sach->hinh_anh) : 'https://via.placeholder.com/300x450?text=No+Image' }}" 
                                        class="card-img-top book-img rounded-0" alt="{{ $sach->tieu_de }}">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ Str::limit($sach->tieu_de, 30) }}</h5>
                                        <p class="card-text text-muted mb-2">{{ $sach->tacGia->ten_tac_gia }}</p>
                                        <span class="badge bg-dark rounded-0 category-badge" title="{{ $sach->danhMuc->ten_danh_muc }}">
                                            {{ Str::limit($sach->danhMuc->ten_danh_muc, 20) }}
                                        </span>
                                    </div>
                                    <div class="card-footer bg-white border-top-0 rounded-0">
                                        <a href="{{ route('sach.detail', $sach->id) }}" class="btn btn-outline-dark w-100 rounded-0">Xem chi tiết</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer bg-white rounded-0">
                    {{ $sachs->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Bỏ góc bo tròn cho tất cả các khung card và header */
    .card, 
    .card-header, 
    .card-footer, 
    .list-group-item {
        border-radius: 0 !important;
    }

    /* Định dạng badge danh mục nằm cân đối trong card */
    .category-badge {
        max-width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
        vertical-align: middle;
        padding: 0.4em 0.6em;
    }

    /* Định dạng cho card sách */
    .book-card {
        transition: transform 0.3s, box-shadow 0.3s;
        border: none;
    }
    
    .book-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
    }
    
    /* Định dạng cho hình ảnh sách */
    .book-img {
        height: 200px;
        object-fit: cover;
    }
    
    /* Định dạng cho tiêu đề sách */
    .card-title {
        font-weight: 600;
        margin-bottom: 0.5rem;
        min-height: 48px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Định dạng cho card footer */
    .card-footer {
        padding: 1rem;
        background-color: white;
    }
    
    /* Hiệu ứng hover cho danh mục đổi sang màu xám đen */
    .list-group-item {
        transition: all 0.2s !important;
        border-left: 3px solid transparent !important;
    }

    .list-group-item:hover {
        background-color: #e9ecef !important;
        border-left: 3px solid #212529 !important;
    }
    
    .list-group-item a {
        color: #212529 !important;
    }

    .list-group-item:hover a {
        color: #212529 !important;
    }
    
    /* Định dạng cho phân trang */
    .pagination {
        justify-content: center;
        margin-bottom: 0;
    }
    
    .pagination .page-item:first-child .page-link,
    .pagination .page-item:last-child .page-link {
        border-radius: 0 !important;
    }
    
    .page-link {
        color: #212529;
        background-color: #fff;
        border: 1px solid #dee2e6;
        padding: 0.5rem 0.75rem;
        border-radius: 0 !important;
    }
    
    .page-item.active .page-link {
        background-color: #212529;
        border-color: #212529;
        color: #fff;
    }
    
    /* Thêm shadow cho các card */
    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }

    /* Định dạng cho nút Outline vuông vức */
    .btn-outline-dark,
    .btn-outline-secondary {
        border-radius: 0 !important;
        transition: all 0.3s;
    }
    
    .btn-outline-dark:hover {
        background-color: #212529;
        color: white;
        transform: translateY(-2px);
    }
</style>
@endsection