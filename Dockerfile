# Utiliser PHP 8.2
FROM php:8.2-fpm

# Installer les dépendances courantes de PHP 
RUN apt-get update && apt-get install -y \
    libfreetype-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    zlib1g-dev \
    libzip-dev \
    unzip \
    nginx \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install zip \
    && docker-php-ext-install mysqli pdo pdo_mysql

# Définir le répertoire de travail
WORKDIR /var/www/app
COPY . /var/www/app

# Copier la configuration Nginx
COPY docker/apiartisanvillage.conf /etc/nginx/sites-available/

# Configurer les permissions
RUN chown -R www-data:www-data /var/www/app
RUN mkdir -p /var/www/app/storage
RUN chmod -R 775 /var/www/app/storage

# Installer Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer:2.6.5 /usr/bin/composer /usr/local/bin/composer

# Copier composer.json et installer les dépendances
COPY composer.json ./ 
RUN composer install

# Exposer le port 80 pour Nginx
EXPOSE 80

# Démarrer PHP-FPM et Nginx
CMD ["sh", "-c", "php-fpm -D & nginx -g 'daemon off;'"]
