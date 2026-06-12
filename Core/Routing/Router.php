<?php 

namespace Core\Routing;

use Exception;
use Core\Application;
use Core\Http\Request;

class Router{
    
    protected array $routes = [];
    protected Application $app;

    public function __construct(Application $app){
        $this->app = $app;
    }


    public function get(string $uri, array|callable $action, array $middlewares = []): void{
        $this->routes['GET'][$uri] = [
            'action' => $action,
            'middlewares' => $middlewares
        ];
    }

    public function post(string $uri, array|callable $action, array $middlewares = []): void{
        $this->routes['POST'][$uri] = [
            "action"=> $action,
            "middlewares" => $middlewares
        ];
    }

    public function put(string $uri, array|callable $action,array $middlewares = []): void{
        $this->routes['PUT'][$uri] = [
            "action"=> $action,
            "middlewares" => $middlewares
        ];
    }

    public function patch(string $uri, array|callable $action, array $middlewares = []): void{
        $this->routes['PATCH'][$uri] = [
            "action"=> $action,
            "middlewares" => $middlewares
        ];
    }

    public function delete(string $uri, array|callable $action, array $middlewares = []): void{
        $this->routes['DELETE'][$uri] = [
            "action"=> $action,
            "middlewares" => $middlewares
        ];
    }


public function resolve(Request $request): mixed
    {
        $uri = $request->getUri();
        $method = $request->getMethod();
        
        $routes = $this->routes[$method] ?? [];

        foreach ($routes as $routeUri => $routeConfig) {
            
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $routeUri);
            
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                $routeParams = $matches;

                $action = $routeConfig['action'];
                $middlewares = $routeConfig['middlewares'];

                $coreAction = function ($req) use ($action, $routeParams) {
                    if (is_callable($action)) {
                        return call_user_func_array($action, array_merge([$req], $routeParams));
                    }

                    if (is_array($action)) {
                        [$controllerClass, $methodName] = $action;
                        
                        $controllerInstance = $this->app->resolve($controllerClass);

                        if (!method_exists($controllerInstance, $methodName)) {
                            throw new Exception("O método {$methodName} não existe em {$controllerClass}");
                        }

                        return call_user_func_array([$controllerInstance, $methodName], array_merge([$req], $routeParams));
                    }
                    throw new Exception("Ação de rota inválida.");
                };

                $pipeline = $coreAction;

                foreach (array_reverse($middlewares) as $middlewareClass) {
                    $middlewareInstance = $this->app->resolve($middlewareClass);
                    
                    $next = $pipeline;
                    
                    $pipeline = function ($req) use ($middlewareInstance, $next) {
                        return $middlewareInstance->handle($req, $next);
                    };
                }

                return $pipeline($request);
            }
        }

        http_response_code(404);
        return json_encode(['erro' => 'Rota não encontrada']);
    }


}