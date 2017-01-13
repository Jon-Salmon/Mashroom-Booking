<?php

require_once("../../../../resources/global.php");

 require_once(CLASSES_PATH . "/events.php");
 $events = new Event($PDO);
 $bookings = $events->listEvents(FALSE);
            
 echo json_encode($bookings);

?>
