#!/bin/sh
set -e

cd /var/www

# Crea il symlink storage se non esiste ancora
if [ ! -L public/storage ]; then
    php artisan storage:link --quiet || true
fi

# Permessi storage (il volume montato sovrascrive quelli impostati nel Dockerfile)
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

exec "${@:-php-fpm}"
