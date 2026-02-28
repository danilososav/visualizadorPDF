#!/bin/bash
set -e

echo "Instalando dependencias..."
composer install --no-dev --optimize-autoloader

echo "Generando APP_KEY..."
php artisan key:generate --force

echo "Ejecutando migraciones..."
php artisan migrate --force

echo "Limpiando caché..."
php artisan cache:clear
php artisan config:clear

echo "Build completado!"
