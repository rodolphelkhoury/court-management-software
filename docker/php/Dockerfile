# Base image
FROM php:8.2-fpm-alpine

# System dependencies
RUN apk add --no-cache \
    git curl zip unzip \
    libpng-dev libxml2-dev oniguruma-dev \
    nodejs npm bash

# PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy Laravel application
COPY . .

# Install backend dependencies
RUN composer install --no-dev --optimize-autoloader

# Install frontend dependencies and build assets
RUN npm install
RUN npm run build

# Create required Laravel directories & set permissions
RUN mkdir -p storage/framework/cache \
         storage/framework/sessions \
         storage/framework/views \
         storage/logs \
         bootstrap/cache && \
    chown -R www-data:www-data /var/www && \
    chmod -R 755 /var/www

# Fix PHP-FPM to listen on all interfaces
RUN sed -i 's/listen = 127.0.0.1:9000/listen = 0.0.0.0:9000/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/;listen.allowed_clients = 127.0.0.1/listen.allowed_clients = 0.0.0.0/' /usr/local/etc/php-fpm.d/www.conf

# E
