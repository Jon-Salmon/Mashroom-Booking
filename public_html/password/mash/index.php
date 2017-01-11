<?php    
    // load up your config file
    require_once("../../../resources/config.php");
     
    require_once(TEMPLATES_PATH . "/header.php");
?>
<div id="container">
        <?php include(TEMPLATES_PATH . "/calendar_edit.php");?>

</div>

<?php require_once(TEMPLATES_PATH . "/footer.php");?>