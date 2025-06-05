#!/bin/sh

# تنظیم دسترسی‌ها
if [ -d /var/www/html/storage ] && [ -d /var/www/html/bootstrap/cache ]; then
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
fi

# اجرای queue worker در پس‌زمینه
echo "Starting Laravel queue worker..."
php artisan queue:work --tries=3 &

# اجرای php-fpm در جلوی برنامه
echo "Starting php-fpm..."
php-fpm
