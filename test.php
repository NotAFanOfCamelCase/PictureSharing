<?php

define('appcore\APP_CONFIG', 'myAppConfig');
require_once realpath(__DIR__) . '/app/autoload.php';

$auth	= new Authentication();
//$auth->createUser('granados.carlos91@gmail.com', 'tespassword7');
//$auth->authenticate('granados.carlos91@gmail.com', 'tespassword7');
//var_dump($auth->updateEmail('carlos@launch3.net', 'granados.carlos91@gmail.com'));

//$auth->revokeAdmin(1);