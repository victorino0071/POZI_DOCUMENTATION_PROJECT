<?php


namespace Core;


use ReflectionClass;
use Exception;

class Container {
    protected array $bindings = [];
    protected array $instances = [];

    public function bind(string $abstract, mixed $concrete = null): void{
        if (is_null($concrete)){
            $concrete = $abstract;
        }

        $this->bindings[$abstract] = $concrete;
    }


    public function singleton(string $abstract, mixed $concrete=null):void{
        $this->bind($abstract, $concrete);

        $this->instances[$abstract]=null;
    }


    public function resolve(string $abstract): object{
        if (array_key_exists($abstract, $this->instances) && $this->instances[$abstract] !== null){
            return $this->instances[$abstract];
        }

        $concrete = $this->bindings[$abstract] ?? $abstract;

        if ($concrete instanceof \Closure){
            $object = $concrete($this);
        }else {
            $object = $this->build($concrete);
        }

        if (array_key_exists($abstract, $this->instances)){
            $this->instances[$abstract] = $object;
        }

        return $object;
    }


    public function build(string $concrete):object{
        try {
            $reflector = new ReflectionClass($concrete);
        }catch(\ReflectionException $e){
            throw new Exception("Não foi possivel instanciar a classe {$concrete}: {$e->getMessage()}");

        }

        if (!$reflector->isInstantiable()){
            throw new Exception("Não foi possivel instanciar a classe {$concrete}");
        }

        $constructor = $reflector->getConstructor();
        if(is_null($constructor)){
            return new $concrete;
        }

        $parameters = $constructor->getParameters();
        $dependencies = $this->resolveDependencies($parameters);
        
        return $reflector->newInstanceArgs($dependencies);
        
    }


    public function resolveDependencies(array $parameters) :array{
        $dependencies = [];

        foreach($parameters as $parameter){
            $type = $parameter->getType();

            if (!$type || $type->isBuiltin()){
                throw new Exception("Não foi possivel resolver a dependencia {$parameter->getName()}.");
            }

            $dependencies[] = $this->resolve($type->getName());
        }

        return $dependencies;
    }
}