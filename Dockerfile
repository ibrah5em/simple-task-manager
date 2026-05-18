# syntax=docker/dockerfile:1.6
# Multi-stage build for Laravel + nginx + php-fpm in a single image.
# BuildKit is disabled on x-server (DOCKER_BUILDKIT=0); keep this Dockerfile compatible.

# ─── Stage 1 — front-end assets (Vite) ────────────────────────────────
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund
COPY vite.config.js postcss.config.js tailwind.config.js ./
COPY resources ./resources
COPY public ./public
RUN npm run build

# ─── Stage 2 — PHP dependencies (Composer, no dev) ────────────────────
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --no-interaction \
        --no-scripts \
        --prefer-dist \
        --optimize-autoloader

# ─── Stage 3 — runtime (php-fpm + nginx + supervisord) ────────────────
FROM php:8.3-fpm-alpine AS runtime

# System deps + PHP extensions
RUN apk add --no-cache \
        nginx \
        supervisor \
        sqlite \
        sqlite-libs \
        icu-libs \
        libzip \
        libpng \
        libjpeg-turbo \
        freetype \
        oniguruma \
        tini \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        icu-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        oniguruma-dev \
        sqlite-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_sqlite \
        mbstring \
        bcmath \
        intl \
        zip \
        gd \
        opcache \
        pcntl \
    && apk del .build-deps \
    && rm -rf /var/cache/apk/*

# Config
COPY docker/php.ini          /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/nginx.conf       /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh    /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# App code (own everything as www-data; php:8.3-fpm-alpine ships this user as UID/GID 82)
WORKDIR /var/www/html
COPY --chown=www-data:www-data . .
COPY --from=vendor --chown=www-data:www-data /app/vendor   ./vendor
COPY --from=assets --chown=www-data:www-data /app/public/build ./public/build

# Wipe stale bootstrap caches — local devs may have indexed Pail / Breeze (require-dev)
# into bootstrap/cache/packages.php; entrypoint regenerates them.
RUN rm -f bootstrap/cache/*.php

# Writable dirs (storage + bootstrap/cache + database for SQLite)
RUN mkdir -p storage/framework/{cache,sessions,views} storage/logs database bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache database \
    && chmod -R ug+rwX storage bootstrap/cache database

# nginx + php-fpm runtime dirs
RUN mkdir -p /run/nginx /var/log/supervisor \
    && chown -R www-data:www-data /run/nginx

EXPOSE 80
ENTRYPOINT ["/sbin/tini", "--", "/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
