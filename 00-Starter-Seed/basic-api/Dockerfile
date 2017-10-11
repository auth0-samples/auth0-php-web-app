FROM tutum/apache-php

RUN apt-get update && apt-get install -yq git && rm -rf /var/lib/apt/lists/*
RUN rm -fr /app

ADD composer.json /app
RUN composer self-update
RUN composer install
ADD . /app

CMD php -S 0.0.0.0:3001

EXPOSE 3001