FROM ubuntu:16.04

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    nano \
    php7.1-mysql php-redis php7.1-gd php-imagick php-ssh2 php-xdebug \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/test

RUN cd /var/www/test && \
    composer install --no-interaction

EXPOSE 80
EXPOSE 443
