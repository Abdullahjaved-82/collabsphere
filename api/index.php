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
    echo "<h3>" . htmlspecialchars($e->getMessage()) . "</h3>";
    echo "<p>File: " . htmlspecialchars($e->getFile()) . " on line " . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
