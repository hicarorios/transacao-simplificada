## Sobre o projeto
- Versão do php: 7.4
- Versão do Laravel 7
- Estou utilizando o arquivo .env.testing para configurações de teste, eu mantive os arquivos .env no repositório apenas por comodidade.
- O banco utilizado foi MariaDB.
- Estou rodando os testes no MariaDB, ao invés do SQLite
- O projeto utiliza filas (Queues).

## Rodar o projeto

### Execução Manual
- Baixar dependências do projeto: `composer install`
- Criar um banco de dados para aplicação e uma para testes
- Executar o migration: `php artisan migrate --seed`
- Executar o servidor embutido: `php artisan serve --port 8080`
- Executar o listener da fila: `php artisan queue:work`

*OBS: caso os testes não funcionem no banco de teste (caso a aplicação não leia os dados do .env.testing) execute os seguintes comandos:
- `php artisan config:clear` ou `php artisan config:cache --env=testing`

### Execução Docker
- execute o comando `docker-compose up -d`
- para popular o banco `docker-compose exec ts-app bash` , `php artisan db:seed`

*OBS: talvez seja necessário criar o banco de testes, no banco levantado pelo Docker

## Sobre o desenvolvimento do teste
Olá, primeiramente obrigado pela oportunidade, eu achei que aqui seria o melhor lugar para explicar alguns pontos sobre meu entendimento do projeto e algumas escolhas. Eu também tomei algumas decisões baseadas no tempo, infelizmente não tenho muito tempo depois do horário comercial, então tive que reduzir um pouco na implementação.

Eu vou resumir um pouco das motivações e escolhas, porem adoraria uma oportunidade para poder explicar melhor sobre isso.

## Escolhas, melhorias

### Framework
Atualmente utilizo mais Laravel para desenvolvimento das aplicações, ele tem uma ampla gama de ferramentas tanto para aplicações fullstack quanto para api's. Eu entendo que ele é um framework meio pesado, dependendo do cenário seria melhor utilizar um microframework.

### Git, GitFloW, branches, etc
Como eu ja tinha um cenário bem definido em mente, e não se tratava de algum muito grande, entendi que não seria necessário utilizar nada além da branch master, tomei cuidado para os commits fazerem sentido e transmitirem a ordem de construção da aplicação.

### Escolhas
Como havia dito, eu tomei algumas decisões para poupar tempo, e também entendo que esta aplicação é um teste, dentre elas estão: Utilização de uma tabela para representar lojista e usuário, UUID, Repositórios, Factories, DTO's, VO's, sistema mais sofisticado para tratar exceções, modelo de resposta seguindo Json API, documentação dos endpoints.

### Melhorias
Caso esta fosse uma aplicação do mundo real eu iria trabalhar de uma forma um pouco diferente nos pontos críticos, eu melhoraria também os seguintes pontos:

- Utilizaria o Redis/RabitMQ para cuidar da sessão, filas, cache, etc
- Criaria uma representação(DTO) da transação na aplicação, para que haja uma confiança na manipulação dos desses dados.
- Criaria uma camada de serviços para lidar melhor com as regras de negócio.

- Criaria um serviço auxiliar para lidar com as configurações e chamadas a API's externas.
- Criaria versões da API.
- Criaria Resource Collections para padronizar as respostas da API.
- Utilizaria ApiBlueprint e Apiary para automatizar a documentação dos endpoints.