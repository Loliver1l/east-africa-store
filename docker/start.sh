#!/bin/sh

set -e

echo "======================================"
echo " East Africa Store - Starting Laravel"
echo "======================================"

cd /var/www/html

# ------------------------------------------------------------
# Make sure Laravel directories exist
# ------------------------------------------------------------

mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache

# ------------------------------------------------------------
# Generate storage symlink
# ------------------------------------------------------------

if [ ! -L public/storage ]; then
    php artisan storage:link || true
fi

# ------------------------------------------------------------
# Wait for database
# ------------------------------------------------------------

echo "Waiting for database..."

MAX_TRIES=30
COUNT=0

until php artisan db:show >/dev/null 2>&1; do
    COUNT=$((COUNT + 1))

    if [ "$COUNT" -ge "$MAX_TRIES" ]; then
        echo "Database connection failed."
        exit 1
    fi

    echo "Database not ready. Retrying..."
    sleep 2
done

echo "Database connection OK."

# ------------------------------------------------------------
# Run migrations
# ------------------------------------------------------------

echo "Running database migrations..."

php artisan migrate --force

# ------------------------------------------------------------
# Laravel optimization
# ------------------------------------------------------------

echo "Optimizing Laravel..."

php artisan config:cache
php artisan route:cache
php artisan view:cache

# ------------------------------------------------------------
# Start Supervisor
# ------------------------------------------------------------

echo "Starting PHP-FPM + Nginx..."

exec /usr/bin/supervisord \
    -c /etc/supervisor/conf.d/supervisord.conf
