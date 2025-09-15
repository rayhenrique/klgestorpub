# 🚀 KL Gestor Pub - Deployment Guide v1.4.0

This directory contains all the necessary scripts and configurations for deploying KL Gestor Pub to production environments. Choose between traditional VPS deployment or modern Docker containerization.

## 🎯 Deployment Options

### 🐳 Docker Deployment (Recommended)
- **Containerized environment** with all dependencies
- **Easy scaling** and maintenance
- **Consistent environment** across different servers
- **Quick setup** with automated scripts

### 🖥️ Traditional VPS Deployment
- **Direct server installation** on Ubuntu
- **Full control** over system configuration
- **Custom optimization** possibilities

## 📋 Prerequisites

### For Docker Deployment
- Ubuntu 20.04+ VPS server with Docker support
- **Docker** and **Docker Compose** installed
- **4GB RAM** minimum
- **20GB** disk space
- Domain name pointing to your server IP

### For Traditional VPS Deployment
- Ubuntu 20.04+ VPS server
- Root or sudo access
- Domain name pointing to your server IP
- SSH access to the server

## 🛠️ Deployment Steps

### 🐳 Docker Deployment (v1.4.0)

#### 1. Clone and Setup
```bash
# Clone repository
git clone https://github.com/your-repo/klgestorpub.git
cd klgestorpub

# Copy production environment
cp .env.docker .env

# Edit environment variables
nano .env
```

#### 2. Production Build
```bash
# Build production images
./docker-build.sh production

# Start production services
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d
```

#### 3. Initialize Application
```bash
# Run migrations and seed
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan db:seed --force

# Optimize for production
docker-compose exec app php artisan optimize
```

#### 4. SSL Setup (Nginx Proxy)
```bash
# Setup reverse proxy with SSL
./deployment/setup-docker-ssl.sh your-domain.com
```

### 🖥️ Traditional VPS Deployment

#### 1. Server Setup
```bash
# Make script executable
chmod +x deployment/setup-server.sh

# Run server setup (on VPS)
./deployment/setup-server.sh
```

#### 2. Database Setup
```bash
# Make script executable
chmod +x deployment/setup-database.sh

# Setup MySQL database
./deployment/setup-database.sh
```

#### 3. Application Deployment
```bash
# Make script executable
chmod +x deployment/deploy-app.sh

# Deploy the application
./deployment/deploy-app.sh
```

#### 4. SSL Certificate (Optional but Recommended)
```bash
# Make script executable
chmod +x deployment/setup-ssl.sh

# Setup SSL certificate
./deployment/setup-ssl.sh your-domain.com
```

## 📁 Files Description

### Docker Deployment Files (v1.4.0)
| File | Description |
|------|-------------|
| `docker-build.sh` | Build Docker images for production |
| `docker-compose.prod.yml` | Production Docker Compose override |
| `setup-docker-ssl.sh` | SSL setup for Docker deployment |
| `nginx-proxy.conf` | Nginx reverse proxy configuration |
| `.env.docker` | Docker environment template |

### Traditional VPS Files
| File | Description |
|------|-------------|
| `setup-server.sh` | Initial server setup with Nginx, PHP, MySQL, Node.js |
| `setup-database.sh` | Creates database and user for the application |
| `deploy-app.sh` | Deploys the Laravel application |
| `setup-ssl.sh` | Configures SSL certificate with Let's Encrypt |
| `nginx-klgestorpub.conf` | Nginx virtual host configuration |
| `supervisor-klgestorpub.conf` | Supervisor configuration for queue workers |
| `.env.production` | Production environment template |

## ⚙️ Configuration

### 🆕 v1.4.0 Improvements

#### Responsive Design
- **Mobile-first approach** with breakpoints for all devices
- **Touch-optimized interface** for tablets and smartphones
- **Collapsible sidebar** with smooth animations
- **Adaptive tables** with horizontal scroll on mobile

#### Backup System
- **Automated backup creation** via web interface
- **Secure download** with authentication
- **Intelligent restoration** with pre-backup safety
- **Command-line tools** for automated backups

#### Enhanced Security
- **WAI-ARIA compliance** for accessibility
- **Improved input validation** with custom Form Requests
- **Enhanced error handling** with user-friendly messages
- **Optimized database queries** for better performance

### Environment Variables

#### Docker Environment
Copy `.env.docker` to `.env` and configure:

```bash
cp .env.docker .env
nano .env
```

#### Traditional VPS Environment
Copy `.env.production` to `.env` and configure:

```bash
cp deployment/.env.production .env
nano .env
```

Key settings to configure:
- `APP_URL` - Your domain URL
- `DB_*` - Database credentials
- `MAIL_*` - Email configuration
- `BACKUP_*` - Backup system settings (v1.4.0)
- Domain-specific settings

### Nginx Configuration

The Nginx configuration includes:
- ✅ Security headers
- ✅ Gzip compression
- ✅ Static file optimization
- ✅ PHP-FPM integration
- ✅ Laravel routing support

### Supervisor Configuration

Queue worker configuration:
- 2 worker processes
- Auto-restart on failure
- Logging to storage/logs/worker.log
- 1-hour max execution time

## 🔒 Security Features

- **Firewall**: UFW configured for SSH and HTTP/HTTPS
- **SSL**: Let's Encrypt automatic certificate
- **Headers**: Security headers in Nginx
- **Permissions**: Correct file and directory permissions
- **Environment**: Production environment settings

## 📊 Monitoring & Maintenance

### Log Files

```bash
# Application logs
tail -f /var/www/klgestorpub/storage/logs/laravel.log

# Nginx logs
sudo tail -f /var/log/nginx/klgestorpub_access.log
sudo tail -f /var/log/nginx/klgestorpub_error.log

# Queue worker logs
sudo tail -f /var/www/klgestorpub/storage/logs/worker.log
```

### Useful Commands

```bash
# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
sudo supervisorctl restart klgestorpub-worker:*

# Update application
cd /var/www/klgestorpub
git pull origin main
composer install --no-dev --optimize-autoloader
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Check status
sudo systemctl status nginx
sudo systemctl status mysql
sudo supervisorctl status
```

## 🆘 Troubleshooting

### Common Issues

1. **Permission Errors**
   ```bash
   sudo chown -R $USER:www-data /var/www/klgestorpub
   sudo chmod -R 775 /var/www/klgestorpub/storage
   ```

2. **Nginx 502 Error**
   ```bash
   sudo systemctl restart php8.2-fpm
   sudo systemctl restart nginx
   ```

3. **Database Connection Error**
   - Check `.env` database credentials
   - Verify MySQL service: `sudo systemctl status mysql`

4. **Queue Not Processing**
   ```bash
   sudo supervisorctl restart klgestorpub-worker:*
   ```

### Health Checks

```bash
# Check all services
sudo systemctl status nginx mysql php8.2-fpm
sudo supervisorctl status
php artisan about
```

## 🔄 Updates & Maintenance

### Application Updates

1. Pull latest code: `git pull origin main`
2. Update dependencies: `composer install --no-dev`
3. Rebuild assets: `npm run build`
4. Run migrations: `php artisan migrate --force`
5. Clear caches: `php artisan optimize`

### Security Updates

```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Update SSL certificate (automatic via cron)
sudo certbot renew --dry-run
```

## 📞 Support

For deployment issues or questions:
- Check logs first
- Review this documentation
- Contact: rayhenrique@gmail.com

---

**KL Gestor Pub v1.4.0** - Production Deployment Guide