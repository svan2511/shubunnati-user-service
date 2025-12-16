# Step 1: Use official PHP image with Apache
FROM php:8.2-apache

# Step 2: Set working directory
WORKDIR /var/www/html

# Step 3: Install system dependencies and PHP extensions
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
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip intl opcache \
    && a2enmod rewrite

# Step 4: Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Step 5: Copy existing application
COPY . .

# Step 6: Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Step 7: Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Step 8: Install Node dependencies (if using frontend assets)
RUN npm install
# RUN npm run build  # Uncomment if using Laravel Mix / Vite

# Step 9: Expose port 80
EXPOSE 80

# Step 10: Run migrations & seeders then start Apache
CMD php artisan migrate --force && php artisan db:seed --force && apache2-foreground
