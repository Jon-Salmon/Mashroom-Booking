<?php    
    // load up your config file
    require_once("../resources/config.php");
     
    require_once(CLASSES_PATH . "/events.php");
    require_once(TEMPLATES_PATH . "/header.php");
?>
<div id="container">
    <div id="content">
        <!-- content -->
        
        <?php #email("trevs.mashroom@gmail.com", "The Mash Room", "jonathan.salmon@hotmail.co.uk", "Composer test", "thas this worked?"); ?>

        <iframe src="https://calendar.google.com/calendar/embed?showPrint=0&amp;showCalendars=0&amp;mode=WEEK&amp;height=600&amp;wkst=2&amp;bgcolor=%23FFFFFF&amp;src=trevs.mashroom%40gmail.com&amp;color=%231B887A&amp;ctz=Europe%2FLondon" style="border-width:0" width="800" height="600" frameborder="0" scrolling="no"></iframe>
    </div>
</div>

<?php require_once(TEMPLATES_PATH . "/footer.php");?>