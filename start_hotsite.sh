#!/bin/bash
# Script para automatizar a execução do Docker Compose no diretório específico
# Muda para o diretório /Documentos/hotsite/
#cd /home/ixcsoft/Documentos/hotsite/
# Executa o docker compose com sudo
sudo docker compose up -d --build
