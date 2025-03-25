FROM php:8.4-apache
COPY . /var/www/html/
RUN chmod 755 /var/www/html/*
RUN chown -R www-data:www-data /var/www/html/database 
RUN chmod 640 /var/www/html/database/website.sqlite
EXPOSE 80