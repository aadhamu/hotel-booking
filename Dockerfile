# Use the official PHP image with Apache
FROM php:8.1-apache

# Install mysqli
RUN docker-php-ext-install mysqli

# Copy your PHP project into the container
COPY . /var/www/html/

# Optional: Enable Apache rewrite (if using .htaccess)
RUN a2enmod rewrite
