<?php

namespace core;

use core\DB as DB;

abstract class Model {

    const DEFAULT_UNIQUE_KEY = 'id';

    public static function getTableName()
    {
        $Model = (new \ReflectionClass(static::class));
        return $Model->hasConstant('DB_TABLE') ? $Model->getConstant('DB_TABLE') : strtolower($Model->getShortName());
    }

    public static function getUniqueKey()
    {
        $Model = (new \ReflectionClass(static::class));
        return $Model->hasConstant('UNIQUE_KEY') ? $Model->getConstant('UNIQUE_KEY') : self::DEFAULT_UNIQUE_KEY;
    }

    public function __set($name, $value)
    {
        $this->props[$name] = $value;
        if(!in_array($name, $this->changeLog))
        {
            $this->changeLog[] = $name;
        }
    }

    public function __get($name)
    {
        if(array_key_exists($this->props[$name]))
        {
            return $this->props[$name];
        }
    }

    public function changed()
    {
        return (count($this->changeLog) > 0) ? true : false;
    }

    public function commitChanges()
    {
        $this->changeLog = [];
    }

    public static function find($id){

        $query = DB::instance()->query("SELECT * FROM " .self::getTableName()." WHERE ".self::getUniqueKey()." = ".$id." LIMIT 1");

        $model = new static();

        $result = $query->fetch(\PDO::FETCH_OBJ);

        if(!$result) return false;

        foreach($result as $property => $value){
            $model->$property = $value;
        }
        return $model;
    }

    public static function create($properties = []){

        $model = new static();

        foreach($properties as $property => $value)
        {
            $model->$property = $value;
        }
        return $model;
    }

    public function getChangedProps(){

        $changedProps = [];

        foreach($this->changeLog as $propName)
        {
            $changedProps[$propName] = $this->props[$propName];
        }
        return $changedProps;
    }

    public function hasId(){
        return array_key_exists(self::getUniqueKey(), $this->props);
    }

    public function save()
    {
        if($this->changed())
        {
            if($this->hasId())
            {
                $model_id = DB::instance()->update(self::getTableName(), $this->getChangedProps(), self::getUniqueKey());
            }
            else
            {
                $model_id = DB::instance()->insert(self::getTableName(), $this->getChangedProps());
            }

            if($model_id)
            {
                if(!array_key_exists(self::getUniqueKey(), $this->props))
                {
                    $this->props[self::getUniqueKey()] = $model_id;
                }
                $this->commitChanges();
                return $model_id;
            }
            return false;
        }
    }

    public static function activate()
    {

    }

    public static function deactivate()
    {

    }

    public static function get($params = [], $limit = null, $order = []){

        $where_str = "";

        if(!empty($params)){

            $where_str .= "WHERE ";

            foreach($params as $param){
                $where_str .=  $param . " AND ";
            }
            $where_str = substr($where_str, 0, -4);
        }

        $order_str = "";

        if(!empty($order)){

            $order_str .= "ORDER BY ";

            foreach($order as $order_item){
                $order_str .=  $order_item . ", ";
            }
            $order_str = substr($order_str, 0, -2);
        }

        $limit_str = isset($limit) ? " LIMIT " . $limit : "";

        $sql = DB::instance()->query("SELECT * FROM ".static::DB_TABLE." {$where_str} {$order_str} {$limit_str}");

        if($limit == 1){
            $result = $sql->fetch(\PDO::FETCH_ASSOC);
        } else {
            $result = $sql->fetchAll(\PDO::FETCH_ASSOC);
        }
        return $result;
    }

}