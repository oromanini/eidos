FROM php:8.2-apache

# Define o diretório de trabalho. Boa prática para o resto dos comandos.
WORKDIR /var/www/html

# Instala as extensões PHP essenciais e ferramentas de build
RUN apt-get update && \
    apt-get install -y \
    libzip-dev \
    libonig-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql opcache mbstring zip exif && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Instala o Composer
RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/local/bin --filename=composer

# Instala Node.js (v18.x) e NPM
RUN apt-get update && \
    apt-get install -y \
    curl \
    gnupg \
    && curl -sL https://deb.nodesource.com/setup_18.x | \
    bash - \
    && apt-get install -y nodejs && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Copia os arquivos do projeto para o contêiner
COPY . /var/www/html

# Instala as dependências do Composer (Requer que os arquivos do projeto já tenham sido copiados)
RUN composer install --no-dev --optimize-autoloader

# Instala as dependências do NPM (Requer que os arquivos do projeto já tenham sido copiados)
# Usando --legacy-peer-deps para resolver o erro ERESOLVE
RUN npm install --legacy-peer-deps

# Configura o Apache
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Expõe a porta do servidor de desenvolvimento do Vite (para visualização)
EXPOSE 5173

# O comando padrão é o Apache no foreground
CMD ["apache2-foreground"]