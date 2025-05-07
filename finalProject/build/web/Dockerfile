FROM php:8.4-apache
COPY files/docker-php-ext-pdo_mysql.ini /usr/local/etc/php/conf.d/docker-php-ext-pdo_mysql.ini
COPY files/pdo_mysql.so /usr/local/lib/php/extensions/no-debug-non-zts-20240924/pdo_mysql.so
EXPOSE 80