#!/bin/bash

echo "Carregando variáveis de ambiente..."
source .env

echo "Executando testes dentro do container..."
docker exec -e DB_HOST=db \
           -e DB_PORT=3306 \
           -e DB_DATABASE=stock \
           -e DB_USERNAME=admin \
           -e DB_PASSWORD=admin \
           -e DB_ROOT_PASSWORD=root123 \
           -it stock_app ./vendor/bin/phpunit
