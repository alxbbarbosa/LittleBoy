<?php

namespace Abbarbosa\LittleBoy\Framework;

use Abbarbosa\LittleBoy\Framework\QueryBuilder;

/**
 * ==============================================================================================================
 *
 * Model: Classe para criar a camada de modelo
 *
 * ----------------------------------------------------
 *
 * @author Alexandre Bezerra Barbosa <alxbbarbosa@yahoo.com.br>
 * @copyright (c) 2018, Alexandre Bezerra Barbosa
 * @version 1.00
 * ==============================================================================================================
 */
abstract class Model
{
    private static $connection;
    private $content;
    protected $table;
    protected $idField;
    protected $logTimestamp;
    protected $query;

    public function __construct()
    {
        if (!is_bool($this->logTimestamp)) {
            $this->logTimestamp = TRUE;
        }

        if (is_null($this->table)) {
            $table       = explode("\\", strtolower(get_class($this)));
            $this->table = array_pop($table);
        }

        if (is_null($this->idField)) {
            $this->idField = 'id';
        }
    }

    public function __set($parameter, $value)
    {
        $this->content[$parameter] = $value;
    }

    public function __get($parameter)
    {
        return $this->content[$parameter];
    }

    public function __isset($parameter)
    {
        return isset($this->content[$parameter]);
    }

    public function __unset($parameter)
    {
        if (isset($parameter)) {
            unset($this->content[$parameter]);
            return true;
        }
        return false;
    }

    private function __clone()
    {
        if (isset($this->content[$this->idField])) {
            unset($this->content[$this->idField]);
        }
    }

    public function toArray()
    {
        return $this->content;
    }

    public function fromArray(array $array)
    {
        $this->content = $array;
    }

    public function toJson()
    {
        return json_encode($this->content);
    }

    public function fromJson(string $json)
    {
        $this->content = json_decode($json);
    }

    private function format($value)
    {
        if (is_string($value) && !empty($value)) {
            return "'".addslashes($value)."'";
        } else if (is_bool($value)) {
            return $value ? 'TRUE' : 'FALSE';
        } else if ($value !== '') {
            return $value;
        } else {
            return "NULL";
        }
    }

    private function convertContent()
    {
        $newContent = array();
        foreach ($this->content as $key => $value) {
            if (is_scalar($value)) {
                $newContent[$key] = $value; //$this->format($value);
            }
        }
        return $newContent;
    }

    public function save()
    {
        $newContent = $this->convertContent();

        if (isset($this->content[$this->idField])) {

            $sets = array_filter(array_map(function($field) {
                    if ($field === $this->idField || $field == 'created_at' || $field == 'updated_at') {
                        return;
                    }
                    return "{$field} = :{$field}";
                }, array_keys($newContent)));

            if ($this->logTimestamp === TRUE) {
                $sets[]       = 'updated_at = :updated_at';
                $newContent[] = "updated_at = '".date('Y-m-d H:i:s')."'";
            }

            $sql = "UPDATE {$this->table} SET ".implode(', ', $sets)." WHERE {$this->idField} = :{$this->idField};";
        } else {
            if ($this->logTimestamp === TRUE) {
                $newContent['created_at'] = "'".date('Y-m-d H:i:s')."'";
                $newContent['updated_at'] = "'".date('Y-m-d H:i:s')."'";
            }
            $sql = "INSERT INTO {$this->table} (".implode(', ', array_keys($newContent)).') VALUES (:'.implode(', :',
                    array_keys($newContent)).');';
        }
        if (self::$connection) {
            $db = self::$connection->prepare($sql);
            $db->execute($newContent);
            return $db->rowCount();
        } else {
            throw new Exception("Não há conexão com Banco de dados!");
        }
    }

    public function _where($arguments)
    {

        QueryBuilder::setConnection(self::$connection);
        $obj  = new QueryBuilder($this->table);
        $data = func_get_args();
        return call_user_func_array(array($obj, 'where'), $data);
    }

    public function _whereIn($field, $arguments)
    {

        QueryBuilder::setConnection(self::$connection);
        $obj = new QueryBuilder($this->table);
        $obj->whereIn($field, $arguments);
        return $obj;
    }

    public static function _find($parameter)
    {
        $sql = 'SELECT * FROM '.self::table();
        $sql .= ' WHERE '.self::id();
        $sql .= " = {$parameter} ;";

        if (self::$connection) {
            $result = self::$connection->query($sql);

            if ($result) {

                $newObject = $result->fetchObject(get_called_class());
            }

            return $newObject;
        } else {
            throw new Exception("Não há conexão com Banco de dados!");
        }
    }

    public function delete()
    {
        if (isset($this->content[$this->idField])) {

            $sql = "DELETE FROM {$this->table} WHERE {$this->idField} = {$this->content[$this->idField]};";

            if (self::$connection) {
                return self::$connection->exec($sql);
            } else {
                throw new Exception("Não há conexão com Banco de dados!");
            }
        }
    }

    public static function all(string $filter = '', int $limit = 0, int $offset = 0)
    {

        $sql = 'SELECT * FROM '.self::table();
        $sql .= ($filter !== '') ? " WHERE {$filter}" : "";
        $sql .= ($limit > 0) ? " LIMIT {$limit}" : "";
        $sql .= ($offset > 0) ? " OFFSET {$offset}" : "";
        $sql .= ';';

        if (self::$connection) {
            $result = self::$connection->query($sql);
            return $result->fetchAll(\PDO::FETCH_CLASS, get_called_class());
        } else {
            throw new Exception("Não há conexão com Banco de dados!");
        }
    }

    public static function count(string $fieldName = '*', string $filter = '')
    {
        $sql = "SELECT count($fieldName) as t FROM ".self::table();
        $sql .= ($filter !== '') ? " WHERE {$filter}" : "";
        $sql .= ';';
        if (self::$connection) {
            $q = self::$connection->prepare($sql);
            $q->execute();
            $a = $q->fetch(\PDO::FETCH_ASSOC);
            return (int) $a['t'];
        } else {
            throw new Exception("Não há conexão com Banco de dados!");
        }
    }

    public static function findFisrt(string $filter = '')
    {
        return self::all($filter, 1);
    }

    public static function setConnection(\PDO $connection)
    {
        self::$connection = $connection;
    }

    public function __call($name, $arguments)
    {

        if ($name === 'where') {
            $obj = get_class();
            $obj = new $obj;
            return call_user_func_array(array($obj, '_where'), $arguments);
        }

        if ($name === 'whereIn') {
            $obj = get_class();
            $obj = new $obj;
            return call_user_func_array(array($obj, '_whereIn'), $arguments);
        }

        if ($name === 'destroy') {
            $obj = get_class();
            return $obj::find($arguments[0])->delete();
        }

        if ($name === 'find') {
            $obj = get_class();
            return $obj::_find($arguments[0]);
        }

        if ($name === 'create') {

            $data = $arguments[0];
            if (!is_array($data)) {
                $data = (array) $data;
                unset($data[self::id()]);
            }
            $obj = get_class();
            $obj = new $obj;
            $obj->fromArray($data);
            $obj->save();
            return $obj;
        }
    }

    public static function __callStatic($name, $arguments)
    {

        if ($name === 'where') {

            $obj = new static;
            return call_user_func_array(array($obj, '_where'), $arguments);
        }

        if ($name === 'whereIn') {

            $obj = new static;
            return call_user_func_array(array($obj, '_whereIn'), $arguments);
        }

        if ($name === 'destroy') {

            return self::find($arguments[0])->delete();
        }

        if ($name === 'find') {

            return self::_find($arguments[0]);
        }

        if ($name === 'create') {

            $data = $arguments[0];
            if (!is_array($data)) {
                $data = (array) $data;
                unset($data[self::id()]);
            }
            $obj = new static;
            $obj->fromArray($data);
            $obj->save();
            return $obj;
        }
    }

    protected static function table()
    {
        return (new static)->table;
    }

    protected static function id()
    {
        return (new static)->idField;
    }
}