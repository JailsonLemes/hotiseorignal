import subprocess
import sys
import os


def encrypt_pdf_with_qpdf(input_path, output_path, password):
    """
    Criptografa PDF usando qpdf (mais confiável)
    """
    try:
        # Comando qpdf para criptografar
        cmd = [
            'qpdf',
            '--encrypt', password, password, '256', '--',
            input_path,
            output_path
        ]

        result = subprocess.run(cmd, capture_output=True, text=True)

        if result.returncode == 0:
            print(f"PDF criptografado com qpdf: {output_path}")
            return output_path
        else:
            raise Exception(f"Erro do qpdf: {result.stderr}")

    except FileNotFoundError:
        raise Exception("qpdf não está instalado. Execute: sudo apt-get install qpdf")
    except Exception as e:
        raise Exception(f"Erro ao criptografar com qpdf: {str(e)}")


def encrypt_pdf_with_pypdf(input_path, output_path, password):
    """
    Criptografa PDF usando pypdf
    """
    try:
        from pypdf import PdfReader, PdfWriter

        reader = PdfReader(input_path)
        writer = PdfWriter()

        for page in reader.pages:
            writer.add_page(page)

        writer.encrypt(password)

        with open(output_path, 'wb') as output_file:
            writer.write(output_file)

        print(f"PDF criptografado com pypdf: {output_path}")
        return output_path

    except Exception as e:
        raise Exception(f"Erro ao criptografar com pypdf: {str(e)}")


def encrypt_pdf_with_reportlab(input_path, output_path, password):
    """
    Alternativa usando reportlab + PyPDF2 mais antiga
    """
    try:
        import PyPDF2

        with open(input_path, 'rb') as input_file:
            pdf_reader = PyPDF2.PdfFileReader(input_file)
            pdf_writer = PyPDF2.PdfFileWriter()

            for page_num in range(pdf_reader.numPages):
                pdf_writer.addPage(pdf_reader.getPage(page_num))

            pdf_writer.encrypt(password)

            with open(output_path, 'wb') as output_file:
                pdf_writer.write(output_file)

        print(f"PDF criptografado com PyPDF2: {output_path}")
        return output_path

    except Exception as e:
        raise Exception(f"Erro ao criptografar com PyPDF2: {str(e)}")


def encrypt_pdf(input_path, output_path, password):
    """
    Tenta criptografar PDF usando diferentes métodos
    """
    methods = [
        ("qpdf", encrypt_pdf_with_qpdf),
        ("pypdf", encrypt_pdf_with_pypdf),
        ("PyPDF2", encrypt_pdf_with_reportlab)
    ]

    last_error = None

    for method_name, method_func in methods:
        try:
            print(f"Tentando criptografar com {method_name}...")
            return method_func(input_path, output_path, password)
        except Exception as e:
            print(f"Falha com {method_name}: {str(e)}")
            last_error = e
            continue

    # Se todos os métodos falharam
    raise Exception(f"Falha em todos os métodos de criptografia. Último erro: {str(last_error)}")


def verify_pdf_encryption(pdf_path, password):
    """
    Verifica se um PDF está criptografado
    """
    try:
        from pypdf import PdfReader
        reader = PdfReader(pdf_path)

        if reader.is_encrypted:
            return reader.decrypt(password)
        else:
            return True

    except Exception as e:
        print(f"Erro ao verificar criptografia: {str(e)}")
        return False