#!/bin/bash
set -e

echo "Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader

if [ ! -f .env ]; then
    echo "Creating .env from .env.example..."
    cp .env.example .env
fi

if ! grep -q "^APP_KEY=base64:" .env; then
    echo "Generating application key..."
    php artisan key:generate
fi

echo "Waiting for MySQL..."
until php artisan db:monitor --databases=mysql > /dev/null 2>&1; do
    sleep 1
done

echo "Running migrations..."
php artisan migrate --force

echo "Seeding database..."
php artisan db:seed --force

echo "Setup complete!"

exec php-fpm
