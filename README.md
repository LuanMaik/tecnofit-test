# Rank Users API 

---
### Estrutura de pastas:
    .
    ├── public                  # Servida pelo webserver
    ├── src
    │   ├── Domain              # Entidades, UseCases and Interface dos repositórios
    │   ├── Infrastructure      # Database e implementações de respositório
    │   └── WebAPI              # Definições de rotas da API
    └── test                    # Test files
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

---


## Consumir a API

Por padrão, a API estará disponível no endereço http://localhost:8080 

### PADRÃO:
Recursos não existentes resultarão em resposta com `statusCode` 404 e mensagem no padrão:

```json
{
  "error": "Not found"
}
```


### ROTAS:
<h3>Rank de Usuários por Movimento: 
<span style="font-weight: normal">[GET] [/movements/{id}/rank](http://localhost:8080/movements/1/rank) </span>
</h3> 


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

