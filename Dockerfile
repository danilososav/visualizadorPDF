FROM php:8.1-apache

RUN apt-get update && apt-get install -y \
    postgresql-client \
    libpq-dev \
    libzip-dev \
    git \
    curl \
    unzip \
    && apt-get clean

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_pgsql \
    zip \
    bcmath \
    ctype \
    tokenizer \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction 2>&1 | head -20

RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions \
    && chmod -R 755 storage bootstrap/cache

EXPOSE 80

RUN php artisan key:generate --force || true

CMD ["apache2-foreground"]
