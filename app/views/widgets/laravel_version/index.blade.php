<?php
$current_version = exec('cd .. && php artisan --version');
$current_version = str_replace('Laravel Framework version ', '', $current_version);
?>

Laravel Framework Version : <span class="badge">{{ $current_version }}</span> of {{ HTML::image('https://poser.pugx.org/laravel/framework/v/stable.svg') }}
