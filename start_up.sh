#!/bin/bash

php artisan optimize --force
php artisan schedule:run
