# Use official PHP image with built-in server
FROM php:8.2-cli
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install required PHP extensions and system libraries
RUN apt-get update && \
    apt-get install -y \
      libpq-dev \
      pkg-config \
      libzip-dev \
      libcurl4-openssl-dev \
      libxml2-dev \
      git \
      zip \
      unzip && \
    docker-php-ext-install \
      pdo_pgsql zip curl xml mysqli pdo pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy composer files first
COPY composer.json ./
COPY composer.lock ./

# Install PHP dependencies without platform requirement checks
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Copy application code
COPY . .

# Create a startup script
RUN echo '#!/bin/bash\nphp -S 0.0.0.0:$PORT -t .' > /app/start.sh \
    && chmod +x /app/start.sh

# Expose port
EXPOSE $PORT

# Start PHP built-in server
CMD ["/app/start.sh"]
