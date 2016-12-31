<?php

require_once("../../resources/config.php");

$start = $_GET["start"];
$end = $_GET["end"];

$stmt = $PDO->prepare("SELECT id, title, start, end, description, band, details, CASE WHEN owner = :user THEN 1 ELSE 0 END AS editAllowed FROM calendar WHERE end > :start && start < :end && deleted = 0 ORDER BY id");
$stmt->execute([':start' => $start, ':end' => $end, ':user' => $USER->username]);
$result = $stmt->fetchAll();
            
#$result = $DB->query("SELECT id, band as title, start, end, details AS description FROM calendar WHERE end > %s && start < %s && deleted = 0 ORDER BY id", $start, $end);

/// List of events

 // Query that retrieves events

 // sending the encoded result to success page
 echo json_encode($result);

?>