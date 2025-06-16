FROM php:8.4-fpm-alpine

WORKDIR /var/www/mini-order-api

RUN apk add --no-cache \
    $PHPIZE_DEPS \
    linux-headers \
    git \
    zip \
    unzip \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    mariadb-client

RUN docker-php-ext-install \
    pdo_mysql \
    intl \
    opcache \
    zip \
    mbstring \
    bcmath

RUN apk del $PHPIZE_DEPS linux-headers

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]

CMD ["php-fpm"]
