 
 
<?php

require_once('../../resources/globalNoUser.php');

?>
 
 <!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    
    <title>MASH Room</title>
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel='stylesheet' href='<?php echo OPEN_ROOT ?>css/fullcalendar.css' />
    <link rel='stylesheet' href='<?php echo OPEN_ROOT ?>css/custom.css' />


</head>
 
<body>
    <nav class="navbar navbar-default navbar-static-top">
      <div class="container">
        <div class="navbar-header" style="width:100%">
          <a class="navbar-brand" href="<?php echo HTTP_ROOT ?>index.php">Mash Room</a>
          <a style="float:left" class="navbar-brand" href="<?php echo HTTP_ROOT ?>index.php">Login</a>
          <a style="float:right" class="navbar-brand" id="support" href="#">Support</a>
        </div>
      </div>
    </nav>



<div class="container">

        <?php include(TEMPLATES_PATH . "/calendar.php");?>
        
</div>

<script>
    $("#support").click(function(e) {
        e.preventDefault();
        $( "#support-dialog" ).dialog( "open" );
    });

    $( function() {

      $( "#support-dialog" ).dialog({
        resizable: false,
        height: "auto",
        modal: true,
        autoOpen: false,
        open: function(){
          jQuery('.ui-widget-overlay').bind('click',function(){
              jQuery('#support-dialog').dialog('close');
          })
        }
      });
      
      $( "#induct-confirm" ).dialog({
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        autoOpen: <?php echo ($_GET['request'] == 'true')?'true':'false' ?>,
        open: function(){
          jQuery('.ui-widget-overlay').bind('click',function(){
              jQuery('#induct-confirm').dialog('close');
          })
        },
        buttons: {
          "Register": function() {
              $.ajax({ url: '<?php echo HTTP_ROOT ?>ajax/induct.php',
                      data: {induct: JSON.stringify('true')},
                      type: 'post',
                      success: function(output) {
                                  var textSpan = document.getElementById("message-text");
                                  textSpan.innerHTML = output;

                                    $('#induct-confirm').dialog("option", "buttons", {
                                      Close: function() {
                                      $( this ).dialog( "close" );
                                    }
                                  });
                      }
              });
          },
          Close: function() {
            $( this ).dialog( "close" );
          }
        }
      });
    });
</script>

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="<?php echo OPEN_ROOT ?>js/ie10-viewport-bug-workaround.js"></script>
  </body>
    <div id="support-dialog" class="dialog" title="Support">
      <span id="induct-text">
      <p>
      For all queries relating to the MASH room, please contact <a href=mailto:<?php echo $ADMINS->mash->email; ?> ><?php echo $ADMINS->mash->name; ?> <span class="glyphicon glyphicon-envelope"></span></a>.<br/><br/>
      For all general tech queries, please contact <a href=mailto:<?php echo $ADMINS->tech->email; ?> ><?php echo $ADMINS->tech->name; ?> <span class="glyphicon glyphicon-envelope"></span></a>.<br/><br/>
      For all any issue or bug with this site, please contact <a href=mailto:<?php echo $ADMINS->web->email; ?> ><?php echo $ADMINS->web->name; ?> <span class="glyphicon glyphicon-envelope"></span></a>.<br/><br/>

      </p>
      </span>
    </div>
    <div id="induct-confirm" class="dialog" title="Request a MASH room induction">
      <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span><span id="message-text">Use of the MASH room and this website requires you to have been inducted by the MASH room manager. Please register your interest with the MASH room manager below.</span></p>
    </div>
</html>
    