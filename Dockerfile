FROM php:7.4-apache

# Instalar extensões PHP e dependências necessárias
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libonig-dev \
    libgd-dev \
    openjdk-11-jdk \
    zip \
    && docker-php-ext-install mbstring xml zip curl gd


# Copiar código PHP
COPY ./html /var/www/html
RUN chmod 777 /var/www/html -Rf
# Expor porta Apache
EXPOSE 80
EXPOSE 443
EXPOSE 22

# Executar Apache
CMD ["apache2-foreground"]
