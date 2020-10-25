#!/usr/bin/env bash

if [ "$1" == "development" ] || [ "$1" == "production" ]; then
  BUILD_ENVIRONMENT="$1"
else
  echo "Invalid build environment option."
  exit 1
fi

# Helper Commands
if [ "$2" == "shell" ]; then
  docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website bash
elif [ "$2" == "composer" ]; then
  docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website composer "${@:3}"
  chown -R "$(whoami)":"$(whoami)" ./../project
  chmod -R o+w ./../project/storage ./../project/bootstrap/cache
elif [ "$2" == "npm" ]; then
  docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec npm "${@:3}"
  chown -R "$(whoami)":"$(whoami)" ./../project
  chmod -R o+w ./../project/storage ./../project/bootstrap/cache
elif [ "$2" == "laravel" ] || [ "$2" == "artisan" ]; then
  docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website php artisan "${@:3}"
  chown -R "$(whoami)":"$(whoami)" ./../project
  chmod -R o+w ./../project/storage ./../project/bootstrap/cache
elif [ "$2" == "compile" ]; then
  if [ "$3" == "dev" ] || [ "$3" == "development" ]; then
    docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website npm run dev
  elif [ "$3" == "production" ]; then
    docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" exec website npm run production
  else
    echo "Invalid compile environment option."
    exit 1
  fi
  chown -R "$(whoami)":"$(whoami)" ./../project
  chmod -R o+w ./../project/storage ./../project/bootstrap/cache
elif [ "$2" == "chown" ]; then
  chown -R "$(whoami)":"$(whoami)" ./../project
  chmod -R o+w ./../project/storage ./../project/bootstrap/cache
elif [ "$2" == "up" ]; then
  docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" up -d
else
  docker-compose -f "docker-compose-${BUILD_ENVIRONMENT}.yml" "${@:2}"
fi
