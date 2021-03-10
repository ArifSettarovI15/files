#!/bin/bash

ls
mkdir -p libraries
cp -r src/ libraries/Brands/

php artisan opztimize
php artisan opztimize