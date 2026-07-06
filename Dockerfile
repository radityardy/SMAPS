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

# Ensure storage dirs exist and are writable
RUN mkdir -p storage/logs storage/framework/{sessions,views,cache} bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# Copy & set entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["docker-entrypoint.sh"]