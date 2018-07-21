#!/bin/bash

docker-compose up -d

docker exec -it ingresse-php-fpm composer install

chmod 777 src/ -R

# Pega o id do container php
id=$(docker ps | grep ingresse-php-fpm | awk '{print $1}')
# Pega o ip do container php
ip=$(docker inspect $id | grep  "IPAddress" | awk '{match($0,/[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/); ip = substr($0,RSTART,RLENGTH); print ip}')
# Adiciona o ip certo para o env do laravel
sed -i "/DB_HOST/s/172.19.0.2/$ip/g" src/.env

docker exec -it ingresse-php-fpm php artisan migrate

chmod 777 src/ -R

