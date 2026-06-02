<?php

require __DIR__ . '/vendor/autoload.php';

use Framework\EnvLoader;
use Framework\Kernel;

$config = [];

try {
    $config = EnvLoader::load(__DIR__ . '/.env.local');
} catch (Exception $e) {
    die("ENV ERROR: " . $e->getMessage());
}

// Initialize the Kernel with configuration
try {
    $kernel = new Kernel($config);
} catch (Exception $e) {
    echo $e->getMessage();
}

$command = $argv[1] ?? null;

if ($command === 'migrate') {
    $kernel->getDatabase()->migrate(__DIR__ . '/database/');
    echo "Migration completed\n";
    exit;
}

echo "Unknown command\n";