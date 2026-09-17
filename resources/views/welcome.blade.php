<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Hệ thống Quản lý Thư viện - Digital Library</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
        <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6 flex justify-between items-center">
            <div class="font-bold text-base text-[#f53003]">📚 Digital Library Management</div>
            @if (Route::has('login'))
                <nav class="flex items-center justify-end gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-block px-5 py-1.5 border border-[#19140035] rounded-sm text-sm font-medium">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="inline-block px-5 py-1.5 border border-transparent hover:border-[#19140035] rounded-sm text-sm font-medium">Đăng nhập</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-block px-5 py-1.5 border border-[#19140035] rounded-sm text-sm font-medium">Đăng ký</a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        <div class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow">
            <main class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row bg-white dark:bg-[#161615] rounded-lg shadow-lg overflow-hidden border border-[#e3e3e0] dark:border-[#3E3E3A]">
                <div class="flex-1 p-6 lg:p-12 flex flex-col justify-center">
                    <span class="text-xs font-semibold uppercase tracking-wider text-[#f53003] mb-2">Đồ án môn học / Phát triển Web</span>
                    <h1 class="text-3xl font-bold mb-4 dark:text-white">Hệ thống Quản lý Thư viện Số</h1>
                    <p class="text-[#706f6c] dark:text-[#A1A09A] mb-6 leading-relaxed">
                        Hệ thống quản lý sách, độc giả, phiếu mượn trả và danh mục trực tuyến được xây dựng dựa trên nền tảng Laravel Framework nhằm tối ưu hóa quy trình vận hành thư viện hiện đại.
                    </p>
                    <div class="space-y-2 mb-8 text-sm dark:text-[#EDEDEC]">
                        <p><strong>Nhóm thực hiện:</strong> Nhóm phát triển phần mềm</p>
                        <p><strong>Công nghệ sử dụng:</strong> Laravel, MySQL, Tailwind CSS</p>
                    </div>
                    <div>
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="inline-block px-6 py-2.5 bg-[#f53003] text-white font-medium rounded-sm shadow hover:bg-opacity-90 transition">Vào trang quản trị</a>
                            @else
                                <a href="{{ route('login') }}" class="inline-block px-6 py-2.5 bg-[#1b1b18] dark:bg-white dark:text-black text-white font-medium rounded-sm shadow transition">Bắt đầu sử dụng</a>
                            @endauth
                        @endif
                    </div>
                </div>
                <div class="bg-[#fff2f2] dark:bg-[#1D0002] p-8 lg:w-[380px] flex flex-col justify-center items-center text-center border-l border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <div class="text-6xl mb-4">📖</div>
                    <h3 class="font-bold text-lg dark:text-white mb-2">Quản lý chuyên nghiệp</h3>
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">Nhanh chóng, minh bạch, bảo mật và dễ dàng mở rộng tính năng theo mô hình MVC.</p>
                </div>
            </main>
        </div>
    </body>
</html>