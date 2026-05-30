<?php

// Dynamic cache path override for serverless hosting on Vercel is now handled in public/index.php and api/index.php

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
        // Intercept raw exceptions early on Vercel before the renderer crashes on the 'view' service
        $exceptions->report(function (\Throwable $e) {
            if (str_contains(__DIR__, 'var/task') || isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
                echo "<h1>RAW BOOTSTRAP EXCEPTION CAUGHT</h1>";
                echo "<h3>Message: " . htmlspecialchars($e->getMessage()) . "</h3>";
                echo "<p>File: " . htmlspecialchars($e->getFile()) . " on line " . $e->getLine() . "</p>";
                echo "<h4>Stack Trace:</h4>";
                echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
                exit;
            }
        });
    })->create();

// Dynamic storage path override for serverless hosting on Vercel (Foolproof directory check)
if (str_contains(__DIR__, 'var/task') || str_contains(getcwd(), 'var/task') || isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
    $app->useStoragePath('/tmp');

    $storageDirs = [
        '/tmp/framework/views',
        '/tmp/framework/cache',
        '/tmp/framework/sessions',
        '/tmp/logs',
        '/tmp/cache',
    ];

    foreach ($storageDirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}

return $app;
