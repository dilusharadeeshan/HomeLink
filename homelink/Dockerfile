FROM php:8.4-apache AS php-base
RUN apt-get update && apt-get install -y --no-install-recommends \
        libicu-dev libzip-dev libonig-dev libsqlite3-dev libpng-dev libjpeg62-turbo-dev \
        libfreetype6-dev zip unzip git curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_sqlite pdo_mysql mbstring exif pcntl bcmath gd intl zip opcache \
    && a2enmod headers rewrite \
    && printf '%s\n' \
        'expose_php=Off' \
        'display_errors=Off' \
        'log_errors=On' \
        'memory_limit=256M' \
        'opcache.enable=1' \
        'opcache.validate_timestamps=0' \
        'opcache.memory_consumption=128' \
        'opcache.max_accelerated_files=10000' \
        > /usr/local/etc/php/conf.d/homelink-production.ini \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
WORKDIR /var/www/html
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && printf '%s\n' \
        'ServerTokens Prod' \
        'ServerSignature Off' \
        '<Directory /var/www/html/public>' \
        'AllowOverride All' \
        'Options -Indexes' \
        'Require all granted' \
        '</Directory>' \
        '<IfModule mod_headers.c>' \
        'Header always set X-Content-Type-Options "nosniff"' \
        'Header always set X-Frame-Options "SAMEORIGIN"' \
        'Header always set Referrer-Policy "strict-origin-when-cross-origin"' \
        'Header always set Permissions-Policy "camera=(), microphone=(), geolocation=()"' \
        '</IfModule>' \
        > /etc/apache2/conf-available/homelink.conf \
    && a2enconf homelink

FROM php-base AS dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction
COPY . .
RUN composer dump-autoload --no-dev --optimize --no-interaction

FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY resources ./resources
COPY vite.config.js ./
COPY --from=dependencies /var/www/html/vendor ./vendor
RUN npm run build

FROM php-base AS application
COPY --from=dependencies /var/www/html /var/www/html
COPY --from=assets /app/public/build /var/www/html/public/build
COPY docker/entrypoint.sh /usr/local/bin/homelink-entrypoint
RUN sed -i 's/\r$//' /usr/local/bin/homelink-entrypoint \
    && chmod +x /usr/local/bin/homelink-entrypoint \
    && mkdir -p /var/lib/homelink storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs \
    && chown -R www-data:www-data storage bootstrap/cache /var/lib/homelink
EXPOSE 80
ENTRYPOINT ["homelink-entrypoint"]
CMD ["apache2-foreground"]
