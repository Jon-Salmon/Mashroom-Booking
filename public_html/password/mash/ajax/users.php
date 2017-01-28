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
        
    } else {
        echo FALSE;
    }

    }
?>