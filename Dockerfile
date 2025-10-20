# Imagen base PHP 8.2 con Apache
FROM php:8.2-apache

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    gnupg2 \
    unixodbc-dev \
    curl \
    apt-transport-https \
    lsb-release \
    && rm -rf /var/lib/apt/lists/*

# Agregar manualmente el repositorio de Microsoft (usando Debian 12 Bookworm)
RUN curl -sSL https://packages.microsoft.com/keys/microsoft.asc -o /etc/apt/trusted.gpg.d/microsoft.asc \
    && echo "deb [arch=amd64] https://packages.microsoft.com/debian/12/prod bookworm main" > /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y msodbcsql18 \
    && pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

# Habilitar mod_rewrite y configurar el contenido web
RUN a2enmod rewrite
COPY ./public /var/www/html/

# Establecer permisos adecuados
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
