FROM php:7.2-rc-alpine

WORKDIR /home/app

ADD composer.json /home/app

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN php composer.phar --no-plugins --no-scripts install

ADD . /home/app

CMD php -S 0.0.0.0:3000

EXPOSE 3000
