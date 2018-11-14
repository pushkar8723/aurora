FROM php:7.2-apache

RUN apt-get update && apt-get install -y --no-install-recommends libxml++2.6-dev \
    zlib1g-dev libicu-dev g++ libssl-dev git \
    && pecl install apcu-5.1.5 && \
           docker-php-ext-enable apcu && \
           docker-php-ext-install \
               intl \
               mbstring \
               pdo_mysql \
               zip \
               bcmath \
               opcache \
    && docker-php-ext-install -j "$(nproc)" mbstring pdo tokenizer xml zip \
    && a2enmod rewrite

WORKDIR /var/www/html

COPY . /var/www/html

EXPOSE 80
EXPOSE 443

CMD apache2-foreground