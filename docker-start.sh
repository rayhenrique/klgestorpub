#!/bin/bash

# KL Gestor Pub - Docker Start Script
# This script starts the application in Docker containers

set -e

echo "🚀 Starting KL Gestor Pub Docker Environment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    print_error "Docker is not running. Please start Docker and try again."
    exit 1
fi

# Check if docker-compose is available
if ! command -v docker-compose &> /dev/null; then
    print_error "docker-compose is not installed. Please install it and try again."
    exit 1
fi

# Set environment (default to development)
ENVIRONMENT=${1:-development}

print_status "Starting in $ENVIRONMENT mode..."

# Create necessary directories
print_status "Creating necessary directories..."
mkdir -p storage/logs/nginx
mkdir -p storage/logs/supervisor
mkdir -p bootstrap/cache

# Set proper permissions
print_status "Setting proper permissions..."
chmod -R 775 storage bootstrap/cache

# Copy environment file if it doesn't exist
if [ ! -f .env ]; then
    if [ -f .env.docker ]; then
        print_status "Copying .env.docker to .env..."
        cp .env.docker .env
    else
        print_warning ".env file not found. Copying from .env.example..."
        cp .env.example .env
        print_warning "Please update .env file with your configuration."
    fi
fi

# Start containers based on environment
if [ "$ENVIRONMENT" = "production" ]; then
    print_status "Starting production containers..."
    docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d
else
    print_status "Starting development containers..."
    docker-compose --profile development up -d
fi

# Wait for MySQL to be ready
print_status "Waiting for MySQL to be ready..."
until docker-compose exec mysql mysqladmin ping -h"localhost" --silent; do
    echo -n "."
    sleep 2
done
echo ""
print_success "MySQL is ready!"

# Wait for Redis to be ready
print_status "Waiting for Redis to be ready..."
until docker-compose exec redis redis-cli ping | grep -q PONG; do
    echo -n "."
    sleep 1
done
echo ""
print_success "Redis is ready!"

# Run Laravel setup commands
print_status "Running Laravel setup commands..."

# Generate application key if not set
if ! grep -q "APP_KEY=base64:" .env; then
    print_status "Generating application key..."
    docker-compose exec app php artisan key:generate
fi

# Run database migrations
print_status "Running database migrations..."
docker-compose exec app php artisan migrate --force

# Seed database if in development
if [ "$ENVIRONMENT" = "development" ]; then
    print_status "Seeding database..."
    docker-compose exec app php artisan db:seed --force
fi

# Create storage link
print_status "Creating storage link..."
docker-compose exec app php artisan storage:link

# Clear and cache configurations
print_status "Optimizing Laravel..."
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Show running containers
print_status "Running containers:"
docker-compose ps

print_success "🎉 KL Gestor Pub is now running!"
echo ""
echo "📱 Application: http://localhost:8080"
if [ "$ENVIRONMENT" = "development" ]; then
    echo "📧 Mailhog: http://localhost:8025"
    echo "🗄️  phpMyAdmin: http://localhost:8081"
fi
echo "📊 Health Check: http://localhost:8080/health"
echo ""
print_status "To stop the application, run: docker-compose down"
print_status "To view logs, run: docker-compose logs -f"

echo "✅ Setup completed successfully!"