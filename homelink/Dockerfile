FROM php:8.4-apache AS php-base
RUN apt-get update && apt-get install -y --no-install-recommends libicu-dev libzip-dev libonig-dev libsqlite3-dev unzip git \
    && docker-php-ext-install pdo_sqlite pdo_mysql mbstring intl zip opcache \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
WORKDIR /var/www/html
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && printf '<Directory /var/www/html/public>\nAllowOverride All\nRequire all granted\n</Directory>\n' > /etc/apache2/conf-available/homelink.conf \
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
