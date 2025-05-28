FROM php:8.2-apache

# Instala o instalador de extensões PHP
ADD https://raw.githubusercontent.com/mlocati/docker-php-extension-installer/master/install-php-extensions /usr/local/bin/
RUN chmod uga+x /usr/local/bin/install-php-extensions && sync

# Instala dependências e extensões
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg-dev \
    libpng-dev \
    libwebp-dev \
    curl \
    git \
    zip unzip \
    && docker-php-ext-enable opcache \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pcntl \
    && install-php-extensions \
      bcmath \
      bz2 \
      calendar \
      exif \
      gd \
      intl \
      ldap \
      memcached \
      mysqli \
      opcache \
      pdo_mysql \
      pdo_pgsql \
      pgsql \
      soap \
      xsl \
      zip \
      sockets \
      iconv \
      mbstring \
    && apt-get autoclean -y && rm -rf /var/lib/apt/lists/*

# Habilita módulos do Apache
RUN a2enmod rewrite headers

# Configura o diretório público do Laravel
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configurações de upload
RUN echo "file_uploads = On\n" \
         "memory_limit = 1024M\n" \
         "upload_max_filesize = 512M\n" \
         "post_max_size = 512M\n" \
         "max_execution_time = 1200\n" \
         > /usr/local/etc/php/conf.d/uploads.ini

# Define o ServerName do Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Instala Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs

# Garante permissões corretas para cache e storage
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache && \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Define diretório de trabalho
WORKDIR /var/www/html
