# Stage 1: Build stage
FROM php:8.2-cli AS build
WORKDIR /app

# Copy application code and composer files
COPY composer.json composer.lock ./
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies and optimize autoloader
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs && \
    composer dump-autoload --optimize

# Stage 2: Production stage
FROM php:8.2-cli
WORKDIR /app

# Install system libraries and PHP extensions
RUN apt-get update && apt-get install -y \
      libpq-dev pkg-config libzip-dev libcurl4-openssl-dev libxml2-dev zip unzip git && \
    docker-php-ext-install pdo_mysql pdo_pgsql zip curl xml mysqli

# Copy built application from build stage
COPY --from=build /app /app

# Create startup script
RUN echo '#!/bin/bash\nphp -S 0.0.0.0:${PORT:-80} -t .' > /app/start.sh && chmod +x /app/start.sh

# Expose port and start
EXPOSE ${PORT:-80}
CMD ["/app/start.sh"]
