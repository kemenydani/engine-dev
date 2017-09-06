<?php

namespace models;

use core\Model as Model;

class User extends Model
{

    const DB_TABLE = "_xyz_users";
    const UNIQUE_KEY = "id";

    public $props = [];
    public $changeLog = [];

    public static function auth()
    {

    }

    public static function login()
    {

    }

    public static function verify_password($password, $prediction)
    {
        return password_verify($prediction, $password);
    }

    public function __set($name, $value)
    {
        $this->setLog[$name] = $value;
    }

}