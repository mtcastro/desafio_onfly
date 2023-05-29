
# API de Gerenciamento de Despesas

Esta API permite gerenciar despesas de usuários, permitindo criar, visualizar, atualizar e excluir despesas. Além disso, também é possível obter informações do usuário autenticado.

## Executando o Servidor

Para executar o servidor pela primeira vez, siga os passos abaixo:

### Pré-requisitos

Certifique-se de ter os seguintes pré-requisitos instalados em seu sistema:

- [PHP](https://php.net) (versão 7.4 ou superior)
- [Composer](https://getcomposer.org)
- [Laravel CLI](https://laravel.com/docs/8.x/installation#installing-laravel)
- Banco de dados (por exemplo, MySQL, PostgreSQL, SQLite)
- Servidor de e-mail (por exemplo, SMTP, Sendmail)

### Passo 1: Clonando o Repositório

Clone o repositório do projeto em seu ambiente local. Abra o terminal e execute o seguinte comando:

```
git clone https://github.com/seu-usuario/seu-projeto.git
```

### Passo 2: Instalando as Dependências

Acesse o diretório do projeto clonado e instale as dependências do Composer executando o seguinte comando no terminal:

```
cd seu-projeto
composer install
```

### Passo 3: Configurando o Arquivo de Ambiente

Faça uma cópia do arquivo `.env.example` e renomeie-o para `.env`. Em seguida, abra o arquivo `.env` e configure as informações de banco de dados, e-mail, JWT e filas. Abaixo estão as principais configurações que você precisa ajustar:

#### Configurações de Banco de Dados

Configure as seguintes variáveis para se conectar ao seu banco de dados:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome-do-banco
DB_USERNAME=usuario-do-banco
DB_PASSWORD=senha-do-banco
```

#### Configurações de E-mail

Configure as seguintes variáveis para permitir o envio de e-mails:

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@example.com
MAIL_PASSWORD=sua-senha-de-email
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=seu-email@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

#### Configurações JWT

Configure as seguintes variáveis para a autenticação JWT (JSON Web Token):

```
JWT_SECRET=chave-secreta-para-jwt
JWT_TTL=1440
```

Para gerar a chave JWT, execute o seguinte comando no terminal:

```
php artisan jwt:secret
```

Esse comando irá gerar uma chave aleatória e atualizá-la na variável JWT_SECRET no arquivo .env.

#### Configurações de Filas

Configure as seguintes variáveis para habilitar a funcionalidade de filas:

```
QUEUE_CONNECTION=database
```

### Passo 4: Gerando a Chave de Criptografia

Execute o seguinte comando para gerar uma chave de criptografia para sua aplicação Laravel:

```
php artisan key:generate
```

### Passo 5: Executando as Migrações do Banco de Dados

Execute as migrações do banco de dados para criar as tabelas necessárias. Use o seguinte comando no terminal:

```
php artisan migrate
```

### Passo 6: Iniciando o Servidor

Agora você está pronto para iniciar o servidor. Execute o seguinte comando:

```
php artisan serve
```

Abra um novo terminal e execute o comando abaixo para iniciar o processo de filas da aplicação 

```
php artisan queue:work
```

Isso iniciará o servidor de desenvolvimento e exibirá o endereço no qual você pode acessar a aplicação no seu navegador.


## Endpoints Disponíveis

### 1. Criar uma Despesa

**Endpoint**: `/api/expenses`

**Método**: `POST`

**Descrição**: Cria uma nova despesa para o usuário autenticado.

#### Parâmetros da Solicitação

| Parâmetro   | Tipo   | Descrição           |
|-------------|--------|---------------------|
| description | string | A descrição da despesa  |
| amount      | number | O valor da despesa      |
| date        | string | A data da despesa (formato: yyyy-mm-dd)|

#### Cabeçalho da Solicitação

| Parâmetro     | Valor           |
|---------------|-----------------|
| Authorization | Bearer {token}  |

#### Resposta de Sucesso

Código: 201 Created

Exemplo de resposta:

```json
{
  "id": 1,
  "description": "Restaurante",
  "amount": 50.0,
  "date": "2023-05-10"
}
```

### 2. Obter Todas as Despesas

**Endpoint**: `/api/expenses`

**Método**: `GET`

**Descrição**: Retorna todas as despesas do usuário autenticado.

#### Cabeçalho da Solicitação

| Parâmetro     | Valor           |
|---------------|-----------------|
| Authorization | Bearer {token}  |

#### Resposta de Sucesso

Código: 200 OK

Exemplo de resposta:

```json
[
  {
    "id": 1,
    "description": "Restaurante",
    "amount": 50.0,
    "date": "2023-05-10"
  },
  {
    "id": 2,
    "description": "Supermercado",
    "amount": 100.0,
    "date": "2023-05-11"
  }
]
```

### 3. Obter uma Despesa Específica

**Endpoint**: `/api/expenses/{id}`

**Método**: `GET`

**Descrição**: Retorna uma despesa específica do usuário autenticado.

#### Parâmetros da Solicitação

| Parâmetro  | Tipo   | Descrição         |
|------------|--------|-------------------|
| id         | number | O ID da despesa  |

#### Cabeçalho da Solicitação

| Parâmetro     | Valor           |
|---------------|-----------------|
| Authorization | Bearer {token}  |

#### Resposta de Sucesso

Código: 200 OK

Exemplo de resposta:

```json
{
  "id": 1,
  "description": "Restaurante",
  "amount": 50.0,
  "date": "2023-05-10"
}
```

### 4. Atualizar uma Despesa (continuação)

#### Parâmetros da Solicitação

| Parâmetro   | Tipo   | Descrição               |
|-------------|--------|-------------------------|
| description | string | A descrição da despesa  |
| amount      | number | O valor da despesa      |
| date        | string | A data da despesa (formato: yyyy-mm-dd)|

#### Cabeçalho da Solicitação

| Parâmetro     | Valor           |
|---------------|-----------------|
| Authorization | Bearer {token}  |

#### Resposta de Sucesso

Código: 200 OK

Exemplo de resposta:

```json
{
  "id": 3,
  "description": "Cinema",
  "amount": 30.0,
  "date": "2023-05-11"
}
```


### 5. Excluir uma Despesa

**Endpoint**: `/api/expenses/{id}`
**Método**: `DELETE`
**Descrição**: Exclui uma despesa do usuário autenticado.

#### Cabeçalho da Solicitação

| Parâmetro     | Valor           |
|---------------|-----------------|
| Authorization | Bearer {token}  |

#### Resposta de Sucesso

Código: 204 No Content

### 6. Obter um Usuário

**Endpoint**: `/api/user`
**Método**: `GET`
**Descrição**: Retorna as informações do usuário autenticado.

#### Cabeçalho da Solicitação

| Parâmetro     | Valor           |
|---------------|-----------------|
| Authorization | Bearer {token}  |

#### Resposta de Sucesso

Código: 200 OK

Exemplo de resposta:

```json
{
  "id": 1,
  "nome": "João Silva",
  "email": "joao@example.com"
}
```

## Erros

A API pode retornar os seguintes erros:

- Código 401 Unauthorized: Acesso não autorizado.
- Código 404 Not Found: Recurso não encontrado.
- Código 422 Unprocessable Entity: Erro de validação dos dados enviados na solicitação.
- Código 500 Internal Server Error: Erro interno do servidor.

## Autenticação

A autenticação na API é feita por meio do uso de tokens JWT (JSON Web Tokens). Os endpoints que exigem autenticação devem incluir o cabeçalho `Authorization` com o valor `Bearer {token}`.

## Considerações Finais

Esta documentação fornece uma visão geral dos recursos disponíveis na API de Gerenciamento de Despesas. Certifique-se de enviar solicitações corretas, fornecendo todos os parâmetros necessários e respeitando a autenticação. Para mais detalhes, consulte a implementação do projeto.

Caso tenha alguma dúvida ou precise de suporte, entre em contato com nossa equipe.

Agradecemos por utilizar a API de Gerenciamento de Despesas!
```

Lembre-se de substituir as informações com os detalhes reais da sua API.
