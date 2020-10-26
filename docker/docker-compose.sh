#!/usr/bin/env bash

if [ "$1" == "development" ] || [ "$1" == "production" ]; then
  BUILD_ENVIRONMENT="$1"
else
  echo "Invalid build environment option."
  exit 1
fi

if [ ! -f ".env" ]; then
  {
    echo "APP_NAME="
    echo "MYSQL_DATABASE="
    echo "MYSQL_USERNAME="
    echo "MYSQL_PASSWORD="
    echo "APACHE_SERVER_ADMIN="
    echo "APACHE_SERVER_NAME="
    echo "AUTHY_API_KEY="
  } > ".env"

  echo "Please edit the .env file before running the compose command again."
  exit 1
fi

# Helper Commands
if [ "$2" == "shell" ]; then
  sudo docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website bash
elif [ "$2" == "composer" ]; then
  sudo docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website composer "${@:3}"
  sudo chown -R "$(whoami)":"$(whoami)" ./../project
  sudo chmod -R o+w ./../project/storage ./../project/bootstrap/cache
elif [ "$2" == "npm" ]; then
  sudo docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec npm "${@:3}"
  sudo chown -R "$(whoami)":"$(whoami)" ./../project
  sudo chmod -R o+w ./../project/storage ./../project/bootstrap/cache
elif [ "$2" == "laravel" ] || [ "$2" == "artisan" ]; then
  sudo docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website php artisan "${@:3}"
  sudo chown -R "$(whoami)":"$(whoami)" ./../project
  sudo chmod -R o+w ./../project/storage ./../project/bootstrap/cache
elif [ "$2" == "compile" ]; then
  if [ "$3" == "dev" ] || [ "$3" == "development" ]; then
    sudo docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website npm run dev
  elif [ "$3" == "production" ]; then
    sudo docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website npm run production
  else
    echo "Invalid compile environment option."
    exit 1
  fi
  sudo chown -R "$(whoami)":"$(whoami)" ./../project
  sudo chmod -R o+w ./../project/storage ./../project/bootstrap/cache
elif [ "$2" == "chown" ]; then
  sudo chown -R "$(whoami)":"$(whoami)" ./../project
  sudo chmod -R o+w ./../project/storage ./../project/bootstrap/cache
elif [ "$2" == "up" ]; then
  sudo docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" up -d
else
  sudo docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" "${@:2}"
fi
