FROM php:7.4-apache

# Argumento para receber o GID do host (definido como 994)
ARG HOST_DOCK_GID=994

# Instalar dependências e Docker CLI
RUN apt-get update && apt-get install -y --no-install-recommends \
    apt-transport-https \
    ca-certificates \
    curl \
    gnupg \
    lsb-release \
    libzip-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libonig-dev \
    libgd-dev \
    openjdk-11-jdk-headless \
    zip \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install mbstring xml zip curl gd

RUN mkdir -p /etc/apt/keyrings \
    && curl -fsSL https://download.docker.com/linux/debian/gpg | gpg --dearmor -o /etc/apt/keyrings/docker.gpg \
    && echo \
      "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/debian \
      $(lsb_release -cs) stable" | tee /etc/apt/sources.list.d/docker.list > /dev/null \
    && apt-get update && apt-get install -y --no-install-recommends docker-ce-cli \
    && rm -rf /var/lib/apt/lists/*

# --- CORREÇÃO DE GID ---
# Cria um grupo 'dockerhost' com o GID do host
RUN groupadd --force --gid ${HOST_DOCK_GID} dockerhost || echo "Grupo com GID ${HOST_DOCK_GID} talvez já exista. Continuando..."
# Adiciona 'www-data' E 'root' a este novo grupo para garantir o acesso
RUN usermod -aG dockerhost www-data
RUN usermod -aG dockerhost root
# --- FIM DA CORREÇÃO DE GID ---

# Copiar código PHP
COPY ./html /var/www/html
RUN chmod 777 /var/www/html -Rf # Considerar permissões mais restritas se possível

# Expor portas
EXPOSE 80
EXPOSE 443

# Executar Apache
CMD ["apache2-foreground"]