<?php


namespace Core\Database;

use PDO;

class QueryBuilder{
    
    protected PDO $db;
    protected Model $model;
    protected array $wheres = [];
    protected array $bindings = [];
    protected array $selects = ['*'];

    public function __construct(PDO $db, Model $model){
        $this->model = $model;
        $this->db = $db;
    }

    public function select(array $columns):static{
        // Se ainda for o asterisco padrão, nós limpamos antes do merge
        if ($this->selects === ['*']) {
            $this->selects = [];
        }
        $this->selects = array_merge($this->selects, $columns);

        return $this;
    }


    public function where(string $column, string $operator, mixed $value): static{
        $this->wheres[] = "{$column} {$operator} ?";
        $this->bindings[] = $value;
        return $this;
    }


    public function get():array{
        $table = $this->model->getTable();

        $columnsStr = implode(', ', $this->selects);
        $sql = "SELECT {$columnsStr} FROM {$table}";

        if (!empty($this->wheres)){
            $sql .= " WHERE " . implode(" AND ", $this->wheres);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($this->bindings);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->hydrate($results);
    }


    protected function hydrate(array $result):array{

        $models = [];

        foreach( $result as $row){
            $instance = clone $this->model;
            $instance->setAttributes($row);

            $models[] = $instance;
        }

        return $models;
    }
}