FROM php:apache

COPY php.ini /usr/local/etc/php/


RUN docker-php-ext-install pdo pdo_mysql