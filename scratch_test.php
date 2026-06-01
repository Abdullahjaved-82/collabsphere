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

auth()->login($user);

$response = app()->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
echo "Content: " . $response->getContent() . "\n";
