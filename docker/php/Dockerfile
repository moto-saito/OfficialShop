# PHP 8.2系の最新パッチ版を明示指定（フローティングタグ回避でバージョンを固定管理）。
# 2026-10-21 緊急セキュリティリリース CVE-2026-109618 / CVE-2026-109619（RCE, CVSS 9.2, 通称"PHP2Shell"）対応。
# 修正版は 8.2.35 だが本ビルド時点では公式Dockerイメージ未公開のため、
# 現時点で入手可能な最新パッチ版 8.2.34 を使用し、php.ini側で暫定緩和策を適用している。
# 8.2.35-apache が Docker Hub に公開されたら、下記タグを 8.2.35-apache に変更して再ビルドすること。
FROM php:8.2.34-apache

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

RUN a2enmod rewrite headers

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY apache/000-default.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
