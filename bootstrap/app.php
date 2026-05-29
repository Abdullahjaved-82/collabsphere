<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'onboarded' => \App\Http\Middleware\EnsureOnboardingCompleted::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

// Dynamic storage path override for serverless hosting on Vercel
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || isset($_ENV['VERCEL_URL']) || isset($_SERVER['VERCEL_URL'])) {
    $app->useStoragePath('/tmp');

    $storageDirs = [
        '/tmp/framework/views',
        '/tmp/framework/cache',
        '/tmp/framework/sessions',
        '/tmp/logs',
    ];

    foreach ($storageDirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}

return $app;
