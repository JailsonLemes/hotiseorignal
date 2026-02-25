#!/bin/bash

# Este script automatiza a criação e habilitação de um serviço systemd para o seu
# aplicativo Streamlit.

# Verifica se o script está sendo executado com permissões de root
if [ "$(id -u)" -ne 0 ]; then
    echo "Este script precisa ser executado como root. Use 'sudo ./configurar_servico.sh'."
    exit 1
fi

echo "Iniciando a configuração do serviço systemd para o aplicativo Streamlit..."

# Pergunta ao usuário o diretório do app.py
read -rp "Digite o caminho COMPLETO até a pasta onde está o app.py: " APP_DIR

# Verifica se o arquivo existe
if [ ! -f "$APP_DIR/app.py" ]; then
    echo "Erro: Não encontrei o arquivo app.py em $APP_DIR"
    exit 1
fi

# Caminho do venv e do streamlit
VENV_PATH="$APP_DIR/venv/bin/streamlit"

if [ ! -x "$VENV_PATH" ]; then
    echo "Erro: Não encontrei o streamlit em $VENV_PATH"
    echo "Verifique se o ambiente virtual existe e se o streamlit está instalado."
    exit 1
fi

# Conteúdo do arquivo de serviço systemd
SERVICE_CONTENT="[Unit]
Description=Aplicativo Streamlit para processamento de PDF
After=network.target

[Service]
User=$SUDO_USER
WorkingDirectory=$APP_DIR
ExecStart=$VENV_PATH run app.py
Restart=always

[Install]
WantedBy=multi-user.target"

# Cria o arquivo de serviço na pasta systemd
echo "Criando o arquivo de serviço /etc/systemd/system/autopdf.service..."
echo "$SERVICE_CONTENT" > /etc/systemd/system/autopdf.service

# Recarrega o systemd daemon para que ele reconheça o novo serviço
echo "Recarregando o daemon do systemd..."
systemctl daemon-reload

# Habilita o serviço para iniciar no boot do sistema
echo "Habilitando o serviço para iniciar automaticamente com o sistema..."
systemctl enable autopdf.service

# Inicia o serviço imediatamente
echo "Iniciando o serviço agora..."
systemctl start autopdf.service

# Exibe o status do serviço para confirmar que ele está rodando
echo "Verificando o status do serviço..."
systemctl status autopdf.service --no-pager

echo "--------------------------------------------------------"
echo "Configuração concluída! Seu aplicativo Streamlit agora será iniciado automaticamente com o sistema."
echo "Use 'sudo systemctl status autopdf.service' para verificar o status a qualquer momento."
echo "--------------------------------------------------------"
