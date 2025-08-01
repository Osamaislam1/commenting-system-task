# syntax=docker/dockerfile:1
FROM php:8.3-cli

# Set noninteractive mode to avoid prompts
ENV DEBIAN_FRONTEND=noninteractive

# ------------------------------------------------------------
# 1. OS packages + Node.js
# ------------------------------------------------------------
RUN apt-get update && apt-get install -y \
        build-essential \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libonig-dev \
        libxml2-dev \
        libzip-dev \
        libsqlite3-dev \
        sqlite3 \
        zip \
        unzip \
        curl \
        git \
        ca-certificates \
        gnupg \
        lsb-release \
    && mkdir -p /etc/apt/keyrings \
    && curl -fsSL https://deb.nodesource.com/gpgkey/nodesource.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_20.x $(lsb_release -cs) main" | tee /etc/apt/sources.list.d/nodesource.list \
    && apt-get update \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

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

# 4-a) PHP deps (cached)
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-scripts

# 4-b) Node deps (cached)
COPY package*.json ./
RUN npm ci --no-audit --no-fund

# 4-c) Source code (exclude unnecessary files)
COPY . .
RUN rm -rf tests/ .git/ .github/ docker/

# 4-d) Front-end build & autoload refresh
RUN npm run build && composer run-script post-autoload-dump

# ------------------------------------------------------------
# 5. Writable dirs + permissions
# ------------------------------------------------------------
RUN mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache \
    && mkdir -p /var/data \
    && chown -R www-data:www-data /var/data \
    && chmod -R 775 /var/data

# Expose port (will be set by Render)
EXPOSE ${PORT:-8000}

# ------------------------------------------------------------
# 6. Runtime entry (inline)
# ------------------------------------------------------------
CMD ["bash", "-c", "\
set -euo pipefail; \
echo \"==> Starting Laravel application...\"; \
echo \"==> APP_ENV: ${APP_ENV:-production}\"; \
echo \"==> APP_DEBUG: ${APP_DEBUG:-false}\"; \
echo \"==> APP_KEY: ${APP_KEY:-not set}\"; \
DB_CONNECTION=\"${DB_CONNECTION:-sqlite}\"; \
DB_DATABASE=\"${DB_DATABASE:-/var/data/database.sqlite}\"; \
echo \"==> DB_CONNECTION=$DB_CONNECTION\"; \
echo \"==> DB_DATABASE=$DB_DATABASE\"; \
if [ \"$DB_CONNECTION\" = \"sqlite\" ]; then \
  echo 'Setting up SQLite…'; \
  mkdir -p \"$(dirname \"$DB_DATABASE\")\"; \
  touch \"$DB_DATABASE\"; \
  chown www-data:www-data \"$DB_DATABASE\"; \
  chmod 664 \"$DB_DATABASE\"; \
  echo 'Running migrations...'; \
  php artisan migrate --force; \
else \
  echo 'Waiting for remote database…'; \
  until php artisan migrate --force; do \
    echo '  ↳ not ready, retrying in 3 s'; \
    sleep 3; \
  done; \
fi; \
echo 'Clearing and caching config...'; \
php artisan config:clear; \
php artisan config:cache; \
echo 'Clearing and caching routes...'; \
php artisan route:clear; \
php artisan route:cache; \
echo 'Clearing and caching views...'; \
php artisan view:clear; \
php artisan view:cache; \
echo 'Creating storage link...'; \
[ -L public/storage ] || php artisan storage:link; \
echo 'Checking Laravel status...'; \
php artisan about; \
echo 'Listing routes...'; \
php artisan route:list; \
echo \"Laravel app starting on port ${PORT:-8000}\"; \
exec php artisan serve --host=0.0.0.0 --port=\"${PORT:-8000}\" \
"]
