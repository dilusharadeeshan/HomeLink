#!/bin/sh
set -eu
cd /var/www/html
mkdir -p /var/lib/homelink /var/lib/homelink-db storage/app/runtime storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
if [ -z "${APP_KEY:-}" ]; then
    if [ "${APP_ENV:-production}" = "production" ]; then
        echo "Provide APP_KEY through your production secret configuration." >&2
        exit 1
    fi
    if [ ! -s storage/app/runtime/app-key ]; then
        umask 077
        php -r 'echo "base64:".base64_encode(random_bytes(32));' > storage/app/runtime/app-key
    fi
    APP_KEY="$(cat storage/app/runtime/app-key)"
    export APP_KEY
fi
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    database_path="${DB_DATABASE:-/var/lib/homelink-db/database.sqlite}"
    mkdir -p "$(dirname "$database_path")"
    touch "$database_path"
fi
php artisan config:clear
php artisan migrate --force --no-interaction
if [ "${DEMO_SEED:-false}" = "true" ] && [ "${APP_ENV:-production}" = "local" ]; then
    php artisan db:seed --force --no-interaction
fi
php artisan optimize
chown -R www-data:www-data storage bootstrap/cache /var/lib/homelink /var/lib/homelink-db
exec docker-php-entrypoint "$@"
