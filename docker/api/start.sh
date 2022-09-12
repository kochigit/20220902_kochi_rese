composer install --ignore-platform-reqs
php artisan cache:clear
php artisan config:clear
php artisan config:cache
php artisan storage:link
php artisan migrate:fresh --seed
php artisan serve --host 0.0.0.0