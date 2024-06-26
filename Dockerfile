FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
        nginx \
        git \
        unzip \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        zip \
        curl \
        && apt-get clean && rm -rf /var/lib/apt/lists/*
ENV SKIP_COMPOSER 1
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY ./ /var/www
WORKDIR /var/www


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer require symfony/maker-bundle
RUN composer install --no-dev --optimize-autoloader

COPY nginx.conf /etc/nginx/nginx.conf
COPY symfony.conf /etc/nginx/conf.d/

RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

EXPOSE 80 9000

CMD php-fpm -D && nginx -g 'daemon off;'

