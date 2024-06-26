<?php

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

Composer::autoload();
//Load Composer's autoloader
//require 'vendor/autoload.php';

class UtilMailer
{

  public static function sendGrid()
  {
    Composer::autoload();

    $email = new \SendGrid\Mail\Mail();
    $email->setFrom("cloud@aifphp.com", "Cloud");
    $email->setSubject("Sending with SendGrid is Fun");
    $email->addTo("mygnet@gmail.com", "Example User");
    $email->addContent("text/plain", "and easy to do anywhere, even with PHP");
    $email->addContent(
      "text/html",
      "<strong>and easy to do anywhere, even with PHP</strong>"
    );
    $sendgrid = new \SendGrid('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');
    try {
      $response = $sendgrid->send($email);
      print $response->statusCode() . "\n";
      print_r($response->headers());
      print $response->body() . "\n";
    } catch (Exception $e) {
      echo 'Caught exception: ' . $e->getMessage() . "\n";
    }
  }

  public static function test($cfg)
  {
    Composer::autoload();
    $cfg = AifLib::urlArray($cfg);
    print_r($cfg);
    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    $cfg['user'] = str_replace('*', '@', $cfg['user']);
    if (strpos($cfg['user'], '@') === FALSE) {
      $cfg['user'] .= '@gmail.com';
    }
    try {

      //siap*ipsp.mx:ecDwPHhB6K3eAHP
      //Server settings
      $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
      $mail->isSMTP();                                            //Send using SMTP
      $mail->Host       = "smtp.gmail.com"; //$cfg['server'];                    //Set the SMTP server to send through
      $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
      $mail->Username   = 'api.cloud.v1'; //$cfg['user'];                     //SMTP username
      $mail->Password   = 'app.1000'; //$cfg['password'];                               //SMTP password
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
      $mail->Port       = 465; //  465;// $cfg['port'];                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

      //Recipients
     //$mail->setFrom('cloud-dgaf@sct.gob.mx', 'Mailer');
      //$mail->addAddress('mygnet@gmail.com', 'Martin Roberto');     //Add a recipient
      $mail->addAddress('mygnet@gmail.com');               //Name is optional
      //$mail->addReplyTo('info@example.com', 'Information');
      //$mail->addCC('cc@example.com');
      //$mail->addBCC('bcc@example.com');

      //Attachments
      //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
      //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

      //Content
      $mail->isHTML(true);                                  //Set email format to HTML
      $mail->Subject = 'Here is the subject';
      $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
      $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

      $mail->send();
      echo 'Message has been sent';
    } catch (Exception $e) {
      echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
  }
}
