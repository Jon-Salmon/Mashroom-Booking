<?php
    // load up your config file
    require_once("../../resources/config.php");
     
    if (!$USER->admin){
        header('Location: ../index.php');
        die();
    }
    
    require_once(TEMPLATES_PATH . "/header.php");

?>
<?php require_once(TEMPLATES_PATH . "/footer.php");?>