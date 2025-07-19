FROM php:8.1-apache

# Enable mysqli
RUN docker-php-ext-install mysqli

# Enable Apache rewrite (if needed)
RUN a2enmod rewrite

# Copy your code into the Apache root
COPY . /var/www/html/

# Set correct ownership and permissions
RUN chown -R www-data:www-data /var/www/html/uploads && \
    chmod -R 755 /var/www/html/uploads
