# FROM php:latest
FROM php:8-fpm-alpine
ENV COMPOSER_ALLOW_SUPERUSER=1
# RUN apt-get update -y && apt-get install -y openssl git
RUN apk update && apk add openssl git
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install pdo pdo_mysql
WORKDIR /app
COPY . .
RUN composer update
# RUN composer update --ignore-platform-reqs
RUN composer install
CMD php artisan serve --host=0.0.0.0 --port=8181
EXPOSE 8181
