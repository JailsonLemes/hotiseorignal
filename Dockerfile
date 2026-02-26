FROM php:7.4-apache

# Instalar extensões PHP e dependências necessárias
RUN apt-get update && apt-get install -y \
    ca-certificates \
    curl \
    gnupg \
    lsb-release \
    libzip-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libonig-dev \
    libgd-dev \
    openjdk-11-jdk \
    zip \
    && install -m 0755 -d /etc/apt/keyrings \
    && curl -fsSL https://download.docker.com/linux/debian/gpg | gpg --dearmor -o /etc/apt/keyrings/docker.gpg \
    && chmod a+r /etc/apt/keyrings/docker.gpg \
    && echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/debian $(. /etc/os-release && echo $VERSION_CODENAME) stable" > /etc/apt/sources.list.d/docker.list \
    && apt-get update \
    && apt-get install -y docker-ce-cli \
    && docker-php-ext-install mbstring xml zip curl gd \
    && rm -rf /var/lib/apt/lists/*


# Copiar código PHP
COPY ./html /var/www/html
RUN chown -R www-data:www-data /var/www/html && chmod -R 775 /var/www/html

# Entrypoint para garantir permissoes do bind mount
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
# Expor porta Apache
EXPOSE 80
EXPOSE 443
EXPOSE 22

# Executar Apache
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
