#!/usr/bin/env bash

if [ -z "$1" ]; then
  echo "Docker Compose helper functions."
  echo ""
  echo "Usage:"
  echo "./docker-compose.sh [BUILD_ENVIRONMENT] [COMMANDS] [ARGS...]"
  echo ""
  echo "Build Environment:"
  echo "  development    Install development tools optimize for development usage"
  echo "  production     Install production tools optimize for production usage"
  echo ""
  echo "Commands:"
  echo "  artisan   Execute laravel command line in the website service (Requires containers to be running)"
  echo "  build     Build or rebuild services"
  echo "  chown     Change the ownership of files and directory to the current user"
  echo "  composer  Execute composer in the website service (Requires containers to be running)"
  echo "  down      Stop and remove containers, networks, images, and volumes"
  echo "  exec      Execute a command in a running container"
  echo "  laravel   Execute laravel command line in the website service (Requires containers to be running)"
  echo "  npm       Execute npm in the website service (Requires containers to be running)"
  echo "  shell     Execute a bash shell in the website service (Requires containers to be running)"
  echo "  start     Start services"
  echo "  stop      Stop services"
  echo "  up        Create and start containers"
  echo "  update    Update existing dependencies and cache (Requires containers to be running)"
  echo "  * Any other docker-compose command unlisted here is available"
  exit 0
fi

if [ "$1" == "development" ] || [ "$1" == "production" ]; then
  BUILD_ENVIRONMENT="$1"
else
  echo "Invalid build environment option."
  exit 1
fi

if [ ! -f ".env" ]; then
  {
    echo "APP_URL="
    echo ""
    echo "APACHE_SERVER_NAME="
    echo ""
    echo "MYSQL_DATABASE="
    echo "MYSQL_USERNAME="
    echo "MYSQL_PASSWORD="
    echo ""
    echo "AUTHY_API_KEY="
    if [ "$BUILD_ENVIRONMENT" == "production" ]; then
      echo ""
      echo "SSL_CERT_PASSWORD="
    fi
  } >".env"

  echo "Please edit the .env file before running ./docker-compose.sh again."
  exit 1
else
  # shellcheck disable=SC2046
  export $(grep -v '^#' .env | xargs -d '\n')
fi

# Main Helper Commands
if [ "$2" == "up" ]; then
  if [ ! -f "./../project/.env" ]; then
    {
      if [ "$BUILD_ENVIRONMENT" == "development" ]; then
        echo "APP_ENV=local"
        echo "APP_DEBUG=true"
      else
        echo "APP_ENV=production"
        echo "APP_DEBUG=false"
      fi
      echo "APP_URL=$APP_URL"
      echo "APP_KEY="
      echo ""
      echo "DB_CONNECTION=mysql"
      echo "DB_HOST=database"
      echo "DB_PORT=3306"
      echo "DB_DATABASE=$MYSQL_DATABASE"
      echo "DB_USERNAME=$MYSQL_USERNAME"
      echo "DB_PASSWORD=$MYSQL_PASSWORD"
      echo ""
      echo "AUTHY_API_KEY=$AUTHY_API_KEY"
    } >"./../project/.env"
  fi

  docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" build
  docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" up -d

  if [ "${BUILD_ENVIRONMENT}" = "development" ]; then
    docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website composer install
  else
    docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website composer install --optimize-autoloader --no-dev
  fi

  if [ -d "./../project/storage" ] && [ -d "./../project/bootstrap/cache" ]; then
    docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website chmod -R o+w /var/www/html/storage /var/www/html/bootstrap/cache
  fi

  if [ ! -f "./../project/.dockerinstalled" ]; then
    docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website php artisan key:generate
    docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website php artisan migrate:fresh --seed --force
    touch ./../project/.dockerinstalled
  fi

  if [ "${BUILD_ENVIRONMENT}" = "production" ]; then
    docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website php artisan config:cache
    docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website php artisan route:cache
    docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website php artisan view:cache
  else
    docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website php artisan config:clear
    docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website php artisan route:clear
    docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website php artisan view:clear
  fi

  chown -R "$(whoami)":"$(whoami)" ./../project
  exit 0
elif [ "$2" == "update" ]; then
  docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec node npm update --legacy-peer-deps

  if [ "${BUILD_ENVIRONMENT}" = "development" ]; then
    docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website composer update
    docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec node npm run dev
  else
    docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website composer update --optimize-autoloader --no-dev
    docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec node npm run production
  fi

  docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website php artisan config:clear
  docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website php artisan route:clear
  docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website php artisan view:clear

  if [ "${BUILD_ENVIRONMENT}" = "production" ]; then
    docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website php artisan config:cache
    docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website php artisan route:cache
    docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website php artisan view:cache
  fi

  if [ -d "./../project/storage" ] && [ -d "./../project/bootstrap/cache" ]; then
    docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website chmod -R o+w /var/www/html/storage /var/www/html/bootstrap/cache
  fi

  chown -R "$(whoami)":"$(whoami)" ./../project
  exit 0
fi

# Helper Commands
if [ "$2" == "shell" ]; then
  docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website bash
elif [ "$2" == "composer" ]; then
  docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website composer "${@:3}"
  docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website chmod -R o+w /var/www/html/storage /var/www/html/bootstrap/cache
  chown -R "$(whoami)":"$(whoami)" ./../project
elif [ "$2" == "npm" ]; then
  docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec node npm "${@:3}"
  chown -R "$(whoami)":"$(whoami)" ./../project
elif [ "$2" == "laravel" ] || [ "$2" == "artisan" ]; then
  docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website php artisan "${@:3}"
  docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website chmod -R o+w /var/www/html/storage /var/www/html/bootstrap/cache
  chown -R "$(whoami)":"$(whoami)" ./../project
elif [ "$2" == "chown" ]; then
  chown -R "$(whoami)":"$(whoami)" ./../project
else
  docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" "${@:2}"
fi
