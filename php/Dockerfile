FROM php:8.2-fpm

# Install required extensions
RUN apt-get update && apt-get install -y \
    libmagickwand-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    libicu-dev

# Install and enable PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql zip gd exif intl
RUN pecl install imagick && docker-php-ext-enable imagick

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
