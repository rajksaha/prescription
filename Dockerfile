FROM php:7.4.26-apache

#Install git and MySQL extensions for PHP

RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql

COPY app /var/www/html/
EXPOSE 80/tcp
EXPOSE 443/tcp