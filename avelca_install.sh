php artisan key:generate
php artisan modules:migrate avelca_user --force
php artisan modules:migrate avelca_module --force
php artisan modules:migrate avelca_setting --force
php artisan modules:seed avelca_user
php artisan modules:seed avelca_module
php artisan modules:seed avelca_setting
php artisan migrate --force
php artisan db:seed --force
