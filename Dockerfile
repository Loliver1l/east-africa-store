# ============================================================
# EAST AFRICA STORE - Laravel Production Dockerfile
# ============================================================

# ------------------------------------------------------------
# 1. Composer dependencies
# ------------------------------------------------------------
FROM composer:2.8 AS composer

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

COPY . .

RUN composer dump-autoload \
    --no-dev \
    --optimize \
    --classmap-authoritative


# ------------------------------------------------------------
# 2. Frontend build
# ------------------------------------------------------------
FROM node:22-bookworm AS frontend

WORKDIR /app

COPY package*.json ./

RUN npm ci

COPY . .

RUN npm run build


# ------------------------------------------------------------
# 3. Production PHP image
# ------------------------------------------------------------
FROM php:8.3-fpm-bookworm

WORKDIR /var/www/html

ENV APP_ENV=production
ENV APP_DEBUG=false

# System packages
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    unzip \
    curl \
    gettext-base \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        mbstring \
        bcmath \
        intl \
        exif \
        pcntl \
        zip \
        gd \
        opcache

# PHP production settings
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY docker/php.ini /usr/local/etc/php/conf.d/99-store.ini

# Application
COPY --from=composer /app /var/www/html

# Built frontend assets
COPY --from=frontend /app/public/build /var/www/html/public/build

# Nginx configuration
COPY docker/nginx.conf /etc/nginx/templates/default.conf.template

# Supervisor configuration
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Startup script
COPY docker/start.sh /usr/local/bin/start.sh

RUN chmod +x /usr/local/bin/start.sh

# Laravel permissions
RUN mkdir -p \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data \
        storage \
        bootstrap/cache \
        public

EXPOSE 10000

CMD ["/usr/local/bin/start.sh"]
