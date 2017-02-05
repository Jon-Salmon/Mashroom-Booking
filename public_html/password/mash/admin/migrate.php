<?php
    // load up your config file
    require_once("../../../../resources/global.php");

     
    if (!$USER->admin){
        header('Location: ../index.php');
        die();
    }


    global $USER;

    $all = $USER->getAll();

    foreach($all as $i){
        $stmt = $user_db->prepare('SELECT surname, firstnames FROM UserDetails WHERE username = ?');
        $stmt->execute(array($i['user']));
        $result = $stmt->fetch();

        $temp = explode(',',$result['firstnames']);
        $firstname = ucwords(strtolower($temp[0]));
        $surname = ucwords(strtolower($result['surname']));
        $stmt = $PDO->prepare("UPDATE users SET firstname=?, surname=? WHERE user = ?");
        $result = $stmt->execute(array($firstname, $surname, $i['user']));
        
    }




    
?>