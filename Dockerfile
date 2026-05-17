FROM php:8.2-apache

RUN docker-php-ext-install mysqli \
    && rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf \
    && a2enmod mpm_prefork rewrite

WORKDIR /var/www/html
COPY . /var/www/html/

EXPOSE 80

CMD rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* \
    && a2enmod mpm_prefork rewrite \
    && sed -i "s/Listen 80/Listen ${PORT:-80}/" /etc/apache2/ports.conf \
    && sed -i "s/:80/:${PORT:-80}/" /etc/apache2/sites-available/000-default.conf \
    && apache2-foreground
