FROM php:7.0.33-cli-alpine3.7

RUN apk --no-cache add git subversion openssh mercurial tini bash patch zip unzip libldap libmcrypt libmemcached

RUN echo "memory_limit=-1" > "$PHP_INI_DIR/conf.d/memory-limit.ini" \
     && echo "date.timezone=${PHP_TIMEZONE:-UTC}" > "$PHP_INI_DIR/conf.d/date_timezone.ini" \
     && echo "apc.enable_cli=1" > "$PHP_INI_DIR/conf.d/apcu-enable-cli.ini"

RUN apk add --no-cache --virtual .build-deps autoconf zlib-dev openldap-dev libmcrypt-dev libmemcached-dev $PHPIZE_DEPS \
    && docker-php-ext-configure hash --with-mhash \
    && docker-php-ext-install bcmath ldap mcrypt \
    && pecl install memcached-3.1.3 \
    && pecl install apcu-5.1.16 \
    && pecl install xdebug-2.6.0 \
    && docker-php-ext-enable apcu bcmath ldap mcrypt memcached xdebug \
    && runDeps="$( \
        scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
        | tr ',' '\n' \
        | sort -u \
        | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
        )" \
    && apk add --virtual .composer-phpext-rundeps $runDeps \
    && apk del .build-deps

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

CMD ["/usr/bin/php"]