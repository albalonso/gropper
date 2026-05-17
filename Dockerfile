FROM php:8.2-apache

RUN docker-php-ext-install mysqli \
    && a2enmod rewrite

WORKDIR /var/www/html
COPY . /var/www/html/

EXPOSE 80

CMD sed -i "s/Listen 80/Listen ${PORT:-80}/" /etc/apache2/ports.conf \
    && sed -i "s/:80/:${PORT:-80}/" /etc/apache2/sites-available/000-default.conf \
    && apache2-foreground
