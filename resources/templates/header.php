 <!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="css/jquery.timepicker.min.css">
    <title>Simple Site</title>
    <link href="<?php echo HTTP_ROOT ?>css/bootstrap.min.css" rel="stylesheet">    
    <link href="<?php echo HTTP_ROOT ?>css/navbar-static-top.css" rel="stylesheet">
    <link rel='stylesheet' href='<?php echo HTTP_ROOT ?>css/fullcalendar.css' />
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
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">
