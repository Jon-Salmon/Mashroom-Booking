
<?php    
    // load up your config file
    require_once("../resources/config.php");
     
    require_once(TEMPLATES_PATH . "/header.php");
    require_once(CLASSES_PATH . "/calender.php");
    require_once(LIBRARY_PATH . "/common.php");
    require_once(LIBRARY_PATH . "/meekrodb.2.3.class.php");
?>
<div id="container">
    <div id="content">
        <!-- content -->

        <?php
        // define variables and set to empty values
        $userErr = $emailErr = "";
        $user = $email = "";
        $valid = FALSE;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $valid = TRUE;
        if (empty($_POST["user"])) {
            $userErr = "Username is required";
            $valid = FALSE;
        } else {
            $user = strtolower(test_input($_POST["user"]));
            // check if name only contains letters and whitespace
            if (!preg_match("/^[a-zA-Z\d]+$/",$user)) {
                $userErr = "Only letters and numbers allowed";
                $valid = FALSE;
            }
        }
        
        if (empty($_POST["email"])) {
            $emailErr = "Email is required";
            $valid = FALSE;
        } else {
            $email = test_input($_POST["email"]);
            // check if e-mail address is well-formed
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
            $valid = FALSE;
            }
        }
        }

        function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
        }
        
        if ($valid){
            setupDatabase(1);
            DB::$error_handler = 'my_error_handler';
            $result = DB::query("INSERT INTO `users`(`user_id`, `email`) VALUES (%s, %s);", $user, $email);
            if ($result){
                header('Location: ./index.php');
            }
        }

        function my_error_handler($params) {
            if ($params['code'] == 1062){
                echo "Duplicate database entry";
            }
            return TRUE;
        }
        
        ?>

        <h2>PHP Form Validation Database Write Example</h2>
        <p><span class="error">* required field.</span></p>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
        UserID: <input type="text" name="user" value="<?php echo $user;?>">
        <span class="error">* <?php echo $userErr;?></span>
        <br><br>
        E-mail: <input type="text" name="email" value="<?php echo $email;?>">
        <span class="error">* <?php echo $emailErr;?></span>
        <br><br>
        <input type="submit" name="submit" value="Submit">  
        </form>

    </div>
</div>
