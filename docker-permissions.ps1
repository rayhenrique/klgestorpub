# KL Gestor Pub - Docker Permissions Setup Script for Windows
# This script sets up proper permissions and directories for Docker on Windows

Write-Host "🔧 Setting up Docker permissions and volumes for KL Gestor Pub..." -ForegroundColor Blue

# Function to create directory if it does not exist
function New-DirectoryIfNotExists {
    param([string]$Path)
    if (!(Test-Path -Path $Path)) {
        New-Item -ItemType Directory -Path $Path -Force | Out-Null
        Write-Host "Created directory: $Path" -ForegroundColor Green
    } else {
        Write-Host "Directory already exists: $Path" -ForegroundColor Yellow
    }
}

# Create necessary directories
Write-Host "📁 Creating necessary directories..." -ForegroundColor Cyan

$directories = @(
    "storage\app\public",
    "storage\framework\cache\data",
    "storage\framework\sessions",
    "storage\framework\testing",
    "storage\framework\views",
    "storage\logs",
    "storage\logs\nginx",
    "storage\logs\supervisor",
    "bootstrap\cache",
    "deployment\docker\nginx",
    "deployment\docker\php",
    "deployment\docker\mysql",
    "deployment\docker\redis",
    "deployment\docker\supervisor"
)

foreach ($dir in $directories) {
    New-DirectoryIfNotExists -Path $dir
}

# Create .gitkeep files for empty directories
Write-Host "📝 Creating .gitkeep files..." -ForegroundColor Cyan

$gitkeepDirs = @(
    "storage\logs\nginx",
    "storage\logs\supervisor"
)

foreach ($dir in $gitkeepDirs) {
    $gitkeepPath = Join-Path $dir ".gitkeep"
    if (!(Test-Path -Path $gitkeepPath)) {
        New-Item -ItemType File -Path $gitkeepPath -Force | Out-Null
        Write-Host "✅ Created .gitkeep: $gitkeepPath" -ForegroundColor Green
    }
}

# Set file attributes (Windows equivalent of chmod)
Write-Host "🔐 Setting file permissions..." -ForegroundColor Cyan

# Make shell scripts executable (add .bat extensions for Windows)
if (Test-Path "docker-start.sh") {
    Write-Host "✅ docker-start.sh is ready" -ForegroundColor Green
}
if (Test-Path "docker-setup.sh") {
    Write-Host "✅ docker-setup.sh is ready" -ForegroundColor Green
}
if (Test-Path "docker-build.sh") {
    Write-Host "✅ docker-build.sh is ready" -ForegroundColor Green
}

# Create Windows batch files for easier execution
Write-Host "🪟 Creating Windows batch files..." -ForegroundColor Cyan

# docker-start.bat
$dockerStartBat = @'
@echo off
echo Starting KL Gestor Pub with Docker...
if "%1"=="" (
    bash docker-start.sh development
) else (
    bash docker-start.sh %1
)
'@

Set-Content -Path "docker-start.bat" -Value $dockerStartBat
Write-Host "✅ Created docker-start.bat" -ForegroundColor Green

# docker-setup.bat
$dockerSetupBat = @'
@echo off
echo Setting up KL Gestor Pub Docker environment...
bash docker-setup.sh
'@

Set-Content -Path "docker-setup.bat" -Value $dockerSetupBat
Write-Host "✅ Created docker-setup.bat" -ForegroundColor Green

# docker-build.bat
$dockerBuildBat = @'
@echo off
echo Building KL Gestor Pub Docker images...
if "%1"=="" (
    bash docker-build.sh development
) else (
    bash docker-build.sh %1 %2 %3
)
'@

Set-Content -Path "docker-build.bat" -Value $dockerBuildBat
Write-Host "✅ Created docker-build.bat" -ForegroundColor Green

# Verify Docker is available
Write-Host "🐳 Checking Docker availability..." -ForegroundColor Cyan

try {
    $dockerVersion = docker --version 2>$null
    if ($dockerVersion) {
        Write-Host "✅ Docker is available: $dockerVersion" -ForegroundColor Green
    } else {
        Write-Host "⚠️  Docker not found. Please install Docker Desktop." -ForegroundColor Yellow
    }
} catch {
    Write-Host "⚠️  Docker not found. Please install Docker Desktop." -ForegroundColor Yellow
}

# Check docker-compose
try {
    $dockerComposeVersion = docker-compose --version 2>$null
    if ($dockerComposeVersion) {
        Write-Host "✅ Docker Compose is available: $dockerComposeVersion" -ForegroundColor Green
    } else {
        Write-Host "⚠️  Docker Compose not found. Please install Docker Compose." -ForegroundColor Yellow
    }
} catch {
    Write-Host "⚠️  Docker Compose not found. Please install Docker Compose." -ForegroundColor Yellow
}

# Create volume directories for Docker
Write-Host "💾 Setting up Docker volumes..." -ForegroundColor Cyan

$volumeDirs = @(
    "docker-volumes\mysql",
    "docker-volumes\redis",
    "docker-volumes\storage",
    "docker-volumes\logs"
)

foreach ($dir in $volumeDirs) {
    New-DirectoryIfNotExists -Path $dir
}

# Summary
Write-Host ""
Write-Host "🎉 Docker setup completed successfully!" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "1. Run 'docker-setup.bat' to initialize the Docker environment" -ForegroundColor White
Write-Host "2. Run docker-start.bat to start the application" -ForegroundColor White
Write-Host "3. Access the application at http://localhost:8080" -ForegroundColor White
Write-Host ""
Write-Host "Available commands:" -ForegroundColor Cyan
Write-Host "- docker-setup.bat    : Initialize Docker environment" -ForegroundColor White
Write-Host "- docker-start.bat    : Start the application" -ForegroundColor White
Write-Host "- docker-build.bat    : Build Docker images" -ForegroundColor White
Write-Host "- docker-start.sh     : Linux/Mac start script" -ForegroundColor White
Write-Host "- docker-setup.sh     : Linux/Mac setup script" -ForegroundColor White
Write-Host "- docker-build.sh     : Linux/Mac build script" -ForegroundColor White
Write-Host ""
Write-Host "✅ Setup completed!" -ForegroundColor Green