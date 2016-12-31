<?php

require_once("../../resources/config.php");
require_once(CLASSES_PATH . "/events.php");


if(isset($_POST['action']) && !empty($_POST['action'])) {
    if ($_POST['action'] == 'add'){
        $data = json_decode($_POST['data']);

        $event = new Event($PDO);
        $event->checkBand($data->title);
        $event->start = new DateTime($data->start);
        $event->end = new DateTime($data->end);
        $event->checkDetails(""); 

        echo $event->createEvent();
    } elseif ($_POST['action'] == 'delete'){
        $id = json_decode($_POST['data']);
        $event = new Event($PDO);
        echo $event->deleteEvent($id, TRUE);
    }


    }
?>