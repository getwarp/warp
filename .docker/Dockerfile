ARG PHP_VERSION=7.4

FROM php:${PHP_VERSION}-cli-alpine3.13

ENV \
    # Fix for iconv: https://github.com/docker-library/php/issues/240
    LD_PRELOAD="/usr/lib/preloadable_libiconv.so php" \
    PATH="${PATH}:/home/space/app/vendor/bin"

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

ADD https://github.com/just-containers/s6-overlay/releases/download/v2.2.0.3/s6-overlay-amd64.tar.gz /tmp/

RUN apk add --update \
        bash \
        less \
        git \
        gnu-libiconv \
    && tar xzf /tmp/s6-overlay-amd64.tar.gz -C / \
    && rm -f /tmp/s6-overlay-amd64.tar.gz \
    && install-php-extensions bcmath intl opcache zip xdebug pcntl uuid

ARG UID=1000
ARG GID=1000

RUN addgroup -g $GID onfire \
    && adduser -D -S -h /home/space -s /bin/bash -G onfire -u $UID space \
    && mkdir -p /home/space/app \
    && echo "PS1='\[\033[01;32m\]\u@\h\[\033[00m\]:\[\033[01;34m\]\w\[\033[00m\]\$ '" > /home/space/.bashrc \
    && chown -R space:onfire /home/space

WORKDIR /home/space/app
USER space
ENTRYPOINT ["/init"]
CMD ["sleep", "infinite"]
