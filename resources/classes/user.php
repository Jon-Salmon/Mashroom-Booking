<?php
class User {
    public $surname;
    public $fullName;
    public $title;
    public $college;
    public $username;
    public $email;
    public $admin;

    function __construct($userID){
        global $config, $DB;

        $user_db = new MeekroDB($config["db"]["users"]["host"], $config["db"]["users"]["username"], $config["db"]["users"]["password"], $config["db"]["users"]["dbname"]);
        $result = $user_db->query("SELECT surname, firstnames, title, college, email FROM UserDetails WHERE username = %s", $userID);
        
        $this->surname = $result[0]['surname'];
        $this->title = $result[0]['title'];
        $this->college = $result[0]['college'];
        $this->email = $result[0]['email'];
        
        $this->username = $userID;
        $this->fullName = explode(',',$result[0]['firstnames'])[0] . " " . $result[0]['surname'];

        $this->admin = $DB->queryOneField("admin","SELECT CASE WHEN (SELECT count(*) FROM admins where user = %s && (admin = 1 || techManager = 1 || mashManager = 1 || webmaster = 1)) = 0 THEN False ELSE True END AS admin;", $userID);
    }
}
?>