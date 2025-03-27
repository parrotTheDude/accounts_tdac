#!/bin/bash

echo ""
echo "🚀 Starting deployment for TDAC accounts..."
echo "--------------------------------------------"

# Navigate to Laravel project root
cd ~/laravel-app || {
    echo "❌ Error: Could not access laravel-app directory."
    exit 1
}

# Pull the latest code from GitHub
echo "📦 Pulling latest code from GitHub..."
git reset --hard
git pull origin main || {
    echo "❌ Git pull failed."
    exit 1
}

# Install/update composer dependencies
echo "🧰 Installing composer dependencies..."
composer install --no-dev --optimize-autoloader || {
    echo "❌ Composer install failed."
    exit 1
}

# Set correct permissions
echo "🔧 Setting file permissions..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Run migrations
echo "🧱 Running migrations..."
php artisan migrate --force || {
    echo "❌ Migration failed."
    exit 1
}

# Clear and cache Laravel configs
echo "🧹 Clearing and caching config..."
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Deployment completed successfully!"
echo ""
