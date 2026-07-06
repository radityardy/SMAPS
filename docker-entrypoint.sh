#!/bin/bash
set -e

# Create .env from example if not mounted
if [ ! -f ".env" ]; then
    cp .env.docker .env
fi

# Generate app key if not set
php artisan key:generate --no-interaction --force 2>/dev/null || true

# Create SQLite DB file if missing
DB_PATH=$(php artisan config:show database.connections.sqlite.database 2>/dev/null | awk '{print $NF}')
if [ -n "$DB_PATH" ] && [ ! -f "$DB_PATH" ]; then
    touch "$DB_PATH"
    echo "Created SQLite database: $DB_PATH"
fi

# Run migrations
php artisan migrate --no-interaction --force

# Seed only if users table is empty
COUNT=$(php artisan tinker --execute "echo \App\Models\User::count();" 2>/dev/null | tail -1)
if [ "$COUNT" = "0" ]; then
    echo "Seeding database..."
    php artisan db:seed --no-interaction --force
fi

# Clear caches
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true

echo "SMAPS API running at http://0.0.0.0:8000"
exec php artisan serve --host=0.0.0.0 --port=8000