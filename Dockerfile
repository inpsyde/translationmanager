FROM pretzlaw/wordpress:7.0-apache

WORKDIR /var/www

RUN docker-php-ext-enable mysqli
RUN docker-php-ext-enable xdebug
RUN a2enmod rewrite
