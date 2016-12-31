<?php

class position { 
    public $name;
    public $email;
    public $title;
    public $id;

    function __construct($name = "", $email = "", $title = "", $id = 0){
        $this->name = $name;
        $this->email = $email;
        $this->title = $title;
        $this->id = $id;
    }

    function set($name, $email, $title, $id){
        $this->name = $name;
        $this->email = $email;
        $this->title = $title;
        $this->id = $id;
    }
}

class Admins {

    public $mash;
    public $tech;
    public $web;

    function __construct(){
        global $PDO;
        
        $this->mash = new position();
        $this->tech = new position();
        $this->web = new position();
        
        $result = $PDO->query('SELECT id, name, email, techManager, mashManager, webmaster FROM admins WHERE (techManager = 1 || mashManager = 1 || webmaster = 1);');

        foreach ($result as $row){
            $name = $row['name'];
            $email = $row['email'];
            $id = $row['id'];
            if ($row['techManager'] == '1'){
                $this->tech->set($name, $email, 'Technical Manager', $id);
            }
            if ($row['mashManager'] == '1'){
                $this->mash->set($name, $email, 'MASH Room Manager', $id);
            }
            if ($row['webmaster'] == '1'){
                $this->web->set($name, $email, 'Webmaster', $id);
            }
        }
    }

    function getAll(){
        global $PDO;
        $stmt = $PDO->query('SELECT id, name, email, techManager, mashManager, webmaster FROM admins WHERE (admin = 1 || techManager = 1 || mashManager = 1 || webmaster = 1);');
        $results = $stmt->fetchAll();
        return $results;
    }

    private function getById($id){
        global $PDO;
        global $USER;
        $stmt = $PDO->prepare('SELECT name, email, techManager, mashManager, webmaster, admin FROM admins WHERE id = ?;');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    function delete($id){
        global $PDO;
        $error = "Ooops, something went wrong. Maybe try again?";
        $sucess = TRUE;
        $data = $this->getById($id);
        if ($data['techManager'] == 1 || $data['mashManager'] == 1 || $data['webmaster'] == 1){
            $error = "Cannot delete user with position responsiblities. Remove the user from there position and try again.";
            $sucess = FALSE;
        }
        else {
            $stmt = $PDO->prepare('DELETE FROM admins WHERE id = ?');
            $sucess = $stmt->execute([$id]);
        }
        return array($sucess, $error);
    }

    function change($pos, $newID){
        global $PDO;
        $error = "Ooops, something went wrong. Maybe try again?";
        $sucess = TRUE;
        switch ($pos){
            case 'mash':
                $sqlPos = 'mashManager';
                break;
            case 'tech':
                $sqlPos = 'techManager';
                break;
            case 'web':
                $sqlPos = 'webmaster';
                break;
            default:
                $sucess = FALSE;
                $error = "Ooops, something went wrong. Maybe try again?";
                global $log;
                $log->error("Incorect format of $pos was passed to change() in class admins.php.");
                return array($sucess, $error);
        }
        $checkNew = $this->getById($newID);
        if ($checkNew['admin'] == 0){
            $sucess = FALSE;
            $error = "User is not admin";
        }
        else {
            $sql = "UPDATE `admins` SET `" . $sqlPos . "` = 0 WHERE `id` = :id";
            $stmt = $PDO->prepare($sql);
            $sucess = $stmt->execute(['id' => $this->{$pos}->id]);
            
            $sql = "UPDATE `admins` SET `" . $sqlPos . "` = 1 WHERE `id` = :id";
            $stmt = $PDO->prepare($sql);
            $sucess = $stmt->execute(['id' => $newID]);

            $this->__construct();
        }
        return array($sucess, $error);
    }

    function addAdmin($email){
        global $user_db;
        global $PDO;
        $sucess = TRUE;
        $error = "";

        $stmt = $user_db->prepare('SELECT surname, firstnames, username, email FROM UserDetails WHERE email = ?');
        $stmt->execute([$email]);
        $result = $stmt->fetch();

        if(!$result){
            $sucess = FALSE;
            $error = "User does not exist on the Durham System";
            return array($sucess, $error);
        }
        
        $name = ucwords(strtolower(explode(',',$result['firstnames'])[0] . " " . $result['surname']));

        try {
            $stmt = $PDO->prepare("INSERT INTO admins(user, name, email, admin) VALUES(?, ?, ?, 1)");
            $result = $stmt->execute([$result['username'], $name, $result['email']]);
        }
        catch (PDOException $e) {
            $sucess = FALSE;
            $code = $e->getCode();
            if( $code == "23000") {
                $error =  "This user already exists";
            } else {
                $error = "Oops! Something went wrong and we couldn't send your message.";
            }
        }
        return array($sucess, $error);
    }

}
?>