#!/bin/bash
cur_dir="$(pwd)"
# php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
# php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
# php composer-setup.php
# php -r "unlink('composer-setup.php');"
# php composer.phar install
php "$(pwd)/vendor/bin/phoenix" "migrate"

# Symlink node_modules ke public/assets/npm
NPM_LINK="$(pwd)/public/assets/node_modules"
[ -e "$NPM_LINK" ] && rm -rf "$NPM_LINK"
if [ -d "$(pwd)/node_modules" ]; then
  ln -s "$(pwd)/node_modules" "$NPM_LINK"
fi
# php "public/index.php" "pub/generate_data_daerah"