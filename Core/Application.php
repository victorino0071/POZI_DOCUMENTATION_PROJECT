<?php

namespace Core;

use Core\Http\Request;
use Core\Routing\Router;

class Application{
    
    public string $basePath;
    public Request $request;
    public Router $router;


    public function __construct(string $basePath){
        $this->basePath = $basePath;
        $this->request = new Request();
        $this->router = new Router($this->request);

    }

    
    public function run():void{
        $this->router->resolve();
    }
}