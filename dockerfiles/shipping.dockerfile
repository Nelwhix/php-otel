FROM php:8.3-cli-alpine

# Install required dependencies
RUN apk add --no-cache \
    postgresql-dev

RUN docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./shipping-service /var/www/html

WORKDIR /var/www/html