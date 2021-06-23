FROM php:8.0-cli-alpine
WORKDIR "/application"

# Install essentials
RUN apk add --no-cache \
  git

# Use UTC as server time
RUN echo "UTC" > /etc/timezone

# Install composer
ENV COMPOSER_hOME /composer
ENV PATH ./vendor/bin:composer/vendor/bin:$PATH
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer

ENTRYPOINT ["sh", "./docker/entrypoint.sh"]
