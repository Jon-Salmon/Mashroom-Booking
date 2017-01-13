<?php

require_once("../../resources/globalNoUser.php");

$start = $_GET["start"];
$end = $_GET["end"];

$stmt = $PDO->prepare("SELECT id, title, start, end, description, band, details FROM calendar WHERE end > :start && start < :end && deleted = 0 ORDER BY id");
$stmt->execute(array(':start' => $start, ':end' => $end));
$result = $stmt->fetchAll();
            
 echo json_encode($result);

?>
