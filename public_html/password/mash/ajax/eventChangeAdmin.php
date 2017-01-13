<?php

require_once("../../../../resources/global.php");
require_once(CLASSES_PATH . "/events.php");

if (!$USER->admin){
    header('Location: ../index.php');
    die();
}

if(isset($_POST['action']) && !empty($_POST['action'])) {
    if ($_POST['action'] == 'delete'){
        list($id, $message) = json_decode($_POST['data']);
        $event = new Event($PDO);
        echo $event->deleteEvent($id, FALSE, TRUE, $message);
        exit();
    } else {
        echo FALSE;
    }

    }
?>