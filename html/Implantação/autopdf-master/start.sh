#!/bin/bash

# Este script garante que o aplicativo seja executado no ambiente virtual correto.

# Verifica se o ambiente virtual existe
if [ ! -d "venv" ]; then
    echo "❌ Erro: Ambiente virtual 'venv' não encontrado."
    echo "Por favor, execute o script './install.sh' primeiro."
    exit 1
fi

# Ativa o ambiente virtual
source venv/bin/activate

echo "✅ Ambiente virtual ativado."
echo "🚀 Iniciando o aplicativo Streamlit..."

# Inicia o aplicativo
streamlit run app.py
