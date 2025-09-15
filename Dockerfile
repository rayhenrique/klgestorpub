# Multi-stage build for KL Gestor Pub Laravel Application
FROM php:8.2-fpm as base

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    cron \
    sudo \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    xml

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Create application user
RUN groupadd -g 1000 www && useradd -u 1000 -ms /bin/bash -g www www

# Copy application files
COPY --chown=www:www . /var/www/html

# Set proper permissions
RUN chown -R www:www /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node dependencies and build assets
RUN npm install && npm run build

# Copy configuration files
COPY deployment/docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY deployment/docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY deployment/docker/nginx/default.conf /etc/nginx/sites-available/default
COPY deployment/docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create Laravel caches
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Expose port
EXPOSE 80

# Create log directories, nginx directories, and PHP session directories
RUN mkdir -p /var/log/supervisor /var/log/nginx /var/run /run/nginx /var/lib/nginx/body /var/lib/nginx/fastcgi /var/lib/nginx/proxy /var/lib/nginx/scgi /var/lib/nginx/uwsgi /var/lib/php/sessions /var/lib/php/wsdlcache \
    && chown -R www:www /var/lib/nginx /var/log/nginx /run/nginx /var/lib/php \
    && chmod -R 755 /var/lib/nginx /var/log/nginx /run/nginx \
    && chmod -R 775 /var/lib/php

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:80/health || exit 1

# Start services using supervisord
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

# Development stage
FROM base as development

# Switch back to root for development setup
USER root

# Install development dependencies
RUN composer install --optimize-autoloader --no-interaction

# Install development Node packages
RUN npm install

# Enable Xdebug for development
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Copy Xdebug configuration
COPY deployment/docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Production stage
FROM base as production

# Additional production optimizations
RUN php artisan optimize