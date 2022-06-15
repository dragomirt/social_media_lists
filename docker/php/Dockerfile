FROM php:8.1-fpm-buster

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update \
    && apt-get install -y gosu libicu-dev libzip-dev \
    g++ curl ca-certificates zip unzip git supervisor sqlite3 libcap2-bin libmpc-dev python2 acl \
    libjpeg-dev libpng-dev libfreetype6-dev

RUN docker-php-ext-install intl opcache pdo pdo_mysql

RUN pecl install apcu \
    && docker-php-ext-enable apcu

RUN docker-php-ext-configure zip \
    && docker-php-ext-install zip

RUN apt-get update && apt-get install -y mariadb-client

RUN apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
