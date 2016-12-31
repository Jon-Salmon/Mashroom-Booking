<?php
class Event {
    protected $PDO;
    public $start;
    public $end;
    private $google_ID;
    private $details;
    private $band;
    private $title;
    private $description;

    public $addErr = '';
    
    private $date;
    

    function __construct($db){
        #require_once(LIBRARY_PATH . "/meekrodb.2.3.class.php");
        #require_once(LIBRARY_PATH . "/google/vendor/autoload.php");
        $this->PDO = $db;
    }
    
    # Functions to validate and set inputs for event creatition

    private function _validateTime($time)
    {
        return DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $time);
    }

    public function checkStart($start){
        $time = $this->_validateTime($start);
        $startErr = '';
        if ($time == FALSE){
            $startErr = 'No valid start time specified.';
        } else {
            $this->start = $time;
        }
        return $startErr;
    }

    public function checkEnd($end){
        $time = $this->_validateTime($end);
        $endErr = '';
        if ($time == FALSE){
            $endErr = 'No valid end time specified.';
        } else {
            if ($this->start->format('d/m/Y') == $time->format('d/m/Y')){
                if ($time < $this->start) {
                    $endErr = 'End time cannot be before start time';
                } elseif ($time < new DateTime()) {
                    $endErr = 'Event is in the past';
                } else {
                    $this->end = $time;
                }
            }
            else {
                $endErr = 'There is an problem with the times entered.';
            }
        }
        return $endErr;
    }

    public function checkBand($band){
        $bandErr = '';
        if (strlen($band) > 255){
            $bandErr = 'Title too long';
        }
        else {
            $this->band = $band;
        }
        return $bandErr;
    }

    public function checkDetails($details){
        $detailsErr = '';
        $this->details = $details;
        return $detailsErr;
    }

    # Event creation

    private function _addToDB(){
        global $USER;
        $stmt = $this->PDO->prepare("INSERT INTO calendar(start,end,owner,name,details,band,created,title,description) VALUES(:start,:end,:owner,:name,:details,:band,NOW(),:title,:description)");
        $stmt->execute(array(':start' => $this->start->format('Y-m-d H:i:s'), ':end' => $this->end->format('Y-m-d H:i:s'), ':owner' => $_ENV["REMOTE_USER"], ':name' => $USER->fullName, ':details' => $this->details, ':band' => $this->band, ':title' => $this->title, ':description' => $this->description));
        $result = $stmt->rowCount();

        #$result = $this->DB->insert('calendar', array(
        #    'start' => $this->start,
        #    'end' => $this->end,
        #    'owner' => $_ENV["REMOTE_USER"],
        #    'details' => $this->details,
        #    'band' => $this->band,
        #    'created' => new DateTime(),
        #    'title' => $this->title,
        #    'description' => $this->description
        #));
        
        if ($result == 1) {return TRUE;}
        else {
            global $log;
            $log->error($result);
            return FALSE;
            }
    }


    public function createEvent(){
        global $USER;
        $this->title = ((empty($this->band)) ? ($USER->fullName):($this->band));
        $this->description = ((empty($this->band)) ? (""):("Booking: " . $this->band . "\n")) . 
            "Booked by: " . $USER->fullName . "\n\n" .
             ((empty($this->details)) ? (""):($this->details . "\n\n")) .
            "Created: " . date("d-m-Y H:i:s");


       if(!isset($this->start) || !isset($this->end)) {
           return array(FALSE, "Times not both set");
       }
       else {

            $stmt = $this->PDO->prepare('SELECT * FROM calendar WHERE start < ? && end > ? && deleted = 0');
            $stmt->execute([$this->end->format('Y-m-d H:i:s'), $this->start->format('Y-m-d H:i:s')]);
            $slotTaken = !empty($stmt->fetch());

            #$slotTaken = !empty($this->DB->query("SELECT * FROM calendar WHERE start < %t && end > %t && deleted = 0;", $this->end, $this->start));
            if ($slotTaken) {
                return array(FALSE, "Slot unavalible");
            }
            else{
                $created = $this->_addToDB();
            }
       }
        return array($created, "");
    }

    # Edit event

    private function _editEventDB($id){
        global $USER;
        $stmt = $this->PDO->prepare("UPDATE calendar SET start = :start, end = :end, details = :details, band = :band, title = :title, description = :description WHERE id = :id");
        $result = $stmt->execute(array(':start' => $this->start->format('Y-m-d H:i:s'), ':end' => $this->end->format('Y-m-d H:i:s'), ':details' => $this->details, ':band' => $this->band, ':title' => $this->title, ':description' => $this->description, ':id' => $id));

        if ($result == 1) {return TRUE;}
        else {
            global $log;
            $log->error($result);
            return FALSE;
            }
    }
    
    public function editEvent($id){
        global $USER;
        $this->title = ((empty($this->band)) ? ($USER->fullName):($this->band));
        $this->description = ((empty($this->band)) ? (""):("Booking: " . $this->band . "\n")) . 
            "Booked by: " . $USER->fullName . "\n\n" .
             ((empty($this->details)) ? (""):($this->details . "\n\n")) .
            "Created: " . date("d-m-Y H:i:s");


       if(!isset($this->start) || !isset($this->end)) {
           return array(FALSE, "Times not both set");
       }
       else {
            $stmt = $this->PDO->prepare('SELECT owner FROM calendar WHERE id = ?');
            $stmt->execute([$id]);
            $alowedUser = $stmt->fetch()['owner'];

            if ($alowedUser != $USER->username){
                return array(FALSE, "Operation forbidden");
            } else {
                $stmt = $this->PDO->prepare('SELECT * FROM calendar WHERE start < ? && end > ? && deleted = 0 && id != ?');
                $stmt->execute([$this->end->format('Y-m-d H:i:s'), $this->start->format('Y-m-d H:i:s'), $id]);
                $slotTaken = !empty($stmt->fetch());

                #$slotTaken = !empty($this->DB->query("SELECT * FROM calendar WHERE start < %t && end > %t && deleted = 0;", $this->end, $this->start));
                if ($slotTaken) {
                    global $log;
                    return array(FALSE, "Slot unavalible");
                }
                else{
                    $created = $this->_editEventDB($id);
                }
            }
            

       }
        return array($created, "");
    }
    

    # Event deletion

    private function _deleteDB($id){

        $stmt = $this->PDO->prepare("UPDATE calendar SET deleted=1 WHERE id = ?");
        $result = $stmt->execute([$id]);

        #$result = $this->DB->query("UPDATE calendar SET deleted=1 WHERE id = %s", $id);
        return $result;
    }


    public function deleteEvent($id, $checkUser) {
        if ($checkUser) {
            global $USER;

            $stmt = $this->PDO->prepare("SELECT CASE WHEN (SELECT owner FROM calendar where id = :id && deleted = 0) = :user THEN False ELSE True END AS disallowed");
            $stmt->execute([':id' => $id, ':user' => $USER->username]);
            $disallowed = $stmt->fetch()['disallowed'];


            if ($disallowed){
                return FALSE;
            }
        }
        $db = $this->_deleteDB($id);
        if (!$db){
            global $log;
            $log->error($db);
            return False;
        }
        return TRUE;
    }

    # Event displaying
    
    public function listEvents($admin) {
        if (!$admin) {
            global $USER;
            $stmt = $this->PDO->prepare("SELECT start, end, band, details, id FROM calendar WHERE end >= NOW() and owner = ? and deleted = FALSE ORDER BY start");
            $stmt->execute([$USER->username]);
            $events = $stmt->fetchAll();
            
            #$events = $this->DB->query("SELECT start, end, band, details, id FROM calendar WHERE end >= NOW() and owner = %s and deleted = FALSE ORDER BY start", $USER->username);
        }
        else {
            $stmt = $this->PDO->query("SELECT start, end, band, details, id, name FROM calendar WHERE end >= NOW() and deleted = FALSE ORDER BY start");
            $events = $stmt->fetchAll();
            #$events = $this->DB->query("SELECT start, end, band, details, id FROM calendar WHERE end >= NOW() and deleted = FALSE ORDER BY start");
        }
        return $events;
    }
    
}