FROM php:7.4-fpm

RUN apt-get update

RUN apt-get install -y git zip cron

RUN pecl install xdebug && docker-php-ext-enable xdebug
COPY ./config/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN docker-php-ext-install pdo_mysql && docker-php-ext-enable pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/bin/composer

WORKDIR /var/www/pay_test_task