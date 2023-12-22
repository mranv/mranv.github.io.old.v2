<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include PHPMailer library

/*
 * CONFIGURE EVERYTHING HERE
 */

// an email address that will be in the From field of the email.
$from = 'Demo contact form <sender@yourdomain.com>';

// an email address that will receive the email with the output of the form
$sendTo = 'Demo contact form <receiver@example.com>';

// subject of the email
$subject = 'New message from contact form';

// form field names and their translations.
// array variable name => Text to appear in the email
$fields = array('InputName' => 'Name', 'InputEmail' => 'Email', 'InputSubject' => 'Subject', 'InputMessage' => 'Message');

// message that will be displayed when everything is OK :)
$okMessage = 'Your message was successfully submitted. Thank you, I will get back to you soon!';

// If something goes wrong, we will display this message.
$errorMessage = 'There was an error while submitting the form. Please try again later';

/*
 * LET'S DO THE SENDING
 */

// if you are not debugging and don't need error reporting, turn this off by error_reporting(0);
error_reporting(E_ALL & ~E_NOTICE);

try {
    // Validate form data
    if (count($_POST) === 0) {
        throw new Exception('Form is empty');
    }

    // Validate email address
    if (!filter_var($sendTo, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address for the recipient');
    }

    // Construct email text
    $emailText = "You have a new message from your contact form\n=============================\n";

    foreach ($_POST as $key => $value) {
        // If the field exists in the $fields array, include it in the email
        if (isset($fields[$key])) {
            $emailText .= "$fields[$key]: $value\n";
        }
    }

    // Create a PHPMailer instance
    $mail = new PHPMailer(true);

    // Set mailer to use SMTP
    $mail->isSMTP();

    // Replace these values with your SMTP configuration
    $mail->Host = 'smtp.hostinger.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'mranv@techanv.com';
    $mail->Password = 'Anubhav@321';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Set email details
    $mail->setFrom($from);
    $mail->addAddress($sendTo);
    $mail->Subject = $subject;
    $mail->Body = $emailText;

    // Send email
    $mail->send();

    $responseArray = array('type' => 'success', 'message' => $okMessage);
} catch (Exception $e) {
    $responseArray = array('type' => 'danger', 'message' => $errorMessage . ' ' . $e->getMessage());
}

// if requested by AJAX request return JSON response
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $encoded = json_encode($responseArray);

    header('Content-Type: application/json');

    echo $encoded;
}
// else just display the message
else {
    echo $responseArray['message'];
}
