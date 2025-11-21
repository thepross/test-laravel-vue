#!/bin/bash

# Instalar dependencias PHP
if [ ! -f "vendor/autoload.php" ]; then
    composer install --no-progress --no-interaction
fi

# Instalar y compilar Frontend
if [ ! -d "node_modules" ]; then
    npm install
fi
npm run build

# Comandos de Laravel
php artisan key:generate
php artisan migrate --seed --force
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Permisos (Ajustados para la ruta de Apache /var/www/html)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# INICIAR APACHE (Cambio principal)
apache2-foreground