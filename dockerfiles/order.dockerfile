FROM php:8.3-cli-alpine


RUN apk add --no-cache \
    postgresql-dev

RUN docker-php-ext-install pdo pdo_pgsql

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN install-php-extensions opentelemetry

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./order-service /var/www/html

WORKDIR /var/www/html