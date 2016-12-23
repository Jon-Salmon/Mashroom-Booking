<?php
class Event {
    
    public $result;

    function __construct(){
        require_once(LIBRARY_PATH . "/meekrodb.2.3.class.php");
    }
    
    public function sqlQuery($query){
        setupDatabase(1);
    	$this->result = DB::query($query);
    }
}