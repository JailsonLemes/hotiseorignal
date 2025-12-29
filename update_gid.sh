#!/bin/bash

# Este script automatiza a reconstrução e o reinício do container da aplicação web
# com as permissões corretas (GID) para acessar o Docker do host.

# --- VARIÁVEIS DE CONFIGURAÇÃO ---
IMAGE_NAME="app-hotsite"
CONTAINER_NAME="apps-hotsite"
PROJECT_DIR="/home/ixcsoft/Documentos/hotsite"

# --- INÍCIO DA EXECUÇÃO ---
set -e # Encerra o script se qualquer comando falhar

echo "INFO: Navegando para o diretório do projeto: $PROJECT_DIR"
cd "$PROJECT_DIR" || { echo "ERRO: Diretório do projeto não encontrado!"; exit 1; }

echo "INFO: Verificando qual grupo gerencia o Docker socket..."
DOCKER_SOCKET="/var/run/docker.sock"
if [ ! -S "$DOCKER_SOCKET" ]; then
    echo "ERRO: O Docker socket não foi encontrado em $DOCKER_SOCKET. Verifique se o serviço do Docker está ativo."
    exit 1
fi

DOCKER_GROUP_NAME=$(stat -c '%G' "$DOCKER_SOCKET")
echo "INFO: O grupo encontrado foi: '$DOCKER_GROUP_NAME'."

HOST_DOCK_GID=$(getent group "$DOCKER_GROUP_NAME" | cut -d: -f3)

if [ -z "$HOST_DOCK_GID" ]; then
    echo "ERRO: Não foi possível encontrar o GID para o grupo '$DOCKER_GROUP_NAME'. Verifique as permissões do sistema."
    exit 1
fi

echo "INFO: GID encontrado: $HOST_DOCK_GID"
echo "--------------------------------------------------"

echo "INFO: Reconstruindo a imagem '$IMAGE_NAME' com o GID correto (pode levar alguns minutos)..."
docker build --no-cache --build-arg HOST_DOCK_GID=$HOST_DOCK_GID -t $IMAGE_NAME .
echo "INFO: Build da imagem concluído com sucesso."
echo "--------------------------------------------------"

echo "INFO: Verificando se o container '$CONTAINER_NAME' já existe..."
if [ "$(docker ps -a -q -f name=^/${CONTAINER_NAME}$)" ]; then
    echo "INFO: Container existente encontrado. Parando e removendo..."
    docker stop $CONTAINER_NAME
    docker rm $CONTAINER_NAME
    echo "INFO: Container antigo removido."
fi
echo "--------------------------------------------------"

echo "INFO: Iniciando o novo container '$CONTAINER_NAME'..."
docker run -d --name $CONTAINER_NAME -p 80:80 -p 443:443 -v /var/run/docker.sock:/var/run/docker.sock -v "$PROJECT_DIR/html":/var/www/html $IMAGE_NAME

echo "--------------------------------------------------"
echo "SUCESSO: O container '$CONTAINER_NAME' foi reiniciado com a nova imagem e as permissões corretas."
echo "Verifique se o container está rodando com 'docker ps'."