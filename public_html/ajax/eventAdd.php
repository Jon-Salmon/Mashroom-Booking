<?php

require_once("../../resources/config.php");
require_once(CLASSES_PATH . "/events.php");

if(isset($_POST['action']) && !empty($_POST['action'])) {
    $data = json_decode($_POST['action']);

    $event = new Event($PDO);
    $event->checkBand($data->title);
    $event->start = new DateTime($data->start);
    $event->end = new DateTime($data->end);
    $event->checkDetails(""); 

    echo $event->createEvent();



    }
?>