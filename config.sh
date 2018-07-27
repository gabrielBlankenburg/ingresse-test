#!/bin/bash

docker-compose up -d

docker exec -it ingresse-php-fpm composer install

docker exec -it ingresse-php-fpm php artisan migrate

docker exec -it ingresse-php-fpm php artisan passport:install

chmod 777 src/ -R

