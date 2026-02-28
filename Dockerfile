FROM php:8.1-fpm-alpine

# Instalar dependencias del sistema PRIMERO
RUN apk add --no-cache \
    build-base \
    postgresql-client \
    postgresql-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    nginx \
    supervisor \
    bash \
    oniguruma-dev

# Instalar extensiones PHP
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    zip \
    bcmath \
    ctype \
    mbstring \
    tokenizer \
    xml

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar archivos del proyecto
WORKDIR /app
COPY . .

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Crear directorios necesarios
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data /app

# Copiar configuración Nginx
COPY nginx.conf /etc/nginx/nginx.conf
COPY default.conf /etc/nginx/conf.d/default.conf

# Copiar configuración Supervisor
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Crear directorio de logs
RUN mkdir -p /var/log/supervisor

# Exponer puerto
EXPOSE 10000

# Generar clave de aplicación
RUN php artisan key:generate --force || true

# Comando de inicio
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
