# Stage 1: Build frontend assets with Node.js
FROM node:20-alpine AS frontend-build

WORKDIR /app

# Copy package files
COPY package*.json ./

# Install npm dependencies
RUN npm install

# Copy all source files
COPY . .

# Build Vite assets
RUN npm run build

# Stage 2: PHP Runtime
FROM php:8.3-cli

# Install system dependencies
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

# Install PHP extensions
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

# Copy application files
COPY . .

# Copy built Vite assets from frontend-build stage
COPY --from=frontend-build /app/public/build /app/public/build

# Install PHP dependencies
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
CMD php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    php artisan storage:link && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8080}