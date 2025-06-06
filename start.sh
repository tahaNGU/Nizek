#!/bin/sh

if [ -d /var/www/html/storage ] && [ -d /var/www/html/bootstrap/cache ]; then
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
fi

echo "Starting Laravel queue worker..."
php artisan queue:work --tries=3 &

echo "Starting php-fpm..."
php-fpm
