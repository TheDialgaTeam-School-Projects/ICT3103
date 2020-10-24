# ICT3103/ICT3203 Secure Software Development
This repo contains the source code for Team 4 (TeamWork) for ICT3103/ICT3203 Secure Software Development.

## Demo Production Website
You can visit the production website at https://teamwork.sitict.net

## Getting Started

### Installation

#### Server Requirements
This repo uses Laravel framework (https://laravel.com) as the base web framework for secure software development.

##### Local installation without docker

If you are not using docker, you will need to make sure your server meets the following requirements:

* PHP >= 7.3
* BCMath PHP Extension
* Ctype PHP Extension
* Fileinfo PHP Extension
* JSON PHP Extension
* Mbstring PHP Extension
* OpenSSL PHP Extension
* PDO PHP Extension
* Tokenizer PHP Extension
* XML PHP Extension
* Composer >= 1.10
* MySQL >= 5.6

You are required to have a web server of your choice to run the website, this README.md will not cover how to configure your web server.

Note: The list given here are not exhaustive and may require additional requirements. Do look up for the vendor requirements for each component.

##### Local installation with docker

For docker users, you will only need the following:

Development requirements:

* Docker Engine >= 19.03
* Docker Compose >= 1.27

Production requirements:

* Docker Engine >= 19.03
* Docker Compose >= 1.27
* Certbot (https://certbot.eff.org/lets-encrypt/ubuntufocal-other)

#### Configuration (Only for local installation without docker)

##### Public Directory

After cloning this repository, you should configure your web server's document / web root to be the `project/public` directory.
The `index.php` in this directory serves as the front controller for all HTTP requests entering our application.

##### Directory Permissions

After cloning this repository, you may need to configure some permissions.
Directories within the `project/storage` and the `project/bootstrap/cache` directories should be writable by your web server or this web application will not run.

##### Application Key

The next thing you should do after cloning this repository is to set your application key to a random string.
You can set this key via `php artisan key:generate` command in the `project` folder.

Typically, this string should be 32 characters long. The key can be set in the `.env` environment file. **If the application key is not set, your user sessions and other encrypted data will not be secure!**

##### Environment Variable File

After cloning this repository, you should create a `.env` environment file in `project` directory containing the minimum:

* APP_NAME=
* APP_ENV=<local/production>
* APP_KEY=<GENERATED VIA `php artisan key:generate`>
* APP_DEBUG=false
* DB_CONNECTION=mysql
* DB_HOST=
* DB_PORT=3306
* DB_DATABASE=
* DB_USERNAME=
* DB_PASSWORD=
* AUTHY_API_KEY=

Value that is blank or enclosed with < > should be edited to your desired configuration.

#### Configuration (For docker)

You are required to set .env for docker to build the correct image for the website.

##### Docker environment file (.env)

After cloning this repository, you should create a `.env` environment file in `docker` directory containing the minimum:

* APP_NAME=
* MYSQL_DATABASE=
* MYSQL_USERNAME=
* MYSQL_PASSWORD=
* APACHE_SERVER_ADMIN=
* APACHE_SERVER_NAME=
* AUTHY_API_KEY=

Production variables:

* SSL_CERT_PASSWORD=

Value that is blank or enclosed with < > should be edited to your desired configuration.
