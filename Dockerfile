FROM php:8.1-apache

RUN a2enmod rewrite

RUN apt-get update \
  && apt-get install -y libzip-dev git wget --no-install-recommends \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN docker-php-ext-install pdo mysqli pdo_mysql zip;

RUN wget https://getcomposer.org/download/latest-stable/composer.phar \
    && mv composer.phar /usr/bin/composer && chmod +x /usr/bin/composer

COPY docker/apache.conf /etc/apache2/sites-enabled/000-default.conf

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

WORKDIR /var/www
COPY . /var/www
RUN chown -R www-data:www-data /var/www

CMD ["apache2-foreground"]

ENTRYPOINT ["/entrypoint.sh"]