#!/bin/sh
set -e

# Wait for DB migrations
echo "Running migrations..."
php artisan migrate --force

echo "Caching configuration and routes..."
php artisan config:clear
php artisan route:clear
php artisan config:cache
php artisan route:cache

echo "Starting Laravel server..."
exec php artisan serve --host=0.0.0.0 --port=8000
