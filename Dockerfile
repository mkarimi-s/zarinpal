FROM php:8-fpm-alpine as php

RUN apk add --no-cache libsodium-dev
RUN docker-php-ext-install sodium pcntl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./composer.json composer.lock ./

RUN composer install --no-scripts --no-autoloader --prefer-dist --no-dev \
                     --working-dir=/var/www/html

COPY ./ /tmp/app
RUN chgrp -R 0 /tmp/app && \
    chmod -R g=u /tmp/app && \
    cp -a /tmp/app/. /var/www/html && \
    rm -rf /tmp/app && \
    composer dump-autoload --classmap-authoritative

VOLUME ["/var/www/html"]
ENTRYPOINT ["/usr/local/sbin/php-fpm"]


FROM php:8-cli-alpine as php_worker

RUN apk add supervisor --repository http://dl-cdn.alpinelinux.org/alpine/edge/community/ --no-cache

RUN apk add --no-cache libsodium-dev
RUN docker-php-ext-install sodium pcntl

COPY --from=php /var/www/html /var/www/html

COPY ./docker/supervisor/queue.conf /etc/supervisor/queue.conf

RUN echo "* * * * * php -d memory_limit=10G /var/www/html/artisan schedule:run >> /dev/null 2>&1" | crontab -

VOLUME ["/etc/supervisor"]

CMD ["supervisord", "-c", "/etc/supervisor/queue.conf"]


FROM nginx:stable-alpine as nginx

COPY --from=php /var/www/html/public /var/www/html/public
RUN chown -R nginx. /var/cache/nginx/
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf