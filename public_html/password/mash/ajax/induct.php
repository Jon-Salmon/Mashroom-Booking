<?php
    require_once("../../../../resources/global.php");

    // Only process POST reqeusts.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the form fields and remove whitespace.
        $valid = trim(json_decode($_POST["induct"]));

        // Check that data was sent to the mailer.
        if ($valid != 'true') {
            echo "Oops! There was a problem with your submission. Please complete the form and try again.";
            exit();
        }

        $worked = TRUE;
        
        try {
            $stmt = $PDO->prepare("INSERT INTO users(user, name, email, requestedInduction) VALUES(?, ?, ?, 1)");
            $result = $stmt->execute(array($USER->username, ucwords($USER->fullName), $USER->email));
        }
        catch (PDOException $e) {
            $worked = FALSE;
            $code = $e->getCode();
        }

        
        if ($worked) {
            $nameArray = array(
                "{fullName}" => $USER->fullName
            );
            email($USER->email, $USER->fullName, $ADMINS->mash->email, DBGet('induct_subject', $nameArray), DBGet('induct_body', $nameArray));
            echo "Thank You! Your request has been sent to the Mash-room manager";
            exit();
            
        } elseif( $code == "23000") {
            $stmt = $PDO->prepare("SELECT created from users WHERE user = ?");
            $stmt->execute(array($USER->username));
            $result = $stmt->fetch();
            
            $date = date("d-m-Y", strtotime($result['created']));
            echo "You've already sumbmitted a request (on " . $date . ").";
            exit();
        } else {
            echo "Oops! Something went wrong and we couldn't send your message.";
            exit();
        }

    } else {
        echo "There was a problem with your submission, please try again.";
        exit();
    }

?>
