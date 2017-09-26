docker build -t auth0-php-link-users .
docker run --env-file .env -p 8000:8000 -it auth0-php-link-users
