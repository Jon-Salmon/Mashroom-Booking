<?php
class User {
    public $surname;
    public $fullName;
    public $title;
    public $college;
    public $username;
    public $email;

    function __construct($userID){
        global $config;
        require_once(LIBRARY_PATH . "/meekrodb.2.3.class.php");

        $db = new MeekroDB($config["db"]["users"]["host"], $config["db"]["users"]["username"], $config["db"]["users"]["password"], $config["db"]["users"]["dbname"]);
        $result = $db->query("SELECT surname, firstnames, title, college, email FROM UserDetails WHERE username = %s", $userID);
        
        $this->surname = $result[0]['surname'];
        $this->title = $result[0]['title'];
        $this->college = $result[0]['college'];
        $this->email = $result[0]['email'];
        
        $this->username = $userID;
        $this->fullName = explode(',',$result[0]['firstnames'])[0] . " " . $result[0]['surname'];
    }
}
?>