<?php
class AddEvent {
    private $start;
    private $end;
    private $google_ID;
    private $details;
    private $band;
    
    private $date;
    

    function __construct(){
        require_once(LIBRARY_PATH . "/meekrodb.2.3.class.php");
        require_once(LIBRARY_PATH . "/google/vendor/autoload.php");
    }
    

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
        }
        return(array($end, $endErr));
    }

    public function checkBand($band){
        if (strlen($band) > 255){
            $bandErr = 'Too long';
        }
        else {
            $this->band = $band;
        }
        return (array($band, $bandErr));
    }

    public function checkDetails($details){
        $bandErr;
        $this->details = $details;
        return (array($band, $bandErr));
    }

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

        $calendarId = 'trevs.mashroom@gmail.com';

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
           $created = $this->_addToCal();
           if (empty($this->google_ID)) {return FALSE;}
           $this->_addToDB($created);
       }
       return TRUE;
    }

}
?>