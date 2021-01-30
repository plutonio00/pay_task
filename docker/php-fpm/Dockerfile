FROM php:7.4-fpm

RUN apt-get update

RUN apt-get install -y git zip cron netcat

RUN pecl install xdebug && docker-php-ext-enable xdebug
COPY ./config/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN docker-php-ext-install pdo_mysql && docker-php-ext-enable pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/bin/composer

RUN echo "0 * * * * cd /var/www/pay_test_task && php yii exec-transfer" >> /var/spool/cron/crontabs/root

COPY ./wait-for.sh /usr/bin/wait-for
RUN chmod +x /usr/bin/wait-for

WORKDIR /var/www/pay_test_task