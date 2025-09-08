#!/bin/bash
# Deploy script for AYPSIS Laravel application

echo "🚀 Deploying AYPSIS Laravel Application..."

# Navigate to web directory
cd /var/www

# Clone repository (run this only once)
if [ ! -d "aypsis" ]; then
    echo "📦 Cloning repository..."
    sudo git clone https://github.com/irhamfrdz/aypsis.git
    sudo chown -R www-data:www-data aypsis
fi

# Navigate to project directory
cd aypsis

# Update from GitHub
echo "🔄 Updating from GitHub..."
sudo -u www-data git pull origin main

# Install/Update Composer dependencies
echo "📚 Installing Composer dependencies..."
sudo -u www-data composer install --no-dev --optimize-autoloader

# Copy environment file
if [ ! -f ".env" ]; then
    echo "⚙️ Setting up environment file..."
    sudo -u www-data cp .env.example .env
    echo "Please edit .env file with your database credentials!"
fi

# Generate application key
echo "🔑 Generating application key..."
sudo -u www-data php artisan key:generate

# Run database migrations
echo "🗄️ Running database migrations..."
sudo -u www-data php artisan migrate --force

# Clear and cache configurations
echo "🧹 Clearing and caching configurations..."
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan route:clear
sudo -u www-data php artisan view:clear

sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

# Set proper permissions
echo "🔐 Setting permissions..."
sudo chown -R www-data:www-data /var/www/aypsis
sudo chmod -R 755 /var/www/aypsis
sudo chmod -R 775 /var/www/aypsis/storage
sudo chmod -R 775 /var/www/aypsis/bootstrap/cache

echo "✅ Deployment completed!"
echo "📍 Application deployed to: /var/www/aypsis"
echo "🌐 Configure your domain in Nginx to point to: /var/www/aypsis/public"
