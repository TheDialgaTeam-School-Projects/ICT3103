#!/usr/bin/env bash

SOURCE="${BASH_SOURCE[0]}"

while [ -h "$SOURCE" ]; do # resolve $SOURCE until the file is no longer a symlink
  DIR="$( cd -P "$( dirname "$SOURCE" )" >/dev/null 2>&1 && pwd )"
  SOURCE="$(readlink "$SOURCE")"
  [[ $SOURCE != /* ]] && SOURCE="$DIR/$SOURCE" # if $SOURCE was a relative symlink, we need to resolve it relative to the path where the symlink file was located
done

DIR="$( cd -P "$( dirname "$SOURCE" )" >/dev/null 2>&1 && pwd )"
PROJECT_DIR="$DIR/../project"

cd "$DIR/website" || exit

docker build --build-arg BUILD_ENVIRONMENT=development -t ict3x03/php:7.4-apache-development .

cd "$PROJECT_DIR" || exit

docker run -v "$PROJECT_DIR":/var/www/html --name ICT3x03-website --rm ict3x03/php:7.4-apache-development composer install
docker run -v "$PROJECT_DIR":/var/www/html --name ICT3x03-node --rm node:lts-alpine sh -c "cd /var/www/html && npm install && npm run dev"

chown -R jenkins:jenkins "$PROJECT_DIR"

docker run -v "$PROJECT_DIR":/var/www/html --name ICT3x03-website --rm ict3x03/php:7.4-apache-development artisan test
