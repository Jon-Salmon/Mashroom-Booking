<?php

require_once("../../resources/config.php");
require_once(CLASSES_PATH . "/events.php");


if(isset($_POST['action']) && !empty($_POST['action'])) {
    if ($_POST['action'] == 'add'){
        $data = json_decode($_POST['data']);

        $event = new Event($PDO);
        
        $error = $event->checkStart($data->start);
        if ($error != ""){
            echo json_encode([FALSE, $error]);
            exit();
        }
        
        $error = $event->checkEnd($data->end);
        if ($error != ""){
            echo json_encode([FALSE, $error]);
            exit();
        }
        
        $error = $event->checkBand($data->title);
        if ($error != ""){
            echo json_encode([FALSE, $error]);
            exit();
        }
        
        $error = $event->checkDetails($data->details);
        if ($error != ""){
            echo json_encode([FALSE, $error]);
            exit();
        }

        echo json_encode($event->createEvent());
        exit();
        
    } elseif ($_POST['action'] == 'delete'){
        $id = json_decode($_POST['data']);
        $event = new Event($PDO);
        echo $event->deleteEvent($id, TRUE);
        exit();
    } elseif ($_POST['action'] == 'change'){
        $data = json_decode($_POST['data']);

        $event = new Event($PDO);
        
        $error = $event->checkStart($data->start);
        if ($error != ""){
            echo json_encode([FALSE, $error]);
            exit();
        }
        
        $error = $event->checkEnd($data->end);
        if ($error != ""){
            echo json_encode([FALSE, $error]);
            exit();
        }
        
        $error = $event->checkBand($data->title);
        if ($error != ""){
            echo json_encode([FALSE, $error]);
            exit();
        }
        
        $error = $event->checkDetails($data->details);
        if ($error != ""){
            echo json_encode([FALSE, $error]);
            exit();
        }

        echo json_encode($event->editEvent($data->id));
        exit();
    
    } else {
        echo FALSE;
    }

    }
?>