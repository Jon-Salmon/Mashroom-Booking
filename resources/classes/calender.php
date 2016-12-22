<?php
class Event {
    
    public $query;
    public $result;

    function _construct(){
    }
    
    public function sqlQuery(){
        setupDatabase(1);
    	$this->result = DB::query($this->query);
    }
}