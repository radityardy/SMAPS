FROM php:8.3-cli-alpine

# Install system deps
RUN apk add --no-cache \
    bash \
    git \
    unzip \
    sqlite \
    sqlite-dev \
    libzip-dev \
    oniguruma-dev \
    && docker-php-ext-install pdo pdo_sqlite zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy dependency files first (layer cache)
COPY composer.json composer.lock ./

RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy full source
COPY . .

RUN composer dump-autoload --optimize

# Ensure storage dirs exist and are writable (no brace expansion - use sh-safe syntax)
RUN mkdir -p storage/logs \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/framework/cache \
    && mkdir -p bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# Copy & set entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8069

ENTRYPOINT ["docker-entrypoint.sh"]