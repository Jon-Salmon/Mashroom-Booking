<?php
 
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
    
require_once('root.php');
    
/*
    The important thing to realize is that the config file should be included in every
    page of your project, or at least any page you want access to these settings.
    This allows you to confidently use these settings throughout a project because
    if something changes such as your database credentials, or a path to a specific resource,
    you'll only need to update it here.
*/
 
$config = array(
    "db" => array(
        "db1" => array(
            "dbname" => "mash",
            "username" => "mash",
            "password" => "u3XxS7QQ8QhEhB2E5kGhPtqVgqFW",
            "host" => "jonny.duckdns.org"
        ),
        "users" => array(
            "dbname" => "user_dets",
            "username" => "mash",
            "password" => "u3XxS7QQ8QhEhB2E5kGhPtqVgqFW",
            "host" => "jonny.duckdns.org"
        )
    ),
    "email" => array(
        "host" => "smtp.gmail.com",
        "port" => 465,
        "username" => "trevs.mashroom@gmail.com",
        "password" => "thecakeisalie"
    )
);
 
 
require_once(LIBRARY_PATH . "/vendor/autoload.php");

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

ob_start();

$log = new Logger('Mash');
$log->pushHandler(new StreamHandler(dirname(__FILE__) . '/errors.log', Logger::WARNING));


$host = $config["db"]["db1"]["host"];
$db = $config["db"]["db1"]["dbname"];
$user = $config["db"]["db1"]["username"];
$pass = $config["db"]["db1"]["password"];
$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$PDO = new PDO($dsn, $user, $pass, $opt);

$host = $config["db"]["users"]["host"];
$db = $config["db"]["users"]["dbname"];
$user = $config["db"]["users"]["username"];
$pass = $config["db"]["users"]["password"];
$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
PDO::ATTR_EMULATE_PREPARES   => false,
];
$user_db = new PDO($dsn, $user, $pass, $opt);
/*
    Standard includes and Global variables
*/
$_ENV["REMOTE_USER"]="nwng84";
#$_ENV["REMOTE_USER"]="bfmm57";

require_once(LIBRARY_PATH . "/common.php");
require_once(CLASSES_PATH . "/user.php");
require_once(CLASSES_PATH . "/admins.php");

$ADMINS = new Admins();
$USER = new User($_ENV["REMOTE_USER"]);


date_default_timezone_set('Europe/London');

/*
    Error reporting.
*/
ini_set("error_reporting", "true");
#ini_set('display_errors', 'Off');
error_reporting(E_ALL|E_STRCT);
#eror_reporting(0);
?>