<?php

// required for PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

// Honeypot check if hidden fields are filled in
$spam1 = $_REQUEST['name'];
$spam2 = $_REQUEST['email'];
$spam3 = $_REQUEST['phone'];
if ((isset($spam1) && !empty($spam1)) ||
    (isset($spam2) && !empty($spam2)) ||
    (isset($spam3) && !empty($spam3))) {
        header('Location: https://developers.google.com/search/docs/advanced/guidelines/report-spam');
        die();
    }

// EDIT THE 2 LINES BELOW AS REQUIRED
$email_to = "clark@clarka.me";
$email_subject = "Message from Clark A Contact Form";

function died($error) {
    // your error code can go here
    echo "We are very sorry, but there were error(s) found with the form you submitted.";
    echo "These errors appear below.<br /><br />";
    echo $error . "<br /><br />";
    echo "Please go back and fix these errors.<br /><br />";
    die();
}

// check if expected data exists
if (!isset($_POST['nameaswq']) ||
    !isset($_POST['emailaswq']) ||
    !isset($_POST['subjectaswq']) ||
    !isset($_POST['messageaswq'])) {
        died('We are sorry, but all fields need to be filled out in order to submit the form.');
    }

// trim inputs
$name = trim($_POST['nameaswq']);
$email_from = trim($_POST['emailaswq']);
$subject = trim($_POST['subjectaswq']);
$testquestion = $_POST['testquestion'];
$message = trim($_POST['messageaswq']);

// error messages
$error_message = "";

$email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
if (!preg_match($email_exp, $email_from)) {
    $error_message .= 'The Email Address you entered does not appear to be valid.<br />';
}
if ($testquestion != 5) {
    $error_message .= 'The Answer to the Math Question is incorrect.<br />';
}
if (strlen($message) < 2) {
    $error_message .= 'The comment needs to be more than two characters.<br />';
}
if (strlen($error_message) > 0) {
    died($error_message);
}

// Email html markup
$email_message = "<html><body>";
$email_message .= "<h2>Your contact details are listed below.</h2>\n\n";

function clean_string($string) {
    $bad = ["content-type", "bcc:", "to:", "cc:", "href"];
    return str_replace($bad, "", $string);
}

$email_message .= "<h3>Name: " . clean_string($name) . "</h3>";
$email_message .= "<h3>Email: " . clean_string($email_from) . "</h3>";
$email_message .= "<h3>Subject: " . clean_string($subject) . "</h3>";
$email_message .= "<h3>Message: " . clean_string($message) . "</h3>";
$email_message .= "</body></html>";
$email_text = strip_tags($email_message); //plain text version of email_message

//Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    // PHPMailer Server settings
    $mail->SMTPDebug = 0; // 0 = off (for production use) - 1 = client messages - 2 = client and server messages - 4 = low level info is displayed
    $mail->isSMTP(); // Set mailer to use SMTP
    $mail->Host = 'mail.smtp2go.com'; // Specify main and backup SMTP servers
    $mail->SMTPAuth = true; // Enable SMTP authentication
    $mail->Username = 'clark@clarka.me'; // SMTP username
    $mail->Password = 'zAfg6%y5uu&uqVDb'; // SMTP password
    $mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` is depracated
    $mail->Port = 587; // TCP port to connect to

    //Recipients
    $mail->setFrom($email_to, 'Contact Form');
    $mail->addAddress($email_to, 'Support Staff'); // Add a recipient
    $mail->addReplyTo($email_from, $name);
    //$mail->addCC('cc@example.com');
    //$mail->addBCC('bcc@example.com');

    //Attachments
    //$mail->addAttachment('/var/tmp/file.tar.gz'); // Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg'); // Optional name

    //Content
    $mail->isHTML(true); // Set email format to HTML
    $mail->CharSet = "text/html; charset=UTF-8;";
    $mail->WordWrap = 80;
    $mail->Subject = $email_subject;
    $mail->MsgHTML($email_message); //$mail->body and $mail->AltBody aren't necessary if using $mail->MsgHTML
    //$mail->Body = $email_message;
    //$mail->AltBody = $email_text;

    // send email
    if (!$mail->send()) {
        // return error to user; they did not pass
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    }
    else {
        // header will only work if $mail->SMTPDebug = 0 (off)
        header('Location: http://www.clarka.me/thankyou.html');
    }
}   catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>
