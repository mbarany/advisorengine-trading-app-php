FROM php:7.2.0-fpm-alpine

COPY ./ /app

# Postgres Support
RUN apk --no-cache add \
    postgresql-dev

RUN docker-php-ext-install \
    pcntl \
    pdo \
    pdo_pgsql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

RUN composer install
