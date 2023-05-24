FROM php:8.1-apache

RUN a2enmod rewrite

RUN rm -f /etc/apt/apt.conf.d/docker-clean

RUN apt-get update \
    && apt-get -y --no-install-recommends --fix-missing install texlive-latex-base\
    texlive-fonts-recommended  \
    texlive-fonts-extra \
    texlive-latex-extra  \
    libzip-dev \
    unzip \
    wget \
    poppler-utils  \
    && apt-get clean

RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN docker-php-ext-install pdo mysqli pdo_mysql zip;

COPY docker/apache.conf /etc/apache2/sites-enabled/000-default.conf

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . /var/www

RUN chown -R www-data:www-data /var/www

RUN groupadd cortest-users \
    && usermod -a -G cortest-users www-data \
    && usermod -a -G cortest-users $(whoami) \
    && chgrp -R cortest-users /tmp \
    && chmod -R g+w /tmp

RUN composer install

CMD ["apache2-foreground"]
