#!/usr/bin/env bash
docker build -t auth0-php-basic-api .
docker run --env-file .env -p 80:80 -it auth0-php-basic-api
