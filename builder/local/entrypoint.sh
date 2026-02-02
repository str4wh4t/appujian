#!/bin/sh
set -e  # Stop jika ada perintah yang gagal

echo "Running application setup..."

echo "Installing composer dependencies..."
composer install

echo "Installing yarn dependencies..."
yarn install

echo "Running prepare script..."
bash ./prepare.sh

echo "Starting supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
