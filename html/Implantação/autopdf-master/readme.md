# 🚀 Sistema de Relatórios IXC

Sistema automatizado para geração e envio de relatórios de acesso para implantações IXC com PDF criptografado e envio automático por e-mail.

![Status](https://img.shields.io/badge/Status-Produção-green)
![Python](https://img.shields.io/badge/Python-3.8+-blue)
![Streamlit](https://img.shields.io/badge/Streamlit-1.28+-red)

## 📋 Funcionalidades

- ✅ **Interface web moderna** com Streamlit
- ✅ **Geração de PDF profissional** com design personalizado
- ✅ **Criptografia automática** de PDFs com senha
- ✅ **Envio automático por e-mail** via Gmail API
- ✅ **Suporte a múltiplos tipos** de implantação (IXC Provedor/Cloud)
- ✅ **Páginas separadas** para cada tipo de informação
- ✅ **Layout responsivo** e otimizado para impressão

## 🎯 Tipos de Relatório

### 📊 **IXC Cloud (2 páginas)**
1. **Dados do Provedor** - Acesso ao sistema
2. **Informações de Backup** - Configurações de backup

### 🖥️ **IXC Provedor (3 páginas)**
1. **Dados do Provedor** - Acesso ao sistema
2. **Informações de Backup** - Configurações de backup  
3. **Informações do Servidor** - Dados de acesso SSH

## 🛠️ Instalação

### Pré-requisitos

- **Python 3.8+**
- **Ubuntu/Debian** (é onde foi testado e aplicado)
- **Conta Google** com acesso à Gmail API - Ultilize a empresarial

**instalação manual:**

### 1. - Extraia o projeto do GitLab e o acesse no terminal

   
### 2. Rode os comandos abaixo
```bash
# Dependências do sistema
sudo apt-get update
sudo apt-get install -y wkhtmltopdf qpdf python3-pip python3-venv

# Dependências Python
pip install -r requirements.txt
```


### 3. Configurar Gmail API

⚠️ **IMPORTANTE: Cada membro da equipe deve ter seu próprio `credentials.json`**

#### 3.1. Acessar Google Cloud Console
1. Vá para [Google Cloud Console](https://console.cloud.google.com/)
2. Crie um novo projeto ou selecione um existente
3. Ative a **Gmail API**

#### 3.2. Criar Credenciais OAuth 2.0
1. Vá para **APIs e Serviços** → **Credenciais**
2. Clique em **+ CRIAR CREDENCIAIS** → **ID do cliente OAuth 2.0**
3. Selecione **Aplicativo para computador**
4. Dê um nome (ex: "Sistema Relatórios IXC")
5. Baixe o arquivo JSON

#### 3.3. Configurar o Arquivo
```bash
# Renomeie o arquivo baixado para credentials.json
mv [arquivo-baixado].json credentials.json

# Coloque na pasta raiz do projeto
# Estrutura deve ficar assim:
# projeto/
# ├── app.py
# ├── credentials.json  ← AQUI
# └── ...
```

### 4. Executar o Sistema

```bash
streamlit run app.py
```

🌐 **Acessar:** http://localhost:8501

## 📁 Estrutura do Projeto

```
sistema-relatorios-ixc/
├── app.py                 # Interface principal Streamlit
├── pdf_generator.py       # Gerador de PDF com design moderno
├── pdf_encryptor.py      # Criptografia de PDF (múltiplos métodos)
├── email_sender.py       # Envio via Gmail API
├── requirements.txt      # Dependências Python
├── install.sh           # Script de instalação automática
├── credentials.json     # Credenciais Gmail (não versionar!)
├── token.pickle         # Token OAuth (gerado automaticamente)
└── README.md           # Esta documentação
```

## 🔐 Segurança

### Arquivos Sensíveis (NÃO VERSIONAR)
```bash
# Adicione ao .gitignore:
credentials.json
token.pickle
*.pdf
```

### Boas Práticas
- ✅ **Cada pessoa** deve ter seu próprio `credentials.json`
- ✅ **PDFs são criptografados** automaticamente
- ✅ **Tokens são salvos** localmente para reuso
- ✅ **Arquivos temporários** são limpos automaticamente

## 📖 Como Usar

### 1. Preenchimento do Formulário

#### Dados Obrigatórios:
- **Nome do Provedor**
- **Domínio/Site**  
- **Usuário (e-mail)**
- **Senha de acesso**
- **Local de Backup**
- **E-mail de Backup**
- **Senha de Backup**
- **E-mail do Cliente** (para envio)
- **Senha do PDF** (para criptografia)

#### Campos Condicionais (apenas IXC Provedor):
- **IP do Servidor**
- **Porta** (padrão: 22)
- **Usuário do Servidor**
- **Senha Root**

### 2. Geração e Envio

1. Preencha todos os campos obrigatórios
2. Clique em **"🚀 Gerar e Enviar Relatório"**
3. Acompanhe o progresso na barra de status
4. Sistema automaticamente:
   - Gera PDF com design profissional
   - Criptografa com a senha fornecida
   - Envia por e-mail com assunto: **"Informações de Acesso - Instalação Finalizada"**

### 3. Primeira Autenticação

Na primeira execução:
1. O sistema abrirá uma janela do navegador
2. Faça login com sua conta Google
3. Autorize o aplicativo a enviar e-mails
4. O token será salvo para uso futuro

## 🎨 Design do PDF

### Página 1 - Cabeçalho Moderno
- **Gradiente azul/roxo** com efeitos visuais
- **Cards glassmorphism** para informações do cliente
- **Seção:** Acesso ao Sistema IXC

### Páginas 2-3 - Headers Simples  
- **Layout clean** sem repetir cabeçalho completo
- **Seções:** Backup e Servidor (se aplicável)
- **Footer** com numeração e informações contextuais



## ❓ Solução de Problemas

### Erro: "credentials.json não encontrado"
```bash
# Verifique se o arquivo está na pasta correta
ls -la credentials.json

# Deve estar no mesmo diretório que app.py
```

### Erro de autenticação Gmail
```bash
# Delete o token e autentique novamente
rm token.pickle

# Execute novamente
streamlit run app.py
```

### Erro na geração de PDF
```bash
# Verifique se wkhtmltopdf está instalado
wkhtmltopdf --version

# Se não estiver:
sudo apt-get install wkhtmltopdf
```

### Erro de criptografia
```bash
# Instale qpdf se necessário
sudo apt-get install qpdf

# O sistema tenta múltiplos métodos automaticamente
```

### Porta 8501 ocupada
```bash
# Use porta diferente
streamlit run app.py --server.port 8502

# Ou mate processo existente
pkill -f streamlit
```

## 📊 Limitações

### Gmail API (Contas Gratuitas)
- **Envios:** 250 mensagens/dia
- **Anexos:** Máximo 25MB por e-mail
- **Token:** Expira após 7 dias de inatividade

### PDF
- **Tamanho:** Otimizado para A4
- **Criptografia:** AES-256 via qpdf/pypdf
- **Fontes:** System fonts (garantia de compatibilidade)


## 👥 Equipe

**Desenvolvido por Rafael Vargas**

- Automatização de processos internos
- Profissionalização da entrega de relatórios
- Segurança e criptografia de dados sensíveis

## 📞 Suporte

Para dúvidas ou problemas:

1. **Verifique a documentação** acima
2. **Consulte os logs** do Streamlit no terminal
3. **Teste com dados simples** (sem caracteres especiais)
4. **Entre em contato** com a equipe de desenvolvimento

---

**⚡ Sistema otimizado para a rotina da equipe de implantação IXC**