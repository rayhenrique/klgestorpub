#!/bin/bash

# KL Gestor Pub - Database Setup Script
# Run this script to create database and user for the application

set -e

echo "🗄️  Setting up MySQL Database for KL Gestor Pub..."
echo "=================================================="

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

# Database configuration
DB_NAME="klgestorpub"
DB_USER="klgestor"

# Generate a random password
DB_PASSWORD=$(openssl rand -base64 32)

print_status "Creating database and user..."

# Create database and user
sudo mysql -e "CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';"
sudo mysql -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

print_status "Database setup completed!"

# Create .env configuration snippet
echo ""
print_warning "Add these lines to your .env file:"
echo "=================================="
echo "DB_CONNECTION=mysql"
echo "DB_HOST=127.0.0.1"
echo "DB_PORT=3306"
echo "DB_DATABASE=${DB_NAME}"
echo "DB_USERNAME=${DB_USER}"
echo "DB_PASSWORD=${DB_PASSWORD}"
echo "=================================="
echo ""

# Save credentials to file (with restricted permissions)
CREDS_FILE="/tmp/klgestorpub_db_credentials.txt"
echo "Database: ${DB_NAME}" > ${CREDS_FILE}
echo "Username: ${DB_USER}" >> ${CREDS_FILE}
echo "Password: ${DB_PASSWORD}" >> ${CREDS_FILE}
chmod 600 ${CREDS_FILE}

print_status "Database credentials saved to: ${CREDS_FILE}"
print_warning "Please copy these credentials and then delete the file for security!"

print_status "Database setup completed successfully! ✅"