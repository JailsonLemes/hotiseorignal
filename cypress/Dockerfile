# Imagem base oficial do Cypress (já vem com Node, npm e navegadores)
FROM cypress/included:13.17.0

# Define o diretório de trabalho dentro do contêiner
WORKDIR /app

# Copia apenas o package.json para instalar dependências
COPY package.json ./

# Instala dependências do projeto (Sharp, Cypress, etc.)
RUN npm install

# Copia o restante do projeto (cypress.config.js, testes, etc.)
COPY . .

# Define o comando padrão — executa o Cypress
ENTRYPOINT ["npx", "cypress", "run"]
