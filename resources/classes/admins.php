<?php

class position { 
    public $name;
    public $email;

    function __construct($name, $email){
        $this->name = $name;
        $this->email = $email;
    }
}

class Admins {

    public $mash;
    public $tech;
    public $web;

    function __construct(){
        global $PDO;
        $result = $PDO->query('SELECT name, email, techManager, mashManager, webmaster FROM users WHERE (techManager = 1 || mashManager = 1 || webmaster = 1);');

        foreach ($result as $row){
            $name = $row['name'];
            $email = $row['email'];
            if ($row['techManager'] == '1'){
                $this->tech = new position($name, $email);
            }
            if ($row['mashManager'] == '1'){
                $this->mash = new position($name, $email);
            }
            if ($row['webmaster'] == '1'){
                $this->web = new position($name, $email);
            }
        }
    }

}
?>