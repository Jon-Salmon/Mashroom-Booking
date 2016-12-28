<?php
    require_once("../../resources/config.php");
    // My modifications to mailer script from:
    // http://blog.teamtreehouse.com/create-ajax-contact-form
    // Added input sanitizing to prevent injection

    // Only process POST reqeusts.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the form fields and remove whitespace.
        $valid = trim($_POST["induct"]);

        // Check that data was sent to the mailer.
        if ($valid != "true") {
            // Set a 400 (bad request) response code and exit.
            http_response_code(400);
            echo "Oops! There was a problem with your submission. Please complete the form and try again.";
            exit;
        }

        $worked = TRUE;
        
        try {
            $stmt = $PDO->prepare("INSERT INTO users(user, name, email, requestedInduction) VALUES(?, ?, ?, 1)");
            $result = $stmt->execute([$USER->username, ucwords($USER->fullName), $USER->email]);
        }
        catch (PDOException $e) {
            $worked = FALSE;
            $code = $e->getCode();
        }
        
        if ($worked) {
            // Set a 200 (okay) response code.
            email($USER->email, $USER->fullName, $ADMINS->mash->email, "Mashroom induction requested from " . $USER->fullName, $USER->fullName . " has requested a mashroom induction.");
            http_response_code(200);
            echo "Thank You! Your request has been sent to the Mash-room manager";
            
        } elseif( $code == "23000") {
            // Set a 500 (internal server error) response code.
            $stmt = $PDO->prepare("SELECT created from users WHERE user = ?");
            $stmt->execute([$USER->username]);
            $result = $stmt->fetch();
            
            http_response_code(200);
            $date = date("d-m-Y", strtotime($result['created']));
            echo "You've already sumbmitted a request (on " . $date . ").";
        } else {
            // Set a 500 (internal server error) response code.
            http_response_code(500);
            echo "Oops! Something went wrong and we couldn't send your message.";
        }

    } else {
        // Not a POST request, set a 403 (forbidden) response code.
        http_response_code(403);
        echo "There was a problem with your submission, please try again.";
    }

?>
