#!/bin/sh

set -e

mkdir -p /var/log/supervisor
mkdir -p /var/run

echo "Starting Laravel application..."

if [ ! -f .env ]; then
    echo "Creating .env file from example..."
    cp .env.example .env
fi

# Force regenerate APP_KEY if not set or empty
if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
    echo "Generating application key..."
    sed -i 's/^APP_KEY=.*/APP_KEY=/' .env
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