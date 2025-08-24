# MyWallet

[![Licença](https://img.shields.io/badge/licen%C3%A7a-Apache%202.0-blue.svg)](https://opensource.org/licenses/Apache-2.0)

> Um protótipo funcional (MVP) de uma aplicação de gestão financeira pessoal, construída com as melhores práticas de desenvolvimento e deploy utilizando Laravel, Vue.js e Docker.

**Acesse a aplicação em produção:** [my-wallet.app.br](https://my-wallet.app.br)

## 🚀 Sobre o Projeto

MyWallet é uma ferramenta intuitiva para ajudar usuários a ter um controle claro e objetivo sobre suas finanças. O projeto foi desenvolvido inicialmente como um MVP e como portfólio para demonstrar habilidades em desenvolvimento full-stack e DevOps, desde a concepção da ideia até o deploy contínuo em um ambiente de produção.

### Principais Funcionalidades

*   ✅ Cadastro de lançamentos financeiros (receitas e despesas).
*   ✅ Gestão de categorias personalizadas para os lançamentos.
*   ✅ Definição e acompanhamento de metas financeiras.
*   ✅ Criação de orçamentos mensais por categoria.
*   ✅ Dashboards e resumos visuais com o balanço mensal e anual.

## 🛠️ Tecnologias Utilizadas e Arquiteura do Projeto (Lógica e Física)

As seguintes tecnologias estão sendo utilizadas para o desenvolvimento do sistema:

- [Laravel 12](https://laravel.com)
- [Vue 3](https://vuejs.org/)
- [Inertia.js](https://inertiajs.com/) para integração Vue + Laravel sem APIs REST tradicionais
- [Laravel Jetstream](https://jetstream.laravel.com/) com autenticação, recuperação de senha, verificação de e-mail, gerenciamento de sessão, etc.
- [PostgreSQL 15](https://www.postgresql.org/)
- [Containers Docker + Docker Compose para orquestração](https://www.docker.com/)
- **Servidor Web:** Nginx
- **CI/CD:** GitHub Actions

### Estilo Arquitetual

O sistema utiliza uma Arquitetura em Camadas em seu monólito Laravel, garantindo uma clara separação de responsabilidades. Camadas do sistema:

- **Camada de Apresentação**: Controllers + Views (Inertia/Vue.js)
- **Camada de Negócio**: Services (regras de negócio)
- **Camada de Acesso aos Dados**: Repositories + Models 

Com a adição do processamento de arquivos, a arquitetura evolui para um modelo Híbrido com elementos de Arquitetura Orientada a Serviços, onde a funcionalidade em Python atua como um serviço especializado e desacoplado para extração de dados.

### Descrição da Infraestrutura Física 

A infraestrutura do sistema é projetada para ser robusta e consistente, utillizando containers docker e automatização através de um pipeline de CI/CD via GitHub Actions .

**Servidor:**
- **DigitalOcean Droplet**: 2 vCPUs, 4GB RAM, 80GB SSD
- **Ubuntu 22.04 LTS** com Docker Engine

**Rede:**
- **Docker Bridge Network**: Comunicação interna entre containers (python e aplicação laravel)
- **Nginx Reverse Proxy**: Roteamento HTTP/HTTPS
- **SSL/TLS**: Certificado Let's Encrypt
- **Domínio**: Domínio regsistrado no registro.br

**Armazenamento:**
- **Volume persistente**: PostgreSQL data

**Monitoramento:**
- **Docker health checks** em todos os containers
- **Laravel logs** centralizados

### Pipeline de CI/CD com GitHub Actions

O deploy é 100% automatizado.

1.  **`build.yml`:** A cada `push` na branch `main`, este workflow é acionado. Ele builda as imagens Docker se preciso e publica no GHCR.**GitHub Container Registry (GHCR)**.
2.  **`deploy.yml`:** Assim que o workflow de build termina com sucesso, este segundo workflow se conecta via SSH no servidor e executa o deploy com `deploy.sh`.

## 📂 Estrutura de Diretórios do Docker

A configuração do Docker é modularizada para separar as responsabilidades de cada ambiente e serviço.

```
my-walet-app/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── docker/
│   ├── common/
│   │   └── php-fpm/
│   │       └── Dockerfile      # Dockerfile base para o PHP-FPM (dev e prod)
│   ├── development/
│   │   ├── nginx/
│   │   │   └── Dockerfile      # Dockerfile do Nginx para dev
│   │   │   └── nginx.conf      # Config do Nginx para dev
│   │   ├── workspace/
│   │   │     └── Dockerfile    # Dockerfile de Workspace (PHP-CLI) para dev
│   │   └── php-fpm/
│   │        └── entrypoint.sh  # Códigos de entrypoint para prod na imagem PHP-FPM 
│   └── production/
│       ├── nginx/
│       │   └── Dockerfile      # Dockerfile do Nginx para prod
│       │   └── nginx.conf      # Config do Nginx para prod
│       ├── php-cli/
│       │   └── Dockerfile      # Dockerfile do PHP-CLI para produção
│       └── php-fpm/
│           └── entrypoint.sh   # Códigos de entrypoint para dev na imagem PHP-FPM
├── compose.dev.yaml            # Orquestração dos containers de dev
└── compose.prod.yaml           # Orquestração dos containers de prod
├── .dockerignore
├── resources/
│   └── js/                     # Vue frontend
├── ...
```

## ⚙️ Ambiente de Desenvolvimento

Siga os passos abaixo para configurar e rodar o projeto em sua máquina local.

### Pré-requisitos

*   **Git**.
*   **Docker e Docker Compose:** Caso use Windows, a forma mais fácil é instalar o [Docker Desktop](https://www.docker.com/products/docker-desktop/).
*   **Recomendado no Windows:** Usar o [WSL 2 (Windows Subsystem for Linux)](https://docs.microsoft.com/pt-br/windows/wsl/install) ou o terminal do Git Bash para uma melhor performance e compatibilidade. Todos os comandos `docker` e `git` podem ser executados dentro do terminal do Git Bash ou WSL2.

### Passos para Configuração

1.  **Clone o repositório:**
    ```bash
    git clone https://github.com/DuduHenriqueMg/my-wallet-app.git
    cd my-wallet-app
    ```

2.  **Crie o arquivo de ambiente:**
    Copie o arquivo de exemplo para criar seu arquivo de configuração local.
    ```bash
    cp .env.example .env
    ```
    Abra o arquivo `.env` e, se necessário, ajuste as variáveis de ambiente (`DB_HOST`, `DB_PORT`, etc.). Os valores padrão já devem funcionar com a configuração do Docker.

    Edite o `.env` com suas configurações de banco:

    ```env
    DB_CONNECTION=pgsql
    DB_HOST=db
    DB_PORT=5432
    DB_DATABASE=exemplo
    DB_USERNAME=exemplo
    DB_PASSWORD=
    ```

3.  **Suba os contêineres:**
    Este comando irá construir as imagens e iniciar todos os serviços necessários em segundo plano.
    ```bash
    docker compose -f compose.dev.yaml up -d --build
    ```

#### 📌 Qual container usar para cada tarefa?

- **php-fpm:** Apenas executa o Laravel junto com o Nginx. **Não usar para comandos CLI**.
- **php-cli:** Este é o container onde você roda **todos os comandos Composer, Artisan e NPM**.

4.  **Instale as dependências do PHP (Composer):**
    Execute o comando de dentro do contêiner `php-cli`.
    ```bash
    docker compose -f compose.dev.yaml exec php-cli composer install
    ```

5.  **Instale as dependências JS (NPM):**
    ```bash
    docker compose -f compose.dev.yaml exec php-cli npm install
    ```

6.  **Gere a chave da aplicação Laravel:**
    ```bash
    docker compose -f compose.dev.yaml exec php-cli php artisan key:generate
    ```

7.  **Execute as migrações e seeders do banco de dados:**
    Isso criará as tabelas e populará o banco com dados iniciais.
    ```bash
    docker compose -f compose.dev.yaml exec php-cli php artisan migrate --seed
    ```

8.  **Inicie o servidor de desenvolvimento do Vite:**
    Este comando compila os assets do frontend e os mantém atualizados com o Hot Module Replacement (HMR).
    ```bash
    docker compose -f compose.dev.yaml exec php-cli npm run dev -- --host
    ```

    Se necessário, ajuste no `.env`:

    ```env
    VITE_HOST=0.0.0.0
    ```

8.  **Acessando o container:**
    Estes comandos acessam o terminal do container.
    ```bash
    docker compose -f compose.dev.yaml exec -it php-cli bash
    ```
    ```sh
    docker compose -f compose.dev.yaml exec -it php-cli sh
    ```

🎉 **Pronto!** Agora você pode acessar a aplicação em [http://localhost:8000](http://localhost:8000) (ou a porta que você definiu no .env em `APP_URL`).

### 🛠️ Comandos úteis para

- Subir e buildar os containers: `docker compose -f compose.dev.yaml up -d --build`
- Parar os containers: `docker compose -f compose.dev.yaml down`
- Acessar o container: `docker compose -f compose.dev.yaml exec -it php-cli bash`
- Ver logs: `docker compose -f compose.dev.yaml logs -f`
- Subir a aplicação `docker compose -f compose.dev.yaml exec php-cli composer run dev`

## 🧾 Licença

Este projeto está licenciado sob a Apache 2.0 License.
