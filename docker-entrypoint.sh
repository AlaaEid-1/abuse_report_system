#!/bin/bash
set -e

# Ensure storage directories exist
mkdir -p /var/www/html/storage/app/private/evidence
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs

# Fix permissions for web server
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Create public storage symlink if missing
if [ ! -L /var/www/html/public/storage ]; then
    php artisan storage:link --quiet || true
fi

# Run database migrations safely if DB connection is configured
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "==> Running database migrations..."
    php artisan migrate --force || echo "==> Migration skipped or database connection not ready."
fi

# Cache configuration, routes, and views if in production mode
if [ "${APP_ENV}" = "production" ]; then
    echo "==> Optimizing Laravel cache for production..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# Execute the primary container command (default: apache2-foreground)
exec "$@"
