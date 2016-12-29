 <!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    
    <script src="<?php echo HTTP_ROOT?>js/induct_ajax.js"></script>
    
    <title>Mash Room</title>
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="css/jquery.timepicker.min.css">
    <link href="<?php echo HTTP_ROOT ?>css/navbar-static-top.css" rel="stylesheet">
    <link rel='stylesheet' href='<?php echo HTTP_ROOT ?>css/fullcalendar.css' />
    <link rel='stylesheet' href='<?php echo HTTP_ROOT ?>css/custom.css' />
</head>
 
<body>
    <nav class="navbar navbar-default navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo HTTP_ROOT ?>index.php">Mash Room</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a href=<?php echo HTTP_ROOT . "index.php"?>>Home</a></li>
            <li><a href=<?php echo HTTP_ROOT . "book.php"?>>Book</a></li>
            <li><a href=<?php echo HTTP_ROOT . "bookings.php"?>>My Bookings</a></li>
            <?php if($USER->admin) {
                echo "
            <li class=\"dropdown\">
              <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">Admin <span class=\"caret\"></span></a>
              <ul class=\"dropdown-menu\">
                <li><a href=\"" . HTTP_ROOT . "admin/manageBookings.php\">All Bookings</a></li>
                <li><a href=\"" . HTTP_ROOT . "admin/index.php\">Admin Users</a></li>
              </ul>
            </li>
            ";} ?>
            <li><a id="bookInduction" href="#">Request Induction</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>


    <script>
    $( function() {
      $( "#induct-confirm" ).dialog({
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        autoOpen: false,
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
                                  var textSpan = document.getElementById("induct-text");
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

    $("#bookInduction").click(function(e) {
        e.preventDefault();
        $( "#induct-confirm" ).dialog( "open" );
    });
    </script>

    <div id="induct-confirm" class="dialog" title="Request a MASH room induction">
      <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span><span id="induct-text">Use of the MASH room requires you to have been inducted. Please register your interest with the MASH room manager below.</span></p>
    </div>


    <div class="container">
