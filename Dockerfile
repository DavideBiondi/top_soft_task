# Use the official PHP 8.2.20 image with Apache as the base image
FROM php:8.2.20apache

# Copy the application files
COPY php_webapp/login_module/ /var/www/html

# Install the necessary extensions
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Setting permissions
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Set the working directory
WORKDIR /var/www/html

# Expose the port
EXPOSE 80

# Start the Apache Server
CMD ["apache2-foreground"]
