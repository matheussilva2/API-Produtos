#!/bin/sh
set -e

if [ ! -f /var/www/.env ]; then
    cp /var/www/.env.example /var/www/.env
fi

if [ -z "$(grep '^APP_KEY=' /var/www/.env | grep -v 'APP_KEY=$')" ]; then
    echo "Gerando chave do app..."
    php artisan key:generate --no-interaction --force
fi

if [ -z "$(grep '^APP_KEY=' /var/www/.env | grep -v 'APP_KEY=$')" ]; then
    echo "Gerando chave do JWT..."
    php artisan jwt:secret --no-interaction --force
fi

php artisan storage:link

php artisan migrate --force --no-interaction

php-fpm -D
nginx -g "daemon off;"