ARG VERSION

FROM php:${VERSION}-fpm-alpine AS dev

RUN apk update && \
    apk add --no-cache --virtual dev-deps git autoconf gcc g++ make && \
    apk add --no-cache zlib-dev libzip-dev linux-headers openssl acl postgresql-dev

RUN docker-php-ext-install pdo_pgsql intl zip

WORKDIR /app

RUN wget https://getcomposer.org/installer && \
    php installer --install-dir=/usr/local/bin/ --filename=composer && \
    rm installer

RUN git config --global --add safe.directory /app

FROM dev as test

WORKDIR /app
COPY . .

RUN composer install --no-interaction --optimize-autoloader
RUN chown -R www-data:www-data .

FROM test AS prod
ENV APP_ENV=prod
WORKDIR /app

RUN composer install --no-dev --no-interaction --optimize-autoloader
RUN apk del dev-deps && \
    rm /usr/local/bin/composer
