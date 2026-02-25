# 1. Imagem Base
FROM python:3.11-slim-bookworm

# 2. Variáveis de Ambiente
ENV PYTHONUNBUFFERED=1 \
    DEBIAN_FRONTEND=noninteractive

# 3. Diretório de Trabalho
WORKDIR /app

# 4. Dependências do Sistema
RUN apt-get update && apt-get install -y --no-install-recommends \
    wkhtmltopdf \
    qpdf \
    && rm -rf /var/lib/apt/lists/*

# 5. Dependências Python
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt && \
    # Verificar a versão instalada
    echo "Verificando versão de google-auth-oauthlib:" && \
    pip show google-auth-oauthlib | grep Version && \
    echo "Verificação concluída."

# 6. Copiar Código da Aplicação
COPY . .

# 7. Expor Porta
EXPOSE 8501

# 8. Comando de Execução
CMD ["streamlit", "run", "app.py", "--server.port=8501", "--server.address=0.0.0.0"]