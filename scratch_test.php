<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
$grammar = DB::connection()->getQueryGrammar();

// Emulate prepares is true
DB::connection()->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

try {
    $user = \App\Models\User::first();
    if ($user) {
        $user->has_completed_onboarding = true;
        $user->save();
        echo "Boolean save successful!\n";
    }
} catch (\Exception $e) {
    echo "Boolean save failed: " . $e->getMessage() . "\n";
}
