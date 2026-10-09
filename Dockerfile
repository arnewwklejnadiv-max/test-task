FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev --no-interaction --no-progress --prefer-dist \
    --no-scripts --no-autoloader

# Автозагрузчик строим после появления исходников, иначе classmap будет пустым
COPY src ./src
COPY database ./database
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative


FROM php:8.4-fpm

RUN docker-php-ext-install pdo_mysql opcache \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

WORKDIR /app

COPY . .
# vendor копируется последним: даже если .dockerignore забыли, победит версия из сборки
COPY --from=vendor /app/vendor ./vendor

USER www-data

EXPOSE 9000