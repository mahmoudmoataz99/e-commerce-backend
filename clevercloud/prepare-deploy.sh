#!/bin/bash

# Set proper ownership and permissions
chown -R bas:bas /home/bas/app_ff953d38-9796-45e3-a05a-81930e09d380

# Set directory permissions (755 for directories)
find /home/bas/app_ff953d38-9796-45e3-a05a-81930e09d380 -type d -exec chmod 755 {} \;

# Set file permissions (644 for files)
find /home/bas/app_ff953d38-9796-45e3-a05a-81930e09d380 -type f -exec chmod 644 {} \;

# Special permissions for storage and cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chmod -R 775 public

# Make index.php executable
chmod 755 public/index.php

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link
php artisan storage:link --force

echo "Deployment preparation complete!"
