FROM php:8.2-fpm

# Install PHP extensions required for Laravel
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       libzip-dev libpng-dev libonig-dev zip unzip \
    && docker-php-ext-install pdo pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
