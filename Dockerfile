FROM php:8.4.12-apache

RUN docker-php-ext-install mysqli
RUN a2enmod ssl
RUN a2ensite default-ssl.conf
RUN a2enmod rewrite
RUN apt-get update && apt-get install -y openssl

COPY .docker/apache/default-ssl.conf /etc/apache2/sites-available/default-ssl.conf
