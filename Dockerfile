# Usa una imagen base oficial de PHP con Apache
FROM php:8.2-apache

# Instala las extensiones necesarias de PHP y otras utilidades
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install zip \
    && docker-php-ext-install pdo_mysql

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Copia el contenido del proyecto en el contenedor
COPY . .

# Instala las dependencias de Composer
RUN composer install --optimize-autoloader --no-dev

# Configura los permisos adecuados para los directorios de almacenamiento y cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Copia el archivo de configuración de Apache
COPY ./docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf

# Habilita el módulo de reescritura de Apache
RUN a2enmod rewrite

# Expone el puerto 80
EXPOSE 80

# Genera la clave de aplicación
RUN php artisan key:generate

# Comando por defecto para ejecutar la aplicación Laravel
CMD ["apache2-foreground"]
