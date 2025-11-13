# Laravel utility image (CLI) with Composer to run artisan tasks
FROM php:8.2-cli

# System deps for PHP extensions and Composer
RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip libzip-dev libpng-dev libonig-dev \
    && docker-php-ext-install pdo pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# If a composer.json exists at build time, try to install vendors (optional)
RUN set -eux; if [ -f composer.json ]; then composer install --no-interaction --prefer-dist --no-progress || true; fi

EXPOSE 8000
