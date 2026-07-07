# Stage 1 — node-builder
FROM node:18-alpine AS node-builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2 — production
FROM php:8.2-fpm-alpine
RUN apk add --no-cache bash curl libpng-dev libjpeg-turbo-dev libwebp-dev freetype-dev \
    libzip-dev zip unzip git supervisor postgresql-dev oniguruma-dev icu-dev shadow

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install pdo pdo_pgsql pgsql gd zip bcmath opcache intl pcntl mbstring

# Install Redis
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Create non-root user
RUN groupadd -g 1000 appgroup && \
    useradd -u 1000 -g appgroup -m -s /bin/bash appuser

WORKDIR /var/www/html

# Copy source
COPY --chown=appuser:appgroup . .

# Copy compiled assets
COPY --from=node-builder --chown=appuser:appgroup /app/public/build ./public/build

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Permissions
RUN chmod 775 -R storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache && \
    chown -R appuser:appgroup storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache

# Copy configs
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

USER appuser
EXPOSE 9000

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
