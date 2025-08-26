#!/bin/bash

# KL Gestor Pub - Application Deployment Script
# This script deploys the Laravel application to the VPS

set -e

echo "📦 Deploying KL Gestor Pub Application..."
echo "========================================"

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Configuration
APP_DIR="/var/www/klgestorpub"
REPO_URL="https://github.com/YOUR_USERNAME/klgestorpub.git"  # Replace with your repo URL
NGINX_SITE="klgestorpub"

# Check if running as correct user
if [ "$EUID" -eq 0 ]; then
    print_error "Please run this script as a non-root user with sudo privileges"
    exit 1
fi

# Navigate to application directory
print_status "Navigating to application directory..."
cd $APP_DIR

# Clone or update repository
if [ -d ".git" ]; then
    print_status "Updating existing repository..."
    git pull origin main
else
    print_status "Cloning repository..."
    git clone $REPO_URL .
fi

# Install PHP dependencies
print_status "Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Install Node.js dependencies
print_status "Installing Node.js dependencies..."
npm ci --only=production

# Build frontend assets
print_status "Building frontend assets..."
npm run build

# Set up environment file
if [ ! -f ".env" ]; then
    print_status "Creating environment file..."
    cp .env.example .env
    print_warning "Please configure your .env file with database credentials and other settings"
else
    print_status "Environment file already exists"
fi

# Generate application key if needed
if ! grep -q "APP_KEY=base64:" .env; then
    print_status "Generating application key..."
    php artisan key:generate --no-interaction
fi

# Set correct permissions
print_status "Setting file permissions..."
sudo chown -R $USER:www-data $APP_DIR
sudo find $APP_DIR -type f -exec chmod 644 {} \;
sudo find $APP_DIR -type d -exec chmod 755 {} \;
sudo chmod -R 775 $APP_DIR/storage
sudo chmod -R 775 $APP_DIR/bootstrap/cache

# Create storage symlink
print_status "Creating storage symlink..."
php artisan storage:link

# Run database migrations (with confirmation)
echo ""
read -p "Do you want to run database migrations? (y/N): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    print_status "Running database migrations..."
    php artisan migrate --force
    
    read -p "Do you want to run database seeders? (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        print_status "Running database seeders..."
        php artisan db:seed --force
    fi
fi

# Clear and cache configuration
print_status "Optimizing Laravel application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Configure Nginx
if [ ! -f "/etc/nginx/sites-available/$NGINX_SITE" ]; then
    print_status "Configuring Nginx..."
    sudo cp deployment/nginx-klgestorpub.conf /etc/nginx/sites-available/$NGINX_SITE
    sudo ln -s /etc/nginx/sites-available/$NGINX_SITE /etc/nginx/sites-enabled/
    
    # Test Nginx configuration
    sudo nginx -t
    
    if [ $? -eq 0 ]; then
        print_status "Reloading Nginx..."
        sudo systemctl reload nginx
    else
        print_error "Nginx configuration error!"
        exit 1
    fi
else
    print_status "Nginx configuration already exists"
fi

# Setup Supervisor for queue workers (optional)
if [ -f "deployment/supervisor-klgestorpub.conf" ]; then
    print_status "Setting up Supervisor for queue workers..."
    sudo cp deployment/supervisor-klgestorpub.conf /etc/supervisor/conf.d/
    sudo supervisorctl reread
    sudo supervisorctl update
    sudo supervisorctl start klgestorpub-worker:*
fi

# Setup cron jobs for Laravel scheduler
print_status "Setting up Laravel scheduler..."
(crontab -l 2>/dev/null; echo "* * * * * cd $APP_DIR && php artisan schedule:run >> /dev/null 2>&1") | crontab -

print_status "Application deployment completed successfully! ✅"
echo ""
print_warning "Important next steps:"
echo "1. Update your .env file with correct database credentials"
echo "2. Configure your domain name in Nginx configuration"
echo "3. Set up SSL certificate (recommended: Let's Encrypt)"
echo "4. Test the application in your browser"
echo "5. Set up monitoring and backups"

print_status "Deployment finished! 🚀"