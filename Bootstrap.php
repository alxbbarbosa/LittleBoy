<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

use \Abbarbosa\LittleBoy\Framework\Router;

try {
    require __DIR__."/vendor/autoload.php";
    session_start();
    $route = router();
    include __DIR__."/routes/routes.php";
    \Abbarbosa\LittleBoy\Framework\WebCache::enable(false);
    $conn = \Abbarbosa\LittleBoy\Framework\Connection::getInstance('database');
    \Abbarbosa\LittleBoy\Framework\Model::setConnection($conn);
    
} catch (\Exception $e) {
    echo $e->getMessage();
}

