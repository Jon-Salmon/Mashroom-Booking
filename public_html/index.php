<?php    
    // load up your config file
    require_once("../resources/config.php");
     
    require_once(TEMPLATES_PATH . "/header.php");

    require_once '../resources/library/meekrodb.2.3.class.php';
    DB::$user = 'mash';
    DB::$password = 'u3XxS7QQ8QhEhB2E5kGhPtqVgqFW';
    DB::$dbName = 'mash';

?>
<div id="container">
    <div id="content">
        <!-- content -->
        <?php
    	$results = DB::query("SELECT user_id, email FROM users");
    	foreach ($results as $row) {
		echo "ID: " . $row['user_id'] . "<br>";
		echo "Email: " . $row['email'] . "<br>";
		echo "<br>";
	}
        phpinfo();
        ?>
    </div>
</div>
