<?php

// Autoload dependencies and classes
require __DIR__ . '/../vendor/autoload.php';

use App\RouteProvider;
use App\ServiceProvider;
use Framework\EnvLoader;
use Framework\Kernel;
use Framework\Request;

$config = [];

$localEnv = __DIR__ . '/../.env.local';

if (file_exists($localEnv)) {
    $config = EnvLoader::load($localEnv);
}

// Initialize the Kernel with configuration
try {
    $kernel = new Kernel($config);
} catch (Exception $e) {
    echo $e->getMessage();
}

if (isset($kernel)) {
    $kernel->registerServices(new ServiceProvider());

// Define routes
    $kernel->registerRoutes(new RouteProvider());
}

date_default_timezone_set('Europe/Amsterdam');
// Get Request data from the global variables
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Extract the path from the URL
$urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (!is_string($urlPath)) {
    $urlPath = '/';
}

// Get query (GET) parameters
$queryParams = $_GET;

// Get POST data
$postData = $_POST;

// Create the Request object
$request = new Request($method, $urlPath, $queryParams, $postData);

if (isset($kernel)) {
    $response = $kernel->handle($request);
    $response->echo();
}
// Handle the request and get the response


// Send the response to the client
