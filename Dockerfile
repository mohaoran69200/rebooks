# Étape 1: Image de base avec PHP et les extensions nécessaires
FROM php:8.3-fpm

# Étape 2: Installer les dépendances nécessaires pour Symfony (extensions PHP)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    git \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Étape 3: Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Étape 4: Définir le répertoire de travail pour l'application Symfony
WORKDIR /var/www/html

# Étape 5: Copier le fichier composer.json et installer les dépendances PHP
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# Étape 6: Copier le reste de l'application Symfony
COPY . .

# Étape 7: Exposer le port par défaut pour le serveur Symfony
EXPOSE 9000
CMD ["php-fpm"]
