#!/bin/bash
set -e

echo "==> Starting Laravel container..."

# Ensure storage directories exist
echo "==> Preparing storage directories..."

mkdir -p /var/www/html/storage/app/private/evidence
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

# Fix permissions for web server
echo "==> Fixing permissions..."

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache


# 1. Clear old cache before production optimization
echo "==> Clearing Laravel cache..."
php artisan optimize:clear || true

# 2. Run database migrations
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "==> Running database migrations..."
    php artisan migrate --force
    echo "==> Database migrations completed successfully."
else
    echo "==> Database migrations skipped (RUN_MIGRATIONS=false)."
fi

# 3. Run database seeders
if [ "${RUN_SEEDERS:-false}" = "true" ]; then
    echo "==> Running database seeders..."
    php artisan db:seed --force
    echo "==> Database seeding completed successfully."
else
    echo "==> Database seeding skipped (RUN_SEEDERS=false)."
fi

# 4. Create public storage symlink if missing
if [ ! -L /var/www/html/public/storage ]; then
    echo "==> Creating storage symlink..."
    php artisan storage:link --quiet || true
fi

# 5, 6, 7. Cache Laravel for production
if [ "${APP_ENV}" = "production" ]; then
    echo "==> Optimizing Laravel cache for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    echo "==> Laravel optimization completed."
fi


# Start Apache
echo "==> Starting Apache..."

exec "$@"
