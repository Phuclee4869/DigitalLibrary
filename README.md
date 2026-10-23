# 📚 Digital Library Management System

Hệ thống **Quản lý Thư viện số (Digital Library Management System)** được phát triển nhằm hỗ trợ quản lý sách, tài liệu, hoạt động mượn trả và phân quyền người dùng. Dự án được xây dựng bằng PHP với Laravel Framework và sử dụng MySQL làm hệ quản trị cơ sở dữ liệu.

## 🌐 Liên kết dự án

- **GitHub Repository:** [Phuclee4869/DigitalLibrary](https://github.com/Phuclee4869/DigitalLibrary)
- **Live Demo:** [Digital Library Management System](https://quanlythuvien-laravel.free.nf/)

## ✨ Chức năng chính

- 📖 **Quản lý sách:** Theo dõi và quản lý thông tin sách, tài liệu trong thư viện.
- 🔄 **Quản lý mượn trả:** Hỗ trợ các hoạt động mượn và trả sách.
- 👥 **Quản lý người dùng:** Quản lý tài khoản người dùng trong hệ thống.
- 🔐 **Phân quyền truy cập:** Phân chia quyền hạn theo vai trò người dùng.
- 🗄️ **Quản lý dữ liệu:** Lưu trữ và truy xuất dữ liệu thông qua cơ sở dữ liệu MySQL.

## 🔑 Tài khoản Demo

Sử dụng các tài khoản dưới đây để trải nghiệm hệ thống với những vai trò khác nhau.

| Vai trò | Email đăng nhập | Mật khẩu |
|---|---|---|
| Admin | `admin@gmail.com` | `123456` |
| Thuthu | `thuthu@gmail.com` | `123456` |
| User | `user@gmail.com` | `123456` |

> **Lưu ý:** Đây là thông tin tài khoản demo do nhóm cung cấp. Vui lòng không sử dụng các mật khẩu này cho tài khoản cá nhân hoặc môi trường thực tế.

## 🛠️ Công nghệ sử dụng

| Công nghệ | Mục đích |
|---|---|
| PHP | Ngôn ngữ lập trình phía máy chủ |
| Laravel Framework | Xây dựng ứng dụng web và xử lý nghiệp vụ |
| MySQL | Quản lý cơ sở dữ liệu |
| Blade Template | Xây dựng giao diện phía máy chủ |
| Bootstrap / Tailwind CSS | Hỗ trợ thiết kế giao diện người dùng |
| InfinityFree | Nền tảng hosting triển khai bản demo |
| Git & GitHub | Quản lý mã nguồn và cộng tác nhóm |

## 👥 Thành viên nhóm phát triển

| STT | Họ và tên | Mã sinh viên | Vai trò |
|---:|---|---|---|
| 1 | Lê Hoàng Phúc | `22010137` | Nhóm trưởng |
| 2 | Nguyễn Văn Thành Hưng | `24100101` | Thành viên |
| 3 | Nguyễn Mạnh Đạt | `23010528` | Thành viên |

## 🚀 Hướng dẫn cài đặt

### 1. Yêu cầu hệ thống

- PHP tương thích với phiên bản Laravel của dự án.
- Composer.
- MySQL.
- Git.
- Các extension PHP cần thiết theo yêu cầu của Laravel.

### 2. Clone repository

```bash
git clone https://github.com/Phuclee4869/DigitalLibrary.git
```

Di chuyển vào thư mục dự án:

```bash
cd DigitalLibrary
```

### 3. Cài đặt các thư viện

```bash
composer install
```

### 4. Cấu hình môi trường

Tạo file `.env` từ file mẫu:

```bash
cp .env.example .env
```

Trên Windows, bạn cũng có thể sao chép `.env.example` và đổi tên bản sao thành `.env`.

Tạo khóa ứng dụng Laravel:

```bash
php artisan key:generate
```

### 5. Cấu hình cơ sở dữ liệu

Tạo cơ sở dữ liệu MySQL, sau đó cập nhật thông tin kết nối trong file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ten_co_so_du_lieu
DB_USERNAME=root
DB_PASSWORD=
```

Thay các giá trị mẫu bằng thông tin cơ sở dữ liệu thực tế của bạn.

### 6. Khởi tạo cơ sở dữ liệu

Nếu dự án có các migration cần chạy, sử dụng:

```bash
php artisan migrate
```

Nếu dự án cung cấp seeder và cần dữ liệu mẫu, hãy kiểm tra cấu hình trước khi chạy:

```bash
php artisan db:seed
```

**Lưu ý:** Chỉ chạy lệnh seed khi đã xác nhận nội dung seeder phù hợp với môi trường của bạn.

### 7. Chạy ứng dụng

```bash
php artisan serve
```

Mở trình duyệt và truy cập:

```text
http://127.0.0.1:8000
```

## 📁 Cấu trúc dự án

Một số thư mục và tệp quan trọng của Laravel:

```text
DigitalLibrary/
├── app/                 # Logic ứng dụng, controllers, models
├── bootstrap/           # Khởi tạo ứng dụng Laravel
├── config/              # Các tệp cấu hình
├── database/            # Migrations, factories, seeders
├── public/              # Điểm truy cập và tài nguyên công khai
├── resources/            # Views, CSS, JavaScript
├── routes/               # Định nghĩa các route
├── storage/              # Logs và dữ liệu lưu trữ
├── tests/                # Các bài kiểm thử
├── .env.example          # Mẫu cấu hình môi trường
├── artisan               # Công cụ dòng lệnh Laravel
├── composer.json         # Khai báo thư viện PHP
└── README.md             # Tài liệu dự án
```

*Cấu trúc trên là cấu trúc tham khảo phổ biến của Laravel; các thư mục và tệp thực tế có thể khác tùy theo phiên bản và cách tổ chức dự án.*

## 🔒 Bảo mật

- Không đưa thông tin nhạy cảm trong file `.env` lên GitHub.
- Không sử dụng mật khẩu demo trong môi trường production.
- Kiểm tra quyền truy cập trước khi thực hiện các thao tác quản trị.
- Sao lưu cơ sở dữ liệu định kỳ.
- Sử dụng HTTPS khi triển khai ứng dụng công khai.

## 🎯 Mục tiêu dự án

Dự án được thực hiện nhằm vận dụng kiến thức phát triển ứng dụng web, quản lý cơ sở dữ liệu và xây dựng hệ thống phân quyền. Đồng thời, dự án giúp các thành viên thực hành làm việc nhóm, quản lý mã nguồn và triển khai ứng dụng web.

## 📄 Giấy phép

Dự án được phát triển phục vụ mục đích học tập và thực hành. Nếu muốn phát hành hoặc tái sử dụng mã nguồn theo một giấy phép cụ thể, hãy bổ sung tệp `LICENSE` tương ứng.

---

<p align="center">
  <strong>Digital Library Management System</strong><br>
  Developed by Group 18 · 2026
</p>
