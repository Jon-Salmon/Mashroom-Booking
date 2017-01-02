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
    <link rel="stylesheet" href="<?php echo HTTP_ROOT ?>css/jquery.timepicker.css">
    <link href="<?php echo HTTP_ROOT ?>css/datepicker.css" rel="stylesheet" />
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
            <li><a id="newBooking" href="#">New Booking</a></li>
            <li><a href=<?php echo HTTP_ROOT . "bookings.php"?>>My Bookings</a></li>
            <li><a id="bookInduction" href="#">Request Induction</a></li>
            <?php if($USER->admin) {
                echo "
            <li class=\"dropdown\">
              <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">Admin <span class=\"caret\"></span></a>
              <ul class=\"dropdown-menu\">
                <li><a href=\"" . HTTP_ROOT . "admin/manageBookings.php\">All Bookings</a></li>
                <li><a href=\"" . HTTP_ROOT . "admin/index.php\">Admin Users</a></li>
                <li><a href=\"" . HTTP_ROOT . "admin/inductions.php\">Induction Requests</a></li>
              </ul>
            </li>
            ";} ?>
            <li><a id="support" href="#">Support</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>



    <div id="induct-confirm" class="dialog" title="Request a MASH room induction">
      <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span><span id="induct-text">Use of the MASH room requires you to have been inducted. Please register your interest with the MASH room manager below.</span></p>
    </div>

    <div id="support-dialog" class="dialog" title="Support">
      <span id="induct-text">
      <p>
      For all queries relating to the MASH room, please contact <a href=mailto:<?php echo $ADMINS->mash->email; ?> ><?php echo $ADMINS->mash->name; ?> <span class="glyphicon glyphicon-envelope"></span></a>.<br/><br/>
      For all general tech queries, please contact <a href=mailto:<?php echo $ADMINS->tech->email; ?> ><?php echo $ADMINS->tech->name; ?> <span class="glyphicon glyphicon-envelope"></span></a>.<br/><br/>
      For all any issue or bug with this site, please contact <a href=mailto:<?php echo $ADMINS->web->email; ?> ><?php echo $ADMINS->web->name; ?> <span class="glyphicon glyphicon-envelope"></span></a>.<br/><br/>

      </p>
      </span>
    </div>

    <div class="container">
