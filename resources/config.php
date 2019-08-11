<?php

ini_set('display_errors', '0');
error_reporting(E_ALL|E_STRICT);
#eror_reporting(0);
register_shutdown_function('shutdown');
set_error_handler('shutdown');

defined("HTTP_ROOT")
    or define("HTTP_ROOT", '/trevelyan.jcr/password/mash/');
defined("OPEN_ROOT")
    or define("OPEN_ROOT", '/trevelyan.jcr/mash/');

$config = array(
    "db" => array(
        "db1" => array(
            "dbname" => "",
            "username" => "",
            "password" => "",
            "host" => ""
        ),
        "users" => array(
            "dbname" => "",
            "username" => "",
            "password" => "",
            "host" => ""
        )
    ),
    "email" => array(
        "host" => "smtp.gmail.com",
        "port" => 465,
        "username" => "",
        "password" => ""
    )
);    

?>