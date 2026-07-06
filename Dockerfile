FROM php:8.4-cli

# Install system deps
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    sqlite3 \
    libsqlite3-dev \
    dos2unix \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo pdo_sqlite zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy dependency files first (layer cache)
COPY composer.json composer.lock ./

RUN composer install --no-scripts --no-autoloader --prefer-dist

# Copy full source
COPY . .

RUN composer dump-autoload --optimize

# Ensure storage dirs exist and are writable
RUN mkdir -p storage/logs \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/framework/cache \
    && mkdir -p bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# Copy & set entrypoint with CRLF conversion (dos2unix)
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN dos2unix /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8069

ENTRYPOINT ["docker-entrypoint.sh"]