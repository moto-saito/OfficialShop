# PHP 8.2系の最新パッチ版を明示指定（フローティングタグ回避でバージョンを固定管理）。
# 8.2.35 = 2026-10-21 セキュリティリリース（CVE-2026-109618 / CVE-2026-109619 修正済み）。
FROM php:8.2.35-apache

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libpq-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

RUN a2enmod rewrite headers

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY apache/000-default.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
