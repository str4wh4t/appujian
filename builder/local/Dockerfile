FROM php:7.4-fpm

# Install extensions and dependencies
RUN apt-get update && apt-get install -y \
    cron \
    supervisor \
    mariadb-client \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libicu-dev \
    libexif-dev \
    libmagickwand-dev \
    locales \
    && pecl install imagick redis \
    && docker-php-ext-install intl pdo pdo_mysql mysqli zip exif \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-enable imagick redis gd

# Setup Indonesian locale
RUN echo "id_ID.UTF-8 UTF-8" >> /etc/locale.gen && \
    locale-gen id_ID.UTF-8 && \
    update-locale LANG=id_ID.UTF-8

ENV LANG=id_ID.UTF-8
ENV LANGUAGE=id_ID:en
ENV LC_ALL=id_ID.UTF-8

# Install Composer (untuk mengelola dependensi PHP)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Node.js dan Yarn
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get update \
    && apt-get install -y nodejs \
    && npm install -g yarn

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Copy php.ini
COPY ./builder/local/php.ini /usr/local/etc/php/conf.d/custom-php.ini

# Copy supervisor config
COPY ./builder/local/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Cron: jalankan cronjob.sh tiap menit di dalam container
COPY ./builder/local/crontab /etc/cron.d/app-cron
RUN chmod 0644 /etc/cron.d/app-cron && chmod +x /var/www/html/cronjob.sh

# Salin entrypoint
COPY ./builder/local/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 9000

# Jalankan pakai entrypoint
ENTRYPOINT ["/entrypoint.sh"]