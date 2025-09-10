#!/bin/bash
source .env
docker compose down
docker compose down --remove-orphans
docker compose build
docker compose run --rm app composer install --optimize-autoloader
docker compose up -d
sleep 10
docker exec -i stock_db mysql -u root -p${DB_ROOT_PASSWORD} ${DB_DATABASE} < mysql/init.sql
docker compose ps