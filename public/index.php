<?php

error_reporting(E_ALL);

date_default_timezone_set('Europe/London');

require '../vendor/autoload.php';

use models\User as User;

$user = User::create();

$user->name = "sno";
$user->email = "sno@sno.com";
$user->country_id = "HU";
$user->password = 'asd';
$user->created_at = date('Y-m-d H:i:s');
$user->updated_at = date('Y-m-d H:i:s');

echo "before save";
var_dump($user);
echo "save";
var_dump($user->save());
echo "after save";
var_dump($user);
$user->email = "sno2@sno.hu";
echo "after update";
var_dump($user->save());

die();