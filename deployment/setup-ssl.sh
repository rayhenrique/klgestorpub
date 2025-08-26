#!/bin/bash

# KL Gestor Pub - SSL Certificate Setup Script
# This script sets up Let's Encrypt SSL certificate

set -e

echo "🔒 Setting up SSL Certificate for KL Gestor Pub..."
echo "================================================"

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

# Check if domain is provided
if [ $# -eq 0 ]; then
    print_error "Please provide your domain name as an argument"
    echo "Usage: $0 your-domain.com"
    exit 1
fi

DOMAIN=$1

# Install Certbot
print_status "Installing Certbot..."
sudo apt update
sudo apt install -y certbot python3-certbot-nginx

# Obtain SSL certificate
print_status "Obtaining SSL certificate for $DOMAIN..."
sudo certbot --nginx -d $DOMAIN --non-interactive --agree-tos --email admin@$DOMAIN

# Test automatic renewal
print_status "Testing automatic renewal..."
sudo certbot renew --dry-run

# Create renewal cron job
print_status "Setting up automatic renewal..."
(crontab -l 2>/dev/null; echo "0 12 * * * /usr/bin/certbot renew --quiet") | sudo crontab -

print_status "SSL certificate setup completed successfully! ✅"
echo ""
print_warning "Your site is now accessible via HTTPS:"
echo "https://$DOMAIN"
echo ""
print_status "Certificate will auto-renew every 90 days."