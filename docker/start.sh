#!/bin/sh
set -eu

cd /var/www/html

if [ -z "${APP_KEY:-}" ]; then
    echo "APP_KEY is missing. Set it in the Render environment variables."
    exit 1
fi

php artisan config:clear
php artisan migrate --force
php artisan db:seed --class=DatabaseSeeder --force

if [ -n "${INITIAL_ADMIN_USERNAME:-}" ]; then
    php artisan db:seed --class=AdminSeeder --force
fi

php artisan config:cache

exec /usr/bin/supervisord -c /etc/supervisord.conf

