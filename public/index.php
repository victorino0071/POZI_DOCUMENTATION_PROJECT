<?php


require_once __DIR__ .'/../autoload.php';



$app = new \Core\Application(dirname(__DIR__));


$router = $app->resolve(\Core\Routing\Router::class);


$router->get('/', function(){
    return "Bem vindo sbdaig";
});


$app->run();