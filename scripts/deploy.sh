#!/bin/sh
set -e

echo "🚀 Iniciando deploy..."

# Puxa as ultimas alterações
git pull origin main

# Entra no modo de manutenção
docker compose -f compose.prod.yaml exec php-fpm php artisan down || echo "Aplicação já está em modo de manutenção."

# Puxa as imagens do github
docker compose -f compose.prod.yaml pull

# Reconstrói e reinicia os contêineres
echo "🏗️  Construindo e reiniciando os contêineres..."
docker compose -f compose.prod.yaml up -d --remove-orphans

# Executa as migrações do banco de dados
echo "⚙️  Executando migrações..."
docker compose -f compose.prod.yaml exec php-fpm php artisan migrate --force

# Limpa e otimiza a aplicação
echo "✨ Otimizando a aplicação..."
docker compose -f compose.prod.yaml exec php-fpm php artisan optimize:clear
docker compose -f compose.prod.yaml exec php-fpm php artisan optimize

# Sai do modo de manutenção
docker compose -f compose.prod.yaml exec php-fpm php artisan up

# Limpa as imagens antigas
docker image prune -f

echo "✅ Deploy finalizado com sucesso!"