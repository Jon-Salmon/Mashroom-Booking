
<?php    
    // load up your config file
    require_once("../resources/config.php");
     
    require_once(CLASSES_PATH . "/events.php");
    require_once(LIBRARY_PATH . "/meekrodb.2.3.class.php");

    require_once(TEMPLATES_PATH . "/header.php");
?>
<script src="js/dtPicking.js"></script>
<script src="js/jquery.timepicker.min.js"></script>
<script>
    $(document).ready(function(){
        $('input.timepicker').timepicker({
             'scrollDefault': 'now' 
        });
    });
</script>
<div id="container">
    <div id="content">
        <!-- content -->

        <?php
        // define variables and set to empty values
        $nameErr = $startErr = $dateErr = $endErr = $detailsErr = "";
        $name = $start = $date = $end = $details = "";
        $valid = FALSE;
        $booking = new Event($PDO);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $valid = TRUE;

        if (empty($_POST["user"])) {
            $userErr = "Username is required";
            #$valid = FALSE;
        } else {
            $user = strtolower(test_input($_POST["user"]));
            // check if name only contains letters and whitespace
            if (!preg_match("/^[a-zA-Z\d]+$/",$user)) {
                $userErr = "Only letters and numbers allowed";
                $valid = FALSE;
            }
        }
        

        list($date, $dateErr) = $booking->checkDate(test_input($_POST["date"]));
        if (!empty($dateErr)) {
            $valid = FALSE;
        }

        list($start, $startErr) = $booking->checkStart(test_input($_POST["start"]));
        if (!empty($startErr)) {
            $valid = FALSE;
        }
        
        list($end, $endErr) = $booking->checkEnd(test_input($_POST["end"]));
        if (!empty($endErr)) {
            $valid = FALSE;
        }
        
        list($name, $nameErr) = $booking->checkBand(test_input($_POST["name"]));
        if (!empty($nameErr)) {
            $valid = FALSE;
        }

        list($details, $detailsErr) = $booking->checkDetails(test_input($_POST["details"]));
        if (!empty($detailsErr)) {
            $valid = FALSE;
        }
        
        }

        function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
        }
        
        if ($valid){
            $result = $booking->createEvent();
            if ($result){
                header('Location: ./index.php');
            }
        }

        ?>

        <h2>PHP Form Validation Database Write Example</h2>
        <p><span class="error">* required field.</span></p>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
        Booking name: <input type="text" name="name" value="<?php echo $name;?>">
        <span class="error"><?php echo $nameErr;?></span>
        <br><br>
        Date: <input type="text" id="datepicker" name="date" autocomplete="off" value="<?php echo $date;?>">
        <span class="error">* <?php echo $dateErr;?></span>
        <br><br>
        Start time: <input type="text" class="timepicker" name="start" autocomplete="off" value="<?php echo $start;?>">
        <span class="error">* <?php echo $startErr;?></span>
        <br><br>
        End time: <input type="text" class="timepicker" name="end" autocomplete="off" value="<?php echo $end;?>">
        <span class="error">* <?php echo $endErr;?></span>
        <br><br>
        Other details: <textarea name="details"><?php echo $details;?></textarea>
        <span class="error"><?php echo $detailsErr;?></span>
        <br>
        <?php
        if (isset($booking->addErr)){
            echo '<br>' . $booking->addErr . '<br>';
        }
        ?>
        <br>
        <input type="submit" name="submit" value="Submit">  
        </form>

    </div>
</div>

<?php require_once(TEMPLATES_PATH . "/footer.php");?>