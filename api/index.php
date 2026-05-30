<?php

if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || str_contains(__DIR__, 'var/task')) {
    $_ENV['APP_SERVICES_CACHE'] = '/tmp/cache/services.php';
    $_ENV['APP_PACKAGES_CACHE'] = '/tmp/cache/packages.php';
    $_ENV['APP_CONFIG_CACHE'] = '/tmp/cache/config.php';
    $_ENV['APP_ROUTES_CACHE'] = '/tmp/cache/routes-v7.php';
    $_ENV['APP_EVENTS_CACHE'] = '/tmp/cache/events.php';
    
    // Serverless-compatible drivers
    $_ENV['CACHE_STORE'] = 'file';
    $_ENV['SESSION_DRIVER'] = 'cookie';
    $_ENV['QUEUE_CONNECTION'] = 'sync';

    putenv('APP_SERVICES_CACHE=/tmp/cache/services.php');
    putenv('APP_PACKAGES_CACHE=/tmp/cache/packages.php');
    putenv('APP_CONFIG_CACHE=/tmp/cache/config.php');
    putenv('APP_ROUTES_CACHE=/tmp/cache/routes-v7.php');
    putenv('APP_EVENTS_CACHE=/tmp/cache/events.php');
    
    putenv('CACHE_STORE=file');
    putenv('SESSION_DRIVER=cookie');
    putenv('QUEUE_CONNECTION=sync');

    $_SERVER['APP_SERVICES_CACHE'] = '/tmp/cache/services.php';
    $_SERVER['APP_PACKAGES_CACHE'] = '/tmp/cache/packages.php';
    $_SERVER['APP_CONFIG_CACHE'] = '/tmp/cache/config.php';
    $_SERVER['APP_ROUTES_CACHE'] = '/tmp/cache/routes-v7.php';
    $_SERVER['APP_EVENTS_CACHE'] = '/tmp/cache/events.php';
    
    $_SERVER['CACHE_STORE'] = 'file';
    $_SERVER['SESSION_DRIVER'] = 'cookie';
    $_SERVER['QUEUE_CONNECTION'] = 'sync';

    if (!is_dir('/tmp/cache')) {
        mkdir('/tmp/cache', 0755, true);
    }
}

// Force error reporting at the PHP level
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // Forward Vercel requests to normal index.php
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    echo "<h1>Early Laravel Boot Error</h1>";
    
    // Get the original, underlying exception if available
    $original = $e->getPrevious() ?: $e;
    
    echo "<h3>Original Exception: " . htmlspecialchars($original->getMessage()) . "</h3>";
    echo "<p>File: " . htmlspecialchars($original->getFile()) . " on line " . $original->getLine() . "</p>";
    echo "<h4>Stack Trace:</h4>";
    echo "<pre>" . htmlspecialchars($original->getTraceAsString()) . "</pre>";
}
