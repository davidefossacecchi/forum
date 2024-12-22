FROM dfossacecchi/php-fpm:8.2-1.3

ARG UID
ARG GID

RUN apk --update --no-cache add \
    postgresql-dev \
    $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-install pdo_pgsql \
    && docker-php-ext-enable redis \
    && apk --no-cache add shadow \
    && usermod -u $UID www-data \
    && groupmod -g $GID www-data \
    && chown -R www-data:www-data /var/www \
    && chown -R www-data:www-data /home/www-data

WORKDIR /var/www
