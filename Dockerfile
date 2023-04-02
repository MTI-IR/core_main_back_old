# FROM php:latest
FROM php:8-fpm-alpine
ENV COMPOSER_ALLOW_SUPERUSER=1
# RUN apt-get update -y && apt-get install -y openssl git
RUN apk update && apk add openssl git
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY . .
RUN composer update
# RUN composer update --ignore-platform-reqs
RUN composer install
RUN docker-php-ext-install pdo pdo_mysql
EXPOSE 8181
COPY env.docker ./
RUN cat env.docker > .env
CMD php artisan serve --host=0.0.0.0 --port=8181
