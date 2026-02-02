#!/bin/sh
set -e  # Stop jika ada perintah yang gagal

echo "Running application setup..."

echo "Installing composer dependencies..."
composer install

echo "Installing yarn dependencies..."
yarn install

echo "Running migrations..."
php /var/www/html/vendor/bin/phoenix migrate

echo "Symlink node_modules ke public/assets/node_modules..."
NPM_LINK="/var/www/html/public/assets/node_modules"
[ -e "$NPM_LINK" ] && rm -rf "$NPM_LINK"
[ -d /var/www/html/node_modules ] && ln -s /var/www/html/node_modules "$NPM_LINK"

# Izin tulis untuk CodeIgniter (cache, logs, writable)
mkdir -p /var/www/html/application/cache /var/www/html/application/logs /var/www/html/log
chmod -R 777 /var/www/html/application/cache /var/www/html/application/logs /var/www/html/log
[ -d /var/www/html/writable ] && chmod -R 777 /var/www/html/writable

echo "Starting supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
