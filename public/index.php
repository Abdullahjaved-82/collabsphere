<?php

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
