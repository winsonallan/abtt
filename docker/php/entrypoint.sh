#!/bin/sh
set -e
cd /var/www/html

php artisan package:discover --ansi

if [ "${APP_ENV}" != "local" ]; then
    chown -R www-data:www-data storage bootstrap/cache
fi

exec docker-php-entrypoint "$@"
