FROM php:8.2-apache-bookworm

# Pacotes do sistema + Chromium e suas dependências de runtime
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    ca-certificates \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    chromium \
    libnss3 \
    libxss1 \
    libxshmfence1 \
    libgbm1 \
    libasound2 \
    libatk1.0-0 \
    libatk-bridge2.0-0 \
    libcups2 \
    libdrm2 \
    libxkbcommon0 \
    libxcomposite1 \
    libxdamage1 \
    libxfixes3 \
    libxrandr2 \
    libpango-1.0-0 \
    libcairo2 \
    fonts-liberation \
    fonts-noto-color-emoji \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Extensões PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Não baixar Chromium via Puppeteer: usamos o do sistema
ENV PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true
ENV CYPRESS_INSTALL_BINARY=0

# Caminho do Chromium do sistema (consumido pelo Browsershot/Puppeteer)
ENV PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium
ENV CHROME_PATH=/usr/bin/chromium

RUN composer install --no-interaction --optimize-autoloader
RUN npm install
RUN npm run build

RUN mkdir -p /var/www/html/public/img/erros \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/img/erros

RUN echo "upload_max_filesize = 20M\npost_max_size = 25M\nmemory_limit = 256M" > /usr/local/etc/php/conf.d/uploads.ini

RUN php artisan storage:link || true

# Diretórios graváveis para caches de Node/Puppeteer e home do www-data
RUN mkdir -p /var/www/.npm /var/www/.cache/puppeteer \
    && chown -R www-data:www-data /var/www/.npm /var/www/.cache

EXPOSE 80