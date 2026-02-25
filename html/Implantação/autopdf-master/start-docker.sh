#!/bin/bash

IMAGE_NAME="relatorio-ixc-app"
CONTAINER_NAME="relatorio-ixc-container"
CREDENTIALS_FILE="credentials.json"
TOKEN_FILE="token.pickle" # Assumindo que este ficheiro existe após a geração manual

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

log_info() { echo -e "${GREEN}✅ $1${NC}"; }
log_warn() { echo -e "${YELLOW}⚠️ $1${NC}"; }
log_error() { echo -e "${RED}❌ $1${NC}"; }

log_info "A iniciar verificações..."
if ! docker info &> /dev/null; then log_error "O serviço do Docker não está em execução."; exit 1; fi
if ! docker image inspect "$IMAGE_NAME" &> /dev/null; then log_error "A imagem Docker '$IMAGE_NAME' não foi encontrada."; exit 1; fi
if [ ! -f "$CREDENTIALS_FILE" ]; then log_error "O ficheiro '$CREDENTIALS_FILE' não foi encontrado."; exit 1; fi

# Verifica se o token existe (crucial para rodar em background)
if [ ! -f "$TOKEN_FILE" ]; then
    log_error "O ficheiro '$TOKEN_FILE' não foi encontrado."
    log_error "Por favor, execute a aplicação localmente uma vez ('bash start.sh') para gerar o token antes de usar este script Docker."
    exit 1
fi
log_info "Verificações concluídas."

if [ "$(docker ps -a -q -f name=^/${CONTAINER_NAME}$)" ]; then
    log_warn "Contentor '$CONTAINER_NAME' existente encontrado. A remover..."
    docker rm -f "$CONTAINER_NAME" > /dev/null
    log_info "Contentor anterior removido."
fi

declare -a DOCKER_VOLUMES
DOCKER_VOLUMES+=("-v" "$(pwd)/${CREDENTIALS_FILE}:/app/${CREDENTIALS_FILE}")
# Monta o token existente
DOCKER_VOLUMES+=("-v" "$(pwd)/${TOKEN_FILE}:/app/${TOKEN_FILE}")

log_info "A iniciar o contentor '$CONTAINER_NAME' em background..."

# --- Executar em modo background (-d) ---
docker run -d \
  -p 127.0.0.1:8501:8501 \
  "${DOCKER_VOLUMES[@]}" \
  --name "$CONTAINER_NAME" \
  "$IMAGE_NAME"

if [ $? -eq 0 ]; then
    echo ""
    log_info "🎉 Contentor iniciado com sucesso!"
    echo "   🌐 Aceda à aplicação em: http://localhost:8501"
    echo ""
    echo "   📋 Para ver os logs: docker logs -f $CONTAINER_NAME"
    echo "   🛑 Para parar o contentor: docker stop $CONTAINER_NAME"
    echo ""
else
    log_error "Ocorreu um erro ao iniciar o contentor."
fi