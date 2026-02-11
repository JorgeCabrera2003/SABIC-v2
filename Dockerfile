FROM docker.io/composer:2 AS composer

FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd zip mbstring exif pcntl bcmath intl

RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT /var/www/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Fix permissions
# RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 80

CMD ["sh", "-c", "composer install --no-interaction --prefer-dist && php artisan migrate --force && php artisan config:clear && php artisan route:clear && apache2-foreground"]
