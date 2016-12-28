<?php    
    // load up your config file
    require_once("../resources/config.php");
     
    require_once(CLASSES_PATH . "/events.php");
    require_once(TEMPLATES_PATH . "/header.php");
?>
<div id="container">
        
        <?php $test = new Admins($PDO); ?>
        <?php include(TEMPLATES_PATH . "/calendar.php");?>

        <?php #email("trevs.mashroom@gmail.com", "The Mash Room", "jonathan.salmon@hotmail.co.uk", "Composer test", "thas this worked?"); ?>

</div>

<?php require_once(TEMPLATES_PATH . "/footer.php");?>