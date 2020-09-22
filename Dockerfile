FROM php:7.4-fpm-alpine3.12

RUN apk add --no-cache shadow openssl bash autoconf make g++ zlib-dev libpng-dev libzip-dev mysql-client nodejs npm
RUN docker-php-ext-install pdo pdo_mysql bcmath

ENV LIBRARY_PATH=/lib:/usr/lib

# RUN yes | pecl install xdebug \
#     && docker-php-ext-enable xdebug \
#     && echo "\n\
#     xdebug.remote_host=172.29.78.215 \n\
#     xdebug.default_enable=1 \n\
#     xdebug.remote_autostart=1 \n\
#     xdebug.remote_connect_back=0 \n\
#     xdebug.remote_enable=1 \n\
#     xdebug.remote_handler="dbgp" \n\
#     xdebug.remote_port=9001 \n\
#     xdebug.remote_log = /var/wwww/xdebug.log \n\
#     " >> /usr/local/etc/php/conf.d/xdebug.ini

COPY ./.docker/php/zz-app.ini /usr/local/etc/php/conf.d/zz-app.ini

WORKDIR /var/www

COPY ./.docker/start.sh /usr/local/bin/start
RUN chmod u+x /usr/local/bin/start

RUN rm -rf /var/www/html
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN usermod -u 1000 www-data
USER www-data

EXPOSE 9000

CMD ["/usr/local/bin/start"]
