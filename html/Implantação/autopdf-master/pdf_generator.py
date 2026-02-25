from jinja2 import Template
import pdfkit
import os

# O template HTML e CSS agora vivem dentro do script.
HTML_TEMPLATE = """
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Credenciais de Acesso - {{ provider_name }}</title>
    <style>
        /* Usando fontes padrão do sistema (Helvetica, Arial) para máxima compatibilidade */
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #212529; /* Cor de texto principal (escuro) */
            background-color: #ffffff; /* Fundo principal branco */
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact !important; /* Força cores no wkhtmltopdf */
            print-color-adjust: exact !important;
        }

        .container {
            width: 100%;
            border-radius: 12px;
            border: 1px solid #dee2e6; /* Borda cinza clara */
            overflow: hidden;
            margin: 0;
            background-color: #f8f9fa; /* Fundo do container (cinza muito claro) */
        }

        /* --- HEADER CLARO E MODERNO --- */
        .brand-header {
            background-color: #0d6efd; /* Cor azul primária */
            color: #ffffff;
            padding: 24px 35px; /* Espaçamento reduzido */
            border-bottom: 3px solid #0a58ca; /* Azul um pouco mais escuro */
        }

        .brand-header h1 {
            font-weight: bold;
            font-size: 28px; /* Tamanho reduzido */
            margin-bottom: 4px;
        }

        .brand-header p {
            font-weight: normal;
            font-size: 15px; /* Tamanho reduzido */
            opacity: 0.9;
        }

        .content {
            padding: 18px 35px 18px 35px; /* Espaçamento reduzido */
        }

        section {
            margin-bottom: 18px; /* Espaçamento reduzido */
        }

        /* --- TÍTULO DE SEÇÃO COM COR --- */
        h2.section-title {
            font-weight: bold;
            font-size: 17px; /* Tamanho reduzido */
            color: #0d6efd; /* Azul primário */
            margin-bottom: 14px; /* Espaçamento reduzido */
            padding-left: 12px; /* Espaçamento reduzido */
            border-left: 4px solid #79a6d2; /* Borda azul mais clara */
        }

        .form-table {
            width: 100%;
            border-collapse: collapse;
        }

        .form-table td {
            width: 50%;
            vertical-align: top;
            padding-bottom: 12px; /* Espaçamento reduzido */
            padding-right: 20px; /* Espaçamento reduzido */
        }

        .form-table td + td {
            padding-right: 0;
        }

        label {
            font-weight: bold;
            font-size: 13px; /* Tamanho reduzido */
            color: #495057; /* Cinza escuro para label */
            display: block;
            margin-bottom: 5px; /* Espaçamento reduzido */
        }

        /* --- CAMPO COM DESTAQUE E TEXTO EM NEGRITO --- */
        .form-field {
            background-color: #ffffff; /* Fundo do campo branco */
            border: 1px solid #ced4da; /* Borda cinza um pouco mais escura */
            border-left: 4px solid #0d6efd; /* Borda azul primária */
            border-radius: 6px; /* Raio reduzido */
            padding: 10px 14px; /* Espaçamento reduzido */
            font-size: 14px; /* Tamanho reduzido */
            color: #212529; /* Texto escuro */
            min-height: 20px;
            /* --- AJUSTE: Usando valor numérico para negrito --- */
            font-weight: 700; /* Equivalente a 'bold', mas pode renderizar diferente */
            word-wrap: break-word; /* Para quebrar texto longo */
        }

        .form-field-empty {
            color: #6c757d; /* Cinza para placeholder */
            font-style: italic;
            font-weight: normal; /* Placeholder não fica em negrito */
        }

        /* --- FOOTER CLARO --- */
        footer {
            text-align: center;
            font-size: 11px; /* Tamanho reduzido */
            color: #6c757d; /* Texto do rodapé (cinza) */
            border-top: 1px solid #dee2e6;
            padding: 16px 40px; /* Espaçamento reduzido */
            background-color: #f8f9fa; /* Fundo cinza claro */
        }

        .confidential {
            font-size: 10px; /* Tamanho reduzido */
            color: #6c757d;
            margin-bottom: 8px; /* Espaçamento reduzido */
            font-style: italic;
        }

        footer .logo {
            font-weight: bold;
            color: #0d6efd; /* Azul para logo */
            margin-bottom: 4px; /* Espaçamento reduzido */
        }
    </style>
</head>
<body>
    <main class="container">

        <header class="brand-header">
            <h1>Relatório de Acesso</h1>
            <p>{{ provider_name or 'N/A' }} | {{ deployment_type or 'N/A' }}</p>
        </header>

        <div class="content">
            <section>
                <h2 class="section-title">Informações do Provedor</h2>
                <table class="form-table">
                    <tr>
                        <td colspan="2">
                            <label for="provider-name">Nome do Provedor</label>
                            <div class="form-field" id="provider-name">
                                {{ provider_name or '<span class="form-field-empty">—</span>' }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="domain">Domínio/Site</label>
                            <div class="form-field" id="domain">
                                {{ domain or '<span class="form-field-empty">—</span>' }}
                            </div>
                        </td>
                        <td>
                            <label for="user">Usuário</label>
                            <div class="form-field" id="user">
                                {{ username or '<span class="form-field-empty">—</span>' }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <label for="password">Senha</label>
                            <div class="form-field" id="password">
                                {{ password or '<span class="form-field-empty">—</span>' }}
                            </div>
                        </td>
                    </tr>
                </table>
            </section>

            {% if deployment_type == "IXC Provedor" and server_ip %}
            <section>
                <h2 class="section-title">Configurações do Servidor</h2>
                <table class="form-table">
                    <tr>
                        <td>
                            <label for="server-ip">IP do servidor</label>
                            <div class="form-field" id="server-ip">
                                {{ server_ip or '<span class="form-field-empty">—</span>' }}
                            </div>
                        </td>
                        <td>
                            <label for="server-password">Senha do Servidor</label>
                            <div class="form-field" id="server-password">
                                {{ server_password or '<span class="form-field-empty">—</span>' }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="port">Porta</label>
                            <div class="form-field" id="port">
                                {{ server_port or '<span class="form-field-empty">—</span>' }}
                            </div>
                        </td>
                        <td>
                            <label for="server-user">Usuário</label>
                            <div class="form-field" id="server-user">
                                {{ server_user or '<span class="form-field-empty">—</span>' }}
                            </div>
                        </td>
                    </tr>
                </table>
            </section>
            {% endif %}

            <section>
                <h2 class="section-title">Backup</h2>
                <table class="form-table">
                    <tr>
                        <td>
                            <label for="backup-location">Local de Backup</label>
                            <div class="form-field" id="backup-location">
                                {{ backup_location or '<span class="form-field-empty">—</span>' }}
                            </div>
                        </td>
                        <td>
                            <label for="backup-email">Email de backup</label>
                            <div class="form-field" id="backup-email">
                                {{ backup_email or '<span class="form-field-empty">—</span>' }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <label for="backup-password">Senha de backup</label>
                            <div class="form-field" id="backup-password">
                                {{ backup_password or '<span class="form-field-empty">—</span>' }}
                            </div>
                        </td>
                    </tr>
                </table>
            </section>
        </div>

        <footer>
            <p class="confidential">
                Este documento contém informações confidenciais. O PDF está criptografado para sua segurança.
            </p>
            <div class="logo">IXCsoft®</div>
            <p>Documento gerado em: {{ generation_date }}</p>
        </footer>
    </main>
</body>
</html>
"""

def generate_pdf(data, output_path):
    """
    Gera um PDF usando PDFKit (wrapper para wkhtmltopdf) com o template de tema claro refinado.
    """
    print("🔍 Gerando PDF com o template claro refinado (página única)...")

    try:
        template = Template(HTML_TEMPLATE)
        safe_data = {k: v for k, v in data.items() if v}
        html_content = template.render(**safe_data)

        options = {
            'page-size': 'A4',
            'margin-top': '20mm',
            'margin-right': '20mm',
            'margin-bottom': '20mm',
            'margin-left': '20mm',
            'encoding': "UTF-8",
            'load-error-handling': 'ignore',
            'disable-smart-shrinking': None,
            'zoom': 0.95, # Mantém o zoom para garantir página única
        }

        # Converte o HTML renderizado para PDF
        pdfkit.from_string(html_content, output_path, options=options)

        print("✅ PDF gerado com sucesso!")
        return output_path

    except ImportError:
        raise ImportError(
            "❌ PDFKit não encontrado. "
            "Por favor, execute o script 'install.sh' para instalar as dependências."
        )
    except OSError as e:
         if 'No wkhtmltopdf executable found' in str(e):
              raise OSError(
                "❌ O executável 'wkhtmltopdf' não foi encontrado no seu sistema. "
                "Por favor, execute o script 'install.sh' para instalá-lo."
              )
         else:
              raise e
    except Exception as e:
        raise Exception(f"❌ Erro ao gerar o PDF com PDFKit: {str(e)}")