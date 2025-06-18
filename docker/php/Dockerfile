# __define-ocg__

FROM php:8.4-fpm

# Argumentos para UID e GID para que possam ser passados durante o build.
# O padrão é 1000, que é o comum para o primeiro usuário em sistemas Linux.
ARG UID=1000
ARG GID=1000

# Define o diretório de trabalho dentro do contêiner
WORKDIR /var/www

# Instalação de dependências do sistema e extensões PHP necessárias para o Laravel
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
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Limpa o cache do apt para reduzir o tamanho da imagem
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# ---- Modificação do Usuário e Grupo ----
# Altera o UID e GID do usuário www-data para corresponder ao usuário do host.
# Isso evita problemas de permissão de arquivos com volumes montados.
RUN groupmod -g ${GID} www-data && \
    usermod -u ${UID} -g www-data www-data && \
    chown -R www-data:www-data /var/www

# Instala o Composer (gerenciador de dependências do PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o usuário do contêiner para ser o www-data (agora com o UID/GID corretos)
USER www-data

COPY . .

# Expõe a porta 9000 para o serviço PHP-FPM
EXPOSE 9000

# Comando padrão para iniciar o serviço PHP-FPM
CMD ["php-fpm"]
