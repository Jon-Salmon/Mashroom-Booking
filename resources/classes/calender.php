<?php
class AddEvent {
    
    public $result;

    private $start;
    private $end;
    private $google_ID;
    private $details;
    

    function __construct(){
        require_once(LIBRARY_PATH . "/meekrodb.2.3.class.php");
        require_once(LIBRARY_PATH . "/google/vendor/autoload.php");

        $this->start = new DateTime();      
        $this->end = new DateTime();
        $this->end->setDate(2016, 12, 24);      
        $this->end->setTime(02, 55);
        $this->google_ID = 9397593483;
        $this->details = "Test details for writing to database.";
    }
    
    public function sqlQuery($query){
        setupDatabase(1);
    	$this->result = DB::query($query);
    }

    private function addToDB(){
        setupDatabase(1);
        $result = DB::insert('Calendar', array(
            'start' => $this->start,
            'end' => $this->end,
            'google_id' => $this->google_ID,
            'owner' => $_ENV["REMOTE_USER"],
            'details' => $this->details
        ));
        
        if ($result == 1) {return TRUE;}
        else {return FALSE;}
    }

    public function addToCal(){
      
        global $config;
        $client = new Google_Client();
        $client->setApplicationName("Mash Booking");
        $client->setAuthConfig($config['keyFile']);
        $client->setScopes(['https://www.googleapis.com/auth/calendar']);
        $service = new Google_Service_Calendar($client);

        $calendarId = 'trevs.mashroom@gmail.com';

        $calEvent = new Google_Service_Calendar_Event(array(
        'summary' => 'Booking:' . $_ENV["REMOTE_USER"],
        'description' => 'Booking: ' . $_ENV["REMOTE_USER"] . "\n" . $this->details .
        "\nCreated: " . date("d-m-Y H:i:s"),
        'start' => array(
            'dateTime' => $this->start->format(\DateTime::RFC3339),
            'timeZone' => 'Europe/London',
        ),
        'end' => array(
            'dateTime' => $this->end->format(\DateTime::RFC3339),
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

        $event = $service->events->insert($calendarId, $calEvent);
        print_r($event);
    }
}

?>