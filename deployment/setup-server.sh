#!/bin/bash

# KL Gestor Pub - VPS Server Setup Script
# This script sets up a Ubuntu server for Laravel deployment

set -e

echo "🚀 Starting KL Gestor Pub VPS Setup..."
echo "======================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Update system
print_status "Updating system packages..."
sudo apt update
sudo apt upgrade -y

# Install basic dependencies
print_status "Installing basic dependencies..."
sudo apt install -y curl git unzip software-properties-common apt-transport-https lsb-release ca-certificates

# Install Nginx
print_status "Installing Nginx..."
sudo apt install -y nginx
sudo systemctl enable nginx
sudo systemctl start nginx

# Install MySQL
print_status "Installing MySQL..."
sudo apt install -y mysql-server
sudo systemctl enable mysql
sudo systemctl start mysql

# Install PHP 8.2
print_status "Installing PHP 8.2 and extensions..."
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2-fpm php8.2-cli php8.2-mysql php8.2-zip php8.2-gd \
    php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath php8.2-intl \
    php8.2-soap php8.2-xsl php8.2-opcache

# Install Composer
print_status "Installing Composer..."
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Install Node.js and NPM
print_status "Installing Node.js..."
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Supervisor for queue management
print_status "Installing Supervisor..."
sudo apt install -y supervisor

# Configure firewall
print_status "Configuring UFW firewall..."
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw --force enable

# Create application directory
print_status "Creating application directory..."
sudo mkdir -p /var/www/klgestorpub
sudo chown -R $USER:www-data /var/www/klgestorpub
sudo chmod -R 755 /var/www/klgestorpub

print_status "Basic server setup completed!"
print_warning "Next steps:"
echo "1. Secure MySQL installation: sudo mysql_secure_installation"
echo "2. Create database and user for the application"
echo "3. Configure Nginx virtual host"
echo "4. Deploy the application code"
echo "5. Configure SSL certificate"

print_status "Setup script finished successfully! ✅"