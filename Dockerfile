FROM php:8.1-fpm-alpine

# Instalar dependencias
RUN apk add --no-cache \
    libpq \
    postgresql-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    nginx \
    supervisor \
    bash

# Instalar extensiones PHP
RUN docker-php-ext-install -j$(nproc) \
    pdo_pgsql \
    zip \
    bcmath \
    ctype \
    mbstring \
    tokenizer

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views \
    && chmod -R 775 storage bootstrap/cache

COPY nginx.conf /etc/nginx/nginx.conf
COPY default.conf /etc/nginx/conf.d/default.conf
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN mkdir -p /var/log/supervisor

EXPOSE 10000

RUN php artisan key:generate --force || true

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
