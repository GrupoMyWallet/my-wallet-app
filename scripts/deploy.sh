#!/bin/bash

set -e

echo "🚀 Deploy MyWallet - $(date)"

# Backup
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)


git pull origin main

docker-compose down
rm -rf ./public/build/*
docker-compose build --no-cache app
docker-compose up -d


sleep 20

docker exec mywallet_app php artisan config:cache
docker exec mywallet_app php artisan route:cache  
docker exec mywallet_app php artisan view:cache
docker exec mywallet_app php artisan migrate --force

docker exec mywallet_app ls -la /var/www/public/build/
docker-compose ps

echo "✅ Deploy concluído!"