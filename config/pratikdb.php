<?php 
include "collection.php";  
class PratikDB { 
    protected $pdo;
    protected $table;
    protected $columns = ['*'];
    protected $where = [];
    protected $bindings = [];
    protected $order = [];
    protected $limit;
    protected $joins = [];
    protected $groupBy = [];
    protected $withs = [];
    
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    
    public function create($data) {
        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        $values = array_values($data);
        $sql = "INSERT INTO $this->table ($columns) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);
        return $this->pdo->lastInsertId();
    }
    
    public function update($data) {
        $set = '';
        $updateValues = [];
        
        foreach ($data as $column => $value) {
            $set .= "$column = ?, ";
            $updateValues[] = $value;
        } 
        $set = rtrim($set, ', '); 
        $sql = "UPDATE $this->table SET $set";
        
        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }
        
        $stmt = $this->pdo->prepare($sql);
        $allBindings = array_merge($updateValues, $this->bindings);
        
        foreach ($allBindings as $index => $value) {
            $stmt->bindValue($index + 1, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        
        $stmt->execute();
        return $stmt->rowCount();
    }
    
    public function delete() {
        $sql = "DELETE FROM $this->table";
        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        return $stmt->rowCount();
    }
    
    
    public function table($table) {
        $this->table = $table;
        return $this;
    }
    
    public function select(...$columns) {
        $this->columns = empty($columns) ? ['*'] : (is_array($columns[0]) ? $columns[0] : $columns);
        return $this;
    }
    
    public function where($column, $operator = null, $value = null)
    {
        if (is_callable($column)) {
            return $this->whereNested($column);
        }
        
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        $this->where[] = "$column $operator ?";
        $this->bindings[] = $value;
        
        return $this;
    }

    public function whereRaw($rawCondition)
    {
        $this->where[] = $rawCondition;
        return $this;
    }

    public function whereNested(callable $callback)
    {
        $subQuery = new static($this->pdo);
        $callback($subQuery);
        $subWhere = implode(' AND ', $subQuery->where);
        if (!empty($subWhere)) {
            $this->where[] = "($subWhere)";
            $this->bindings = array_merge($this->bindings, $subQuery->bindings);
        }
        return $this;
    }
    
    public function orWhere($column, $operator = null, $value = null) {
        return $this->where($column, $operator, $value, 'OR');
    }

    public function when($condition, callable $callback, callable $default = null)
    {
        if ($condition) {
            $callback($this);
        } elseif ($default) {
            $default($this);
        }
    
        return $this;
    }

    
    public function whereIn($column, $values) {
        if (strpos($column, '.') === false) {
            $column = "$this->table.$column";
        }
        $valueString = implode(',', array_fill(0, count($values), '?'));
        $this->where[] = "$column IN ($valueString)";
        $this->bindings = array_merge($this->bindings, $values);
        return $this;
    }
    
  /*  public function whereBetween($column, $min, $max) {
        $this->where[] = "$column BETWEEN ? AND ?";
        $this->bindings[] = $min;
        $this->bindings[] = $max;
        return $this;
    } */
    public function whereBetween($column, $min, $max, $boolean = 'and') {
        $this->where[] = [
            'type' => 'between',
            'column' => $column,
            'boolean' => $boolean,
            'min' => $min,
            'max' => $max
        ];
        $this->bindings[] = $min;
        $this->bindings[] = $max;
        return $this;
    }
   /* public function orderBy($column, $direction = 'ASC') {
        $this->order[] = "$column $direction";
        return $this;
    } */

    public function orderBy($column, $direction = 'ASC') {
        if (strpos($column, '.') === false) {
            $column = "$this->table.$column";
        }
        $this->order[] = "$column $direction";
        return $this;
    }
    
    public function limit($value) {
        $this->limit = $value;
        return $this;
    }
    
  /*  public function join($table, $first, $operator = '=', $second = null, $type = 'INNER') {
        if ($second === null) {
            $second = $operator;
            $operator = '=';
        }
        $this->joins[] = "$type JOIN $table ON $first $operator $second";
        return $this;
    } */

    public function join($table, $firstColumn, $operator, $secondColumn)
{
    $this->joins[] = "INNER JOIN $table ON $firstColumn $operator $secondColumn";
    return $this;
}
    
    public function get() {  
        $sql = $this->buildQuery(); 
        $stmt = $this->pdo->prepare($sql); 
        foreach ($this->bindings as $index => $value) {
            if (is_array($value)) {
                foreach ($value as $val) {
                    $stmt->bindValue($index + 1, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
                    $index++;
                }
            } else {
                $stmt->bindValue($index + 1, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
        }  
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function first() {
        $this->limit(1);
        $result = $this->get();
        return count($result) ? reset($result) : null;
    }
    
    
    public function count() {
        $sql = "SELECT COUNT(*) as count FROM $this->table";
        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        return $stmt->fetchColumn();
    }
    
    public function with($alias, callable $callback) {
        $subQuery = new static($this->pdo);
        $callback($subQuery);
        $subQuerySql = $subQuery->buildQuery();
        $this->bindings = array_merge($this->bindings, $subQuery->bindings);
        $this->withs[] = "$alias AS ($subQuerySql)";
        return $this;
    }
    
    
    public function cte($alias, callable $callback)
    {
        $subQuery = new static($this->pdo);
        $callback($subQuery);
        $subQuerySql = $subQuery->buildQuery();
        $this->bindings = array_merge($this->bindings, $subQuery->bindings);
        $this->table = "$alias ($subQuerySql)";
        return $this;
    }
    
    public function groupBy(...$columns)
    {
        $this->groupBy = $columns;
        return $this;
    }

    public function buildQuery() {
        $query = "";
        if (!empty($this->withs)) {
            $query .= "WITH " . implode(', ', $this->withs) . " ";
        }

        $query = "SELECT " . implode(', ', $this->columns) . " FROM $this->table";
        
        if (!empty($this->joins)) {
            $query .= " " . implode(' ', $this->joins);
        }

        if (!empty($this->where)) {
            $query .= " WHERE " . implode(' AND ', $this->where);
        }
 
        if (!empty($this->groupBy)) {
            $query .= " GROUP BY " . implode(', ', $this->groupBy);
        }
         
        if (!empty($this->order)) {
            $query .= " ORDER BY " . implode(', ', $this->order);
        }
        if ($this->limit !== null) {
            $query .= " LIMIT " . $this->limit;
        }
        return $query;
    }
    
    
    public function pluck($column, $key = null) {
        $this->columns = [$column];
        if ($key !== null) {
            $this->columns[] = $key;
        }
        $result = $this->get();
        if ($key !== null) {
            $values = array_column($result, $column, $key);
        } else {
            $values = array_column($result, $column);
        }
        return new Collection($values);
    }
    
 
    public function toArray() {
        return $this->get();
    }
    
    public function toJson()
    {
        return json_encode($this->toArray());
    }
    
    public function toSql() {
        echo $this->buildQuery();
    }
}

function dd($value) {
    echo "<pre>";
    print_r($value);
    echo "</pre>";
    die();
}