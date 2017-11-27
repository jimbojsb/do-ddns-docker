FROM alpine
RUN apk add --update \
        php7 \
        php7-phar \
        php7-json \
        php7-openssl \
        php7-mbstring \
        php7-zlib \
        bash \
        curl \
    && rm -rf /var/cache/apk/*
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
CMD /usr/bin/php /app/ddns.php
COPY composer.json composer.lock /app/
WORKDIR /app
RUN composer install --no-dev
COPY ddns.php /app