<?php


namespace Core\Providers;

use PDO;
use Exception;


class DatabaseServiceProvider extends ServiceProvider{


    public function register():void{
        $this->container->singleton(PDO::class, function(){
            $host = '127.0.0.1';
            $db   = 'meu_banco';
            $user = 'root';
            $pass = '';
            $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";

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



