# Usar a versão estável mais recente do PHP com suporte a FPM
FROM php:8.3-fpm

# Instalar dependências e extensões comuns
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install zip pdo_mysql

# Instalar o Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Configurar o Xdebug para cobertura de código
RUN echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=0" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Configurar logs (opcional, para debug)
RUN mkdir -p /var/log/php && touch /var/log/php/php.log

# Instalar o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Definir o diretório de trabalho padrão
WORKDIR /var/www/html
