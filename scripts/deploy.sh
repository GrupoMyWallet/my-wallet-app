#!/bin/sh
set -e # Para o script se qualquer comando falhar

echo "🚀 Iniciando deploy..."

# 1. Entra no modo de manutenção
# (Opcional, mas bom para evitar que usuários vejam erros durante o deploy)
docker compose -f compose.prod.yaml exec php-fpm php artisan down || echo "Aplicação já está em modo de manutenção."

# 2. Puxa as últimas alterações do repositório
git pull origin main

# 3. Instala dependências do Composer (se o composer.lock mudou)
# O Dockerfile já faz isso no build, mas pode ser uma segurança extra
# docker compose -f compose.prod.yaml run --rm composer install --no-dev --optimize-autoloader

# 4. Reconstrói e reinicia os contêineres
echo "🏗️  Construindo e reiniciando os contêineres..."
docker compose -f compose.prod.yaml up --build -d

# 5. Executa as migrações do banco de dados
echo "⚙️  Executando migrações..."
docker compose -f compose.prod.yaml exec php-fpm php artisan migrate --force

# 6. Limpa e otimiza a aplicação
echo "✨ Otimizando a aplicação..."
docker compose -f compose.prod.yaml exec php-fpm php artisan optimize:clear
docker compose -f compose.prod.yaml exec php-fpm php artisan optimize

# 7. Sai do modo de manutenção
docker compose -f compose.prod.yaml exec php-fpm php artisan up

echo "✅ Deploy finalizado com sucesso!"