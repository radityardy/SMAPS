#!/bin/bash
set -e

cd /var/www/html

# Create .env from docker template if not present
if [ ! -f ".env" ]; then
    cp .env.docker .env
fi

# Generate app key if empty
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    php artisan key:generate --no-interaction --force
fi

# Create SQLite DB file if missing
DB_FILE="/var/www/html/database/database.sqlite"
if [ ! -f "$DB_FILE" ]; then
    touch "$DB_FILE"
    echo "[SMAPS] Created SQLite database."
fi

# Run migrations
echo "[SMAPS] Running migrations..."
php artisan migrate --no-interaction --force

# Seed if users table is empty
USER_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null | grep -E '^[0-9]+$' | head -1)
if [ -z "$USER_COUNT" ] || [ "$USER_COUNT" = "0" ]; then
    echo "[SMAPS] Seeding database..."
    php artisan db:seed --no-interaction --force
fi

# Clear caches
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

echo "[SMAPS] API running at http://0.0.0.0:8069"
exec php artisan serve --host=0.0.0.0 --port=8069