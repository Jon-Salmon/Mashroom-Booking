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
    ),
    "keyFile" => LIBRARY_PATH . '/../service-account-credentials.json',
    "calendarID" => 'trevs.mashroom@gmail.com',
    "urls" => array(
        "baseUrl" => "http://example.com"
    ),
    "paths" => array(
        "resources" => "/path/to/resources",
        "images" => array(
            "content" => $_SERVER["DOCUMENT_ROOT"] . "/images/content",
            "layout" => $_SERVER["DOCUMENT_ROOT"] . "/images/layout"
        )
    )
);
 
 
require_once(LIBRARY_PATH . "/meekrodb.2.3.class.php");
require_once(LIBRARY_PATH . "/vendor/autoload.php");

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$log = new Logger('Mash');
$log->pushHandler(new StreamHandler(dirname(__FILE__) . '/errors.log', Logger::WARNING));


$DB = new MeekroDB($config["db"]["db1"]["host"], $config["db"]["db1"]["username"], $config["db"]["db1"]["password"], $config["db"]["db1"]["dbname"]);

/*
    Standard includes and Global variables
*/
$_ENV["REMOTE_USER"]="nwng84";
#$_ENV["REMOTE_USER"]="bfmm57";

require_once(LIBRARY_PATH . "/common.php");
require_once(CLASSES_PATH . "/user.php");

$USER = new User($_ENV["REMOTE_USER"]);

date_default_timezone_set('Europe/London');

/*
    Error reporting.
*/
ini_set("error_reporting", "true");
error_reporting(E_ALL|E_STRCT);
?>