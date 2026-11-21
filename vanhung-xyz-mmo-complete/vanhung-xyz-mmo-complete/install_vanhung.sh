#!/usr/bin/env bash
set -euo pipefail

DOMAIN="vanhung.xyz"
APP_DIR="/var/www/${DOMAIN}"

echo "=== [1/5] Clone source gốc hoangtuanqn/nro-mart-core ==="
if [ ! -d "$APP_DIR" ]; then
  sudo git clone https://github.com/hoangtuanqn/nro-mart-core.git "$APP_DIR"
fi

echo "=== [2/5] Chép toàn bộ module MMO vào ${APP_DIR} ==="
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
sudo cp -r "$SCRIPT_DIR/app/"* "$APP_DIR/app/"
sudo cp -r "$SCRIPT_DIR/database/"* "$APP_DIR/database/"
sudo cp -r "$SCRIPT_DIR/resources/"* "$APP_DIR/resources/"
sudo cp "$SCRIPT_DIR/routes/web.php" "$APP_DIR/routes/web.php"
sudo cp "$SCRIPT_DIR/routes/api.php" "$APP_DIR/routes/api.php"

if [ ! -f "$APP_DIR/.env" ]; then
  sudo cp "$SCRIPT_DIR/config_server/.env.production.example" "$APP_DIR/.env"
  echo ">>> Đã tạo $APP_DIR/.env — hãy điền DB_DATABASE, DB_USERNAME, DB_PASSWORD, BANK_WEBHOOK_SECRET rồi chạy lại migration!"
fi

echo "=== [3/5] Cài dependencies & tối ưu Laravel ==="
cd "$APP_DIR"
composer install --no-dev --optimize-autoloader
php artisan key:generate --force || true
php artisan storage:link || true
sudo chown -R www-data:www-data "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
sudo chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"

echo "=== [4/5] Cấu hình Nginx cho ${DOMAIN} ==="
sudo cp "$SCRIPT_DIR/config_server/nginx-vanhung.xyz.conf" "/etc/nginx/sites-available/${DOMAIN}"
sudo ln -sf "/etc/nginx/sites-available/${DOMAIN}" "/etc/nginx/sites-enabled/${DOMAIN}"
sudo nginx -t && sudo systemctl reload nginx

echo "=== [5/5] Cài SSL HTTPS miễn phí (Certbot) ==="
echo "Chạy lệnh sau sau khi đã trỏ DNS A record của ${DOMAIN} về IP VPS:"
echo "  sudo certbot --nginx -d ${DOMAIN} -d www.${DOMAIN} --redirect"
echo "Và chạy khởi tạo dữ liệu:"
echo "  cd ${APP_DIR} && php artisan migrate --force && php artisan db:seed --class=MmoInitialSeeder"
