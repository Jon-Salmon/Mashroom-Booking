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
    public $inducted;

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

            # Alows admin bookings to be unrestricted in time scale
            global $GLOBALS;
            $GLOBALS["max_daily_time"] = '1440';

        } else {
            $this->admin = FALSE;
        }
        
        $stmt = $PDO->prepare('select * from users where user = ?');
        $stmt->execute(array($userID));
        if ($stmt->rowCount() == 1){
            $this->inducted = TRUE;
        } else {
            $this->inducted = FALSE;
        }
        
        
    }
    
    function getAll($all = TRUE){
        global $PDO;
        if ($all){
            $stmt = $PDO->query('SELECT user, name, email, created FROM users ORDER BY name');
        } else {
            $stmt = $PDO->query('SELECT user, name, email, created FROM users WHERE staff = false ORDER BY name');
        }
        $results = $stmt->fetchAll();
        return $results;
    }

    function deleteUser($user){
        global $PDO;
        $error = "Ooops, something went wrong. Maybe try again?";
        $sucess = TRUE;
        $admins = new Admins;
        $data = $admins->checkAdmin($user);
        if ($data == 1){
            $error = "Cannot delete user with admin responsiblities. Remove the user from there position and try again.";
            $sucess = FALSE;
        }
        else {
            $stmt = $PDO->prepare('DELETE FROM users WHERE user = ?');
            $sucess = $stmt->execute(array($user));
        }
        return array($sucess, $error);
    }
    
    function addUser($email){
        global $user_db;
        global $PDO;
        $sucess = TRUE;
        $error = "";

        $stmt = $user_db->prepare('SELECT surname, firstnames, username, email, current_staff, current_student FROM UserDetails WHERE email = ?');
        $stmt->execute(array($email));
        $result = $stmt->fetch();

        if(!$result){
            $sucess = FALSE;
            $error = "User does not exist on the Durham System";
            return array($sucess, $error);
        }
        
        $temp = explode(',',$result['firstnames']);
        $name = ucwords(strtolower($temp[0] . " " . $result['surname']));
        $user = $result['username'];

        $staff = $result['current_staff'] && !($result['current_student']);


        try {
            $stmt = $PDO->prepare("INSERT INTO users(user, name, email, staff) VALUES(?, ?, ?, ?)");
            $result = $stmt->execute(array($result['username'], $name, $result['email'], $staff));
            $stmt = $PDO->prepare("DELETE FROM induction_requests WHERE user = ?");
            $temp = $stmt->execute(array($user));
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