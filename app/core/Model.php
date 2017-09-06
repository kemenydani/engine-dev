<?php

namespace core;

use core\DB as DB;

abstract class Model {

    public static function find($id){

        $query = DB::instance()->query("SELECT * FROM " .static::DB_TABLE." WHERE ".static::UNIQUE_KEY." = ".$id." LIMIT 1");

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

        foreach($properties as $property => $value){
            $model->$property = $value;
        }
        return $model;
    }

    public function save(){

        if(property_exists($this, static::UNIQUE_KEY)){
            $model_id = DB::instance()->update(static::DB_TABLE, $this, static::UNIQUE_KEY);
        } else {
            $model_id = DB::instance()->insert(static::DB_TABLE, $this);
        }

        if($model_id){

            $new_model_params = self::find($model_id);

            foreach($new_model_params as $param => $value){
                $this->$param = $value;
            }
            return $model_id;
        }
        return false;
    }

    public static function activate(){

    }

    public static function deactivate(){

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