# Use official PHP 8.2 image with Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl \
    && docker-php-ext-install pdo_mysql zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy composer and install
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
