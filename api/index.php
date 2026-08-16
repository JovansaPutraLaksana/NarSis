<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

foreach ([
    'VIEW_COMPILED_PATH' => '/tmp/narsis/views',
    'APP_SERVICES_CACHE' => '/tmp/narsis/services.php',
    'APP_PACKAGES_CACHE' => '/tmp/narsis/packages.php',
    'APP_CONFIG_CACHE' => '/tmp/narsis/config.php',
    'APP_ROUTES_CACHE' => '/tmp/narsis/routes.php',
    'APP_EVENTS_CACHE' => '/tmp/narsis/events.php',
] as $key => $value) {
    if (! getenv($key)) {
        putenv($key.'='.$value);
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

@mkdir('/tmp/narsis/views', 0777, true);

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->handleRequest(Request::capture());
