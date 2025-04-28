# Dockerfile
FROM php:8.2-apache

# No hace falta volver a compilar pdo/pdo_sqlite
# Si en el futuro necesitas otra extensión:
# RUN docker-php-ext-install mysqli

RUN a2enmod rewrite

# Copia tu app (ajusta la ruta si usas /public)
COPY . /var/www/html/
