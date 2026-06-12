<?php

require_once __DIR__ . '/../autoload.php';


\Core\Support\Env::load(dirname(__DIR__) . '/.env');

$app = new \Core\Application(dirname(__DIR__));
$app->run();
