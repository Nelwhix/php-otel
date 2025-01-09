FROM php:8.3-cli-alpine

# Install required dependencies
RUN apk add --no-cache \
    postgresql-dev \
    curl \
    nodejs \
    npm

RUN docker-php-ext-install pcntl pdo pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./order-service /var/www/html

WORKDIR /var/www/html

RUN npm install && npm run build