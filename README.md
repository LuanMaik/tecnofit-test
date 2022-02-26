# Rank Users API
![CI-Status](https://github.com/LuanMaik/tecnofit-test/actions/workflows/docker-image.yml/badge.svg)
![Docker](https://img.shields.io/badge/docker-%230db7ed.svg?style=flat&logo=docker&logoColor=white)&nbsp;
![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=flat&logo=php&logoColor=white)&nbsp;
![MySQL](https://img.shields.io/badge/mysql-%2300f.svg?style=flat&logo=mysql&logoColor=white)&nbsp;

- PHP 8.1
- MySQL 8
- PHPUnit
- XDebug
- Docker / Docker Compose

---
### Estrutura de pastas:
    .
    ├── public                    # Servida pelo webserver
    ├── src
    │   ├── Domain                # Entidades, UseCases, DTOs e Interfaces dos repositórios
    │   ├── Infrastructure        # Conexão com banco de dados e implementações de respositórios
    │   └── WebAPI                # Definições de rotas da API
    └── test                      # Test files
        ├── test-coverage-report  # Relatório de cobertura dos testes (gerado após rodar os testes)
        └── ...

---

## Como executar a aplicação

É esperado que tenha o
[Docker](https://www.docker.com/get-started) e
[Docker Compose](https://docs.docker.com/compose/install/)
instalado no computador.

No terminal, execute os comandos:
- `docker-compose up -d` para subir a api e o banco de dados.
- `docker-compose exec api composer install` para instalar as dependências da aplicação.

#### Obs: Aguarde alguns segundos até que o banco de dados esteja disponível. 

---

## Consumir a API

Por padrão, a API estará disponível no endereço `http://localhost:8080`. 

Todas as respostas utilizam o formato json: `Content-Type: application/json` 

Em caso de erros com exceptions não tratadas, caso a aplicação estiver com a 
variável de ambiente `MODE=PROD`, a API irá responder com `status code` 500 e a mensagem padrão:
```json
{
  "error": "Server error"
}
```

Recursos não existentes resultarão em resposta com `status code` 404 e mensagem no padrão:

```json
{
  "error": "Not found"
}
```


### ROTAS:
### Rank de Usuários por Movimento: `[GET] /movements/{id}/rank`


**Descrição:** Obtém o rank de usuários em um determinado movimento. 


Esta rota suporta paginação dos dados através dos parâmetros `page` (padrão 1) e `pageSize` (padrão 10):

Exemplo paginação: http://localhost:8080/movements/1/rank?page=1&pageSize=10

Exemplo resposta:

```json
{
    "movement": {
        "id": 1,
        "name": "Deadlift"
    },
    "rank": [
        {
            "id": 2,
            "name": "Jose",
            "date": "2021-01-04 00:00:00",
            "record": 190,
            "rank": 1
        },
        {
            "id": 1,
            "name": "Joao",
            "date": "2021-01-01 00:00:00",
            "record": 180,
            "rank": 2
        },
        {
            "id": 3,
            "name": "Paulo",
            "date": "2021-01-01 00:00:00",
            "record": 170,
            "rank": 3
        }
    ],
    "currentPage": 1,
    "pageSize": 10,
    "nextPage": null
}
```

---

## Variáveis de ambiente
`MODE` define o modo de execução da aplicação, sendo os valores possíveis: `DEV`, `PROD` ou `TEST`.

`DB_HOST` define o host de conexão com o banco de dados.

`DB_USER` define o nome do usuário da conexão com o banco de dados.

`DB_PASS` define a senha do usuário da conexão com o banco de dados.

`DB_NAME` define o nome do schema da conexão com o banco de dados.

---

## Como executar os testes

De modo a facilitar e garantir que haja as dependências necessárias, é indicado que os testes sejam executados via docker.

No terminal, execute o comando `docker-compose exec api composer test`

A execução do comando acima também irá gerar o relatório de cobertura de teste na pasta `tests/test-coverage-report`.

---


# CONSIDERAÇÕES

## Arquitetura do projeto:
Eu optei por não utilizar um framework, pois entendo que desta forma eu consigo demonstrar que conheço os princípios necessário para a criação de uma aplicação. 

Para a criação da API, eu utilizei 2 bibliotecas como base:
- [league/route](https://route.thephpleague.com/): responsável por fazer o roteamento das requisições.
- [league/container](https://container.thephpleague.com/): container de dependências para conseguir realizar inversão de dependências de forma limpa

Com o uso dessas duas bibliotecas foi possível montar um Micro Framework próprio, acreditando que assim eu poderia demonstrar que o meu conhecimento não se limita a apenas o uso de frameworks populares.

A estrutura das pastas foi baseado em estruturas populares quando aplicado Clean Architecture.

## Dificuldades encontradas
Embora eu tenha criado vários testes _end-to-end_, usando Guzzle para realizar requisições HTTP para endpoins da API, 
eu não consegui adicionar no relatório de cobertura de testes, sendo possível apenas para os testes unitário e de integração,
pois o XDebug aparenta não ser capaz de realizar a análise de cobertura quando se trata de requisições externas. 

Nos frameworks populares como o Laravel e Symfony é possível realizar essa cobertura de testes através dos testes de endpoint da API, 
porém por debaixo dos panos não é realizado uma requisição http de verdade, utilizando meios mais avançados do framework para identificar
a cobertura de código testado.

## Resolução do problema principal:
A resolução é baseada nas funções [`Rank`](https://dev.mysql.com/doc/refman/8.0/en/window-function-descriptions.html), disponíveis a partir do Mysql 8.

Eu decidi utilizar essa funcionalidade do Mysql 8 por eu entender como sendo a melhor escolha, pois desta forma é possível criar 
paginação da lista do rank, e se necessário, pode-se obter diretamente a posição de um usuário no rank.

Um ponto negativo nesta forma de resolução, é que o banco de dados precisa realizar uma consulta mais complexa, porém pode ser facilmente resolvido através de cache.

## Outras formas de resolução:
- (Mais simples e limitado) Realizar um pesquisa simples no banco de dados ordenando por recorde e fazer a análise dos dados no PHP, 
percorrendo o array de dados retornados pelo banco e realizando a ordenação e definição da posição no rank. 
O problema desta resolução é que em um cenário real, a massa de dados pode ficar muito grande e criar problemas de memória e processamento, 
outro problema seria a impossibilidade de fazer paginação, pois seria complexo a retomada da posição do rank nas próximas páginas solicitadas.
- (Mais complexo e performático) Criar uma rotina de processamento periódica (ex: madrugada) para realizar a análise dos dados dos recordes
e gerar o rank completo em uma nova tabela do banco.
