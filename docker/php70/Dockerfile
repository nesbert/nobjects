FROM php:7.0

RUN apk --no-cache add git subversion openssh mercurial tini bash patch zip unzip libldap libmemcached

RUN echo "memory_limit=-1" > "$PHP_INI_DIR/conf.d/memory-limit.ini" \
     && echo "date.timezone=${PHP_TIMEZONE:-UTC}" > "$PHP_INI_DIR/conf.d/date_timezone.ini" \
     && echo "apc.enable_cli=1" > "$PHP_INI_DIR/conf.d/apcu-enable-cli.ini"

RUN apk add --no-cache --virtual .build-deps autoconf zlib-dev openldap-dev libmemcached-dev $PHPIZE_DEPS \
    && docker-php-ext-configure hash --with-mhash \
    && docker-php-ext-install bcmath ldap \
    && pecl install apcu \
    && pecl install xdebug \
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