# Use the official PHP Apache image
FROM php:8.2-apache

# Copy your PHP files to the container
COPY . /var/www/html/

# Enable Apache rewrite module (if needed)
RUN a2enmod rewrite

# Expose port 80
EXPOSE 80