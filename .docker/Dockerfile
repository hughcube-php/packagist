

ARG BASE_IMAGE=phpswoole/swoole:latest-alpine

#########################################################################################################
####
#########################################################################################################
FROM ${BASE_IMAGE} AS builder
RUN apk add tzdata


#########################################################################################################
#### 实际基础镜像
#########################################################################################################
FROM ${BASE_IMAGE}

ARG COMPOSER_VERSION=""
ARG ENVIRONMENT="production"

ENV LARAVEL_OCTANE 1
ENV APP_BASE_PATH "/app"
ENV OCTANE_STATE_FILE "${APP_BASE_PATH}/storage/logs/octane-server-state.json"

#USER root

COPY --from=builder /usr/share/zoneinfo/Asia/Shanghai /etc/localtime
COPY --chown=0555 docker-php-install-composer docker-app-bootstrap /usr/local/bin/

WORKDIR ~
RUN \
    # sed -i 's/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g' /etc/apk/repositories && \
    # autoconf dpkg-dev dpkg file g++  libc-dev make pkgconf re2c
    apk update && \
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS && \
    #
    #
    apk add --no-cache git && \
    #
    # Install Chokidar for non-production environments
    if [ "$ENVIRONMENT" != "production" ]; then apk add --no-cache npm && npm install -g chokidar; fi && \
    #
    # Install pcntl
    docker-php-ext-install pcntl && \
    #
    # Zip \
    apk add --no-cache libzip-dev && \
    apk add --no-cache --virtual .build-deps zlib-dev && \
    docker-php-ext-install zip && \
    #
    # Gmp \
    apk add --no-cache gmp-dev && \
    docker-php-ext-install gmp && \
    #
    # BCMath
    docker-php-ext-install bcmath && \
    #
    # Install composer \
    if [ -n "$COMPOSER_VERSION" ]; then /usr/local/bin/docker-php-install-composer "${COMPOSER_VERSION}"; fi && \
    #
    # Zend OpCache \
    docker-php-ext-enable opcache && mkdir -p /tmp/opcache && chmod -R 0777 /tmp/opcache && \
    rm -rf /usr/local/etc/php/conf.d/*opcache.ini && \
    touch /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
    echo "zend_extension=opcache" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
    echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
    echo "opcache.enable_cli=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
    echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
    echo "opcache.save_comments=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
    echo "opcache.use_cwd=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
    echo "opcache.file_cache=/tmp/opcache" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
    echo "opcache.file_cache_consistency_checks=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
    rm -rf "/tmp/opcache/*" && \
    #
    # Clear \
    #pecl clear-cache && \
    docker-php-source delete && apk del .build-deps

WORKDIR /app

#CMD /usr/local/bin/docker-app-bootstrap
CMD /usr/local/bin/php -d variables_order=EGPCS /app/vendor/bin/swoole-server ${OCTANE_STATE_FILE}
