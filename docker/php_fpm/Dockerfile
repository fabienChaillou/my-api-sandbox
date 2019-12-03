# the different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/compose/compose-file/#target

ARG PHP_VERSION=7.4

### PHP
FROM php:${PHP_VERSION}-fpm-alpine AS symfony_docker_php

RUN apk add --no-cache \
		git \
		icu-libs \
		zlib \
		jq

ENV APCU_VERSION 5.1.18
RUN set -eux \
	&& apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		icu-dev \
		libzip-dev \
		openssl \
	&& docker-php-ext-install -j$(nproc) \
		intl \
		zip \
	&& pecl install \
		apcu-${APCU_VERSION} \
	&& docker-php-ext-enable --ini-name 20-apcu.ini apcu \
	&& docker-php-ext-enable --ini-name 05-opcache.ini opcache \
	&& runDeps="$( \
        scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
            | tr ',' '\n' \
            | sort -u \
            | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
    )" \
    && apk add --no-cache --virtual .api-phpexts-rundeps $runDeps \
	&& apk del .build-deps

RUN ln -s $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
COPY conf.d/symfony.ini $PHP_INI_DIR/conf.d/symfony.ini
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /srv/app

# Use prestissimo to speed up builds
RUN composer global require "symfony/flex" --prefer-dist --no-progress --no-suggest --classmap-authoritative  --no-interaction

RUN mkdir -p var/cache var/logs var/sessions \
    && mkdir -m 755 var/{cache,logs,sessions} \
    && chown -R www-data var
