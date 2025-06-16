#!/bin/bash

# Render Deployment Script for UZOCA PHP Application

echo "Starting deployment process..."

# Install PHP dependencies
echo "Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

# Check if database connection is available
echo "Checking database connection..."
php -r "
include 'config/database.php';
if (\$conn->connect_error) {
    echo 'Database connection failed: ' . \$conn->connect_error . PHP_EOL;
    exit(1);
} else {
    echo 'Database connection successful!' . PHP_EOL;
}
\$conn->close();
"

# Run database migrations if needed
echo "Setting up database..."
if [ -f "database.sql" ]; then
    echo "Running database setup..."
    # Note: This should be run manually or through Render's database import
    echo "Please import database.sql manually in your Render PostgreSQL dashboard"
fi

echo "Deployment process completed successfully!"
