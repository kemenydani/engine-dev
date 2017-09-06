<?php

namespace Core;

class DB extends \PDO{
	
	public static $_instance;
	
	public static function instance(){
		if(self::$_instance === null){
            self::$_instance = new DB('mysql:host=localhost;dbname=app-dev', 'root', '');
			self::$_instance->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
			self::$_instance->exec("set names utf8");
		}
		return self::$_instance;
	}

	public static function insert($table, $params){

	    $params = (array)$params;

        $names = implode(',', array_keys($params));
        $questionMarks = join(",", array_pad([], count($params), "?"));

        $stmt = DB::instance()->prepare("INSERT INTO {$table} ({$names}) VALUES ({$questionMarks})");

        $current_bind = 1;

        foreach($params as $param => $value){
            $stmt->bindValue($current_bind, $value);
            $current_bind++;
        }
        if($stmt->execute()){
            return DB::instance()->lastInsertId();
        }
        return false;
    }

    public static function update($table, $params, $key){

        $params = (array)$params;
        $id = $params[$key];
        unset($params[$key]);

        $names = "";
        foreach($params as $param => $value){
            $names .= "`" . $param . "` = ?,";
        }
        $names = substr($names, 0, -1);

        $stmt = DB::instance()->prepare("UPDATE {$table} SET {$names} WHERE ".$key." = ? ");
        //TODO: update not executes
        $current_bind = 1;

        foreach($params as $param => $value){
            $stmt->bindValue($current_bind, $value);
            $current_bind++;
        }
        $stmt->bindValue($current_bind, $id);
        if($stmt->execute()){
            return $id;
        }
        return false;
    }

}