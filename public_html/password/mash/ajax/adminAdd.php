<?php

require_once("../../../../resources/global.php");
require_once(CLASSES_PATH . "/admins.php");

if (!$USER->admin){
    header('Location: ../index.php');
    die();
}
    
if(isset($_POST['action']) && !empty($_POST['action'])) {
    $data = json_decode($_POST['action']);

    global $ADMINS;
    
    $result = $ADMINS->addAdmin($data);

    echo json_encode($result);

    }
    else {
        echo json_encode(array(FALSE, "No data provided."));
    }
?>