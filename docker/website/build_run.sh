#!/usr/bin/env bash

apt-get update
apt-get install --no-install-recommends -y lsb-release gnupg zip unzip

curl -sL https://deb.nodesource.com/setup_lts.x | bash -
apt-get install --no-install-recommends -y nodejs

apt-get autoremove -y
apt-get clean
rm -rf /var/lib/apt/lists/*

mv "${PHP_INI_DIR}/php.ini-${BUILD_ENVIRONMENT}" "${PHP_INI_DIR}/php.ini"

if [ "${BUILD_ENVIRONMENT}" = "development" ]; then
  rm "${PHP_INI_DIR}/php.ini-production"
  install-php-extensions xdebug bcmath pdo_mysql
else
  rm "${PHP_INI_DIR}/php.ini-development"
  install-php-extensions bcmath pdo_mysql
fi

rm /usr/bin/install-php-extensions

a2enmod rewrite
a2dissite 000-default

if [ "${BUILD_ENVIRONMENT}" = "development" ]; then
  rm /etc/apache2/sites-available/apache-site-ssl.conf
  a2ensite apache-site
else
  rm /etc/apache2/sites-available/apache-site.conf
  a2enmod ssl
  a2ensite apache-site-ssl
fi
