<?php

require_once __DIR__ . '/Core/Container.php';

use Core\Container;

class BancoDeDados{}

class UsuarioController{
    public BancoDeDados $banco;

    public function __construct(BancoDeDados $banco){
        $this->banco = $banco;
    }
}

$container = new Container();

$controller =  $container->resolve(UsuarioController::class);

var_dump($controller->banco);