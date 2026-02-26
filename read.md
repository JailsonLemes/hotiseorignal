# Hotsite - Capturas de Tela

## Visao Geral
Este projeto gera capturas da Central do Assinante usando um container Cypress (`harbor.ixcsoft.com.br/papaya/fastprint:latest`).
O fluxo usa fila + worker: o web enfileira o job e o worker processa e gera o ZIP.

## Subir o ambiente
```bash
docker compose up -d --build
```

Servicos:
- `apache-hotsite`: web
- `hotsite-worker`: processa a fila de capturas

## Como usar
1. Acesse `http://localhost/onboarding/printScreen/`
2. Preencha URL, login, senha e plataforma
3. Envie o formulario
4. O sistema acompanha o job e faz o download automatico do ZIP ao concluir

## Endpoints
- Status do job: `gerar_capturas.php?status=<job_id>`
- Download do ZIP: `gerar_capturas.php?download_zip=<job_id>`
- Download do log: `gerar_capturas.php?download_log=<job_id>`

## Estrutura de arquivos
- `html/onboarding/printScreen/Files/queue`: fila de jobs
- `html/onboarding/printScreen/Files/status`: status dos jobs
- `html/onboarding/printScreen/Files/jobs/<job_id>`: screenshots, zip e logs

## Observacoes de seguranca
O `docker.sock` do host esta montado nos containers para permitir `docker run`.
Isso concede alto nivel de acesso ao host. Para producao, recomenda-se:
- Rodar o worker fora do container
- Ou usar um wrapper com `sudo` restrito

