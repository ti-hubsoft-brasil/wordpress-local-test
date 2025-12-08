# Guia: Como Rodar o WordPress com Docker

Este guia explica como iniciar o ambiente de desenvolvimento local com WordPress e banco de dados MySQL utilizando Docker Compose.

## Pré-requisitos

*   Docker instalado
*   Docker Compose instalado (geralmente incluído no Docker Desktop ou versões recentes do Docker)

## Passos para Iniciar

1.  **Abra o terminal** na pasta onde está este arquivo (`docker-compose.yml` e `README.md`).

2.  **Execute o comando** para subir os containers em segundo plano (modo detached):

    ```bash
    docker compose up -d
    ```

    *Se você estiver usando uma versão mais antiga do Docker, talvez precise usar `docker-compose up -d`.*

3.  **Aguarde o download** das imagens e a inicialização dos serviços.

4.  **Acesse o WordPress** no seu navegador:

    [http://localhost:8000](http://localhost:8000)

## Gerenciando o Ambiente

*   **Verificar status dos containers:**
    ```bash
    docker compose ps
    ```

*   **Parar os serviços:**
    ```bash
    docker compose stop
    ```

*   **Parar e remover os containers (derrubar tudo):**
    ```bash
    docker compose down
    ```
    *Nota: Os dados do banco de dados e arquivos do WordPress são persistidos nos volumes definidos no `docker-compose.yml`, então eles não serão perdidos ao rodar o `down`, a menos que você delete os volumes especificamente.*
