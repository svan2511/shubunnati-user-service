# ========================
# Step 1: Base image
# ========================
FROM php:8.2-apache

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
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip intl opcache \
    \
    # Fix Apache MPM conflict
    && a2dismod mpm_event mpm_worker || true \
    && a2enmod mpm_prefork rewrite

# ========================
# Step 4: Install Composer globally
# ========================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ========================
# Step 5: Copy application code
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
# Step 8: Expose port 80
# ========================
EXPOSE 80

# ========================
# Step 9: Run migrations, seeders, then start Apache
# ========================
CMD php artisan migrate --force && php artisan db:seed --force && apache2-foreground
