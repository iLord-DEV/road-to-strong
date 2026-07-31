# Stage 1: build Vite assets
FROM node:22-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json vite.config.js ./
RUN npm ci

COPY resources ./resources
# app.css references these paths via @source; they don't exist in this stage
RUN mkdir -p storage/framework/views vendor/laravel/framework/src/Illuminate/Pagination/resources/views \
    && npm run build

# Stage 2: PHP runtime (pdo_sqlite is bundled in the official image)
FROM php:8.4-cli-alpine

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-autoloader --no-scripts --no-interaction

COPY . .
COPY --from=assets /app/public/build ./public/build

RUN composer dump-autoload --optimize

ENV PHP_CLI_SERVER_WORKERS=4

EXPOSE 8080

ENTRYPOINT ["sh", "docker/entrypoint.sh"]
