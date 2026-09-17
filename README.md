# 📚 Hệ thống Quản lý Thư viện Số (Digital Library)

Đồ án môn học Phát triển Ứng dụng Web - Xây dựng hệ thống quản lý thư viện trực tuyến bằng Laravel Framework.

---

## 👥 1. Thông tin Nhóm & Phân công nhiệm vụ (Phiếu khởi động nhóm)
* **Mã đề tài:** Digital Library Management System
* **Nhóm phát triển:** Nhóm phần mềm
* **Phân công thành viên:**
  * **V1 (Lead / Architecture):** Quản lý chung kiến trúc hệ thống, cấu hình Git, phân nhánh và kiểm soát mã nguồn.
  * **V2 (Database):** Thiết kế cơ sở dữ liệu MySQL, xây dựng các Migration, Seeder và quản lý truy vấn.
  * **V3 (Business Logic):** Phát triển các tính năng nghiệp vụ chính (quản lý sách, độc giả, mượn trả).
  * **V4 (Security):** Xử lý phân quyền người dùng, bảo mật biểu mẫu, kiểm tra lỗi đầu vào.
  * **V5 (Deployment):** Cấu hình môi trường triển khai, xuất bản trang chào qua HTTPS và quản lý minh chứng.

---

## 🛠 2. Bảng phiên bản chuẩn của nhóm
Toàn bộ thành viên trong nhóm bắt buộc phải cấu hình đồng bộ môi trường phát triển theo thông số sau:

| Thành phần hệ thống | Phiên bản chuẩn bắt buộc |
| :--- | :--- |
| **PHP** | v8.2+ |
| **Composer** | v2.x |
| **Laravel Framework** | v11.x |
| **Database** | MySQL (thông qua Laragon) |
| **Node.js / NPM** | v18+ / v10+ |

---

## 🚀 3. Hướng dẫn cài đặt cho thành viên

Thực hiện lần lượt các lệnh sau tại terminal để chạy dự án trên máy cá nhân:

1. Clone repository về máy:
   ```bash
   git clone [https://github.com/Phueclee4869/DigitalLibrary.git](https://github.com/Phueclee4869/DigitalLibrary.git)
   cd DigitalLibrary