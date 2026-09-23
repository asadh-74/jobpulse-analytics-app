FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
        git unzip curl libzip-dev libpng-dev libonig-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql pgsql zip \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && npm install \
    && npm run build \
    && npm cache clean --force

RUN chmod -R 775 storage bootstrap/cache

EXPOSE 10000

CMD php artisan migrate --force \
    && php artisan config:cache \
    && php artisan serve --host 0.0.0.0 --port ${PORT:-10000}