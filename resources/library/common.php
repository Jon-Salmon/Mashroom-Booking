<?php

function email($from, $fromName, $to, $subject, $message) {

    global $config;

    $mail = new PHPMailer();
    $mail->CharSet = "UTF-8";
    // telling the class to use SMTP
    $mail->IsSMTP();
    // enables SMTP debug information (for testing)
    // 1 = errors and messages
    // 2 = messages only
    $mail->SMTPDebug  = 0;
    // enable SMTP authentication
    $mail->SMTPAuth   = true;
    // sets the prefix to the servier
    $mail->SMTPSecure = "ssl";
    // sets GMAIL as the SMTP server
    $mail->Host       = $config['email']['host'];
    // set the SMTP port for the GMAIL server
    $mail->Port       = $config['email']['port'];
    // GMAIL username
    $mail->Username   = $config['email']['username'];
    // GMAIL password
    $mail->Password   = $config['email']['password'];
    //Set reply-to email this is your own email, not the gmail account 
    //used for sending emails
    $mail->AddReplyTo($from);
    $mail->FromName = $fromName;
  
    // Mail Subject
    $mail->Subject    = $subject;

    //Main message
    $mail->Body = $message;

    //Your email, here you will receive the messages from this form. 
    //This must be different from the one you use to send emails, 
    //so we will just pass email from functions arguments
    $mail->AddAddress($to, "");
    if(!$mail->Send()) 
    {
        //couldn't send
    var_dump($mail);
        global $log;
        $log->error($mail->ErrorInfo);
        return FALSE;
    } 
    else 
    {
        //successfully sent
        return TRUE;
    }
}
?>