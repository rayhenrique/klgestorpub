#!/bin/bash

# KL Gestor Pub - Docker Setup Script
# This script performs initial setup for the Docker environment

set -e

echo "🔧 Setting up KL Gestor Pub Docker Environment..."

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

print_status "Creating necessary directories..."

# Create all necessary directories
mkdir -p storage/app/public
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/testing
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p storage/logs/nginx
mkdir -p storage/logs/supervisor
mkdir -p bootstrap/cache
mkdir -p deployment/docker/nginx
mkdir -p deployment/docker/php
mkdir -p deployment/docker/mysql
mkdir -p deployment/docker/redis
mkdir -p deployment/docker/supervisor

print_success "Directories created successfully!"

print_status "Setting up environment files..."

# Create .env.docker if it doesn't exist
if [ ! -f .env.docker ]; then
    print_status "Creating .env.docker file..."
    cat > .env.docker << 'EOF'
# KL Gestor Pub - Docker Environment Configuration

APP_NAME="KL Gestor Pub"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=America/Sao_Paulo
APP_URL=http://localhost:8080
APP_VERSION=1.4.0

APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=pt_BR
APP_FAKER_LOCALE=pt_BR

APP_MAINTENANCE_DRIVER=file
APP_MAINTENANCE_STORE=database

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=klgestorpub
DB_USERNAME=klgestorpub
DB_PASSWORD=klgestorpub_password

# Session Configuration
SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

# Cache Configuration
CACHE_STORE=redis
CACHE_PREFIX=klgestorpub_cache

# Queue Configuration
QUEUE_CONNECTION=redis

# Redis Configuration
REDIS_CLIENT=phpredis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1

# Mail Configuration (Mailhog for development)
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@klgestorpub.local"
MAIL_FROM_NAME="${APP_NAME}"

# File Storage
FILESYSTEM_DISK=local

# Broadcasting
BROADCAST_CONNECTION=log

# Vite Configuration
VITE_APP_NAME="${APP_NAME}"
VITE_APP_ENV="${APP_ENV}"

# City Settings
CITY_NAME="Sua Cidade"
CITY_STATE="SP"
CITY_CNPJ="00.000.000/0001-00"

# Backup Configuration
BACKUP_DISK=local
BACKUP_RETENTION_DAYS=30

# Security
SANCTUM_STATEFUL_DOMAINS=localhost:8080
SESSION_SECURE_COOKIE=false
EOF
    print_success ".env.docker created successfully!"
else
    print_warning ".env.docker already exists, skipping..."
fi

# Create .dockerignore if it doesn't exist
if [ ! -f .dockerignore ]; then
    print_status "Creating .dockerignore file..."
    cat > .dockerignore << 'EOF'
# Git
.git
.gitignore
.gitattributes

# Documentation
README.md
*.md
docs/

# IDE
.vscode/
.idea/
*.swp
*.swo
*~

# OS
.DS_Store
Thumbs.db

# Node
node_modules/
npm-debug.log*
yarn-debug.log*
yarn-error.log*

# Laravel
/storage/app/*
!/storage/app/.gitignore
!/storage/app/public/
/storage/framework/cache/*
!/storage/framework/cache/.gitignore
/storage/framework/sessions/*
!/storage/framework/sessions/.gitignore
/storage/framework/testing/*
!/storage/framework/testing/.gitignore
/storage/framework/views/*
!/storage/framework/views/.gitignore
/storage/logs/*
!/storage/logs/.gitignore
/bootstrap/cache/*
!/bootstrap/cache/.gitignore

# Environment files
.env
.env.*
!.env.example
!.env.docker

# Testing
/coverage/
.phpunit.result.cache

# Composer
/vendor/
composer.lock

# Build files
/public/build/
/public/hot

# Logs
*.log

# Temporary files
*.tmp
*.temp

# Docker
docker-compose.override.yml

# Deployment
/deployment/secrets/
/deployment/ssl/

# Backup files
*.backup
*.bak
*.sql
EOF
    print_success ".dockerignore created successfully!"
else
    print_warning ".dockerignore already exists, skipping..."
fi

print_status "Setting proper permissions..."

# Set proper permissions for Laravel directories
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Make scripts executable
chmod +x docker-start.sh 2>/dev/null || true
chmod +x docker-build.sh 2>/dev/null || true
chmod +x docker-setup.sh 2>/dev/null || true

print_success "Permissions set successfully!"

print_status "Building Docker images..."

# Build the Docker images
docker-compose build --no-cache

print_success "Docker images built successfully!"

print_status "Pulling required Docker images..."

# Pull required images
docker-compose pull

print_success "Docker images pulled successfully!"

print_status "Creating Docker networks and volumes..."

# Create networks and volumes
docker-compose up --no-start

print_success "Docker networks and volumes created successfully!"

print_success "🎉 Docker environment setup completed!"
echo ""
echo "Next steps:"
echo "1. Update .env.docker with your specific configuration"
echo "2. Run './docker-start.sh' to start the application"
echo "3. Access the application at http://localhost:8080"
echo ""
print_status "For development with additional services, run:"
print_status "./docker-start.sh development"
echo ""
print_status "For production deployment, run:"
print_status "./docker-start.sh production"

echo "✅ Setup completed successfully!"