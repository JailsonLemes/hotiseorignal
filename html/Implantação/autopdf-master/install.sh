#!/bin/bash
# Usar 'set -e' faz com que o script pare imediatamente se um comando falhar.
set -e

# --- Funções de Log para Melhor Visualização ---
log_info() {
    echo -e "\e[32m✅ $1\e[0m"
}
log_error() {
    echo -e "\e[31m❌ $1\e[0m"
}
log_warn() {
    echo -e "\e[33m⚠️ $1\e[0m"
}

echo "🚀 [1/6] Iniciando instalação do Sistema IXC Relatórios (Modo Estável)..."
sleep 1

export DEBIAN_FRONTEND=noninteractive

if [ "$EUID" -ne 0 ]; then
  log_error "Por favor, execute este script com sudo: sudo ./install.sh"
  exit 1
fi

echo "📦 [2/6] Atualizando pacotes do sistema (Debian)..."
# CORREÇÃO: Removido 'add-apt-repository universe', pois é do Ubuntu.
apt-get update -y
apt-get -y -o Dpkg::Options::="--force-confold" -o Dpkg::Options::="--force-confdef" upgrade

# --- Instalação de Dependências Básicas ---
echo "🧰 [3/6] Instalando dependências básicas do sistema..."
apt-get install --reinstall -y python3 python3-pip python3-venv qpdf curl

# --- Instalação manual do wkhtmltopdf ---
echo "🔧 [4/6] Instalando wkhtmltopdf manualmente..."

# Limpa tentativas anteriores
apt-get remove --purge -y wkhtmltox &> /dev/null || true

log_warn "Baixando a versão recomendada para Debian 12 (Bookworm)..."
ARCH=$(dpkg --print-architecture)

# --- CORREÇÃO: URL alterada de 'jammy' (Ubuntu) para 'bookworm' (Debian 12) ---
WKHTMLTOPDF_DEB="wkhtmltox_0.12.6.1-3.bookworm_${ARCH}.deb"
WKHTMLTOPDF_URL="https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6.1-3/${WKHTMLTOPDF_DEB}"

curl -L -o "/tmp/${WKHTMLTOPDF_DEB}" "${WKHTMLTOPDF_URL}"

# Tenta instalar o pacote (é esperado que falhe se houver dependências)
log_warn "Tentando instalar o pacote (é esperado que falhe e mostre erros de dependência)..."
dpkg -i "/tmp/${WKHTMLTOPDF_DEB}" || true

# Força a correção das dependências que o dpkg não conseguiu resolver
log_info "Corrigindo dependências e finalizando a instalação do wkhtmltopdf..."
apt-get --fix-broken install -y

rm "/tmp/${WKHTMLTOPDF_DEB}"

# --- VERIFICAÇÃO ---
log_info "Verificando a instalação do wkhtmltopdf..."
if ! command -v wkhtmltopdf &> /dev/null; then
    log_error "Falha crítica: wkhtmltopdf não foi encontrado no PATH após a instalação."
    log_error "Por favor, verifique os erros acima e tente resolver as dependências manualmente."
    exit 1
else
    log_info "wkhtmltopdf encontrado com sucesso em: $(command -v wkhtmltopdf)"
fi


# --- Criação do Ambiente Virtual ---
echo "🐍 [5/6] Criando ambiente virtual Python limpo..."
rm -rf venv
python3 -m venv venv
log_info "Ambiente virtual 'venv' criado."

# --- Instalação de Dependências Python ---
echo "📚 [6/6] Instalando e validando dependências Python..."

source venv/bin/activate
pip install --upgrade pip setuptools wheel

REQUIREMENTS=(
    "streamlit==1.28.1"
    "pdfkit==1.0.0"
    "pypdf==3.17.4"
    "PyPDF2==2.12.1"
    "google-auth==2.23.4"
    "google-auth-oauthlib==1.1.0"
    "google-auth-httplib2==0.1.1"
    "google-api-python-client==2.108.0"
    "Jinja2==3.1.2"
    "Pillow"
)

pip install --ignore-installed "${REQUIREMENTS[@]}"
pip cache purge
log_info "Dependências Python instaladas na versão correta."

echo ""
log_info "🎉 Instalação concluída com sucesso!"
echo ""
echo "Para iniciar o sistema, use o script 'start.sh':"
echo "-----------------------------------------"
echo "./start.sh"
echo "-----------------------------------------"
echo ""