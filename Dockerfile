FROM php:8.3-cli

# Install system dependencies including PostgreSQL
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions with PostgreSQL support
RUN docker-php-ext-install \
    pdo_pgsql \
    pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy ALL files
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev --no-interaction --prefer-dist

# Create storage directories and set permissions
RUN mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# Expose port
EXPOSE 8080

# Start server
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8080}