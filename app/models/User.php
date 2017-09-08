<?php

namespace models;

use core\Model as Model;

class User extends Model
{
    use UserValidation;

    public function __construct()
    {
        var_dump(User::getRulesFor('username'));
    }

    public static function auth()
    {

    }

    public static function login()
    {

    }

    public static function verify_password($password, $prediction)
    {
        //return password_verify($prediction, $password);
    }

}