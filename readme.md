# API Produtos

## Tecnologias:
- PHP ^8.4
- Laravel ^12.0
- Scramble (Docs API)

### Banco de Dados
- MySQL 8.0

## Configurando o projeto
O Projeto usa Docker para "containizar" o projeto com Nginx como servidor web.
Para instalar siga os seguintes passos:
- Acesse a pasta do projeto da api ```cd api```
    - Se quiser customizar alguma variável do ```.env```, execute ```cp .env.example .env``` e faça as customizações, se não, ignore esse passo (O .env já é criado automaticamente).
    - Execute ```docker-compose up --build -d``` para subir o container
    - As migrations já estão sendo executadas automaticamente, mas você pode executar ```docker-compose exec php artisan migrate```
    - Execute os testes ```docker-compose exec php artisan test```
    - Execute ```docker-compose exec php artisan db:seed``` para criar dados de seed


## Credenciais de Teste (Necessário ter executado as seeds)
- admin:
    - email: admin@teste.com
    - senha: senha123
- cliente:
    - email: cliente@teste.com
    - senha: senha123

## Documentação da API
- Para ver a documentação da api, acesse: http://localhost:8000/docs/api
    - Ou exporte o json na url acima e importe no Postman/Insomnia

## Banco de Dados

![Modelagem ER](https://github.com/matheussilva2/API-Produtos/blob/master/assets/modelagem_conceitual.png?raw=true)

O diagrama ER está disponível está disponível no arquivo ```modelagem_conceitual.brM3```

### Documentação da API
Para a documentação da API foi utilizado a lib Scramble

### Testando API pelo Scramble

Ao lado esquerdo da tela, você verá a coleção onde ficam todas as requisições.
Expanda cada item e veja quais requisições podem ser feitas.
![docs/collection.png](https://github.com/matheussilva2/API-Produtos/blob/master/assets/collection.png?raw=true)

Faça o login e associe o token no campo conforme a imagem abaixo. Quando o token estiver associado, ele ficará em todas as páginas automaticamente (o mesmo se aplica quando o token for alterado).

e-mail: admin@teste.com / cliente@teste.com

senha: senha123

![docs/auth.png](https://github.com/matheussilva2/API-Produtos/blob/master/assets/auth.png?raw=true)

Na tela da requisição você verá o token junto do corpo da requisição (item 1). O token será preenchido automaticamente quando feito o login na seção auth.login.

No item 2 você verá o CURL da requisição e abaixo o exemplo de resposta. Quando a requisição for feita, a resposta aparecerá embaixo do CURL.

As regras dos parâmetros da requisição pode ser encontrada no Body (item 3).

![docs/collection.png](https://github.com/matheussilva2/API-Produtos/blob/master/assets/sending_requests.png?raw=true)
