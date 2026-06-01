<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;

$task = Task::first();
$user = User::first(); // Assuming this is the team leader

$request = Request::create('/tasks/' . $task->id . '/position', 'PATCH', [
    'status' => 'in_progress',
    'position' => 1,
]);
$request->headers->set('Accept', 'application/json');

// Bypass CSRF by mocking the middleware or just replacing the route middleware
$app->make(Illuminate\Contracts\Http\Kernel::class);
app('router')->aliasMiddleware('verify_csrf', \Illuminate\Routing\Middleware\SubstituteBindings::class);

auth()->login($user);

// Better way to call a controller method directly to skip all middleware:
$controller = app()->make(\App\Http\Controllers\KanbanController::class);
try {
    $response = $controller->updatePosition($request, $task);
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content: " . $response->getContent() . "\n";
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
