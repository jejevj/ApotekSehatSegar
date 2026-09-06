FROM php:8.2-fpm-alpine AS base

# Add www-data user (Alpine doesn't have it by default for FPM)
RUN addgroup -g 82 -S www-data 2>/dev/null || true \
    && adduser -u 82 -D -S -G www-data www-data 2>/dev/null || true

RUN apk add --no-cache \
    nginx \
    supervisor \
    linux-headers \
    $PHPIZE_DEPS \
    icu-dev \
    oniguruma-dev \
    libzip-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libxml2-dev \
    curl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application files first (needed for composer install)
COPY composer.json composer.lock artisan ./
COPY bootstrap/ ./bootstrap/
COPY routes/ ./routes/
COPY package.json package-lock.json* ./
COPY app/ ./app/
COPY config/ ./config/
COPY database/ ./database/
COPY public/ ./public/
COPY resources/ ./resources/
COPY storage/ ./storage/
COPY tests/ ./tests/

# Copy environment files
COPY .env .env.example ./

# Install PHP dependencies first (before any package discovery)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Install Node dependencies
RUN npm ci --omit=dev 2>/dev/null || npm install --omit=dev 2>/dev/null || true

# Build assets
RUN npm run build 2>/dev/null || true

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Ensure PHP-FPM directories exist
RUN mkdir -p /var/run/php /var/log/php-fpm \
    && chown -R www-data:www-data /var/run/php /var/log/php-fpm

# Fix PHP-FPM config to use alpine-compatible settings
RUN sed -i 's|^listen = /run/php/php8.2-fpm.sock|listen = /var/run/php/php8.2-fpm.sock|' /usr/local/etc/php-fpm.d/www.conf 2>/dev/null || true \
    && sed -i 's|^listen.owner = www-data|listen.owner = www-data|' /usr/local/etc/php-fpm.d/www.conf 2>/dev/null || true \
    && sed -i 's|^listen.group = www-data|listen.group = www-data|' /usr/local/etc/php-fpm.d/www.conf 2>/dev/null || true

COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 7000

ENTRYPOINT ["entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]