# Production image, deployed by Coolify through docker-compose.prod.yml.
# Local development keeps using Sail (docker-compose.yml).

FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build


# PHP 8.1: composer.lock pins nette/* below 8.2
FROM serversideup/php:8.1-fpm-nginx

ENV PHP_OPCACHE_ENABLE=1

WORKDIR /var/www/html

COPY --chown=www-data:www-data composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-scripts --no-autoloader --prefer-dist

COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=assets /app/public/build ./public/build

RUN composer dump-autoload --no-dev --optimize
