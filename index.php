<?php

declare(strict_types=1);

use App\Router;

require_once __DIR__ . '/vendor/autoload.php';

$request = json_decode(file_get_contents('php://input')) ?? new stdClass();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$router = new Router($request);

$dotenv->load();

require_once 'config.php';
require_once 'routes.php';

try {
    $router->matchRouteUri(get_routes());
} catch (Exception $exception) {
    echo $exception->getMessage();
}
