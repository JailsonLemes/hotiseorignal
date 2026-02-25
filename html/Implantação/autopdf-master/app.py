import streamlit as st
import os
import tempfile
from datetime import datetime
from pathlib import Path

# Importações dos módulos personalizados
from pdf_generator import generate_pdf
from email_sender import send_email_with_attachment
from pdf_encryptor import encrypt_pdf

# Configuração da página
st.set_page_config(
    page_title="Sistema de Relatórios IXC",
    page_icon="📊",
    layout="wide",
    initial_sidebar_state="expanded"
)

# CSS personalizado para melhorar o visual
st.markdown("""
<style>
    .main-header {
        background: linear-gradient(90deg, #1f4e79 0%, #2e6da4 100%);
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 2rem;
    }
    .main-header h1 {
        color: white;
        text-align: center;
        margin: 0;
        font-weight: 600;
    }
    .section-header {
        background: #f8f9fa;
        padding: 0.5rem 1rem;
        border-left: 4px solid #2e6da4;
        margin: 1rem 0;
        border-radius: 5px;
    }
    .stButton > button {
        background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
        color: white;
        border: none;
        padding: 0.5rem 2rem;
        border-radius: 5px;
        font-weight: 600;
    }
    .success-message {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
        padding: 1rem;
        border-radius: 5px;
        margin: 1rem 0;
    }
    .error-message {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
        padding: 1rem;
        border-radius: 5px;
        margin: 1rem 0;
    }
</style>
""", unsafe_allow_html=True)

# Cabeçalho principal
st.markdown("""
<div class="main-header">
    <h1>🚀 Sistema de Relatórios de Implantação IXC</h1>
</div>
""", unsafe_allow_html=True)

# Sidebar com informações
with st.sidebar:
    st.markdown("### ℹ️ Informações do Sistema")
    st.info("Este sistema gera relatórios profissionais para implantações IXC e os envia automaticamente por e-mail.")

    st.markdown("### 📋 Processo:")
    st.markdown("""
    1. **Preencha** os dados do cliente
    2. **Selecione** o tipo de implantação  
    3. **Configure** o e-mail de destino
    4. **Gere e envie** o relatório
    """)


def main():
    # Seção: Dados do Provedor/Cliente
    st.markdown('<div class="section-header"><h3>📊 Dados do Provedor</h3></div>', unsafe_allow_html=True)

    col1, col2 = st.columns(2)

    with col1:
        provider_name = st.text_input("Nome do Provedor (Cliente)", placeholder="Ex: Provedor Conecta")
        domain = st.text_input("Domínio (Site)", placeholder="Ex: www.provedorconecta.com.br")
        username = st.text_input("Usuário (E-mail)", placeholder="Ex: admin@provedorconecta.com.br")
        password = st.text_input("Senha", type="password", placeholder="Digite a senha")

    with col2:
        backup_location = st.text_input("Local de Backup", placeholder="Ex: Google Drive")
        backup_email = st.text_input("E-mail de Backup", placeholder="Ex: backup@provedorconecta.com.br")
        backup_password = st.text_input("Senha de Backup", type="password", placeholder="Digite a senha de backup")

    # Seção: Tipo de Implantação
    st.markdown('<div class="section-header"><h3>⚙️ Configuração da Implantação</h3></div>', unsafe_allow_html=True)

    deployment_type = st.selectbox(
        "Tipo de Implantação",
        ["IXC Provedor", "IXC Cloud"],
        help="Selecione o tipo de implantação que está sendo realizada"
    )

    # Campos condicionais para IXC Provedor
    server_ip = server_port = server_user = server_password = None

    if deployment_type == "IXC Provedor":
        st.markdown("**🔧 Configurações do Servidor**")
        col3, col4 = st.columns(2)

        with col3:
            server_ip = st.text_input("IP do Servidor", placeholder="Ex: 192.168.1.100")
            server_port = st.text_input("Porta", placeholder="Ex: 22", value="22")

        with col4:
            server_user = st.text_input("User do Servidor", placeholder="Ex: root")
            server_password = st.text_input("Senha Root", type="password", placeholder="Digite a senha root")

    # Seção: Configurações de Envio
    st.markdown('<div class="section-header"><h3>📧 Configurações de Envio</h3></div>', unsafe_allow_html=True)

    col5, col6 = st.columns(2)

    with col5:
        client_email = st.text_input("E-mail do Cliente para Envio", placeholder="Ex: cliente@empresa.com.br")

    with col6:
        pdf_password = st.text_input("Senha para o arquivo PDF", type="password",
                                     placeholder="Senha para criptografar o PDF",
                                     help="Esta senha será necessária para abrir o PDF")

    # Botão de envio
    st.markdown("---")

    if st.button("🚀 Gerar e Enviar Relatório", use_container_width=True):
        # Validação dos campos obrigatórios
        required_fields = {
            'Nome do Provedor': provider_name,
            'Domínio': domain,
            'Usuário': username,
            'Senha': password,
            'E-mail do Cliente': client_email,
            'Senha do PDF': pdf_password
        }

        if deployment_type == "IXC Provedor":
            required_fields.update({
                'IP do Servidor': server_ip,
                'User do Servidor': server_user,
                'Senha Root': server_password
            })

        missing_fields = [field for field, value in required_fields.items() if not value or not value.strip()]

        if missing_fields:
            st.markdown(f"""
            <div class="error-message">
                <strong>❌ Erro:</strong> Os seguintes campos são obrigatórios:<br>
                • {' • '.join(missing_fields)}
            </div>
            """, unsafe_allow_html=True)
            return

        # Verificar se o arquivo credentials.json existe
        if not os.path.exists('credentials.json'):
            st.markdown("""
            <div class="error-message">
                <strong>❌ Erro:</strong> Arquivo 'credentials.json' não encontrado.<br>
                Por favor, certifique-se de que o arquivo está na pasta raiz do projeto.
            </div>
            """, unsafe_allow_html=True)
            return

        # Progress bar
        progress_bar = st.progress(0)
        status_text = st.empty()

        try:
            # Preparar dados
            data = {
                'provider_name': provider_name,
                'domain': domain,
                'username': username,
                'password': password,
                'backup_location': backup_location,
                'backup_email': backup_email,
                'backup_password': backup_password,
                'deployment_type': deployment_type,
                'server_ip': server_ip,
                'server_port': server_port,
                'server_user': server_user,
                'server_password': server_password,
                'client_email': client_email,
                'pdf_password': pdf_password,
                'generation_date': datetime.now().strftime("%d/%m/%Y às %H:%M")
            }

            # Etapa 1: Gerar PDF
            status_text.text("📄 Gerando PDF...")
            progress_bar.progress(25)

            with tempfile.NamedTemporaryFile(suffix='.pdf', delete=False) as tmp_file:
                pdf_path = tmp_file.name

            generate_pdf(data, pdf_path)

            # Etapa 2: Criptografar PDF
            status_text.text("🔐 Criptografando PDF...")
            progress_bar.progress(50)

            encrypted_pdf_path = pdf_path.replace('.pdf', '_encrypted.pdf')
            encrypt_pdf(pdf_path, encrypted_pdf_path, pdf_password)

            # Etapa 3: Enviar por e-mail
            status_text.text("📧 Enviando por e-mail...")
            progress_bar.progress(75)

            filename = f"Relatorio_Acesso_{provider_name.replace(' ', '_')}_{datetime.now().strftime('%Y%m%d_%H%M')}.pdf"

            send_email_with_attachment(
                to_email=client_email,
                subject="Informações de Acesso - Instalação Finalizada",
                body=f"""
Prezado(a) cliente,

Segue em anexo o relatório com as informações de acesso da sua instalação para {provider_name}.

O arquivo PDF está protegido por senha. Utilize a senha fornecida durante o processo para acessar o conteúdo.

Data da implantação: {data['generation_date']}
Tipo de implantação: {deployment_type}

Atenciosamente,
Equipe de Implantação IXC
                """.strip(),
                attachment_path=encrypted_pdf_path,
                attachment_name=filename,
                credentials_path='credentials.json'
            )

            # Etapa 4: Finalizado
            progress_bar.progress(100)
            status_text.text("✅ Concluído!")

            # Limpar arquivos temporários
            try:
                os.unlink(pdf_path)
                os.unlink(encrypted_pdf_path)
            except:
                pass

            st.markdown(f"""
            <div class="success-message">
                <strong>✅ Sucesso!</strong> Relatório gerado e enviado com sucesso!<br>
                <strong>Destinatário:</strong> {client_email}<br>
                <strong>Arquivo:</strong> {filename}<br>
                <strong>Data/Hora:</strong> {data['generation_date']}
            </div>
            """, unsafe_allow_html=True)

            st.balloons()

        except Exception as e:
            progress_bar.progress(0)
            status_text.text("")

            st.markdown(f"""
            <div class="error-message">
                <strong>❌ Erro durante o processamento:</strong><br>
                {str(e)}
            </div>
            """, unsafe_allow_html=True)


if __name__ == "__main__":
    main()