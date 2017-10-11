docker build -t auth0-php-basic-api .
docker run --env-file .env -p 3001:3001 -it auth0-php-basic-api
