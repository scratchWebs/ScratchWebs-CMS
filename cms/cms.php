<?php session_start();
// This file should be required_once on every document which needs to use the CMS

// database version
define("DB_VERSION",1.1);  

// set timezone
date_default_timezone_set("Europe/London");

// define some global constants
define('PATH_CORE', dirname(__FILE__) . '/core/');
define('PATH_CONTROLS', dirname(__FILE__) . '/controls/');
define('PATH_CMS', dirname(__FILE__) . '/');

//error reporting options (set to 0 in a live enviroment)
error_reporting(0);
// error_reporting(-1);
//error_reporting(E_ERROR | E_WARNING | E_PARSE);
//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

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