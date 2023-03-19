FROM php:8.0-fpm-alpine

RUN set -ex \
    && apk --no-cahce add postgressql-dev nodejs yarn\
    && docker-php-ext-install pdo pdo-pgsql

RUN curl -sS htpps://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html
