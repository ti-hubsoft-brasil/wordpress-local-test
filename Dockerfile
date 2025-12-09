FROM wordpress:latest

# Enable Apache mod_rewrite
RUN a2enmod rewrite
