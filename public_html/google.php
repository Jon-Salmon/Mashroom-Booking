<?php

// Load the Google API PHP Client Library.
require_once __DIR__ . '/../resources/library/google/vendor/autoload.php';

// Use the developers console and download your service account
// credentials in JSON format. Place them in this directory or
// change the key file location if necessary.
$KEY_FILE_LOCATION = __DIR__ . '/../resources/service-account-credentials.json';

// Create and configure a new client object.
$client = new Google_Client();
$client->setApplicationName("Mash Booking");
$client->setAuthConfig($KEY_FILE_LOCATION);
$client->setScopes(['https://www.googleapis.com/auth/calendar']);
$service = new Google_Service_Calendar($client);

$calendarId = 'trevs.mashroom@gmail.com';
$optParams = array(
  'maxResults' => 10,
  'orderBy' => 'startTime',
  'singleEvents' => TRUE,
  'timeMin' => date('c'),
);

$event = new Google_Service_Calendar_Event(array(
  'summary' => 'Google I/O 2015',
  'location' => '800 Howard St., San Francisco, CA 94103',
  'description' => 'A chance to hear more about Google\'s developer products.',
  'start' => array(
    'dateTime' => '2016-12-28T09:00:00Z',
    'timeZone' => 'Europe/London',
  ),
  'end' => array(
    'dateTime' => '2016-12-28T17:00:00Z',
    'timeZone' => 'Europe/London',
  ),
  'reminders' => array(
    'useDefault' => FALSE,
    'overrides' => array(
      array('method' => 'email', 'minutes' => 24 * 60),
      array('method' => 'popup', 'minutes' => 10),
    ),
  ),
));

$event = $service->events->insert($calendarId, $event);


$results = $service->events->listEvents($calendarId, $optParams);

if (count($results->getItems()) == 0) {
  print "No upcoming events found.\n";
} else {
  print "Upcoming events:\n";
  foreach ($results->getItems() as $event) {
    $start = $event->start->dateTime;
    if (empty($start)) {
      $start = $event->start->date;
    }
    printf("%s (%s)\n", $event->getSummary(), $start);
  }
}
?>