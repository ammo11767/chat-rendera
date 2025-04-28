FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_sqlite
RUN a2enmod rewrite

COPY public/ /var/www/html/
