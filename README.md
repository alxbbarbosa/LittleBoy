# LittleBoy - MVC
------------------------------------------------------------------------------

## Uma breve descrição do projeto

LittleBoy foi criado para ser base de artigos para meu blog e até um treinamento que
está sendo desenvolvido. Ele foi testado mas não está homologado  para ambientes  de
produção. É um brinquedo por enquanto, garotinho que está engatinhando!

Muitas ideias foram inspiradas do Laravel, meu Framework favorito.

O Little Boy um framework MVC com muitos recursos, que permite a criação de diversos aplicativos web
Todos os recursos destacados mais abaixo são implementação própria.   É  possível  criar
aplicativos respeitando o conceito MCV e DDD. É uma versão bem minimalista, praticamente
um brinquedo. Não foi desenvolvido nesse momento pensado para ser usado em produção mas
é perfeitamente possível extendê-lo com novos pacotes e criar aplicativos bem robustos.

Como o projeto ainda está em estudo, ainda não há uma licença em anexo para uso. Contudo,
em breve será anexado. Além disso, se você gostou do projeto, pode até participar propondo
mudanças e alteraçoes que o enriqueça. Mas será importante manter os crédito. No entanto,
poderá adionar créditos de atualização.

Está produzido com uma aplicação modelo que usará a camada Modelo para Persistencia de dados.
Existe uma validação sendo executada. A aplicação modelo é bem simplificada, mas apresenta a
ideia. Você pode usá-lo para estudar também, pois as classes estão bem simplificadas. 

## Executando a aplicação exemplo

Para utilizá-lo, você precisará criar a base de dados e rodar o script sql (Para MySQL) que está
na pasta exemplo_tabelas. Depois configure o banco de dados fazendo uma cópia do arquivo que
está na pasta config para database.ini. Ajusto-o conforme as configurações do seu ambiente.


## Framework MVC com muitos recursos: 

Sistema de Roteamento, Cache, QueryBuilder, Active Record, Validations, Flash messages, etc

Possui o fluxo normal todo POO:

´´´

Request -> Routing -> Dispach -> Controller -> Response

´´´

## Conheça o Little Boy:

Assim como os frameworks atuais em produção:
Há uma classe para gerenciar as Requests que pode ser filtrada (Algo para o futuro)
Antes de chegar, todo processo inicia no roteamento. Quando iniciada, a aplicação coleta
as rotas definidas no arquivo routes.php no diretório route. Estas rotas são adionadas na
RouterCollection que é uma classe para armazear e auxiliar o Router a resolver as rotas.
Uma vez resolvida uma rota, sera enviada para despacho, processo em que o Router usará
um método despachante para invocar o controller e passar o comando para ele.
O controller executará a ação definida em sua action invocada e deverá prover um retorno.
O retorno é encapsulado em uma Response que poderá ser qualquer tipo de resposta e seus
cabeçalhos. Este framework está baseado no MVC.


Alexandre Bezerra Barbosa - 2019 # LittleBoy
------------------------------------------------------------------------------
