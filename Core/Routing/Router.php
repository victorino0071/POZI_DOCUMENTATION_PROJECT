<?php 

namespace Core\Routing;

use Exception;
use Core\Application;

class Router{
    
    protected array $routes = [];


    protected Application $app;

    public function __contruct(Application $app){
        $this->app = $app;
    }


    public function get(string $uri, array|callable $action): void{
        $this->routes['GET'][$uri] = $action;
    }

    public function post(string $uri, array|callable $action): void{
        $this->routes['POST'][$uri] = $action;
    }

    public function put(string $uri, array|callable $action): void{
        $this->routes['PUT'][$uri] = $action;
    }

    public function patch(string $uri, array|callable $action): void{
        $this->routes['PATCH'][$uri] = $action;
    }

    public function delete(string $uri, array|callable $action): void{
        $this->routes['DELETE'][$uri] = $action;
    }


    public function resolve(string $uri, string $method):mixed{
        $action = $this->routes[$method][$uri] ?? null;

        if (is_null($action)){
            throw new Exception("Rota {$uri} não encontrada");
        }

        if (is_callable($action)){
            return call_user_func($action);
        }

        if (is_array($action)){
            [$controllerClass, $methodName] = $action;

            $controllerInstance = $this->app->resolve($controllerClass);

            if (!method_exists($controllerInstance, $methodName)){
                throw new Exception("Método {$methodName} não encontrado no controller {$controllerClass}");
            }

            return call_user_func([$controllerInstance, $methodName]);
        }

        throw new Exception("Ação inválida para a rota {$uri}");
    }


}