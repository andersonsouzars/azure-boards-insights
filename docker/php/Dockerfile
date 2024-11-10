# Usar a versão estável mais recente do PHP com suporte a FPM
FROM php:8.3-fpm

# Instalar dependências e extensões comuns
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install zip pdo_mysql

# Configurar logs (opcional, para debug)
RUN mkdir -p /var/log/php && touch /var/log/php/php.log

# Instalar o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Definir o diretório de trabalho padrão
WORKDIR /var/www/html
