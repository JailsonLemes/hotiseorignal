#!/bin/bash

# Garante que o script pare se algum comando falhar
set -e

# 1. Garante que estamos no diretório do projeto (onde o script está)
cd "$(dirname "$0")"

# 2. Exporta o GID do grupo Docker do host para que o docker-compose possa usá-lo.
# Isso é crucial para que o container PHP tenha permissão para executar comandos docker.
export DOCKER_GID=$(stat -c '%g' /var/run/docker.sock)
if [ -z "$DOCKER_GID" ]; then
    echo "ERRO: Não foi possível obter o GID do Docker. Verifique se o serviço do Docker está ativo."
    exit 1
fi
echo "INFO: GID do Docker encontrado: $DOCKER_GID"

echo "INFO: Parando e removendo containers antigos para evitar conflitos..."
# 3. Para e remove containers gerenciados pelo compose para garantir um início limpo.
# O '--remove-orphans' remove containers de serviços que não existem mais no compose.
docker compose down --remove-orphans

echo "INFO: Construindo e iniciando os serviços..."
# 4. Constrói a imagem (passando o GID) e sobe os serviços em background.
docker compose up -d --build

echo "✅ SUCESSO: Ambiente iniciado. Verifique os containers com 'docker ps'."