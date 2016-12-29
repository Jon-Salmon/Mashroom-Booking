<?php    
    // load up your config file
    require_once("../resources/config.php");
     
    require_once(TEMPLATES_PATH . "/header.php");
?>
<div id="container">
        
        <script src="js/jquery.timepicker.min.js"></script>

        <?php $test = new Admins($PDO); ?>
        <?php include(TEMPLATES_PATH . "/calendar_edit.php");?>

        <?php #email("trevs.mashroom@gmail.com", "The Mash Room", "jonathan.salmon@hotmail.co.uk", "Composer test", "thas this worked?"); ?>

</div>

<?php require_once(TEMPLATES_PATH . "/footer.php");?>