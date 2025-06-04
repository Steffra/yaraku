#!/bin/bash

composer install
for i in $(seq 1 30); do
  echo -n "." && sleep 1
done
php artisan migrate:fresh --seed
chown -R www-data:www-data storage