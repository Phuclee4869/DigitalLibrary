#!/bin/bash
echo "=== BẮT ĐẦU TRIỂN KHAI HỆ THỐNG THƯ VIỆN SỐ (BUỔI 07) ==="

# 1. Tạo tệp database.sqlite nếu chưa có tại thư mục gốc
if [ ! -f "database.sqlite" ]; then
    touch database.sqlite
    echo "[OK] Đã khởi tạo tệp database.sqlite"
fi

# 2. Cập nhật mã nguồn mới nhất từ kho chứa
git pull origin main

# 3. Cài đặt các gói phụ thuộc Production
composer install --optimize-autoloader --no-dev

# 4. Xóa sạch bộ nhớ tạm cấu hình
php artisan config:clear

# 5. Cập nhật CSDL (bao gồm các Index mới của Buổi 7) và nạp dữ liệu biến tự động
php artisan migrate --force
php artisan db:seed --class=ExtendedDatasetSeeder --force

# 6. Tối ưu bộ nhớ tạm cho cấu hình, route và view
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== TRIỂN KHAI THÀNH CÔNG PHIÊN BẢN BUỔI 07 ==="