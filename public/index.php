<?php

// Autoload dependencies and classes
require __DIR__ . '/../vendor/autoload.php';

use App\RouteProvider;
use App\ServiceProvider;
use Framework\EnvLoader;
use Framework\Kernel;
use Framework\Request;

$config = [];

try {
    $config = EnvLoader::load(__DIR__ . '/../.env.local');
} catch (Exception $e) {
    die("ENV ERROR: " . $e->getMessage());
}

// Initialize the Kernel with configuration
try {
    $kernel = new Kernel($config);
} catch (Exception $e) {
    echo $e->getMessage();
}

$kernel->registerServices(new ServiceProvider());

date_default_timezone_set('Europe/Amsterdam');

// Define routes
$kernel->registerRoutes(new RouteProvider());

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

// Handle the request and get the response
$response = $kernel->handle($request);

// Send the response to the client
$response->echo();
