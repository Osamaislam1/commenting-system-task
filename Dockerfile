# syntax=docker/dockerfile:1
FROM php:8.3-fpm

# ------------------------------------------------------------
# 1. OS packages + Node.js
# ------------------------------------------------------------
RUN apt-get update && apt-get install -y \
        build-essential libpng-dev libjpeg-dev libfreetype6-dev \
        libonig-dev libxml2-dev libzip-dev libsqlite3-dev \
        zip unzip curl git \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs npm \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ------------------------------------------------------------
# 2. PHP extensions
# ------------------------------------------------------------
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd zip

# ------------------------------------------------------------
# 3. Composer (copied from official image)
# ------------------------------------------------------------
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ------------------------------------------------------------
# 4. App code + dependencies
# ------------------------------------------------------------
WORKDIR /var/www

#   4-a) PHP deps (cached)
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-scripts

#   4-b) Node deps (cached)
COPY package*.json ./
RUN npm ci --omit=dev

#   4-c) Source code
COPY . .

#   4-d) Front-end build & autoload refresh
RUN npm run build && composer run-script post-autoload-dump

# ------------------------------------------------------------
# 5. Writable dirs + permissions
# ------------------------------------------------------------
RUN mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache \
    && chown -R www-data:www-data /var/www

USER www-data
EXPOSE 8000

# ------------------------------------------------------------
# 6. Runtime entry (inline)
# ------------------------------------------------------------
CMD ["bash", "-c", "\
set -euo pipefail; \
DB_CONNECTION=\"${DB_CONNECTION:-sqlite}\"; \
DB_DATABASE=\"${DB_DATABASE:-/var/data/database.sqlite}\"; \
echo \"==> DB_CONNECTION=$DB_CONNECTION\"; \
echo \"==> DB_DATABASE=$DB_DATABASE\"; \
if [ \"$DB_CONNECTION\" = \"sqlite\" ]; then \
  echo 'Setting up SQLite…'; \
  mkdir -p \"$(dirname \"$DB_DATABASE\")\"; \
  touch \"$DB_DATABASE\"; \
  chmod 664 \"$DB_DATABASE\"; \
  php artisan migrate --force; \
else \
  echo 'Waiting for remote database…'; \
  until php artisan migrate --force; do \
    echo '  ↳ not ready, retrying in 3 s'; \
    sleep 3; \
  done; \
fi; \
php artisan config:cache; \
php artisan route:cache; \
php artisan view:cache; \
[ -L public/storage ] || php artisan storage:link; \
echo \"Laravel app starting on :${PORT:-8000}\"; \
exec php artisan serve --host=0.0.0.0 --port=\"${PORT:-8000}\" \
"]
