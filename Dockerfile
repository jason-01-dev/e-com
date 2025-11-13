# NOUVELLE IMAGE DE BASE : Debian + PHP-FPM
FROM php:8.3-fpm

# Mise à jour des dépôts
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpq-dev \
    postgresql-client \
    unzip \
    libzip-dev \
    libmariadb-dev \
    nodejs \
    npm \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Installation des extensions PHP
RUN docker-php-ext-install pdo pdo_pgsql zip pdo_mysql

#  Installation de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#  Modification de php.ini en copiant php.ini-production
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini \
    && sed -i 's/memory_limit = 128M/memory_limit = 256M/g' /usr/local/etc/php/php.ini

# Répertoire de travail
WORKDIR /var/www
