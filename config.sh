#!/bin/bash

docker-compose up -d

docker ps

docker exec -it ingresse-php-fpm composer install

docker exec -it ingresse-php-fpm php artisan migrate

chmod 777 src/ -R
