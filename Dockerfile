# Use the official PHP with Apache image as the base image
FROM php:apache

# Set the working directory inside the container
WORKDIR /var/www/html

# Copy the contents of the current directory (local machine) to the working directory in the container
COPY . /var/www/html

# Expose ports for HTTP and HTTPS traffic
EXPOSE 80
EXPOSE 443

# Start the Apache web server when the container starts
CMD ["apache2-foreground"]
