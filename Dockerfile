FROM php:8.2-apache

WORKDIR /var/www/html

RUN apt-get update && \
    apt-get install -y \
    libzip-dev \
    libonig-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql opcache mbstring zip exif && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get update && \
    apt-get install -y \
    curl \
    gnupg \
    && curl -sL https://deb.nodesource.com/setup_22.x | \
    bash - \
    && apt-get install -y nodejs && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

COPY . /var/www/html

RUN composer install --optimize-autoloader

RUN npm install

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

EXPOSE 5173

CMD ["apache2-foreground"]
