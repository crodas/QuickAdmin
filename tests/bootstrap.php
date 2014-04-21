<?php

require __DIR__ . "/../vendor/autoload.php";

function get_conn() {
    static $xxx;
    if (!empty($xxx)) return $xxx;
    $conn = new \MongoClient;
    $conn->dropDB('demo44');

    $db = $conn->selectDB('demo44');


    $conf = new \ActiveMongo2\Configuration(
        __DIR__  . "/tmp/foobar.php"
    );
    $conf->addModelPath(__DIR__);
    $conf->development();

   return $xxx = new \ActiveMongo2\Connection($conf, $conn, $db);
}

//$admin = new \crodas\QuickAdmin\QuickAdmin($mongo->getReflection('foobar'), $mongo);
//$admin->handleUpdate($mongo->foobar->findOne(), ['email'=> 'xxx@far.com'],  '/foo');
//$admin->handleCreate(array('email' => 'saddor@gmail.com', 'first_name' => 'foobar', 'foobar' => "<br>cesa\n\ncesar<br>"), '/foo');
