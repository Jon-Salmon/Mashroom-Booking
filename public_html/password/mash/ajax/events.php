<?php

require_once("../../../../resources/global.php");

$start = $_GET["start"];
$end = $_GET["end"];

$stmt = $PDO->prepare("SELECT id, title, start, end, description, band, details, CASE WHEN owner = :user THEN 1 ELSE 0 END AS editAllowed FROM calendar WHERE end > :start && start < :end && deleted = 0 ORDER BY id");
$stmt->execute(array(':start' => $start, ':end' => $end, ':user' => $USER->username));
$result = $stmt->fetchAll();
            
 echo json_encode($result);

?>
