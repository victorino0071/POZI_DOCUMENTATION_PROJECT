<?php


namespace Core\Database;

use PDO;


abstract class Model{
    protected static ?PDO $db = null;
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $attributes = [];


    public static function setConnection(PDO $pdo):void{
        self::$db = $pdo;
    }

    public function __set(string $name, mixed $value): void{
        $method = 'set' . str_replace(' ', '', ucwords(str_replace('_',' ', $name))) . 'Attribute';


        if (method_exists($this, $method)){
            $this->attributes[$name] = $this->$method($value);
            return;
        }

        $this->attributes[$name] = $value;
    }

    public function __get(string $name):mixed{
        $method = 'get' . str_replace(' ', '', ucwords(str_replace('_',' ', $name))) . 'Attribute';
        

        if (method_exists($this, $method)){
            return $this->$method();
        }

        return $this->attributes[$name]?? null;
    }
    

    public static function find(int $id): ?static{
        $instance = new static();
        $stmt = self::$db->prepare("SELECT * FROM {$instance->table} WHERE {$instance->primaryKey} = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$data){
            return null;
        }

        $instance->attributes = $data;
        return $instance;
    }

    public function save():boll{
        if(isset($this->attributes[$this->primaryKey])){
            return $this->update();
        }

        return $this->insert();
    }

    public function insert():boll{
        $columns = array_keys($this->attributes);

        $values = array_values($this->attributes);

        $placeholders = array_fill(0, $count($columns), '?');

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );


        $stmt = self::$db->prepare($sql);
        $result = $stmt->execute($values);

        if($result){
            $this->attribute[$this->primaryKey] = self::db->lastInsertId();
        }

        return $result;
    }

    public function update():boll{
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
}