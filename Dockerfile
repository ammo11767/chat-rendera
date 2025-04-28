FROM php:8.2-apache

# PDO y SQLite YA están incluidos; no vuelvas a compilarlos
RUN a2enmod rewrite

# Copia todo el proyecto (ajusta si usas carpeta /public)
COPY . /var/www/html/
