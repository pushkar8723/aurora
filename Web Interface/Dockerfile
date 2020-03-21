FROM php:7.4-apache

RUN apt-get update && apt-get install -y --no-install-recommends libxml++2.6-dev \
    zlib1g-dev libicu-dev g++ libssl-dev git libzip-dev
RUN docker-php-ext-install intl pdo_mysql zip bcmath opcache
RUN docker-php-ext-install -j "$(nproc)" tokenizer xml
RUN a2enmod rewrite && a2enmod ssl && a2enmod headers

WORKDIR /var/www/html

COPY . /var/www/html

EXPOSE 80
EXPOSE 443

CMD apache2-foreground