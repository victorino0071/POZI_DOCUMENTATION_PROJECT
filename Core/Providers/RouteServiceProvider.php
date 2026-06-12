<?php

namespace Core\Providers;

use Core\Routing\Router;
use Core\Http\Request;
use Exception;

class RouteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $router = $this->container->resolve(Router::class);
        $request = $this->container->resolve(Request::class);
        
        $uri = $request->getUri();

        $manifestPath = __DIR__ . '/../../App/Config/channels.php';
        
        if (!file_exists($manifestPath)) {
            throw new Exception("Manifesto de Canais não encontrado.");
        }
        
        $channelMap = require $manifestPath;

        foreach ($channelMap as $prefix => $routeFilePath) {
            
            if (str_starts_with($uri, $prefix)) {
                
                if (!file_exists($routeFilePath)) {
                    throw new Exception("Arquivo de rotas não encontrado: {$routeFilePath}");
                }
                
                require_once $routeFilePath;
                
                return;
            }
        }

        $router->get('/', function() {
            return "Engine Pozi V.1.0 - Sem canal associado.";
        });
    }
}
