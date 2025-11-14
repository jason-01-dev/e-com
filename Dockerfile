# IMAGE DE BASE : Debian + PHP-FPM
FROM php:8.3-fpm

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpq-dev \
    postgresql-client \
    unzip \
    libzip-dev \
    nodejs \
    npm \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Installation des extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_pgsql zip pdo_mysql

# Installation de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Définir le répertoire de travail
WORKDIR /var/www

# Copier le code Laravel
COPY . .

# Installer les dépendances Laravel
RUN composer install --no-dev --optimize-autoloader

# Copier le fichier .env si nécessaire
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Générer la clé Laravel
RUN php artisan key:generate

# Modifier php.ini
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini \
    && sed -i 's/memory_limit = 128M/memory_limit = 256M/g' /usr/local/etc/php/php.ini

# Exposition du port pour Laravel
EXPOSE 8080

# Commande de démarrage
CMD php artisan serve --host=0.0.0.0 --port=8080
