#!/usr/bin/env bash

if [ ! -f "/var/www/html/.dockerinstaller" ]; then
  if [ ! -f "/var/www/html/.env" ]; then
    {
      echo "APP_NAME=${APP_NAME}"
      echo "APP_ENV=${APP_ENV}"
      echo "APP_KEY="
      echo "APP_DEBUG=${APP_DEBUG}"
      echo "DB_CONNECTION=mysql"
      echo "DB_HOST=database"
      echo "DB_PORT=3306"
      echo "DB_DATABASE=${MYSQL_DATABASE}"
      echo "DB_USERNAME=${MYSQL_USERNAME}"
      echo "DB_PASSWORD=${MYSQL_PASSWORD}"
      echo "AUTHY_API_KEY=${AUTHY_API_KEY}"
    } >".env"
  fi

  npm install

  if [ "${BUILD_ENVIRONMENT}" = "development" ]; then
    composer install
    composer dump-autoload
    npm run dev
  else
    composer install --no-dev
    composer dump-autoload -o
    npm run production
  fi

  php artisan key:generate
  php artisan migrate:fresh --seed

  touch .dockerinstaller
fi

if [ -d "/var/www/html/storage" ] && [ -d "/var/www/html/bootstrap/cache" ]; then
  chmod -R o+w /var/www/html/storage /var/www/html/bootstrap/cache
fi

apache2-foreground
