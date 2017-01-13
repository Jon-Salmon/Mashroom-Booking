<?php

#ini_set("error_reporting", "true");
ini_set('display_errors', '0');
error_reporting(E_ALL|E_STRICT);
#eror_reporting(0);
register_shutdown_function('shutdown');
set_error_handler('shutdown');

defined("HTTP_ROOT")
    or define("HTTP_ROOT", '/dev/public_html/password/mash/');
defined("OPEN_ROOT")
    or define("OPEN_ROOT", '/dev/public_html/mash/');


    
$config = array(
    "db" => array(
        "db1" => array(
            "dbname" => "mash",
            "username" => "mash",
            "password" => "u3XxS7QQ8QhEhB2E5kGhPtqVgqFW",
            "host" => "localhost"
        ),
        "users" => array(
            "dbname" => "user_dets",
            "username" => "mash",
            "password" => "u3XxS7QQ8QhEhB2E5kGhPtqVgqFW",
            "host" => "localhost"
        )
    ),
    "email" => array(
        "host" => "smtp.gmail.com",
        "port" => 465,
        "username" => "trevs.mashroom@gmail.com",
        "password" => "thecakeisalie"
    )
);

$_ENV["REMOTE_USER"]="nwng84";
#$_ENV["REMOTE_USER"]="bfmm57";
 
 ?>