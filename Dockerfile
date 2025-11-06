FROM php:8.4.12-apache
RUN docker-php-ext-install mysqli
RUN a2enmod ssl
RUN a2ensite default-ssl.conf
RUN a2enmod rewrite

COPY .docker/apache/default-ssl.conf /etc/apache2/sites-available/default-ssl.conf
