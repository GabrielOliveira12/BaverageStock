#!/bin/bash
docker compose down
docker compose down --remove-orphans
docker compose build
docker compose run --rm app composer install --optimize-autoloader
docker compose up -d
docker compose ps