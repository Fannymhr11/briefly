<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Entry point alternatif untuk XAMPP (http://localhost/briefly).
if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/vendor/autoload.php';

(require_once __DIR__.'/bootstrap/app.php')
    ->handleRequest(Request::capture());
