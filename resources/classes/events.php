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
        $this->PDO = $db;
    }
    

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
            $timeSub1 = clone $time;
            if (($this->start->format('d/m/Y') == $time->format('d/m/Y')) || ($this->start->format('d/m/Y') == $timeSub1->modify("-1 second")->format('d/m/Y'))){
                if ($time < $this->start) {
                    $endErr = 'End time cannot be before start time.';
                } elseif ($time < new DateTime()) {
                    $endErr = 'Event is in the past.';
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
            $stmt->execute(array($this->end->format('Y-m-d H:i:s'), $this->start->format('Y-m-d H:i:s')));
            $temp = $stmt->fetchAll();
            $slotTaken = !empty($temp);

            # Set's max bookable time
            $stmt = $this->PDO->prepare('SELECT start, end FROM calendar WHERE start < ? && end > ? && deleted = 0 && owner = ?');
            $tempEnd = clone $this->end;
            $tempEnd->add(new DateInterval('P1D'));
            $stmt->execute(array($tempEnd->format('Y-m-d 00:00:00'), $this->start->format('Y-m-d 00:00:00'), $USER->username));
            $temp = $stmt->fetchAll();
            $totalInterval = new DateTime('00:00');
            foreach ($temp as $booking){
                $totalInterval->add(date_diff(new DateTime($booking["start"]), new DateTime($booking["end"])));
            }
            $totalInterval->add(date_diff($this->start, $this->end));

            $min = ($totalInterval->getTimeStamp() - (new DateTime('00:00'))->getTimeStamp())/60;

            if ($slotTaken) {
                return array(FALSE, "Slot unavalible");
            } elseif($min > (int)$GLOBALS["max_daily_time"]) {
                return array(FALSE, "Max " . round((int)$GLOBALS["max_daily_time"]/60, 1) . " hours of booking per day");
            } else {
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
            $stmt->execute(array($id));
            $temp = $stmt->fetch();
            $alowedUser = $temp['owner'];


            if ($alowedUser != $USER->username){
                return array(FALSE, "Operation forbidden.");
            } else {
                $stmt = $this->PDO->prepare('SELECT * FROM calendar WHERE start < ? && end > ? && deleted = 0 && id != ?');
                $stmt->execute(array($this->end->format('Y-m-d H:i:s'), $this->start->format('Y-m-d H:i:s'), $id));
                $temp = $stmt->fetchAll();
                $slotTaken = !empty($temp);

                # Set's max bookable time
                $stmt = $this->PDO->prepare('SELECT start, end FROM calendar WHERE start < ? && end > ? && deleted = 0 && owner = ? && id != ?');
                $tempEnd = clone $this->end;
                $tempEnd->add(new DateInterval('P1D'));
                $stmt->execute(array($tempEnd->format('Y-m-d 00:00:00'), $this->start->format('Y-m-d 00:00:00'), $USER->username, $id));
                $temp = $stmt->fetchAll();
                $totalInterval = new DateTime('00:00');
                foreach ($temp as $booking){
                    $totalInterval->add(date_diff(new DateTime($booking["start"]), new DateTime($booking["end"])));
                }
                $totalInterval->add(date_diff($this->start, $this->end));

                global $GLOBALS;

                $min = ($totalInterval->getTimeStamp() - (new DateTime('00:00'))->getTimeStamp())/60;
                
                if ($slotTaken) {
                    return array(FALSE, "Slot unavalible");
                } elseif($min > (int)$GLOBALS["max_daily_time"]) {
                    return array(FALSE, "Max " . round((int)$GLOBALS["max_daily_time"]/60, 1) . " hours of booking per day");
                } else {
                    $created = $this->_editEventDB($id);
                }
            }
            

       }
        return array($created, "");
    }
    

    # Event deletion

    private function _deleteDB($id){

        $stmt = $this->PDO->prepare("UPDATE calendar SET deleted=1 WHERE id = ?");
        $result = $stmt->execute(array($id));

        return $result;
    }


    public function deleteEvent($id, $checkUser, $sendEmail = FALSE, $message = "") {
        if ($checkUser) {
            global $USER;

            $stmt = $this->PDO->prepare("SELECT CASE WHEN (SELECT owner FROM calendar where id = :id && deleted = 0) = :user THEN False ELSE True END AS disallowed");
            $stmt->execute(array(':id' => $id, ':user' => $USER->username));
            $temp = $stmt->fetch();
            $disallowed = $temp['disallowed'];


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
        if ($sendEmail){
            global $USER;
            global $ADMINS;
            
            $stmt = $this->PDO->prepare("SELECT owner, start FROM calendar where id = :id");
            $stmt->execute(array(':id' => $id));
            $result = $stmt->fetch();
            $username = $result['owner'];
            $start = new DateTime($result['start']);
            $owner = new User($username);

            if (count($USER->role) == 1){
                $name = "Admin";
            } else {
                $name = $ADMINS->{$USER->role[0]}->title;
            }
            $temp = trim($message);
            $temp = empty($temp);
            $selectedBody = ($temp ? 'cancel_body' : 'cancel_body_message');
            $bodyDets = array(
                "{fullName}" => $owner->fullName,
                "{date}" => $start->format("d/m/y"),
                "{start}" => $start->format("H:i"),
                "{cancelledBy}" => $name,
                "{message}" => $message
            );
            email($USER->email, $name, $owner->email, DBGet('cancel_subject'), DBGet($selectedBody, $bodyDets));
        }
        return TRUE;
    }

    # Event displaying
    
    public function listEvents($admin) {
        if (!$admin) {
            global $USER;
            $stmt = $this->PDO->prepare("SELECT start, end, band, details, id FROM calendar WHERE end >= NOW() and owner = ? and deleted = FALSE ORDER BY start");
            $stmt->execute(array($USER->username));
            $events = $stmt->fetchAll();
            
        }
        else {
            $stmt = $this->PDO->query("SELECT start, end, band, details, id, name FROM calendar WHERE end >= NOW() and deleted = FALSE ORDER BY start");
            $events = $stmt->fetchAll();
        }
        return $events;
    }
    
}