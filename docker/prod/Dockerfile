FROM composer:2 AS builder

COPY composer.json /app
COPY composer.lock /app

WORKDIR /app

RUN composer install --ignore-platform-reqs --no-dev --optimize-autoloader


FROM php:8-apache

# Define default mode argument to 'local' when not provided in the --build-arg
# Exemplo: docker build -t myimage:latest \
#               --build-arg COMMIT=123 \
#               --build-arg DB_HOST=192.168.1.5 \
#               --build-arg DB_USER=root \
#               --build-arg DB_PASS=123456 \
#               --build-arg DB_NAME=rank \
#               -f docker/prod/Dockerfile .
ARG COMMIT=LOCAL
ARG MODE=PROD
ARG DB_HOST
ARG DB_USER
ARG DB_PASS
ARG DB_NAME

ENV MODE=$MODE
ENV APP_COMMIT=$COMMIT
ENV DB_HOST=$DB_HOST
ENV DB_USER=$DB_USER
ENV DB_PASS=$DB_PASS
ENV DB_NAME=$DB_NAME

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite headers

RUN apt-get update && apt-get install -y curl zip unzip && apt-get clean -y
RUN docker-php-ext-install pdo pdo_mysql

COPY . /var/www/html

COPY --from=builder /app /var/www/html

