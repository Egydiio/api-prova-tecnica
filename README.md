# API Prova Técnica — Instruções de Execução

Este repositório contém uma API REST de produtos construída com Laravel 13 e PHP 8.3. A API expõe endpoints para CRUD de produtos e suporta filtros por nome, preço e estoque.


## Tecnologias
- PHP 8.3
- Laravel 13
- MySQL 8
- Composer e Node.js (para ambiente sem Docker ou para build de assets)
- Docker e Docker Compose (opcional, recomendado)


## Pré‑requisitos
Você pode executar de duas formas:
- Com Docker: apenas Docker e Docker Compose instalados.
- Sem Docker: PHP 8.3, Composer 2.x, Node.js 18+ e um servidor MySQL 8 acessível.


## Como rodar com Docker (recomendado)
1. Copie o arquivo de ambiente e gere a chave da aplicação:
   ```bash
   cp .env .env.local || cp .env.example .env
   ```
   O projeto já traz um `.env` configurado para Docker (DB_HOST=db, DB_DATABASE=dbProva, DB_USERNAME=root, DB_PASSWORD=root).

2. Suba os containers:
   ```bash
   docker compose up -d --build
   ```
   - A aplicação ficará acessível em: http://localhost:8000
   - O MySQL ficará exposto na porta do host 3307 (para clientes locais). De dentro do container, a porta é 3306 e o host é `db`.

Pronto! A API está disponível em http://localhost:8000.


## Como rodar sem Docker (ambiente local)
1. Pré‑requisitos instalados: PHP 8.3, Composer, Node.js, MySQL 8.
2. Copie o arquivo `.env` e ajuste as variáveis de banco para o seu ambiente local (ex.: `DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_DATABASE=seu_db`, `DB_USERNAME=seu_user`, `DB_PASSWORD=seu_password`).
3. Instale as dependências e gere a chave:
   ```bash
   composer install
   php artisan key:generate
   ```
4. Execute as migrações:
   ```bash
   php artisan migrate
   ```
5. (Opcional) Instale dependências JS e rode o Vite em dev ou build de produção:
   ```bash
   npm install
   npm run dev   # desenvolvimento
   # ou
   npm run build # produção
   ```
6. Inicie o servidor local do Laravel:
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```
   Acesse: http://localhost:8000


## Scripts úteis (Composer)
- Setup completo (instala, gera .env, key, migra, instala e builda front):
  ```bash
  composer run setup
  ```
- Ambiente de desenvolvimento combinado (servidor, fila, logs, vite) — requer Node e `npx`:
  ```bash
  composer run dev
  ```
- Testes:
  ```bash
  composer test
  ```


## Variáveis de ambiente essenciais (.env)
- `APP_URL` (ex.: `http://localhost:8000`)
- `DB_CONNECTION=mysql`
- `DB_HOST` (Docker: `db`; local: `127.0.0.1`)
- `DB_PORT` (Docker interno: `3306`; host acessando container: `3307`)
- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

Outras já vêm configuradas para desenvolvimento. Ajuste conforme necessidade.


## Endpoints da API
Base URL (Docker e local): `http://localhost:8000/api`

Recurso principal: `products` (rotas RESTful padrão)
- GET `/products` — lista produtos com filtros opcionais:
  - `name` (substring, like)
  - `min_price` (>=)
  - `max_price` (<=)
  - `stock_quantity` (>=)
- GET `/products/{id}` — detalha um produto
- POST `/products` — cria produto
- PUT `/products/{id}` — atualiza produto (completo)
- PATCH `/products/{id}` — atualiza produto (parcial, se validado pela request)
- DELETE `/products/{id}` — remove produto

Observação: a API retorna respostas padronizadas via `ApiResponseTrait` e usa `ProductResource` para formatação.


## Banco de dados
- Migrações: `php artisan migrate`
- Rollback: `php artisan migrate:rollback`


## Testes
- Executar todos os testes:
  ```bash
  php artisan test
  # ou
  composer test
  ```
