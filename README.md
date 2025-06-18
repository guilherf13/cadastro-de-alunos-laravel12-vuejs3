<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Logo do Laravel"></a></p>

# Teste Técnico - Microsserviço de Gerenciamento de Alunos

Este repositório contém a solução para o teste técnico de Desenvolvedor PHP Pleno, que consiste em um microsserviço back-end para gerenciamento de cadastros de alunos, construído com a versão mais recente do Laravel.

O serviço disponibiliza uma API RESTful para as operações de CRUD de alunos, incluindo regras de negócio específicas, autenticação baseada em tokens JWT e controle de acesso por perfil.

## Pré-requisitos

Para executar este projeto, você precisará ter os seguintes softwares instalados em sua máquina:
-   [Docker](https://www.docker.com/get-started)
-   [Docker Compose](https://docs.docker.com/compose/install/)

O PHP e o Composer não são necessários na sua máquina host, pois o ambiente de desenvolvimento é totalmente encapsulado pelos contêineres Docker.

---

## 🚀 Instalação e Execução com Docker

Siga os passos abaixo para configurar e executar o ambiente de desenvolvimento localmente.

### Passo 1: Clonar o Repositório

Primeiro, clone este repositório para a sua máquina local:

```bash
git clone https://github.com/guilherf13/teste-tecnico-alunos.git
cd teste_tecnico
```

### Passo 2: Configurar Variáveis de Ambiente e Permissões

#### 1. Criar Arquivo de Ambiente do Laravel

Copie o arquivo de ambiente de exemplo para criar seu arquivo de configuração local.

```bash
cp .env.example .env
```

#### 2. Configurar Conexão com o Banco de Dados

Abra o arquivo `.env` recém-criado e verifique se as variáveis de banco de dados correspondem à configuração do Docker Compose. Elas devem estar assim:

```dotenv
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=user_password
```

#### 3. Ajuste de Permissões de Usuário (Opcional, mas Recomendado)

Para evitar problemas de permissão de arquivos entre o seu sistema (host) e o contêiner Docker, é recomendado que o usuário dentro do contêiner tenha o mesmo ID de usuário (UID) e de grupo (GID) que o seu.

**a) Descubra seu UID e GID:**

Execute os seguintes comandos no seu terminal:

* **Para Linux e macOS:**
    ```bash
    echo "Seu UID é: $(id -u)"
    echo "Seu GID é: $(id -g)"
    ```

* **Para Windows (com WSL 2 ou Git Bash):**
    O comando acima geralmente funciona. Se não, o padrão costuma ser `1000`.

**b) Altere o Dockerfile:**

Abra o arquivo `docker/php/Dockerfile`. Localize as seguintes linhas no topo do arquivo:

```dockerfile
# docker/php/Dockerfile

ARG UID=1000
ARG GID=1000
```

Substitua os valores `1000` pelos valores de UID e GID que você descobriu no passo anterior. Por exemplo, se seu UID e GID são `1001`, as linhas deverão ficar assim:

```dockerfile
ARG UID=1001
ARG GID=1001
```
Este passo garante que os arquivos gerados pelo Laravel (como logs e cache) terão as permissões corretas no seu sistema.

### Passo 3: Construir e Iniciar os Contêineres

Com as configurações prontas, suba o ambiente com o Docker Compose. Este comando irá construir a imagem PHP personalizada (com seu UID/GID) e iniciar todos os serviços.

```bash
docker-compose up -d --build
```

### Passo 4: Finalizar a Instalação do Laravel

Após os contêineres estarem no ar, execute os seguintes comandos para instalar as dependências, gerar a chave da aplicação e criar a estrutura do banco de dados.

**1. Instalar dependências do Composer:**

```bash
docker-compose exec app composer install
```

**2. Gerar a chave da aplicação:**

```bash
docker-compose exec app php artisan key:generate
```

**3. Executar as Migrations e Seeders:**

Este comando irá criar a estrutura do banco de dados e popular a tabela de usuários com um perfil de "Gestor" e um de "Funcionário".

```bash
docker-compose exec app php artisan migrate:fresh --seed --seeder=UserSeeder
```

---

## 🚀 Credenciais de Teste

Após executar os seeders, você pode usar as seguintes credenciais para fazer o login na aplicação e testar os diferentes níveis de permissão.

A tela de login pode ser acessada em: 👉 **[http://localhost:5173/login](http://localhost:5173/login)**

### Perfil Gestor
-   **E-mail:** `gestor@test.com`
-   **Senha:** `password`

### Perfil Funcionário
-   **E-mail:** `funcionario@test.com`
-   **Senha:** `password`

---

## Utilização

Após a conclusão de todos os passos, a aplicação estará disponível em:
-   **Frontend (Vue.js):** 👉 **[http://localhost:5173](http://localhost:5173)**
-   **Backend (API Laravel):** 👉 **[http://localhost:8080/api](http://localhost:8080/api)**

O banco de dados estará acessível externamente (por um client como DBeaver ou TablePlus) em:
-   **Host:** `localhost`
-   **Porta:** `33066`

### Comandos Úteis do Docker

-   **Para parar os contêineres:**
    ```bash
    docker-compose down
    ```
-   **Para acessar o terminal do contêiner da aplicação (PHP):**
    ```bash
    docker-compose exec app bash
    ```
-   **Para ver os logs em tempo real:**
    ```bash
    docker-compose logs -f
    ```

## Detalhes da API

A API está protegida usando **Laravel Sanctum**. Para interagir com os endpoints protegidos, é necessário primeiro obter um token de autenticação através do endpoint de login e incluí-lo no cabeçalho `Authorization: Bearer <token>` de cada requisição.

**Endpoint de Login:**
-   `POST /api/login`

**Perfis de Usuário:**
-   **Gestor:** Acesso total a todas as operações de CRUD e alteração de status.
-   **Funcionário:** Acesso limitado à criação e edição de alunos.