#!/usr/bin/env bash
docker build -t auth0-php-fitbit .
docker run -p 3000:3000 -it auth0-php-fitbit
