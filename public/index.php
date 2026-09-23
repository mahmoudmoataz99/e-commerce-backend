<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// forward server requests to underlying router if using PHP built-in server
if ($_SERVER['PHP_SELF'] !== '/index.php') {
    return false;
}

require __DIR__ . '/../vendor/autoload.php';

(require_once __DIR__ . '/../bootstrap/app.php')
    ->handleRequest(Request::capture());
