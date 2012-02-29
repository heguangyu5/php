<?php
require_once 'UserController.php';


$db = new PDO('sqlite::memory:');
$db->exec(file_get_contents('schema.sql'));
$db->exec("insert into Users(username, email) values('何广宇', 'heguangyu5')");

Configuration::init(array('db' => $db));

$_POST['email'] = 'heguangyu5';

$controller = new UserController();
$view = $controller->resetPasswordAction();

print_r($view);
