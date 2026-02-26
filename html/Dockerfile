# Use uma imagem PHP
FROM nginx

# Defina o diretório de trabalho no contêiner
WORKDIR /var/www/html

# Copie os arquivos de aplicação para o contêiner
COPY ./* /var/www/html
# COPY default /etc/nginx/sites-enabled/default
COPY default /etc/nginx/conf.d/default.conf

# Instale as dependências do PHP (se necessário)
RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y \
        zlib1g-dev \
        libzip-dev \
        libxml2-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        imagemagick 
       # openjdk-11-jdk 
       # php7.4 
       # php7.4-fpm 
       # php7.4-mbstring 
       # php7.4-xml 
       # php7.4-zip 
       # php7.4-gd 

# Copie o arquivo php.ini para o diretório de configuração do PHP
COPY php.ini /var/www/html/php.ini

# Exponha a porta necessária pelo servidor web (por exemplo, Apache)
EXPOSE 80

