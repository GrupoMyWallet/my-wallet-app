# .docker/php/Dockerfile

# Usamos uma imagem base oficial do PHP 8.2 com FPM (FastCGI Process Manager)
FROM php:8.2-fpm

# Argumentos para usuário e grupo (útil para permissões)
ARG user
ARG uid

# Define o diretório de trabalho
WORKDIR /var/www

# Instala dependências do sistema
# - libpq-dev: Essencial para a extensão pgsql do PHP
# - git, curl, libpng-dev, libonig-dev, libxml2-dev, zip, unzip: Comuns em projetos Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    libxml2-dev \
    libonig-dev \
    libzip-dev \
    libpq-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala as extensões do PHP
# - pdo_pgsql e pgsql: Para conectar com o PostgreSQL
# - bcmath: Para matemática de precisão
# - gd: Para manipulação de imagens
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip pdo_pgsql

# Instala o Composer (gerenciador de dependências do PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instala Node.js e NPM para o build do frontend (Inertia/Vue)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Cria um usuário para rodar a aplicação (mais seguro que usar root)
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Copia o código da aplicação para o container
COPY . /var/www

# Ajusta as permissões dos diretórios que o Laravel precisa escrever
RUN chown -R $user:www-data /var/www/storage /var/www/bootstrap/cache

# Expõe a porta 9000 para o PHP-FPM
EXPOSE 9000

# Comando para iniciar o PHP-FPM
CMD ["php-fpm"]