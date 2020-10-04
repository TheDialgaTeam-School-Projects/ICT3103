FROM php:7.4-apache

ARG BUILD_ENVIRONMENT="development"

ENV APACHE_SERVER_ADMIN="jianming1993@gmail.com"
ENV APACHE_SERVER_NAME="localhost"

COPY php.ini-* ${PHP_INI_DIR}/
COPY *.conf /etc/apache2/sites-available/
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN mv "${PHP_INI_DIR}/php.ini-${BUILD_ENVIRONMENT}" "${PHP_INI_DIR}/php.ini" && \
    if [ "${BUILD_ENVIRONMENT}" = "development" ] ; then install-php-extensions xdebug ; fi && \
    install-php-extensions mysqli && \
    a2enmod rewrite && \
    a2dissite 000-default && \
    if [ "${BUILD_ENVIRONMENT}" = "development" ] ; then a2ensite apache-site ; else a2ensite apache-site-ssl ; fi

WORKDIR /var/www/html

EXPOSE 80 443
