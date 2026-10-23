#!/bin/bash
set -e
echo "=== BẮT ĐẦU TRIỂN KHAI HỆ THỐNG THƯ VIỆN SỐ (BUỔI 08) ==="

# 0. Chặn phát hành nếu cấu hình chưa an toàn
grep -q '^APP_ENV=production' .env || { echo "[LỖI] APP_ENV phải là production"; exit 1; }
grep -q '^APP_DEBUG=false'    .env || { echo "[LỖI] APP_DEBUG phải là false"; exit 1; }

# 1. Tạo tệp database.sqlite nếu chưa có
if [ ! -f "database.sqlite" ]; then
  touch database.sqlite
  echo "[OK] Đã khởi tạo tệp database.sqlite"
fi

# 2. Cập nhật mã nguồn mới nhất
git pull origin main

# 3. Cài gói phụ thuộc cho Production
composer install --optimize-autoloader --no-dev

# 4. Xóa bộ nhớ tạm cấu hình
php artisan config:clear

# 5. Cập nhật CSDL (gồm chỉ mục Buổi 07) và nạp dữ liệu biên
php artisan migrate --force
php artisan db:seed --class=ExtendedDatasetSeeder --force

# 6. Tối ưu bộ nhớ tạm
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Tự kiểm tra thẻ tiêu đề bảo mật (máy chủ phải đang chạy ở cổng 8000)
URL="${CHECK_URL:-http://127.0.0.1:8000}"
curl -sI "$URL/" | grep -iE 'x-content-type-options|x-frame-options|referrer-policy|content-security-policy|permissions-policy' \
  || echo "[CẢNH BÁO] Chưa thấy thẻ tiêu đề bảo mật"

echo "=== TRIỂN KHAI THÀNH CÔNG PHIÊN BẢN BUỔI 08 ==="