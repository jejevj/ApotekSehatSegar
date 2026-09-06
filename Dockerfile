FROM php:8.2-fpm-alpine

# ===============================
# Install system dependencies
# ===============================
RUN apk add --no-cache \
    nginx \
    supervisor \
    nodejs \
    npm \
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
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
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


# ===============================
# Install Composer
# ===============================
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


WORKDIR /var/www/html


# ===============================
# Copy composer files first
# (Docker cache optimization)
# ===============================
COPY composer.json composer.lock ./


RUN composer install \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-interaction \
    --no-progress


# ===============================
# Copy Laravel application
# ===============================
COPY . .


# ===============================
# Frontend build
# ===============================
RUN npm ci --omit=dev \
    && npm run build


# ===============================
# Laravel permissions
# ===============================
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache \
    \
    && chown -R www-data:www-data /var/www/html \
    \
    && chmod -R 775 \
        storage \
        bootstrap/cache


# ===============================
# Config nginx + supervisor
# ===============================
COPY docker/nginx.conf \
    /etc/nginx/http.d/default.conf

COPY docker/supervisord.conf \
    /etc/supervisor/conf.d/supervisord.conf


COPY docker/entrypoint.sh \
    /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh


EXPOSE 7000


ENTRYPOINT ["entrypoint.sh"]

CMD [
    "/usr/bin/supervisord",
    "-c",
    "/etc/supervisor/conf.d/supervisord.conf"
]