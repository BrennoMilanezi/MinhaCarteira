# MinhaCarteira

API REST desenvolvida para realização de transações entre usuários

## Tecnologias utilizadas

As seguintes tecnologias foram usadas na construção do projeto:

- [PHP](https://www.php.net/)
- [MySQL](https://www.mysql.com/)
- [Apache](https://www.apache.org/)
- [JWT](https://jwt.io/)

## Ferramentas utilizadas

As seguintes ferramentas foram usadas na construção do projeto:

- [Visual Studio Code](https://code.visualstudio.com/)
- [Postman](https://www.postman.com/)
- [XAMPP](https://www.apachefriends.org/pt_br/index.html)
- [Navicat](https://www.navicat.com/en/)

## Documentação API

Recursos disponíveis para acesso via API:
* [**Auth**] (/auth)
* [**Users**] (/users)
* [**Transactions**] (/transactions)

## Métodos
Requisições para a API devem seguir os padrões:
| Método | Descrição |
|---|---|
| `GET` | Retorna informações de um ou mais registros. |
| `POST` | Utilizado para criar um novo registro. |

## Respostas

| Código | Descrição |
|---|---|
| `200` | Requisição executada com sucesso (success).|
| `401` | Dados de acesso inválidos.|
| `404` | Registro pesquisado não encontrado (Not found).|

# Group Recursos

# Auth [/auth]

Para receber o token de acesso.

### [GET]

+ Request (application/json)

    + Body

            {
                "email": "milanezibrenno@gmail.comm",
                "senha": "123456"
            }

+ Response 200 (application/json)

            {
                "status": "success",
                "data": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6Im1pbGFuZXppYnJlbm5vQGdtYWlsLmNvbW0ifQ.XspoviWBdYuRHa9EqproTUG1GGPymAgcLo2hmUUSLCA"
            }
            
+ Response 401 (application/json)

            {
                "status": "error",
                "message": "Usuário(a) sem acesso"
            }


# Users [/users]

Os usuários podem ser comuns e lojistas.


### Listar (List) [GET]

+ Request (application/json)

    + Headers

            Authorization: Bearer [access_token]

+ Response 200 (application/json)

          {
              "status": "success",
              "data": [
                  {
                      "id": "1",
                      "nome": "Brenno Milanezi",
                      "num_inscricao": "11976550777",
                      "email": "milanezibrenno@gmail.comm",
                      "tipo": "Comum",
                      "valor_carteira": "7.97"
                  },
                  {
                      "id": "2",
                      "nome": "Teste",
                      "num_inscricao": "1197655077700",
                      "email": "teste@gmail.com",
                      "tipo": "Lojista",
                      "valor_carteira": "22.03"
                  }
              ]
          }

+ Response 401 (application/json)

          {
              "status": "error",
              "message": "Não autenticado"
          }

### Novo (Create) [POST]

+ Attributes (object)

    + nome: nome do contato (string, required)
    + num_inscricao: CPF/CNPJ (string,, apenas numeros, required)
    + email: (string, required)
    + senha: (string, required)
    + tipo: 1 - Comum, 2 - Lojista (number 1 or 2, required)

+ Request (application/json)

    + Body

            {
                "nome": "Brenno Milanezi",
                "num_inscricao": "11976550777",
                "email": "milanezibrenno@gmail.com",
                "senha": "123456",
                "tipo": 1
            }

+ Response 200 (application/json)

            {
                "status": "success",
                "data": "Usuário(a) inserido com sucesso"
            }
            
+ Response 401 (application/json)
  Quando o usuário já está cadastrado.

            {
                "status": "error",
                "message": "E-mail ou CPF/CNPJ do usuário(a) já cadastrado"
            }

### Detalhar (Read) [GET /users/{user_id}]

+ Parameters
    + codigo (required, number) ... ID do usuário

+ Request (application/json)

    + Headers

            Authorization: Bearer [access_token]

+ Response 200 (application/json)

            {
                "status": "success",
                "data": {
                    "id": "1",
                    "nome": "Brenno Milanezi",
                    "num_inscricao": "11976550777",
                    "email": "milanezibrenno@gmail.comm",
                    "tipo": "Comum",
                    "valor_carteira": "7.97"
                }
            }

+ Response 401 (application/json)
  Quando registro não for encontrado.

            {
                "status": "error",
                "message": "Nenhum usuário encontrado"
            }

# Transactions [/transactions]

Usuários podem enviar dinheiro (efetuar transferência) para lojistas e entre usuários.
Lojistas só recebem transferências, não enviam dinheiro para ninguém.

### Listar (List) [GET]

+ Request (application/json)

    + Headers

            Authorization: Bearer [access_token]

+ Response 200 (application/json)

         {
            "status": "success",
            "data": [
                {
                    "id": "1",
                    "id_pagador": "1",
                    "id_beneficiario": "2",
                    "valor": "12.03",
                    "data": "2021-04-07 12:01:47"
                }
            ]
          }

+ Response 401 (application/json)

          {
              "status": "error",
              "message": "Não autenticado"
          }

### Novo (Create) [POST]

+ Attributes (object)

    + valor: valor da transação (float, required)
    + id_pagador: id do usuário comum pagador (number, required)
    + id_beneficiario: id do usuário comum/lojista beneficiário (number, required)

+ Request (application/json)

    + Body

            {
                "valor": 12.03,
                "id_pagador": 1,
                "id_beneficiario": 2
            }

+ Response 200 (application/json)

            {
                "status": "success",
                "data": "Transação realizada"
            }
            
+ Response 401 (application/json)
  Quando o pagador e beneficiário são iguais

            {
                "status": "error",
                "message": "Pagador e beneficiário iguais"
            }
            
  Quando o pagador não tem saldo suficiente e/ou o pagador é um lojista

            {
                "status": "error",
                "message": "Transação não autorizada"
            }

Quando o beneficiário não é encontrado

            {
                "status": "error",
                "message": "Beneficiário não encontrado"
            }

### Detalhar (Read) [GET /transactions/{transaction_id}]

+ Parameters
    + codigo (required, number) ... ID da transação

+ Request (application/json)

    + Headers

            Authorization: Bearer [access_token]

+ Response 200 (application/json)

            {
                "status": "success",
                "data": {
                    "id": "1",
                    "id_pagador": "1",
                    "id_beneficiario": "2",
                    "valor": "12.03",
                    "data": "2021-04-07 12:01:47"
                }
            }

+ Response 401 (application/json)
  Quando registro não for encontrado.

            {
                "status": "error",
                "message": "Nenhuma transação encontrada"
            }
