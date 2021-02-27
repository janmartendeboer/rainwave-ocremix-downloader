FROM composer:2 AS builder

WORKDIR /app

COPY src src
COPY composer.json composer.json
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

FROM php:8

WORKDIR /app

COPY --from=builder /app/vendor vendor
COPY --from=builder /app/src src
COPY bin/console bin/console
COPY .env.dist .env

ENTRYPOINT ["bin/console"]
