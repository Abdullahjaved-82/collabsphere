<?php

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
