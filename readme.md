# Ingresse Backend Devloper test

## Sobre

O projeto se trata de uma api RESTful responsável pelo gerenciamento de usuários

## Requisitos

Todo o projeto foi testado no Ubuntu 18.04. Além disso para conseguir rodar o projeto, é necessário algumas ferramentas:

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)
- Opcional: [Postman](https://www.getpostman.com) ou similar para fazer as requisições.

## Instalando

Para instalar o projeto, primeiro clone este repositório `git clone https://github.com/gabrielBlankenburg/ingresse-test` e então dê permissão para executar o config.sh `sudo chmod 777 config.sh` e execute o script `sudo ./config.sh`.
Após os passos acima, acessando [localhost:8080](http://localhost:8080/) você deve ver a página padrão do Laravel. <br/>
É também possível utilizar o PHPMyAdmin acessando [localhost:8000](http://localhost:8000/):
- Server: ingresse-mysql
- Username: root
- Password: 123456

## Como Funciona

Qualquer requisição api feita, é necessário colocar os seguintes headers: 
- `Content-Type: application/x-www-form-urlencoded`
- `X-Requested-With: XMLHttpRequest` 

### Admin Genérico
Você pode gerar um novo admin com as credenciais padrões fazendo uma requisição POST para [localhost:8000](http://localhost:8000/api/register-first-admin). Isso irá gerar um usuários com as credenciais: 

- E-mail: gabriel@admin.com
- Senha: 123456

### Novo Usuário simples
POST [localhost:8000/api/register](http://localhost:8000/api/register)

#### Body

- name (obrigatório): string
- last_name (obrigatório): string
- cpf (obrigatório): string, cpf válido
- rg (opcional): string, tamanho máximo é 14
- email (obrigatório): string, email válido
- birth_date (obrigatório): date
- password (obrigatório): string, tamanho mínimo é 6

Essa requisição irá gerar um novo usuário e irá retornar o token do mesmo. Este token será necessário para autenticar o usuário em requisições futuras.

### Login
POST [localhost:8000/api/login](http://localhost:8000/api/login)

#### Body

- email (obrigatório)
- password (obrigatório)

Essa requisição irá autenticar um usuário (caso as credenciais estejam certas) e retornar o token.

### Oauth2
Para fazer requisições para qualquer CRUD de usuários, é necessário além do header mencionado anteriormente o token:
- Authorization: Bearer &lt;token&gt;

### Listar Usuários
GET [localhost:8000/api/users](http://localhost:8000/api/users)

### Mostrar um usuário específico
GET [localhost:8000/api/users/{id}](http://localhost:8000/api/users/{id})

### Criar um novo usuário
POST [localhost:8000/api/users](http://localhost:8000/api/users) - Apenas usuários administradores podem cadastrar novos usuários

#### Body
- name (obrigatório): string
- last_name (obrigatório): string
- cpf (obrigatório): string, cpf válido
- rg (opcional): string, tamanho máximo é 14
- email (obrigatório): string, email válido
- birth_date (obrigatório): date
- password (obrigatório): string, tamanho mínimo é 6
- admin (opcional): boolean. Só é possível definir se o usuário é admin caso o usuário atual autenticado seja admin também.

### Atualizar um usuário
PUT [localhost:8000/api/users/{id}](http://localhost:8000/api/users/{id}) - É possível atualizar apenas o usuário atual autenticado, a menos que este seja um usuário administrador.

#### Body
- name (obrigatório): string
- last_name (obrigatório): string
- cpf (obrigatório): string, cpf válido
- rg (opcional): string, tamanho máximo é 14
- email (obrigatório): string, email válido
- birth_date (obrigatório): date
- password (obrigatório): string, tamanho mínimo é 6
- admin (opcional): boolean. Só é possível definir se o usuário é admin caso o usuário atual autenticado seja admin também.

### Deletar um usuário
DELETE [localhost:8000/api/users/{id}](http://localhost:8000/api/users/{id}) - É possível deletar apenas o usuário atual autenticado, a menos que este seja um usuário administrador.

## Estrutura
A maior parte da lógica fica em `src/app/Http`.

### Controllers
Os controllers são todos REST, e ficam em `src/app/Http/Controllers/Api/`

### Requests
As regras de validação de requisições fica em `src/app/Http/Requests/`

### Resources
Para customizar o formato de retorno de um objeto do Eloquent, é utilizado `src/app/Http/Resources/`

### Repositories
Algumas vezes para isolar o código do controller é possível utilizar repositórios: `src/app/Repositories/`

### Rotas
As rotas são definidas em `src/routes/api.php`

### Testando
Os testes ficam em `src/tests/`, execute-os utilizando `docker exec -it ingresse-php-fpm vendor/bin/phpunit`.
Após executar testes, é gerado o code coverage em `src/report/index.html`

## Pode ser útil
- [Docker](https://www.docker.com/)
- [Laravel](https://laravel.com/)
- [MySQL](https://www.mysql.com/)
- [NGINX](https://www.nginx.com/)
- [PHPMyAdmin](https://www.phpmyadmin.net/)