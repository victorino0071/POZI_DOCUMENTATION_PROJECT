<?php
$_SERVER['REQUEST_URI'] = '/admin/contratos/1';
$_SERVER['REQUEST_METHOD'] = 'GET';
require "autoload.php";
$app = new \Core\Application(__DIR__);
$ref = new ReflectionProperty(\Core\Application::class, 'container');
$ref->setAccessible(true);
$container = $ref->getValue($app);
$router = $container->resolve(\Core\Routing\Router::class);
var_dump($router);
