
# 💰 MyWallet App - Em construção 🚧🚧

Este é um projeto de controle financeiro pessoal desenvolvido em **Laravel 12** e **Jetstream**, **Vue 3** com **Inertia.js**, e **PostgreSQL**, dockerizado para facilitar o ambiente de desenvolvimento.

---

## 🔐 Autenticação

Este projeto já vem com autenticação pronta usando Laravel Jetstream com Inertia.js:

- Registro de usuários
- Login
- Recuperação de senha
- Verificação de e-mail
- Gerenciamento de sessão

---

## 🚀 Tecnologias

- [Laravel 12](https://laravel.com)
- [Vue 3 + Vite](https://vitejs.dev/)
- [PostgreSQL 15](https://www.postgresql.org/)
- [Docker + Docker Compose](https://www.docker.com/)
- [Laravel Jetstream](https://jetstream.laravel.com/) com autenticação, verificação de e-mail, sessão de usuários, etc.
- [Inertia.js](https://inertiajs.com/) para integração Vue + Laravel sem APIs REST tradicionais
- Apache + PHP 8.2 + Composer + Node.js 20

---

## 📦 Pré-requisitos

Antes de iniciar, você precisa ter instalado em sua máquina:

- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/)

---

## ⚙️ Configuração do ambiente

1. **Clone o repositório**:

   ```bash
   git clone https://github.com/seu-usuario/mywallet.git
   cd mywallet
   ```

2. **Crie o arquivo `.env`** com base no exemplo:

   ```bash
   cp .env.example .env
   ```

   Edite o `.env` com suas configurações de banco:

   ```env
   DB_CONNECTION=pgsql
   DB_HOST=db
   DB_PORT=5432
   DB_DATABASE=mywallet
   DB_USERNAME=postgres
   DB_PASSWORD=
   ```

---

## 🐳 Subindo o projeto com Docker

1. **Build e execução dos containers**:

   ```bash
   docker-compose up -d --build
   ```

2. **Acesse o container da aplicação**:

   ```bash
   docker exec -it mywallet_app bash
   ```

3. **Dentro do container instale as dependências do Laravel e do frontend, gere a chave e rode as migrations**:

   ```bash
   composer install
   npm install
   php artisan key:generate
   php artisan migrate
   ```

---

## 🔥 Acessando a aplicação

- **Rode a aplicação com esse comando:**
   ```bash
   composer run dev
   ```
- **Laravel (backend)**: http://localhost:8000  
- **Vue (frontend com Vite)**: http://localhost:5173

---

## 🛠️ Comandos úteis

- Subir os containers: `docker-compose up -d`
- Parar os containers: `docker-compose down`
- Acessar o container: `docker exec -it mywallet_app bash`
- Ver logs: `docker-compose logs -f`
- Rodar a aplicação: `composer run dev`

---

## 📂 Estrutura do Projeto

```
my-wallet-app/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
│   └── js/  <-- Vue frontend
├── routes/
├── storage/
├── tests/
├── .env
├── Dockerfile
├── docker-compose.yml
├── package.json
├── vite.config.js
└── README.md
```

---

## 🧪 Testes

Você pode rodar os testes com:

```bash
php artisan test
```

---

## 🧾 Licença

Este projeto está licenciado sob a MIT License.
