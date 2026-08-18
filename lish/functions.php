<?php

date_default_timezone_set("Africa/Nairobi");

function sendEmail($email, $companyName, $subject, $mailContent, $attachments,$emailalt = null) {

  // Require PHPMailer library
  require_once 'PHPMailer/PHPMailerAutoload.php';

  $mail = new PHPMailer(true); // Enable exceptions

  try {
    // Configure SMTP settings (Replace with your actual values)
    $mail->isSMTP();
    $mail->Host = 'secure350.servconfig.com';
    $mail->SMTPAuth = true;
    $mail->SMTPDebug = 0;  // Enable verbose debug output

          $mail->Username = 'support@hawlast.com';
          $mail->Password = 'CEOuBER2023#';
          $mail->SMTPSecure = 'SSL';
          $mail->Port = 465;
          //this must be from the domain on this server 
          $mail->setFrom('support@hawlast.com', $companyName);
          $mail->addReplyTo($emailalt);
          //$mail->addReplyTo($email, $companyName);
          
          $mail->addAddress($email);
    
     //if ($emailalt !== null) {
       // $mail->addCC($emailalt);
     //}
    
    // Add attachments if provided
    if (!empty($attachments)) {
      //foreach ($attachments as $attachment) {
        //$mail->addAttachment($attachment['path'], $attachment['filename']);
      //}
    $mail->addAttachment($attachments);
    }

    // Set email content and format
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $mailContent;
     $mail->SMTPSecure = "ssl";  // Set the secure connection type
    $mail->SMTPOptions = array("ssl" => array("verify_peer" => false,"verify_peer_name" => false,"allow_self_signed" => true));

    // Send email and handle errors
    $mail->send();
    
    return true;

  } catch (Exception $e) {
      $makosa = $mail->ErrorInfo;
            //Write on file 
      $time = date("Y:m:d h:i:sa");
     $both = "<br/> $time $email $makosa";
    //file_put_contents($filex, PHP_EOL . $both, FILE_APPEND);
   
     $fp = fopen("TumaEmailError.txt", "a") or die("Unable to open file!");
	fwrite($fp, $both);
	fclose($fp);
    return false;
  }
}
?>