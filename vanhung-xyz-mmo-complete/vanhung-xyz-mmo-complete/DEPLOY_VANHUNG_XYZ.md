# Hướng dẫn triển khai Production cho tên miền `https://vanhung.xyz`

Bộ tệp này cấu hình sẵn cho tên miền **vanhung.xyz** ghép trên nền Laravel 10 [hoangtuanqn/nro-mart-core](https://github.com/hoangtuanqn/nro-mart-core), bổ sung **Nhập kho tài nguyên thật hàng loạt (`mmo_product_items`)**, **Nginx + SSL HTTPS**, và **AdminMiddleware** bảo mật.

## Bước 1: Trỏ DNS tên miền `vanhung.xyz`
Tại nhà cung cấp tên miền (Cloudflare / nhà đăng ký):
- Bản ghi `A` | Tên: `@` | Giá trị: `<IP_VPS_CUA_BAN>`
- Bản ghi `A` | Tên: `www` | Giá trị: `<IP_VPS_CUA_BAN>`

## Bước 2: Clone source gốc & chép đè bộ mã MMO
```bash
cd /var/www
git clone https://github.com/hoangtuanqn/nro-mart-core.git vanhung.xyz
cd /var/www/vanhung.xyz
# Giải nén gói vanhung-xyz-mmo-deploy.zip và chép các thư mục app/, database/, resources/, routes/ vào /var/www/vanhung.xyz/
composer install --no-dev --optimize-autoloader
npm install && npm run build
```

## Bước 3: Cấu hình `.env` Production (tắt Debug, ép HTTPS)
Sao chép `config_server/.env.production.example` thành `.env`, điền thông tin MySQL thật rồi chạy:
```bash
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache && php artisan route:cache && php artisan view:cache
chown -R www-data:www-data /var/www/vanhung.xyz/storage /var/www/vanhung.xyz/bootstrap/cache
chmod -R 775 /var/www/vanhung.xyz/storage /var/www/vanhung.xyz/bootstrap/cache
```

## Bước 4: Cấu hình Nginx trỏ vào `public/` & bật SSL Let's Encrypt
```bash
sudo cp config_server/nginx-vanhung.xyz.conf /etc/nginx/sites-available/vanhung.xyz
sudo ln -s /etc/nginx/sites-available/vanhung.xyz /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
sudo certbot --nginx -d vanhung.xyz -d www.vanhung.xyz --redirect
```

## Bước 5: Nhập kho tài nguyên thật (`mmo_product_items`)
1. Đăng nhập tài khoản có `role = 'admin'` tại `https://vanhung.xyz/admin/inventory/import`.
2. Chọn sản phẩm MMO, dán danh sách tài khoản thật (`UID|Pass|2FA|Mail...`, mỗi dòng 1 tài khoản) hoặc tải lên file `.txt`.
3. Hệ thống tự động lọc trùng lặp, mã hóa/lưu vào bảng `mmo_product_items` với trạng thái `available` và xuất kho tự động bằng `lockForUpdate()` ngay khi khách thanh toán.
