FROM php:8.2-cli

WORKDIR /var/www

# install dependencies
RUN apt-get update && apt-get install -y \
    git curl unzip libpq-dev zip \
    && docker-php-ext-install pdo pdo_pgsql

# install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# copy project
COPY . .

# install laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# fix permission (penting di render)
RUN chmod -R 775 storage bootstrap/cache

# expose port render
EXPOSE 10000

RUN php artisan config:clear && php artisan migrate --force

CMD php artisan serve --host=0.0.0.0 --port=10000