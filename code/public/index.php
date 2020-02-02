<?php
/**
 * Index file
 *
 *  Here happens the initialization and the routing of each page
 */

require_once(__DIR__ . '/..//Lib/Core/Autoloader.php');

session_start();

if(empty($_REQUEST)) {
    $controllerName = 'startGame';
} else {
    $controllerName = $_REQUEST['controller'];
}

$controllerName = 'Lib\Controllers\\' . $controllerName;
$controller = new   $controllerName();
$templateParams = $controller->action();


include 'templates/mainTemplate.php';
