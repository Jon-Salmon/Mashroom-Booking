<?php

require_once("../../../../resources/global.php");

if (!$USER->admin){
    die();
}

if(isset($_POST['action']) && !empty($_POST['action'])) {
    if ($_POST['action'] == 'delete'){
        $user = json_decode($_POST['data']);
        echo json_encode($USER->deleteUser($user));
        exit();
    } elseif ($_POST['action'] == 'add') {

        $email = json_decode($_POST['data']);

        $result = $USER->addUser($email);

        echo json_encode($result);

    } elseif ($_POST['action'] == 'view') {
        
        $users = $USER->getAll();
        echo json_encode($users);
        
    } elseif ($_POST['action'] == 'clean') {
        
        global $USER, $user_db;
        $users = $USER->getAll();
        foreach($users as $user){
            $stmt = $user_db->prepare('SELECT * FROM UserDetails WHERE username = ?');
            $stmt->execute(array($user['user']));
            $result = $stmt->fetch();
            if (empty($result)){
                $USER->deleteUser($user['user']);
            }
        }
        echo json_encode(TRUE);
    } else {
        echo FALSE;
    }

    }
?>