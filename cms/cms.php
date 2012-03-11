<?php session_start();
// This file should be required_once on every document which needs to use the CMS

error_reporting(0);
//error_reporting(-1);
//error_reporting(E_ERROR | E_WARNING | E_PARSE);
//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

define('PATH_CORE', dirname(__FILE__) . '/core/');
define('PATH_CONTROLS', dirname(__FILE__) . '/controls/');
define('PATH_CMS', dirname(__FILE__) . '/');

require_once("core/constants.php");
require_once("core/dbConnect.php");

// this function will automatically include class files as required
function __autoload($className) {
    if(file_exists(PATH_CORE . $className . '.php')) {
        require_once(PATH_CORE . $className . '.php');
    }
}

header("Content-type: text/html");
?>