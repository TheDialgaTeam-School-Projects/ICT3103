FROM php:7.4-apache

ARG BUILD_ENVIRONMENT="development"

ENV APACHE_SERVER_NAME="localhost"

COPY *.sh /root/
COPY php.ini-* ${PHP_INI_DIR}/
COPY apache-*.conf /etc/apache2/sites-available/
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN bash /root/build_run.sh && rm /root/build_run.sh

WORKDIR /var/www/html

EXPOSE 80 443
