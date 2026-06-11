<?php


namespace Core\Routing;


use Core\Http\Request;


class Router{
    
    public Request $request;


    protected array $routes = [];

    public function __construct(Request $request){
        $this->request = $request;
    }

    public function get(string $path, callable|array $callback):void{
        $this->routes['get'][$path] = $callback;
    }

    public function resolve(){
        $path = $this->request->getUri();
        $method = $this->request->getMethod();

        $callback = $this->routes[$method][$path] ?? false;

        if ($callback === false){
            http_response_code(404);
            echo "<h1>Erro 404</h1><p>Página não encontrada!</p>";
            return;
        }

        if (is_array($callback)){
            $callback[0] = new $callback[0]();
        }

        if (is_callable($callback)){
            return call_user_func($callback);
        }
        
    }
}