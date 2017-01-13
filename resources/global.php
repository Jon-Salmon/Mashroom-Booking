<?php


/*
    Error reporting.
*/

function shutdown()
{
    // This is our shutdown function, in 
    // here we can do any last operations
    // before the script is complete.

    $errfile = "unknown file";
    $errstr  = "shutdown";
    $errno   = E_CORE_ERROR;
    $errline = 0;

    $error = error_get_last();

    if( $error !== NULL) {
        $errno   = $error["type"];
        $errfile = $error["file"];
        $errline = $error["line"];
        $errstr  = $error["message"];

        ob_end_clean();

        require(TEMPLATES_PATH . "/error.php");

        $errorMessage = date("Y-m-d h:i:sa") . ": " . $error["message"];
        
        global $log;
        $log->error($errorMessage);
        
        die();
    }
}


require_once('config.php');
 
ob_start();
/*
    Creating constants for heavily used paths makes things a lot easier.
    ex. require_once(LIBRARY_PATH . "Paginator.php")
*/
defined("LIBRARY_PATH")
    or define("LIBRARY_PATH", realpath(dirname(__FILE__) . '/library'));
     
defined("TEMPLATES_PATH")
    or define("TEMPLATES_PATH", realpath(dirname(__FILE__) . '/templates'));
 
defined("CLASSES_PATH")
    or define("CLASSES_PATH", realpath(dirname(__FILE__) . '/classes'));
    

require_once(LIBRARY_PATH . "/vendor/autoload.php");

use Monolog\Logger;
use Monolog\Handler\StreamHandler;


date_default_timezone_set('Europe/London');
$log = new Logger('Mash');
$log->pushHandler(new StreamHandler(dirname(__FILE__) . '/errors.log', Logger::WARNING));


$host = $config["db"]["db1"]["host"];
$db = $config["db"]["db1"]["dbname"];
$user = $config["db"]["db1"]["username"];
$pass = $config["db"]["db1"]["password"];
$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = array(
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
);
$PDO = new PDO($dsn, $user, $pass, $opt);

$host = $config["db"]["users"]["host"];
$db = $config["db"]["users"]["dbname"];
$user = $config["db"]["users"]["username"];
$pass = $config["db"]["users"]["password"];
$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = array(
PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
PDO::ATTR_EMULATE_PREPARES   => false,
);

$user_db = new PDO($dsn, $user, $pass, $opt);
/*
    Standard includes and Global variables
*/

require_once(LIBRARY_PATH . "/common.php");
require_once(CLASSES_PATH . "/user.php");
require_once(CLASSES_PATH . "/admins.php");

$ADMINS = new Admins();
$USER = new User($_ENV["REMOTE_USER"]);

?>