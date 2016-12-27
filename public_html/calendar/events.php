<?php

require_once("../../resources/config.php");

$start = $_GET["start"];
$end = $_GET["end"];

$result = $DB->query("SELECT id, band as title, start, end, details AS description FROM calendar WHERE end > %s && start < %s && deleted = 0 ORDER BY id", $start, $end);

/// List of events
 $json = array();

 // Query that retrieves events

 // sending the encoded result to success page
 echo json_encode($result);

?>