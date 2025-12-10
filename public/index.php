<?php

require __DIR__ . '/../vendor/autoload.php';

use Essential\Application;
use Slim\Factory\AppFactory;

// 1. Cria aplicação
$app = new Application(dirname(__DIR__));

// 2. Registra Slim Router
$app->singleton('router', function ($container) {
    return AppFactory::create();
});

// 3. Define rotas
$router = $app->get('router');

$router->get('/', function ($request, $response) {
    $response->getBody()->write('Hello from Essential Framework POC!');
    return $response;
});

$router->get('/about', function ($request, $response) {
    $response->getBody()->write('This is a proof of concept');
    return $response;
});

// 4. Executa
$router->run();
