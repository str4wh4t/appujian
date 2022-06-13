#!/bin/bash
cur_dir="$(pwd)"
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
php composer.phar install
php "$(pwd)/vendor/bin/phoenix" "migrate"
ln -s "$(pwd)/writeable/yarn/node_modules" "$(pwd)/public/assets/yarn"
php "public/index.php" "pub/generate_data_daerah"