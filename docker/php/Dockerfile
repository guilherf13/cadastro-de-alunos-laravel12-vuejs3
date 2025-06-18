# __define-ocg__

FROM php:8.4-fpm

# Argumentos para UID e GID para que possam ser passados durante o build.
# O padrão é 1000, que é o comum para o primeiro usuário em sistemas Linux.
ARG UID=1000
ARG GID=1000

# Define o diretório de trabalho dentro do contêiner
WORKDIR /var/www

# Instalação de dependências do sistema e extensões PHP
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Limpa o cache do apt para reduzir o tamanho da imagem
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Configura o Git para ignorar problemas de ownership no diretório de trabalho
RUN git config --global --add safe.directory /var/www

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia os arquivos de dependência e instala.
# Isso irá gerar um composer.lock dentro do contêiner baseado no PHP 8.4
COPY composer.json ./
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist

# Copia o restante do código da aplicação
COPY . .

# Otimiza o autoloader
RUN composer install --no-interaction --optimize-autoloader

# Ajusta as permissões dos arquivos e do usuário
RUN groupmod -g ${GID} www-data && \
    usermod -u ${UID} -g www-data www-data && \
    chown -R www-data:www-data /var/www

# Muda para o usuário não-root
USER www-data

# Expõe a porta 9000 para o serviço PHP-FPM
EXPOSE 9000

# Comando padrão para iniciar o serviço PHP-FPM
CMD ["php-fpm"]
