#!/bin/bash
# AYPSIS Laravel Deployment Script for Ubuntu Server
# Run this script on your Ubuntu server

echo "🚀 Starting AYPSIS Laravel Deployment..."

# Update system
echo "📦 Updating system packages..."
sudo apt update && sudo apt upgrade -y

# Install required packages
echo "🔧 Installing required packages..."
sudo apt install -y nginx mysql-server php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-intl php8.2-gd unzip git curl

# Install Composer
echo "🎼 Installing Composer..."
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Install Node.js and npm
echo "📦 Installing Node.js..."
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

echo "✅ Basic installation completed!"
echo "Next steps:"
echo "1. Configure MySQL database"
echo "2. Clone your repository"
echo "3. Configure Nginx"
echo "4. Set up SSL certificate"
