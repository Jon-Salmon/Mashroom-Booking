<?php    
    // load up your config file
    require_once("../resources/config.php");
     
    require_once(TEMPLATES_PATH . "/header.php");
    require_once(CLASSES_PATH . "/calender.php");
    require_once(LIBRARY_PATH . "/common.php");
?>
<div id="container">
    <div id="content">
        <!-- content -->
        <?php

        $event = new Event;
        $event->sqlQuery("SELECT user_id, email FROM users");
        
        foreach ($event->result as $row) {
            echo "ID: " . $row['user_id'] . "<br>";
            echo "Email: " . $row['email'] . "<br>";
            echo "<br>";
        }
        
        phpinfo();
        ?>
    </div>
</div>
