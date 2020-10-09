# ICT3103/ICT3203 Secure Software Development
This repo contains the source code for Team 4 (TeamWork) for ICT3103/ICT3203 Secure Software Development.

## Demo Production Website
You can visit the production website at https://teamwork.sitict.net

## Web application requirements
These are the recommended requirements for this web application.

### For local installation
1. PHP: 7.4.11
2. PHP extensions: MySQLi
3. MySQL: 8.0.21
4. Web Server: Apache (2.4.29)

### Via Docker
For docker environment, all the required images is installed via the `docker-compose.yml` file in `docker` folder.
It is assumed that you have used the certbot to generate an SSL cert for production site.

1. Go to `docker` folder.
2. Copy/Rename the file `docker-compose-development.yml` or `docker-compose-production.yml` to `docker-compose.yml`.
3. Create a file called `.env` and write the environment variables required in the definition below. `E.G: MYSQL_DATABASE=TEST`
4. In the command line execute `sudo docker-compose up`.

## Environment Variables Definition
For security reasons, certain information was redacted from the original source.
You are required to set these environment variables in your web server in order for the web application to work.

### Development/Production variables
1. `MYSQL_DATABASE` => MySQL Database.
2. `MYSQL_USERNAME` => MySQL Username.
3. `MYSQL_PASSWORD` => MySQL Password.
4. `BUILD_ENVIRONMENT` => "development" for debugging, "production" for production server.
5. `APACHE_SERVER_ADMIN` => Apache server administrator email.
6. `APACHE_SERVER_NAME` => Apache target server. "localhost" for development.

### For production only
1. `SSL_CERT_PASSWORD` => Jenkins SSL cert password.
