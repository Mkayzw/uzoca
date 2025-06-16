# Use official PHP image with built-in server
FROM php:8.2-cli

# Install required PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Install PostgreSQL extension (in case you want to use PostgreSQL)
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy composer files first
COPY composer.json ./
COPY composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy application code
COPY . .

# Create a startup script
RUN echo '#!/bin/bash\nphp -S 0.0.0.0:$PORT -t .' > /app/start.sh \
    && chmod +x /app/start.sh

# Expose port
EXPOSE $PORT

# Start PHP built-in server
CMD ["/app/start.sh"]
