FROM php:8.1.1-fpm AS base

WORKDIR /var/www/html

# php packages
RUN apt-get update && apt-get install -y \
    curl \
    openssl \
    libssl-dev \
    libcurl4-openssl-dev \
    libfreetype6-dev \
    libzip-dev \
    libpq-dev \
    libfcgi0ldbl \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install -j$(nproc) iconv zip bcmath calendar posix sockets \
    && docker-php-ext-install pdo pdo_pgsql pgsql

ENV LC_ALL C.UTF-8

ARG UID
ENV UID $UID

# app user
RUN addgroup --gid $UID --system app && adduser --uid $UID --system --disabled-login --disabled-password --gid $UID app

# important for unoconv
RUN mkdir /var/www/.config \
    && chown app:app /var/www/.config \
    # unoconv in rabbit is trying to create dir in this folder under user app :(
    && chown app:app /root \
    # for composer cache
    && mkdir /var/www/.composer \
    && chown app:app /var/www/.composer

ARG PHP_INI=php.ini
ENV PHP_INI $PHP_INI

COPY docker/service-php/$PHP_INI /usr/local/etc/php/php.ini
COPY docker/service-php/www.conf /usr/local/etc/php-fpm.d/www.conf

ENV TZ=Europe/Moscow

FROM base AS service-php
COPY --chown=app:app . ./

FROM base AS worker
USER app
CMD ["php", "artisan", "queue:listen", "--delay=30"]
