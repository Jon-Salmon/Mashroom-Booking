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
        global $config, $PDO;

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

        #$user_db = new MeekroDB($config["db"]["users"]["host"], $config["db"]["users"]["username"], $config["db"]["users"]["password"], $config["db"]["users"]["dbname"]);
        #$result = $user_db->query("SELECT surname, firstnames, title, college, email FROM UserDetails WHERE username = %s", $userID);

        $stmt = $user_db->prepare('SELECT surname, firstnames, title, college, email FROM UserDetails WHERE username = ?');
        $stmt->execute([$userID]);
        $result = $stmt->fetch();
        
        $this->surname = $result['surname'];
        $this->title = $result['title'];
        $this->college = $result['college'];
        $this->email = $result['email'];
        
        $this->username = $userID;
        $this->fullName = ucwords(strtolower(explode(',',$result['firstnames'])[0] . " " . $result['surname']));


        $stmt = $PDO->prepare('SELECT CASE WHEN (SELECT count(*) FROM users where user = ? && (admin = 1 || techManager = 1 || mashManager = 1 || webmaster = 1)) = 0 THEN False ELSE True END AS admin;');
        $stmt->execute([$userID]);
        $this->admin = $stmt->fetch()['admin'];

        #$this->admin = $DB->queryOneField("admin","SELECT CASE WHEN (SELECT count(*) FROM admins where user = %s && (admin = 1 || techManager = 1 || mashManager = 1 || webmaster = 1)) = 0 THEN False ELSE True END AS admin;", $userID);
    }
}
?>