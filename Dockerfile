FROM php:8.1-apache

RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    git \
    curl \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql zip bcmath ctype tokenizer mbstring \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions \
    && chmod -R 775 storage bootstrap/cache \
    && a2enmod rewrite

COPY .htaccess /app/public/.htaccess

EXPOSE 80

RUN php artisan key:generate --force || true

CMD ["apache2-foreground"]
