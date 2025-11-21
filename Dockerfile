FROM php:8.2-apache

# 1. Instalar dependencias del sistema y Postgres
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libpq-dev \
    postgresql-client \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip

# 2. Instalar Node.js y NPM (Para compilar el Frontend)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 3. Configurar Apache para Laravel
# Cambiamos la raíz del servidor de /var/www/html a /var/www/html/public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Habilitar mod_rewrite para las rutas amigables de Laravel
RUN a2enmod rewrite

# 4. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Configurar directorio de trabajo
WORKDIR /var/www/html

# 6. Copiar permisos iniciales
RUN chown -R www-data:www-data /var/www/html