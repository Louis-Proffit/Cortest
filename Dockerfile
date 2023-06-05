FROM php:8.2-apache

RUN a2enmod rewrite

RUN --mount=target=/var/lib/apt/lists,type=cache,sharing=locked \
    --mount=target=/var/cache/apt,type=cache,sharing=locked \
    rm -f /etc/apt/apt.conf.d/docker-clean \
    && apt-get update \
    && apt-get -y --no-install-recommends --fix-missing install texlive-latex-base \
    texlive-fonts-recommended  \
    texlive-fonts-extra \
    texlive-latex-extra  \
    libicu-dev \
    libzip-dev \
    unzip \
    wget \
    poppler-utils  \
    && apt-get clean

RUN docker-php-ext-install pdo mysqli pdo_mysql zip intl;

WORKDIR /var/www

COPY --from=composer/composer:latest /usr/bin/composer /usr/bin/composer

COPY docker/apache.conf /etc/apache2/sites-enabled/000-default.conf

COPY . /var/www

RUN groupadd cortest-users \
    && usermod -a -G cortest-users www-data \
    && usermod -a -G cortest-users $(whoami) \
    && chgrp -R cortest-users /tmp \
    && chmod -R g+w /tmp

RUN composer install

CMD ["apache2-foreground"]
