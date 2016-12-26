<?php
class Event {
    private $start;
    private $end;
    private $google_ID;
    private $details;
    private $band;

    public $addErr = '';
    
    private $date;
    

    function __construct(){
        require_once(LIBRARY_PATH . "/meekrodb.2.3.class.php");
        require_once(LIBRARY_PATH . "/google/vendor/autoload.php");
    }
    
    # Functions to validate and set inputs for event creatition

    private function _validateDate($date)
    {
        $d = DateTime::createFromFormat('d/m/Y', $date);
        return $d && $d->format('d/m/Y') === $date;
    }

    public function checkDate($date){
        $dateErr = '';
        if (empty($date)){
            $dateErr = "Date is required";
        }
        elseif (!$this->_validateDate($date)){
            $dateErr = "Date is invalid";
        }
        else {
            $this->date = DateTime::createFromFormat('d/m/Y', $date);
        }
        return(array($date, $dateErr));
    }

    private function _checkTime($time){
        $timeErr = '';
        $cleanTime ='';
        if (empty($time)){
            $timeErr = "Time is required";
        }
        else {
            $cleanTime = DateTime::createFromFormat('g:ia', $time);
            if ($cleanTime == FALSE){
                $timeErr = "Time is invalid";
            }
        }
        return(array($cleanTime, $timeErr));
    }

    public function checkStart($start){
        list($time, $startErr) = $this->_checkTime($start);
        if ($time != FALSE){

            list($hour, $min) = explode(':', $time->format('H:i'));
            if ($this->date != FALSE){
                $this->start = clone $this->date;
                $this->start->setTime($hour, $min);
            }
        }
        return(array($start, $startErr));
    }

    public function checkEnd($end){
        list($time, $endErr) = $this->_checkTime($end);
        if ($time != FALSE){

            list($hour, $min) = explode(':', $time->format('H:i'));
            if ($this->date != FALSE){
                $this->end = clone $this->date;
                $this->end->setTime($hour, $min);
            }
            if ($this->end < $this->start) {
                $endErr = 'End time cannot be before start time';
            }
            if ($this->end < new DateTime()) {
                $endErr = 'Event is in the past';
            }
        }
        return(array($end, $endErr));
    }

    public function checkBand($band){
        $bandErr = '';
        if (strlen($band) > 255){
            $bandErr = 'Too long';
        }
        else {
            $this->band = $band;
        }
        return (array($band, $bandErr));
    }

    public function checkDetails($details){
        $detailsErr = '';
        $this->details = $details;
        return (array($details, $detailsErr));
    }

    # Event creation

    private function _addToDB($created){
        setupDatabase(1);
        $result = DB::insert('calendar', array(
            'start' => $this->start,
            'end' => $this->end,
            'google_id' => $this->google_ID,
            'owner' => $_ENV["REMOTE_USER"],
            'details' => $this->details,
            'band' => $this->band,
            'created' => $created
        ));
        
        if ($result == 1) {return TRUE;}
        else {return FALSE;}
    }

    private function _addToCal(){
        global $config, $USER;
        $client = new Google_Client();
        $client->setApplicationName("Mash Booking");
        $client->setAuthConfig($config['keyFile']);
        $client->setScopes(['https://www.googleapis.com/auth/calendar']);
        $service = new Google_Service_Calendar($client);

        $calendarId = $config['calendarID'];

        $calEvent = new Google_Service_Calendar_Event(array(
        'summary' => 'Booking: ' . ((empty($this->band)) ? ($USER->fullName):($this->band)),
        'description' => ((empty($this->band)) ? (""):("Booking: " . $this->band . "\n")) . 
            "Booked by: " . $USER->fullName . "\n\n" .
             $this->details . "\n\n" .
            "Created: " . date("d-m-Y H:i:s"),
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
        $this->google_ID = $event->id;
        return (new DateTime($event->created));
    }

    public function createEvent(){
       if(!isset($this->start) || !isset($this->end)) {
           return FALSE;
       }
       else {
            setupDatabase(1);
            $slotTaken = !empty(DB::query("SELECT * FROM calendar WHERE start < %t && end > %t && deleted = 0;", $this->end, $this->start));
            if ($slotTaken) {
                $this->addErr = 'Slot unavalible';
                return FALSE;
            }
            else{
                $created = $this->_addToCal();
                if (empty($this->google_ID)) {return FALSE;}
                $created = $this->_addToDB($created);
                if (!$created){
                    $this->_deleteDB($this->google_ID);
                }
            }
       }
       return TRUE;
    }

    # Event deletion

    private function _deleteDB($google_id){
        setupDatabase(1);
        $result = DB::query("UPDATE calendar SET deleted=1 WHERE google_id = %s", $google_id);
        return $result;
    }

    private function _deleteCal($google_id){
        global $config, $USER;
        try {
            $client = new Google_Client();
            $client->setApplicationName("Mash Booking");
            $client->setAuthConfig($config['keyFile']);
            $client->setScopes(['https://www.googleapis.com/auth/calendar']);
            $service = new Google_Service_Calendar($client);

            $calendarId = $config['calendarID'];

            $service->events->delete($calendarId, $google_id);
        }
        catch(Exception $e) {
            return FALSE;
        }
        return TRUE;
    }

    public function deleteEvent($google_id, $checkUser) {
        if ($checkUser) {
            global $USER;
            setupDatabase(1);
            if (DB::query("SELECT CASE WHEN (SELECT owner FROM calendar where google_id = %s && deleted = 0) = %s THEN False ELSE True END AS disallowed;", $google_id, $USER->username)[0]['disallowed']){
                return FALSE;
            }
        }
        $sucess = $this->_deleteCal($google_id);
        if ($sucess){
            $db = $this->_deleteDB($google_id);
            if (!$db){
                sleep(4);
                $this->_deleteDB($google_id);
            }
        }
        return TRUE;
    }

    # Event displaying
    
    public function listEvents() {
        global $USER;
        setupDatabase(1);
        $events = DB::query("SELECT start, end, band, details, google_id FROM calendar WHERE end >= NOW() and owner = %s and deleted = FALSE", $USER->username);
        return $events;
    }
    
}