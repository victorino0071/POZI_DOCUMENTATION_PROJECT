<?php

namespace Core;

use Core\Container\Container;
use Core\Routing\Router;
use Core\Http\Request;
use Core\Providers\DatabaseServiceProvider;

class Application{

    protected string $basePath;
    protected Container $container;
    protected array $providers = [
        DatabaseServiceProvider::class,
    ];

    public function __construct(string $basePath, array $providers = [] ){
        $this->basePath = $basePath;
        $this->container = new Container();
        $this->providers = array_merge($this->providers, $providers);

        

        foreach ($this->providers as $providerClass){
            $provider = new $providerClass($this->container);
            $provider->register();
        }
        $this->container->singleton(Application::class, $this);

        $this->container->singleton(Request::class, function(){
            return new Request();
        });

        $this->container->singleton(Router::class, function(){
            return new Router($this);
        });

    }


    public function run():void{
        $router = $this->container->resolve(Router::class);

        $request = $this->container->resolve(Request::class);

        echo $router->resolve($request->getUri());
    }
}