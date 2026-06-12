<?php


namespace Core\Providers;

use PDO;
use Exception;
use Core\Support\Env; 

class DatabaseServiceProvider extends ServiceProvider{

    public function register(): void
    {
        $this->container->singleton(PDO::class, function () {
            
            $host = Env::get('DB_HOST', '127.0.0.1');
            $db   = Env::get('DB_DATABASE', 'pozi_docs');
            $user = Env::get('DB_USERNAME', 'root');
            $pass = Env::get('DB_PASSWORD', '');
            $port = Env::get('DB_PORT', '3306');


            $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

            try {
                return new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (\PDOException $e) {
                throw new Exception("Falha catastrófica ao conectar no banco: " . $e->getMessage());
            }
        });
    }

}



