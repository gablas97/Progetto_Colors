#!/bin/sh
set -e

cd /var/www

# Crea il symlink storage se non esiste ancora
if [ ! -L public/storage ]; then
    php artisan storage:link --quiet || true
fi

exec php-fpm
