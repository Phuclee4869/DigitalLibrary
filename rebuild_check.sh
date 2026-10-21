#!/usr/bin/env bash
# Ban Linux/macOS/Codespaces cua phep thu dung lai (Buoi 10 - V3). Chay tai thu muc goc du an.
set -u
LOG=rebuild_log.txt; : > "$LOG"; T0=$(date +%s); FAIL=0
step(){ local id="$1" name="$2"; shift 2; local s=$(date +%s); echo "=== [$id] $name" | tee -a "$LOG"
  if "$@" >>"$LOG" 2>&1; then r=DAT; else r=LOI; FAIL=$((FAIL+1)); fi
  echo "    -> $r ($(( $(date +%s)-s )) giay)" | tee -a "$LOG"; }
step B1 "Danh muc tep" bash -c 'for f in artisan composer.json composer.lock .env.example routes/api.php bootstrap/app.php database/seeders/BorrowFlowSeeder.php; do test -e "$f" || { echo THIEU $f; exit 1; }; done'
step B2 "PHP/Composer/SQLite" bash -c 'php -v && composer -V && php -m | grep -qi pdo_sqlite'
step B3 "composer install" composer install --no-interaction
step B4 "Tao .env, APP_KEY" bash -c '[ -f .env ] || cp .env.example .env; php artisan key:generate --force'
step B5 "Tao SQLite va migrate" bash -c 'touch database/database.sqlite; php artisan migrate --force && php artisan migrate:status | grep -q personal_access_tokens'
step B6 "Nap du lieu mau" bash -c 'php artisan db:seed --class=BorrowFlowSeeder --force && php artisan db:seed --class=SearchDemoSeeder --force'
step B7 "php artisan test" php artisan test --filter="CatalogCacheTest|SearchPaginationTest"
echo "Tong: $(( ($(date +%s)-T0)/60 )) phut; so buoc loi: $FAIL (xem $LOG). Buoc B8-B10 (serve, dang nhap, luong nghiep vu): lam bang tay hoac dung rebuild_check.ps1." | tee -a "$LOG"
