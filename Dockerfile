# Use lightweight PHP-FPM Alpine base image
FROM php:8.2-fpm-alpine AS backend

# Set working directory
WORKDIR /var/www/html

# Install system dependencies and PHP extensions
RUN apk update && apk add --no-cache \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    libzip-dev \
    nginx \
    supervisor \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_mysql gd zip \
    && rm -rf /var/cache/apk/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Manually download and install Faker for seeders
RUN mkdir -p /tmp/faker && \
    cd /tmp/faker && \
    composer require fakerphp/faker --no-interaction && \
    cp -r /tmp/faker/vendor/fakerphp /var/www/html/vendor/ && \
    rm -rf /tmp/faker

# Install Node dependencies and build frontend assets
RUN npm install && npm run build

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Configure Nginx
RUN printf 'server {\n\
    listen 80;\n\
    server_name _;\n\
    root /var/www/html/public;\n\
    index index.php;\n\
\n\
    location / {\n\
        try_files $uri $uri/ /index.php?$query_string;\n\
    }\n\
\n\
    location ~ \\.php$ {\n\
        fastcgi_pass 127.0.0.1:9000;\n\
        fastcgi_index index.php;\n\
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;\n\
        include fastcgi_params;\n\
    }\n\
}\n' > /etc/nginx/http.d/default.conf

# Configure Supervisor
RUN printf '[supervisord]\n\
nodaemon=true\n\
user=root\n\
\n\
[program:php-fpm]\n\
command=php-fpm\n\
autostart=true\n\
autorestart=true\n\
stdout_logfile=/dev/stdout\n\
stdout_logfile_maxbytes=0\n\
stderr_logfile=/dev/stderr\n\
stderr_logfile_maxbytes=0\n\
\n\
[program:nginx]\n\
command=nginx -g "daemon off;"\n\
autostart=true\n\
autorestart=true\n\
stdout_logfile=/dev/stdout\n\
stdout_logfile_maxbytes=0\n\
stderr_logfile=/dev/stderr\n\
stderr_logfile_maxbytes=0\n' > /etc/supervisord.conf

# Expose port
EXPOSE 80

# Run supervisor to manage PHP-FPM and Nginx
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
