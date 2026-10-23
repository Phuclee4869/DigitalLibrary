<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thư Viện Drak Phenikaa - @yield('title')</title>
    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            display: flex;
        }

        #sidebar {
            min-width: 250px;
            max-width: 250px;
            min-height: 100vh;
            background: #343a40;
            color: #fff;
            transition: all 0.3s;
        }

        #sidebar.active {
            margin-left: -250px;
        }

        #sidebar .sidebar-header {
            padding: 20px;
            background: #2c3136;
        }

        #sidebar ul.components {
            padding: 20px 0;
        }

        #sidebar ul p {
            padding: 10px;
            font-size: 1.1em;
            display: block;
        }

        #sidebar ul li a {
            padding: 10px 20px;
            font-size: 1.1em;
            display: block;
            color: #fff;
            text-decoration: none;
        }

        #sidebar ul li a:hover {
            background: #e9a6ec;
        }

        #sidebar ul li.active>a {
            background: #e9a6ec;
        }

        #content {
            width: 100%;
            min-height: 100vh;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }

        #content-body {
            flex: 1;
            padding: 20px;
        }

        .dropdown-toggle::after {
            display: block;
            position: absolute;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
        }

        @media (max-width: 768px) {
            #sidebar {
                margin-left: -250px;
            }

            #sidebar.active {
                margin-left: 0;
            }
        }
    </style>

    @yield('styles')
</head>

<body>
    <div id="sidebar">
        <div class="sidebar-header">
            <h3>Thư Viện Dark PKA</h3>
        </div>

        <ul class="list-unstyled components">
            <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
            </li>
            <li class="{{ request()->routeIs('sach.*') ? 'active' : '' }}">
                <a href="{{ route('sach.index') }}"><i class="fas fa-book me-2"></i>Quản lý Sách</a>
            </li>
            <li class="{{ request()->routeIs('danh-muc.*') ? 'active' : '' }}">
                <a href="{{ route('danh-muc.index') }}"><i class="fas fa-list me-2"></i>Quản lý Danh Mục</a>
            </li>
            <li class="{{ request()->routeIs('tac-gia.*') ? 'active' : '' }}">
                <a href="{{ route('tac-gia.index') }}"><i class="fas fa-user-tie me-2"></i>Quản lý Tác Giả</a>
            </li>
            <li class="{{ request()->routeIs('nha-xuat-ban.*') ? 'active' : '' }}">
                <a href="{{ route('nha-xuat-ban.index') }}"><i class="fas fa-building me-2"></i>Quản lý NXB</a>
            </li>
            <li class="{{ request()->routeIs('doc-gia.*') ? 'active' : '' }}">
                <a href="{{ route('doc-gia.index') }}"><i class="fas fa-users me-2"></i>Quản lý Độc Giả</a>
            </li>
            <li class="{{ request()->routeIs('phieu-muon.*') ? 'active' : '' }}">
                <a href="{{ route('phieu-muon.index') }}"><i class="fas fa-clipboard-list me-2"></i>Quản lý Phiếu Mượn</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('thong-ke.index') }}">
                    <i class="fas fa-chart-bar"></i>
                    Thống kê tổng quan
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('thong-ke.muon-thang') }}">
                    <i class="fas fa-calendar-alt"></i>
                    Thống kê theo tháng
                </a>
            </li>
            @if(auth()->user()->isAdmin())
            <li class="{{ request()->routeIs('user.*') ? 'active' : '' }}">
                <a href="{{ route('user.index') }}"><i class="fas fa-user-cog me-2"></i>Quản lý Người Dùng</a>
            </li>
            @endif
            <li>
                <a href="{{ route('home') }}"><i class="fas fa-home me-2"></i>Về Trang Chủ</a>
            </li>
            <li>
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt me-2"></i>Đăng Xuất
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>
    </div>

    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <button type="button" id="sidebarCollapse" class="btn btn-primary">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="ms-auto d-flex align-items-center">
                    <span class="me-3">Xin chào, {{ Auth::user()->name }}</span>
                </div>
            </div>
        </nav>

        <div id="content-body">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <h2 class="mb-4">@yield('title')</h2>

            @yield('content')
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#sidebarCollapse').on('click', function() {
                $('#sidebar').toggleClass('active');
            });
        });
    </script>

    @yield('scripts')
</body>

</html>