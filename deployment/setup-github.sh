#!/bin/bash

# KL Gestor Pub - GitHub Repository Setup Script
# This script helps you set up and push to GitHub repository

set -e

echo "📡 Setting up GitHub Repository for KL Gestor Pub..."
echo "=================================================="

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
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

print_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

# Check if git is configured
print_status "Checking Git configuration..."
if ! git config user.name > /dev/null 2>&1; then
    print_warning "Git user.name not configured. Please set it:"
    echo "git config --global user.name \"Your Name\""
    exit 1
fi

if ! git config user.email > /dev/null 2>&1; then
    print_warning "Git user.email not configured. Please set it:"
    echo "git config --global user.email \"your.email@example.com\""
    exit 1
fi

# Check if we're in a git repository
if [ ! -d ".git" ]; then
    print_error "Not in a Git repository!"
    exit 1
fi

# Check for existing remote
if git remote | grep -q origin; then
    print_info "GitHub remote already exists:"
    git remote -v
    echo
    read -p "Do you want to push to the existing remote? (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        print_status "Pushing to existing remote..."
        git push -u origin main
        print_status "Successfully pushed to GitHub! ✅"
        exit 0
    else
        print_info "Skipping push to existing remote."
        exit 0
    fi
fi

# Get GitHub username
echo
print_info "GitHub Repository Setup"
echo "======================="
echo
read -p "Enter your GitHub username: " GITHUB_USERNAME

if [ -z "$GITHUB_USERNAME" ]; then
    print_error "GitHub username is required!"
    exit 1
fi

# Repository name
REPO_NAME="klgestorpub"
GITHUB_URL="https://github.com/${GITHUB_USERNAME}/${REPO_NAME}.git"

print_status "GitHub repository URL: $GITHUB_URL"
echo

# Instructions for creating GitHub repository
print_warning "IMPORTANT: Create the GitHub repository first!"
echo "==========================================="
echo "1. Go to: https://github.com/new"
echo "2. Repository name: $REPO_NAME"
echo "3. Description: Sistema de Gestão de Contas Públicas - KL Gestor Pub v1.4.0"
echo "4. Choose Public or Private"
echo "5. DON'T initialize with README, .gitignore, or license (we already have them)"
echo "6. Click 'Create repository'"
echo

read -p "Have you created the GitHub repository? (y/N): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    print_warning "Please create the GitHub repository first, then run this script again."
    exit 0
fi

# Add remote and push
print_status "Adding GitHub remote..."
git remote add origin $GITHUB_URL

print_status "Verifying remote..."
git remote -v

print_status "Pushing to GitHub..."
git push -u origin main

if [ $? -eq 0 ]; then
    print_status "🎉 Successfully pushed to GitHub!"
    echo
    print_info "Your repository is now available at:"
    echo "https://github.com/${GITHUB_USERNAME}/${REPO_NAME}"
    echo
    print_status "Repository features:"
    echo "✅ Enhanced Laravel 11.31 application"
    echo "✅ Comprehensive test suite with PHPUnit"
    echo "✅ Form validation with Request classes"
    echo "✅ Service layer for business logic"
    echo "✅ Complete VPS deployment scripts"
    echo "✅ Production-ready configurations"
    echo "✅ Security enhancements"
    echo "✅ Full documentation"
    echo
    print_status "GitHub repository update completed! ✅"
else
    print_error "Failed to push to GitHub!"
    print_warning "Common solutions:"
    echo "1. Check your GitHub credentials"
    echo "2. Verify repository exists and you have access"
    echo "3. Try: git push -u origin main --force (if safe to do)"
    exit 1
fi