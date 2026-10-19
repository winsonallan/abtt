ARG PHP_VERSION=8.5

# 1. PHP dependencies (Composer)
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist \
        --no-interaction --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --no-dev --optimize --no-scripts --ignore-platform-reqs

# 2. Front-end assets (Vite + Tailwind + Chart.js)
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json* .npmrc* ./
RUN npm ci || npm install
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public
RUN npm run build

# 3. PHP-FPM runtime
FROM php:${PHP_VERSION}-fpm-alpine AS app
RUN docker-php-ext-install -j"$(nproc)" pdo_mysql \
    && if [ "$(php -r 'echo PHP_VERSION_ID;')" -lt 80500 ]; then docker-php-ext-install opcache; fi
COPY docker/php/custom.ini $PHP_INI_DIR/conf.d/custom.ini

WORKDIR /var/www/html
COPY --from=vendor /app ./
COPY --from=assets /app/public ./public
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]

# 4. Nginx
FROM nginx:1.27-alpine AS nginx
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=app /var/www/html/public /var/www/html/public
