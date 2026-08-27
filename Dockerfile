# ---------- 1. フロントエンド資産のビルド ----------
FROM node:20-bookworm-slim AS assets
WORKDIR /app
COPY backend/package.json ./
RUN npm install
COPY backend/ .
RUN npm run build

# ---------- 2. 本番アプリイメージ ----------
FROM php:8.2-apache AS app

RUN apt-get update && apt-get install -y --no-install-recommends \
        git curl zip unzip \
        libpng-dev libonig-dev libxml2-dev libzip-dev libsqlite3-dev \
    && docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd zip \
    && a2enmod rewrite headers \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY docker/prod/php.ini   /usr/local/etc/php/conf.d/custom.ini
COPY docker/prod/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/prod/ports.conf /etc/apache2/ports.conf

WORKDIR /var/www/html

COPY backend/ .
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# ビルド済みの JS/CSS を取り込む
COPY --from=assets /app/public/build ./public/build

RUN chown -R www-data:www-data storage bootstrap/cache database \
    && chmod -R 775 storage bootstrap/cache

COPY docker/prod/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

CMD ["start.sh"]
