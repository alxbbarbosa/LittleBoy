<?php

namespace Abbarbosa\LittleBoy\Framework;

/**
 * ==============================================================================================================
 *
 * QueryBuilder: Classe para criar consultas
 *
 * ----------------------------------------------------
 *
 * @author Alexandre Bezerra Barbosa <alxbbarbosa@yahoo.com.br>
 * @copyright (c) 2018, Alexandre Bezerra Barbosa
 * @version 1.00
 * ==============================================================================================================
 */
class QueryBuilder
{
    private static $connection;
    protected $built = '';
    protected $select;
    protected $table;

    public function __construct($table = '')
    {
        $this->select = "*";
        $this->table  = $table;
    }

    public function table($table)
    {
        return $this->table;
    }

    public function toSql()
    {
        return $this->built;
    }

    public function update($data)
    {

        if (!is_array($data)) {
            $data = (array) $data;
        }

        $sets = array_filter(array_map(function($field) {
                return "{$field} = :{$field}";
            }, array_keys($data)));

        $sql = "UPDATE {$this->table} SET ".implode(', ', $sets).$this->built.';';

        $db = self::$connection->prepare($sql);
        $db->execute($data);
        return $db->rowCount();
    }

    public function delete()
    {
        $sql = "DELETE FROM {$this->table} ".$this->built;
        return self::$connection->exec($sql);
    }

    public function count($arguments)
    {
        if (func_num_args() == 1 && is_string($arguments)) {
            $this->select = "SELECT count({$arguments}) ";
        }

        if (func_num_args() == 1 && is_array($arguments)) {
            $this->select = 'SELECT count('.implode(',', $arguments).') ';
        }

        if (func_num_args > 1) {
            $fields       = func_get_args();
            $this->select = 'SELECT count('.implode(',', $fields).') ';
        }
        $sql = $this->select." FROM {$this->table} ".$this->built;

        $q = self::$connection->prepare($sql);
        $q->execute();
        $a = $q->fetch(\PDO::FETCH_ASSOC);
        return (int) $a['t'];
    }

    public function get()
    {
        if (is_null($this->table)) {
            throw new Exception("Parametros incorretos para query: tabela não informada");
        }

        $sql = $this->select." FROM {$this->table} ".$this->built;

        $result = self::$connection->query($sql);

        if ($this->select == '*') {
            return $result->fetchAll(\PDO::FETCH_CLASS, get_called_class());
        } else {
            return $result->fetchAll(\PDO::FETCH_CLASS);
        }
    }

    public function select($arguments)
    {
        if (func_num_args() == 1 && is_string($arguments)) {
            $this->select = "SELECT {$arguments} ";
            return $this;
        }

        if (func_num_args() == 1 && is_array($arguments)) {
            $this->select = 'SELECT '.implode(',', $arguments).' ';
            return $this;
        }

        if (func_num_args() > 1) {
            $fields       = func_get_args();
            $this->select = 'SELECT '.implode(',', $fields).' ';
            return $this;
        }
    }

    public function whereExists($data)
    {

        if (strlen($this->built) > 1 && substr($this->built, -1, 1) !== '(') {
            $this->built .= ' AND ';
        }

        $result = " WHERE EXISTS ";

        if (is_callable($data)) {
            $result .= $this->subQuery($data);
        }
        $this->built .= $result;
        return $this;
    }

    public function whereIsNull($field)
    {

        $result      = " WHERE $field IS NULL ";
        $this->built .= $result;
        return $this;
    }

    public function whereIsNotNull($field)
    {

        $result      = " WHERE $field IS NOT NULL ";
        $this->built .= $result;
        return $this;
    }

    public function where($arguments)
    {

        if (func_num_args() == 1 && is_string($arguments)) {
            if (strlen($this->built) > 1 && substr($this->built, -1, 1) !== '(') {
                $this->built .= ' AND ';
            }
            $this->built .= " WHERE {$arguments} ";
            return $this;
        }

        $data = func_get_args();

        if (strlen($this->built) > 1 && substr($this->built, -1, 1) !== '(') {
            $this->built .= ' AND ';
        }
        return call_user_func_array(array($this, 'constructStmtWhere'), $data);
    }

    public function orWhere($arguments)
    {

        if (func_num_args() == 1 && is_string($arguments)) {
            if (strlen($this->built) > 1 && substr($this->built, -1, 1) !== '(') {
                $this->built .= ' OR ';
            }
            $this->built .= " WHERE {$arguments} ";
            return $this;
        }

        $data = func_get_args();

        if (strlen($this->built) > 1 && substr($this->built, -1, 1) !== '(') {
            $this->built .= ' OR ';
        }
        return call_user_func_array(array($this, 'constructStmtWhere'), $data);
    }

    public function whereBetween($field, array $data)
    {

        if (strlen($this->built) > 1 && substr($this->built, -1, 1) !== '(') {
            $this->built .= ' AND ';
        }

        $result = " WHERE $field BETWEEN ";

        if (is_array($data)) {
            $result .= implode(' AND ', $data);
        }
        $this->built .= $result;
        return $this;
    }

    public function whereNotBetween($field, array $data)
    {

        if (strlen($this->built) > 1 && substr($this->built, -1, 1) !== '(') {
            $this->built .= ' AND ';
        }

        $result = " WHERE $field NOT BETWEEN ";

        if (is_array($data)) {
            $result .= implode(' AND ', $data);
        }
        $this->built .= $result;
        return $this;
    }

    public function whereIn($field, $data)
    {

        if (strlen($this->built) > 1 && substr($this->built, -1, 1) !== '(') {
            $this->built .= ' AND ';
        }

        $result = " WHERE $field IN ";

        if (is_callable($data)) {
            $result .= $this->subQuery($data);
        } else if (is_array($data)) {
            $result .= '('.implode(',', $data).')';
        }
        $this->built .= $result;
        return $this;
    }

    public function whereNotIn($field, $data)
    {

        if (strlen($this->built) > 1 && substr($this->built, -1, 1) !== '(') {
            $this->built .= ' AND ';
        }

        $result = " WHERE $field NOT IN ";

        if (is_callable($data)) {
            $result .= $this->subQuery($data);
        } else if (is_array($data)) {
            $result .= '('.implode(',', $data).')';
        }
        $this->built .= $result;
        return $this;
    }

    protected function constructStmtWhere($arguments)
    {

        $data = func_get_args();

        if (is_callable($data[0])) {
            $this->subQuery($data[0]);
        }

        if (!is_array($data[0])) {

            $result[] = call_user_func_array(array($this, 'build'), $data);
        } else {
            foreach ($data as $piece) {
                $result[] = call_user_func_array(array($this, 'build'), $piece);
            }
        }
        $this->built .= implode(' AND ', $result);
        return $this;
    }

    protected function build($arguments)
    {

        $num = func_num_args();

        if ($num > 1) {

            $data = func_get_args();

            if (!is_array($data[0])) {

                if ($num == 2) {
                    return " WHERE $data[0] = '$data[1]' ";
                } elseif ($num == 3) {
                    return " WHERE $data[0] $data[1] '$data[2]' ";
                } else {
                    throw new Exception("Parametros incorretos para query");
                }
            }
        }
    }

    protected function subQuery($callback)
    {
        $this->built .= '(';
        $callback($this);
        $this->built .= ')';
    }

    public static function setConnection(\PDO $connection)
    {
        self::$connection = $connection;
    }
}