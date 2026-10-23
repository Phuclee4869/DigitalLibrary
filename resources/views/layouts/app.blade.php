<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Thư Viện Sách') }} @yield('title')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        main {
            flex: 1;
            padding-top: 70px; /* Đảm bảo nội dung không bị che bởi navbar */
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .book-card {
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-radius: 0 !important;
        }
        
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .book-img {
            height: 250px;
            object-fit: cover;
            border-radius: 0 !important;
        }
        
        footer {
            margin-top: auto;
        }

        /* Style cho navbar fixed màu xám đen & vuông góc */
        .navbar {
            position: fixed !important;
            top: 0 !important;
            width: 100% !important;
            z-index: 1000 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
            background-color: #212529 !important; /* Màu xám đen đồng bộ với sidebar */
            border-radius: 0 !important;
        }

        /* Ép tất cả các input, button, dropdown trong header bỏ góc bo tròn */
        .navbar .form-control,
        .navbar .btn,
        .navbar .dropdown-menu {
            border-radius: 0 !important;
        }

        /* Style cho view-all-btn */
        .view-all-btn {
            padding: 8px 16px;
            border-radius: 0 !important;
            background-color: #f8f9fa;
            color: #212529;
            border: 1px solid #212529;
            transition: all 0.3s;
            font-weight: 500;
            text-decoration: none;
        }
        
        .view-all-btn:hover {
            background-color: #212529;
            color: white;
        }
        
        /* Style cho card-footer và giá */
        .card-footer {
            padding: 0.75rem 1.25rem;
            background-color: white;
            border-top: 1px solid #dee2e6;
            border-radius: 0 !important;
        }
        
        .card-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            height: 40px;
            overflow: hidden;
        }

        .book-price {
            color: #dc3545;
            font-weight: bold;
            font-size: 1.1rem;
            text-align: right;
            margin-bottom: 10px;
        }

        .list-group-item {
            transition: background-color 0.2s;
            border-left: 3px solid transparent;
            border-radius: 0 !important;
        }
        
        .list-group-item:hover {
            background-color: #f8f9fa;
            border-left: 3px solid #212529;
        }

        .text-danger.fw-bold {
            font-size: 1.1rem;
        }
        
        /* Cải thiện phân trang */
        .pagination {
            margin-top: 2rem;
            margin-bottom: 1rem;
        }
        
        .page-link {
            color: #212529;
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 0 !important;
        }
        
        .page-item.active .page-link {
            background-color: #212529;
            border-color: #212529;
            color: #fff;
        }

        /* Cải thiện form */
        .form-control:focus {
            border-color: #495057;
            box-shadow: 0 0 0 0.25rem rgba(33, 37, 41, 0.25);
        }
        
        .btn-outline-primary {
            border-color: #212529;
            color: #212529;
            border-radius: 0 !important;
        }
        
        .btn-outline-primary:hover {
            background-color: #212529;
            color: white;
        }
        
        /* Nút xem chi tiết */
        .btn-outline-primary {
            padding: 0.375rem 0.75rem;
            border-radius: 0 !important;
            transition: all 0.15s ease-in-out;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home') }}">
                    <i class="fas fa-book-open me-2"></i>Thư Viện Dark PKA 
                </a>
                <button class="navbar-toggler rounded-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">Trang chủ</a>
                        </li>
                        @auth
                            @if(auth()->user()->isAdmin() || auth()->user()->isThuThu())
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.dashboard') }}">Quản lý</a>
                                </li>
                            @endif
                        @endauth
                    </ul>
                    
                    <form class="d-flex me-2" action="{{ route('search') }}" method="GET">
                        <input class="form-control me-2 rounded-0" type="search" name="query" placeholder="Tìm kiếm sách..." value="{{ request('query') }}">
                        <button class="btn btn-light rounded-0" type="submit"><i class="fas fa-search"></i></button>
                    </form>
                    
                    <ul class="navbar-nav">
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">Đăng nhập</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">Đăng ký</a>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    {{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end rounded-0">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            Đăng xuất
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Thêm banner ở đây -->
        @include('partials.banner')
    </header>

    <main class="py-4">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-0">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-0">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @yield('content')
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    @yield('scripts')
</body>
</html>