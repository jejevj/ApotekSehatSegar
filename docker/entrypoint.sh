#!/bin/sh

set -e

mkdir -p /var/log/supervisor
mkdir -p /var/run

echo "Starting Laravel application..."

if [ ! -f .env ]; then
    echo "Creating .env file from example..."
    cp .env.example .env
fi

if [ -z "$(grep '^APP_KEY=' .env | cut -d'=' -f2)" ] || [ "$(grep '^APP_KEY=' .env | cut -d'=' -f2)" = "" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

echo "Running database migrations..."
php artisan migrate --force || true

echo "Optimizing application..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "Creating storage link..."
php artisan storage:link || true

echo "Starting services..."
exec "$@"