<?php

error_reporting(E_ALL);

date_default_timezone_set('Europe/London');

require '../vendor/autoload.php';

use models\User as User;

$user = User::create();

var_dump($user);

$user->username = "sno";
$user->email = "sno@sno.com";
$user->country_code = "HU";

var_dump($user);

$user->save();

var_dump($user);

die();