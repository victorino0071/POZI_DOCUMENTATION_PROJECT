<?php

namespace Core;

use Core\Container\Container;
use Core\Routing\Router;
use Core\Http\Request;

class Application extends Container{

    protected string $basePath;

    public function __construct(string $basePath){
        $this->basePath = $basePath;
        
        $this->singleton(Application::class, $this);

        $this->singleton(Request::class, function($app){
            return new Request();
        });

        $this->singleton(Router::class, function($app){
            return new Router($app);
        });

    }


    public function run():void{
        $router = $this->resolve(Router::class);

        $request = $this->resolve(Request::class);

        echo $router->resolve($request->getUri());
    }
}