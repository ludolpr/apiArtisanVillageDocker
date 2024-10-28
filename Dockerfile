# use PHP 8.2
FROM php:8.2-apache
# Install common php extension dependencies
RUN apt-get update && apt-get install -y \
    libfreetype-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    zlib1g-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install zip \
    && docker-php-ext-install mysqli pdo pdo_mysql
# Set the working directory
COPY . /var/www/app
WORKDIR /var/www/app
COPY docker/.env /var/www/app/.env
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite
RUN chown -R www-data:www-data /var/www/app
RUN mkdir -p /var/www/app/storage
RUN chmod -R 775 /var/www/app/storage
# install composer
# Configurer la variable d'environnement
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer:2.6.5 /usr/bin/composer /usr/local/bin/composer
# copy composer.json to workdir & install dependencies
COPY composer.json ./
RUN composer install
