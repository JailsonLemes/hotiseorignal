import os
import pickle
import base64
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from email.mime.application import MIMEApplication
from google.auth.transport.requests import Request
from google.oauth2.credentials import Credentials
from google_auth_oauthlib.flow import InstalledAppFlow
from googleapiclient.discovery import build
from googleapiclient.errors import HttpError

# Escopos necessários para envio de email
SCOPES = ['https://www.googleapis.com/auth/gmail.send']


def authenticate_gmail(credentials_path='credentials.json'):
    """
    Autentica com a API do Gmail usando OAuth2

    Args:
        credentials_path (str): Caminho para o arquivo credentials.json

    Returns:
        google.oauth2.credentials.Credentials: Credenciais autenticadas
    """
    creds = None
    import os
    import hashlib

    # Cria um nome de token único baseado no e-mail da credencial (ou client_id)
    token_path = None
    if os.path.exists(credentials_path):
        with open(credentials_path, 'rb') as f:
            creds_hash = hashlib.md5(f.read()).hexdigest()[:8]
        token_path = f'token_{creds_hash}.pickle'
    else:
        token_path = 'token_default.pickle'

    # O arquivo token.pickle armazena os tokens de acesso e atualização do usuário
    if os.path.exists(token_path):
        with open(token_path, 'rb') as token:
            creds = pickle.load(token)

    # Se não há credenciais válidas disponíveis, deixe o usuário fazer login
    if not creds or not creds.valid:
        if creds and creds.expired and creds.refresh_token:
            try:
                creds.refresh(Request())
            except Exception as e:
                print(f"Erro ao atualizar credenciais: {e}")
                # Remove token inválido e força nova autenticação
                if os.path.exists(token_path):
                    os.remove(token_path)
                creds = None

        if not creds:
            if not os.path.exists(credentials_path):
                raise FileNotFoundError(f"Arquivo de credenciais não encontrado: {credentials_path}")

            flow = InstalledAppFlow.from_client_secrets_file(credentials_path, SCOPES)
            creds = flow.run_local_server(port=0)

        # Salva as credenciais para a próxima execução
        with open(token_path, 'wb') as token:
            pickle.dump(creds, token)

    return creds


def create_message_with_attachment(to_email, subject, body, attachment_path, attachment_name):
    """
    Cria uma mensagem de email com anexo

    Args:
        to_email (str): Email do destinatário
        subject (str): Assunto do email
        body (str): Corpo do email
        attachment_path (str): Caminho do arquivo anexo
        attachment_name (str): Nome do anexo

    Returns:
        dict: Mensagem formatada para a API do Gmail
    """
    message = MIMEMultipart()
    message['to'] = to_email
    message['subject'] = subject

    # Adicionar corpo do email
    message.attach(MIMEText(body, 'plain', 'utf-8'))

    # Adicionar anexo
    try:
        with open(attachment_path, 'rb') as f:
            attachment_data = f.read()

        attachment = MIMEApplication(attachment_data)
        attachment.add_header('Content-Disposition', 'attachment', filename=attachment_name)
        message.attach(attachment)

    except Exception as e:
        raise Exception(f"Erro ao anexar arquivo: {str(e)}")

    # Codificar mensagem em base64
    raw_message = base64.urlsafe_b64encode(message.as_bytes()).decode('utf-8')
    return {'raw': raw_message}


def send_email_with_attachment(to_email, subject, body, attachment_path, attachment_name,
                               credentials_path='credentials.json'):
    """
    Envia um email com anexo usando a API do Gmail

    Args:
        to_email (str): Email do destinatário
        subject (str): Assunto do email
        body (str): Corpo do email
        attachment_path (str): Caminho do arquivo anexo
        attachment_name (str): Nome do anexo
        credentials_path (str): Caminho para o arquivo credentials.json

    Returns:
        dict: Resposta da API do Gmail
    """
    try:
        # Autenticar
        creds = authenticate_gmail(credentials_path)

        # Construir serviço
        service = build('gmail', 'v1', credentials=creds)

        # Criar mensagem
        message = create_message_with_attachment(
            to_email=to_email,
            subject=subject,
            body=body,
            attachment_path=attachment_path,
            attachment_name=attachment_name
        )

        # Enviar email
        result = service.users().messages().send(userId='me', body=message).execute()

        print(f"Email enviado com sucesso! ID da mensagem: {result['id']}")
        return result

    except HttpError as error:
        error_details = error.error_details[0] if error.error_details else {}
        error_message = error_details.get('message', str(error))
        raise Exception(f"Erro da API do Gmail: {error_message}")

    except Exception as e:
        raise Exception(f"Erro ao enviar email: {str(e)}")


def test_gmail_connection(credentials_path='credentials.json'):
    """
    Testa a conexão com a API do Gmail

    Args:
        credentials_path (str): Caminho para o arquivo credentials.json

    Returns:
        bool: True se a conexão for bem-sucedida
    """
    try:
        creds = authenticate_gmail(credentials_path)
        service = build('gmail', 'v1', credentials=creds)

        # Testar acesso básico
        profile = service.users().getProfile(userId='me').execute()
        print(f"Conexão estabelecida com sucesso! Email: {profile['emailAddress']}")
        return True

    except Exception as e:
        print(f"Erro ao conectar com Gmail: {str(e)}")
        return False