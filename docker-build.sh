#!/bin/bash

# KL Gestor Pub - Docker Build Script
# This script builds Docker images for the application

set -e

echo "🔨 Building KL Gestor Pub Docker Images..."

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

# Parse command line arguments
BUILD_TYPE=${1:-development}
NO_CACHE=${2:-false}

print_status "Building for $BUILD_TYPE environment..."

# Set build arguments based on environment
if [ "$BUILD_TYPE" = "production" ]; then
    TARGET="production"
    print_status "Building production image with optimizations..."
else
    TARGET="development"
    print_status "Building development image with debugging tools..."
fi

# Build arguments
BUILD_ARGS=""
if [ "$NO_CACHE" = "true" ] || [ "$NO_CACHE" = "--no-cache" ]; then
    BUILD_ARGS="--no-cache"
    print_status "Building without cache..."
fi

# Clean up old images if requested
if [ "$3" = "--clean" ]; then
    print_status "Cleaning up old Docker images..."
    docker system prune -f
    docker image prune -f
    print_success "Old images cleaned up!"
fi

# Build the main application image
print_status "Building application image..."
docker build $BUILD_ARGS \
    --target $TARGET \
    --tag klgestorpub:$BUILD_TYPE \
    --tag klgestorpub:latest \
    .

print_success "Application image built successfully!"

# Build using docker-compose for consistency
print_status "Building with docker-compose..."
if [ "$BUILD_TYPE" = "production" ]; then
    docker-compose -f docker-compose.yml -f docker-compose.prod.yml build $BUILD_ARGS
else
    docker-compose build $BUILD_ARGS
fi

print_success "Docker-compose build completed!"

# Show built images
print_status "Built images:"
docker images | grep -E "(klgestorpub|mysql|redis|nginx)" | head -10

# Verify the build
print_status "Verifying the build..."
if docker run --rm klgestorpub:$BUILD_TYPE php --version > /dev/null 2>&1; then
    print_success "✅ PHP is working correctly!"
else
    print_error "❌ PHP verification failed!"
    exit 1
fi

if docker run --rm klgestorpub:$BUILD_TYPE composer --version > /dev/null 2>&1; then
    print_success "✅ Composer is working correctly!"
else
    print_error "❌ Composer verification failed!"
    exit 1
fi

if docker run --rm klgestorpub:$BUILD_TYPE node --version > /dev/null 2>&1; then
    print_success "✅ Node.js is working correctly!"
else
    print_error "❌ Node.js verification failed!"
    exit 1
fi

# Test Laravel installation
print_status "Testing Laravel installation..."
if docker run --rm -v $(pwd):/var/www/html klgestorpub:$BUILD_TYPE php artisan --version > /dev/null 2>&1; then
    print_success "✅ Laravel is working correctly!"
else
    print_warning "⚠️  Laravel test skipped (may need environment setup)"
fi

print_success "🎉 Build completed successfully!"
echo ""
echo "Built images:"
echo "- klgestorpub:$BUILD_TYPE"
echo "- klgestorpub:latest"
echo ""
echo "Next steps:"
echo "1. Run './docker-start.sh $BUILD_TYPE' to start the application"
echo "2. Or use 'docker-compose up -d' for manual startup"
echo ""
print_status "Build information:"
echo "- Target: $TARGET"
echo "- Environment: $BUILD_TYPE"
echo "- Cache: $([ "$NO_CACHE" = "true" ] && echo "disabled" || echo "enabled")"
echo "- Image size: $(docker images klgestorpub:$BUILD_TYPE --format 'table {{.Size}}' | tail -n 1)"

echo "✅ Build process