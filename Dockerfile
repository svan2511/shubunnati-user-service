# ========================
# Step 1: Base image - PHP-FPM
# ========================
FROM php:8.2-fpm

# ========================
# Step 2: Set working directory
# ========================
WORKDIR /var/www/html

# ========================
# Step 3: Install system dependencies and PHP extensions
# ========================
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    libicu-dev \
    libjpeg-dev \
    libfreetype6-dev \
    supervisor \
    nginx \
    gettext-base \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip intl opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ========================
# Step 4: Install Composer globally
# ========================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ========================
# Step 5: Copy Laravel application
# ========================
COPY . .

# ========================
# Step 6: Set permissions for Laravel
# ========================
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ========================
# Step 7: Install PHP dependencies
# ========================
RUN composer install --no-dev --optimize-autoloader

# ========================
# Step 8: Copy Nginx configuration template
# ========================
COPY ./nginx.conf.template /etc/nginx/sites-available/default.template

# ========================
# Step 9: Copy Supervisor configuration
# ========================
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ========================
# Step 10: Copy and set entrypoint
# ========================
COPY ./entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

# ========================
# Step 11: Start Supervisor (runs PHP-FPM + Nginx + migrations/seeders)
# ========================
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]