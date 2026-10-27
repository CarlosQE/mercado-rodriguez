#!/bin/sh
set -eu

: "${APP_KEY:?Configure APP_KEY before starting the service}"
: "${APP_URL:?Configure the public HTTPS URL in APP_URL}"

php artisan config:cache
if [ "${INITIALIZE_DEMO:-false}" = "true" ]; then
    php artisan deploy:demo
fi
if [ ! -L public/storage ]; then
    php artisan storage:link
fi
php artisan view:cache
chown -R www-data:www-data storage bootstrap/cache
exec apache2-foreground
