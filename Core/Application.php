<?php

namespace Core;

use Core\Container\Container;
use Core\Routing\Router;

class Application extends Container{

    protected string $basePath;

    public function __construct(string $basePath){
        $this->basePath = $basePath;
        
        $this->singleton(Application::class, $this);

        $this->singleton(Router::class, function($app){
            return new Router($app);
        });

    }


    public function run():void{
        $router = $this->resolve(Router::class);

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $method = $_SERVER['REQUEST_METHOD'];

        echo $router->resolve($uri, $method);
    }
}