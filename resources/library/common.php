<?php
function setupDatabase($database){
    global $config;
    if ($database == 1) {
        DB::$user = $config["db"]["db1"]["username"];
        DB::$password = $config["db"]["db1"]["password"];
        DB::$dbName = $config["db"]["db1"]["dbname"];
        DB::$host = $config["db"]["db1"]["host"];
    }
}