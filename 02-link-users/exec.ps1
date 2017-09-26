docker build -t auth0-php-link-users .
docker run --env-file .env -p 3000:3000 -it auth0-php-link-users
