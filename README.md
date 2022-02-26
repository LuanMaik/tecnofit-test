# Rank Users API 
![Docker](https://img.shields.io/badge/docker-%230db7ed.svg?style=flat&logo=docker&logoColor=white)&nbsp;
![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=flat&logo=php&logoColor=white)&nbsp;
![MySQL](https://img.shields.io/badge/mysql-%2300f.svg?style=flat&logo=mysql&logoColor=white)&nbsp;

---
### Estrutura de pastas:
    .
    ├── public                    # Servida pelo webserver
    ├── src
    │   ├── Domain                # Entidades, UseCases and Interface dos repositórios
    │   ├── Infrastructure        # Database e implementações de respositório
    │   └── WebAPI                # Definições de rotas da API
    └── test                      # Test files
        ├── test-coverage-report  # Relatório de cobertura dos testes
        └── ...

---

## Como executar a aplicação

É esperado que tenha o
[Docker](https://www.docker.com/get-started) e
[Docker Compose](https://docs.docker.com/compose/install/)
instalado no computador.

No terminal, execute o comando `docker-compose up -d`

---

## Como executar os testes

De modo a facilitar e garantir que haja as dependências necessárias, é indicado que os testes sejam executados via docker.

No terminal, execute o comando `docker-compose exec api composer test`

A execução do comando acima também irá gerar o relatório de cobertura de teste na pasta `tests/test-coverage-report`.

---


## Consumir a API

Por padrão, a API estará disponível no endereço `http://localhost:8080` 

Todas as respostas utilizam o `Content-Type: application/json` 

### PADRÃO:
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

# CONSIDERAÇÕES

## Arquitetura do projeto:
Eu não optei por utilizar nenhum framework, pois entendo que desta forma eu consigo demonstrar que conheço os princípios necessário para a criação de uma aplicação. Eu utilizei uma biblioteca de rota e outra de container de dependências para montar um Micro Framework, acreditando que assim eu poderia demonstrar que meu conhecimento não se limita a apenas frameworks famosos.

## Dificuldades encontradas
Embora eu tenha criado vários testes _end-to-end_, eu não consegui adicioná-los no relatório de cobertura de testes, sendo possível apenas para os testes unitário e de integração. 
Nos grandes frameworks como o Laravel e Symfony é possível realizar essa cobertura de testes através dos testes de endpoint da API, porém por debaixo dos panos não é realizado uma requisição http de verdade, assim como também ocorre em testes end-to-end com `supertest` em nodeJs.

## Resolução do problema principal:
A resolução é baseada nas funções `Rank`, disponível a partir do Mysql 8.

Eu decidi utilizar essa funcionalidade do Mysql 8 por eu entender como sendo a melhor escolha, uma vez que desta forma é possível criar paginação da lista do rank.

Um ponto negativo nesta forma de resolução, é que cria-se uma carga extra sobre o banco de dados, porém pode ser facilmente resolvido através de cache.

## Outras formas de resolução:
- (Mais simples e limitado) Realizar um pesquisa simples no banco de dados e fazer a analise dos dados no PHP, percorrendo o array de dados retornados pelo banco e realizando a ordenação e definição da posição no rank. O problema desta resolução é que em um cenário real, a massa de dados pode ficar muito grande e criar problemas de memória e processamento, outro problema seria a a falta possibilidade de paginação, uma vez que seria complexo a retomada da posição do rank nas próximas páginas solicitadas.
- (Mais complexa) Criar uma rotina de processamento periódica (ex: madrugada) para realizar a análise dos dados dos recordes e gerar o rank completo.
