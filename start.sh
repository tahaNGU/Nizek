#!/bin/sh

if [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if [ -d storage ] && [ -d bootstrap/cache ]; then
    chown -R www-data:www-data storage bootstrap/cache
    chmod -R 775 storage bootstrap/cache
fi

php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan config:cache

php-fpm -F
