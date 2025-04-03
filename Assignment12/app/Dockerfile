FROM php:8.4-apache
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql
COPY . /var/www/html/
RUN chmod 755 /var/www/html/*
EXPOSE 80