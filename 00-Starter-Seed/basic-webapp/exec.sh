#!/usr/bin/env bash
docker build -t auth0-php-web-app .
docker run --env-file .env -p 3000:3000 -it auth0-php-web-app
