Ótimo! Aqui está o arquivo `descritivo.md` completo, com todas as tags de Markdown para que você possa copiar e colar diretamente.

---

# Eidos: Seu Sistema de Aprendizado Personalizado

## Visão Geral do Projeto

O **Eidos** é um sistema de aprendizado pessoal que utiliza o método de repetição espaçada para ajudar o usuário a memorizar conteúdo de forma eficaz. Inspirado na dinâmica de aplicativos como o Duolingo, o Eidos foca em avaliações de múltipla escolha para reforçar o conhecimento.

A principal funcionalidade do sistema é permitir que o usuário importe conteúdo e, em seguida, faça quizzes personalizados com base nesses dados.

## Funcionalidades Principais (Fase 1)

### 1. Importação de Conteúdo
O usuário pode enviar um arquivo no formato CSV que contém perguntas de múltipla escolha. Este arquivo é processado pelo backend e as perguntas são salvas no banco de dados.

### 2. Avaliações de Múltipla Escolha
O usuário seleciona um tema e inicia uma avaliação. As perguntas são apresentadas de forma fluida, sem recarregamento da página, graças ao Splade.

### 3. Feedback e Animações
* O sistema fornece feedback visual instantâneo para cada resposta (correta ou incorreta), com efeitos e animações dinâmicas.
* Ao final da avaliação, o usuário recebe uma nota e um review detalhado das questões que errou.
* Animações de comemoração são exibidas para notas altas, criando uma experiência de usuário mais gratificante.

## Arquitetura do Backend

O sistema é construído com **Laravel** e segue uma arquitetura limpa, dividida em camadas para garantir escalabilidade e fácil manutenção.

* **Controllers:** Responsáveis por gerenciar as requisições HTTP e o fluxo do aplicativo.
    * `HomeController`: Página inicial e dashboard do usuário.
    * `TopicController`: Listagem de temas disponíveis.
    * `QuizController`: Lógica principal dos quizzes, desde a importação até a exibição dos resultados.

* **Services:** Contêm a lógica de negócio principal, coordenando ações complexas.
    * `QuizService`: Lida com a importação de CSV e, no futuro, com o cálculo de notas.
    * `TopicService`: Gerencia as operações de temas.
    * `UserAnswerService`: Salva e gerencia as respostas dos usuários.

* **Repositories:** Abstraem a camada de banco de dados, lidando com operações de persistência.
    * `QuestionRepository`: CRUD para perguntas.
    * `TopicRepository`: CRUD para temas.
    * `UserAnswerRepository`: CRUD para as respostas dos usuários.

## Tecnologias Utilizadas

* **Backend:** PHP 8.2 com **Laravel 12.x**.
* **Frontend:** **Splade** (com **Vue.js**) e **Tailwind CSS**.
* **Banco de Dados:** **MySQL 8.0**.
* **Ambiente de Desenvolvimento:** **Docker** (com `docker compose`).
