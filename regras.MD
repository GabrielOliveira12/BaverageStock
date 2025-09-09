1. Apresentação
O objetivo deste desafio é avaliar suas habilidades no desenvolvimento de
APIs e interfaces visuais para a gestão de um estoque de bebidas.
O sistema será responsável por armazenar dois tipos de bebidas:
● Alcoólicas
● Não alcoólicas
Cada tipo de bebida deve ser armazenada em seções separadas.
As restrições do estoque são as seguintes:
● Seção de bebidas alcoólicas: capacidade de 500 litros.
● Seção de bebidas não alcoólicas: capacidade de 400 litros.
● Não é permitido misturar bebidas alcoólicas e não alcoólicas na
mesma seção.
2. Requisitos da API
A API deve ser capaz de:
1. Cadastrar e consultar as bebidas armazenadas em cada seção.
2. Consultar o volume total do estoque por tipo de bebida.
3. Verificar se há espaço disponível para armazenar uma nova bebida
em uma seção específica.
4. Registrar o histórico de entrada e saída de bebidas, incluindo:
○ Data e hora
○ Tipo de bebida
○ Volume
○ Seção
○ Responsável pela entrada

Regras de negócio
● Um tipo de bebida não pode ser armazenada em uma seção do
outro tipo.
● Não pode haver entrada ou saída de estoque sem um registro no
histórico.
● O endpoint de histórico deve permitir ordenação por data e seção,
via parâmetros.
● As respostas da API devem seguir um padrão claro e retornar
mensagens de erro adequadas.

3. Tecnologias exigidas
● Back-end: PHP
● Microframework: Flight PHP
● Banco de Dados: MySQL
● Front-end: Angular.js ou React

4. Diferenciais (não obrigatórios, mas valorizados)
✅ Conteinerização da aplicação (Docker).
✅ Implementação de boas práticas RESTful na API.
✅ Testes unitários para garantir a qualidade do código.

5. Entrega do desafio
1. O código deve ser enviado via Bitbucket ou GitLab em um
repositório privado.
2. Compartilhe o repositório com os seguintes e-mails:
○ jose.fonseca@magis5.com.br
○ wilber.silva@magis5.com.br
○ wanderson.martins@magis5.com.br

6. Feedback do candidato
No arquivo README.md, gostaríamos de saber:
1. O que achou do desafio? (grau de dificuldade, desafios encontrados,
etc.).
2. Alteraria algo no desafio? O que sugeriria para avaliar melhor suas
habilidades?