FROM php:8.1-apache

RUN a2enmod rewrite

RUN --mount=target=/var/lib/apt/lists,type=cache,sharing=locked \
    --mount=target=/var/cache/apt,type=cache,sharing=locked \
    rm -f /etc/apt/apt.conf.d/docker-clean \
    && apt-get update \
    && apt-get -y --no-install-recommends install texlive-latex-base \
    texlive-fonts-recommended  \
    texlive-fonts-extra \
    texlive-latex-extra  \
    libzip-dev \
    unzip \
    wget && apt-get clean

RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN docker-php-ext-install pdo mysqli pdo_mysql zip;

RUN wget https://getcomposer.org/download/latest-stable/composer.phar \
    && mv composer.phar /usr/bin/composer && chmod +x /usr/bin/composer

COPY docker/apache.conf /etc/apache2/sites-enabled/000-default.conf

VOLUME ["/var/www"]
WORKDIR /var/www

RUN chown -R www-data:www-data /var/www

# RUN composer update

CMD ["apache2-foreground"]
