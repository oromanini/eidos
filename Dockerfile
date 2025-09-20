FROM php:8.2-apache

# Instala as extensões PHP essenciais
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libonig-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql opcache mbstring zip exif

# Instala o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instala Node.js e NPM
RUN apt-get update && apt-get install -y \
    curl \
    gnupg \
    && curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Copia os arquivos do projeto para o contêiner
COPY . /var/www/html

# Instala as dependências do Composer
RUN composer install --no-dev --optimize-autoloader

# Instala as dependências do NPM
RUN npm install

# Configura o Apache para servir de /var/www/html
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Expõe a porta do servidor de desenvolvimento do Vite
EXPOSE 5173
