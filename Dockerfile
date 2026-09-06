FROM php:8.2-cli-alpine

RUN apk add --no-cache \
    $PHPIZE_DEPS \
    icu-dev \
    oniguruma-dev \
    libzip-dev \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    bcmath \
    intl \
    opcache \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application files first (needed for composer install)
COPY composer.json composer.lock artisan ./
COPY bootstrap/ ./bootstrap/
COPY routes/ ./routes/
COPY app/ ./app/
COPY config/ ./config/
COPY database/ ./database/
COPY public/ ./public/
COPY resources/ ./resources/
COPY storage/ ./storage/
COPY tests/ ./tests/

# Copy environment files
COPY .env .env.example ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 7000

ENTRYPOINT ["entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=7000"]