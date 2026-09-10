FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql mysqli && a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT=/var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN echo "upload_max_filesize = 10M\npost_max_size = 10M\nmemory_limit = 256M\ndate.timezone = Africa/Algiers\ndisplay_errors = Off\n" \
    > /usr/local/etc/php/conf.d/hdellaya.ini

RUN mkdir -p /var/www/html/uploads && \
    chown -R www-data:www-data /var/www/html/uploads && \
    chmod 755 /var/www/html/uploads

COPY . /var/www/html/

# Permet d'écrire le port dynamique au démarrage
RUN chmod +x /var/www/html/start.sh 2>/dev/null || true

EXPOSE 10000

CMD ["bash", "-c", "sed -i \"s/Listen 80/Listen ${PORT:-10000}/\" /etc/apache2/ports.conf && sed -i \"s/:80>/:${PORT:-10000}>/\" /etc/apache2/sites-available/000-default.conf && apache2-foreground"]
