<?php


define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/autoload.php';

use Core\Application;

$app = new Application(BASE_PATH);


$app->router->get("/", function(){
    echo "<h1>SUCESSO</h1>";
    echo "<p>A aplicação está rodando</p>";
});
$app->router->get("/", function(){
    echo "<h1>SUCESSO</h1>";
    echo "<p>A aplicação está rodando</p>";
});

$app->run();