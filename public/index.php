<?php

if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || str_contains(__DIR__, 'var/task')) {
    $_ENV['APP_SERVICES_CACHE'] = '/tmp/cache/services.php';
    $_ENV['APP_PACKAGES_CACHE'] = '/tmp/cache/packages.php';
    $_ENV['APP_CONFIG_CACHE'] = '/tmp/cache/config.php';
    $_ENV['APP_ROUTES_CACHE'] = '/tmp/cache/routes-v7.php';
    $_ENV['APP_EVENTS_CACHE'] = '/tmp/cache/events.php';
    putenv('APP_SERVICES_CACHE=/tmp/cache/services.php');
    putenv('APP_PACKAGES_CACHE=/tmp/cache/packages.php');
    putenv('APP_CONFIG_CACHE=/tmp/cache/config.php');
    putenv('APP_ROUTES_CACHE=/tmp/cache/routes-v7.php');
    putenv('APP_EVENTS_CACHE=/tmp/cache/events.php');
    $_SERVER['APP_SERVICES_CACHE'] = '/tmp/cache/services.php';
    $_SERVER['APP_PACKAGES_CACHE'] = '/tmp/cache/packages.php';
    $_SERVER['APP_CONFIG_CACHE'] = '/tmp/cache/config.php';
    $_SERVER['APP_ROUTES_CACHE'] = '/tmp/cache/routes-v7.php';
    $_SERVER['APP_EVENTS_CACHE'] = '/tmp/cache/events.php';

    if (!is_dir('/tmp/cache')) {
        mkdir('/tmp/cache', 0755, true);
    }
}

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

if (str_contains(__DIR__, 'var/task')) {
    // On Vercel, manually handle the request in a try-catch to print the raw, original exception
    try {
        $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
        $request = Request::capture();
        $response = $kernel->handle($request);
        $response->send();
        $kernel->terminate($request, $response);
    } catch (\Throwable $e) {
        echo "<h1>Original Laravel Boot Error (Vercel)</h1>";
        echo "<h3>" . htmlspecialchars($e->getMessage()) . "</h3>";
        echo "<p>File: " . htmlspecialchars($e->getFile()) . " on line " . $e->getLine() . "</p>";
        echo "<h4>Stack Trace:</h4>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
} else {
    // Locally, run the standard request lifecycle
    $app->handleRequest(Request::capture());
}
