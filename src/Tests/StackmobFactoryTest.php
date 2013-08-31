<?php

/**
* Incomplete. This should implement PHP Unit testing.
*/

require '../../vendor/autoload.php';

use Stackmob\Configuration;
use Stackmob\User;
use Stackmob\Object;
use Stackmob\Query;

Configuration::setKey();
Configuration::setSecret();
Configuration::setEnvironment();

$o = new Object('product', array('column_name' => 'id'));
$o->fetch();
var_dump($o);

$user = new User(array('username' => 'user_name'));
$user->fetch();
var_dump($user);

$query = new Query('product');
$query->isEqual('field_name','value');
$results = $query->find();
$count = count($results);
var_dump($count);
