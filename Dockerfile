FROM php:7.1-fpm

USER nginx
COPY . /var/www/html/

USER root
RUN apk add --update --no-cache \
    libgcc libstdc++ libx11 glib libxrender libxext libintl \
    libcrypto1.0 libssl1.0 \
    ttf-dejavu ttf-droid ttf-freefont ttf-liberation ttf-ubuntu-font-family

RUN chmod 777 -Rf /var/www/html/
