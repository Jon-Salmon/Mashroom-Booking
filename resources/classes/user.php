<?php
class User {
    public $surname;
    public $fullName;
    public $title;
    public $college;
    public $username;
    public $email;
    public $admin;
    public $role = array();

    function __construct($userID){
        global $config, $PDO, $user_db;

        $stmt = $user_db->prepare('SELECT surname, firstnames, title, college, email FROM UserDetails WHERE username = ?');
        $stmt->execute(array($userID));
        $result = $stmt->fetch();
        
        $this->surname = $result['surname'];
        $this->title = $result['title'];
        $this->college = $result['college'];
        $this->email = $result['email'];
        
        $this->username = $userID;
        $temp = explode(',',$result['firstnames']);
        $this->fullName = ucwords(strtolower($temp[0] . " " . $result['surname']));

        $stmt = $PDO->prepare('select admin, techmanager, mashmanager, webmaster from admins where user = ? && (admin = 1 || techmanager = 1 || mashmanager = 1 || webmaster = 1);');
        $stmt->execute(array($userID));
        if ($stmt->rowCount() > 0){
            $this->admin = TRUE;
            $result = $stmt->fetch();
            if ($result['mashmanager'] == '1'){
                $this->role[] = 'mash';
            }
            if ($result['techmanager'] == '1'){
                $this->role[] = 'tech';
            }
            if ($result['webmaster'] == '1'){
                $this->role[] = 'web';
            }
            $this->role[] = 'admin';
        } else {
            $this->admin = FALSE;
        }
        
    }
}
?>