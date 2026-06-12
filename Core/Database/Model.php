<?php


namespace Core\Database;

use PDO;


abstract class Model{
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $attributes = [];
    protected array $relations = [];

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }


    public function getTable():string{
        return $this->table;
    }

    public function setAttributes(array $attributes):void{
        $this->attributes = $attributes;
    }


    
    public function __set(string $name, mixed $value): void{
        $method = 'set' . str_replace(' ', '', ucwords(str_replace('_',' ', $name))) . 'Attribute';


        if (method_exists($this, $method)){
            $this->attributes[$name] = $this->$method($value);
            return;
        }

        $this->attributes[$name] = $value;
    }

    public function __get(string $name): mixed  
    {
        
        $method = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $name))) . 'Attribute';
        if (method_exists($this, $method)) {
            return $this->$method();
        }


        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }

        
        if (method_exists($this, $name)) {
            
            if (array_key_exists($name, $this->relations)) {
                return $this->relations[$name];
            }

            $relationData = $this->$name();
            $this->relations[$name] = $relationData;
            
            return $relationData;
        }

        return null;
    }


    public function __call(string $method, array $parameters){
        $builder = new QueryBuilder($this->db, $this);

        return call_user_func_array([$builder, $method], $parameters);
    }
    

    public function find(int $id): ?static{
        
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$data){
            return null;
        }

        $this->attributes = $data;
        return $this;
    }

    public function save():bool{
        if(isset($this->attributes[$this->primaryKey])){
            return $this->update();
        }

        return $this->insert();
    }

    public function insert():bool{
        $columns = array_keys($this->attributes);

        $values = array_values($this->attributes);

        $placeholders = array_fill(0, count($columns), '?');

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );


        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute($values);

        if($result){
            $this->attributes[$this->primaryKey] = $this->db->lastInsertId();
        }

        return $result;
    }

    public function update():bool{
        $columns = array_keys($this->attributes);
        $setClause = [];

        foreach($columns as $column){
            if($column !== $this->primaryKey){
                $setClause[] = "{$column} = ?";
            }
        }

        $sql = sprintf(
            "UPDATE %s SET %s WHERE %s = ?",
            $this->table,
            implode(', ', $setClause),
            $this->primaryKey
        );

        $idValue = $this->attributes[$this->primaryKey];
        $valueWithoutId = array_filter($column, fn($col)=>$col !== $this->primaryKey);

        $finalValues = [];

        foreach ($valuesWithoutId as $col){
            $finalValues[] = $this->attributes[$col];
        }

        $finalValues[] = $idValue;

        $stmt = self::$db->prepare($sql);
        return $stmt->execute($finalValues);
    }

    protected function hasMany(string $relatedClass, string $foreignKey): array
    {
        $instance = new $relatedClass(); 
        
        $myId = $this->attributes[$this->primaryKey] ?? null;
        if (!$myId) return []; 

        $sql = "SELECT * FROM {$instance->table} WHERE {$foreignKey} = :id";
        $stmt = self::$db->prepare($sql);
        $stmt->execute(['id' => $myId]);
        
        $results = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $obj = new $relatedClass();
            $obj->attributes = $row;
            $results[] = $obj;
        }

        return $results;
    }


    protected function belongsTo(string $relatedClass, string $foreignKey): ?object
    {
        $instance = new $relatedClass();
        
        $foreignId = $this->attributes[$foreignKey] ?? null;
        if (!$foreignId) return null;

        $sql = "SELECT * FROM {$instance->table} WHERE {$instance->primaryKey} = :id LIMIT 1";
        $stmt = self::$db->prepare($sql);
        $stmt->execute(['id' => $foreignId]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $instance->attributes = $row;
            return $instance;
        }

        return null;
    }



}