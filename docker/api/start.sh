composer install --ignore-platform-reqs
php artisan storage:link
php artisan migrate:fresh --seed
php artisan serve --host 0.0.0.0