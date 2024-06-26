<?php

class UtilSendGrid
{

  public static $api = array(
    'aifphp.com' => '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',
    'notmail.us' => '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'
  );

  public static function htmlTemplate($template, $vars)
  {
    $html = '';
    if (is_string($template)) {
      if (strpos($template, '.phtml')) { // si es una plantilla
        $html = new AifView($template);
        if (is_array($vars) && count($vars)) {
          foreach ($vars as $name => $val) {
            $html->{$name} = $val;
          }
        }
      } else {
        $html = $template;
      }
    } else {
      if (is_array($vars)) {
        //Parser de variables
      } else {
        $html = $vars;
      }
    }
    //die($html);
    return $html . "";
  }

  public static function send($emails = array(), $subject = '', $template = NULL, $vars = NULL, $files = NULL)
  {

    Aif::$cfg->mail = isset(Aif::$cfg->mail) ? Aif::$cfg->mail : null;
    $app = isset(Aif::$application::$ver) ? Aif::$application : Aif::$application . 'Vars';
    $html = self::htmlTemplate($template, $vars);
    $sender = false;
    if ($app::$production) {
      $sender = self::sendMail($emails, $subject, $html, $files);
      $subject = "{$subject} | " . (implode(",", $emails)) . " | PROD";
    }
    if (UtilEmail::$test) {
      $subject = "{$subject} | DEV";
      if (count(UtilEmail::$testMails)) {
        $sender = self::sendMail(UtilEmail::$testMails, $subject, $html, $files);
      } else {
        die($html);
      }
    }
    return $sender;
  }

  public static function sendMail($emails = array(), $subject = '', $html = '', $files = null)
  {
    Composer::autoload();
    $cfg = Aif::getElementConfig('mail');
    $cfg = AifLib::urlArray($cfg);
    $api = isset(self::$api[$cfg['server']]) ? self::$api[$cfg['server']] : self::$api['aifphp.com'];
    $sender = false;
    $email = new \SendGrid\Mail\Mail();
    $email->setFrom($cfg['email'], $cfg['name']);
    $email->setSubject($subject);
    if (is_array($emails)) {
      foreach ($emails as $em => $dd) {
        $name = NULL;
        if (is_array($dd)) {
          if (count($dd) > 1) $name = $dd[1];
          $em = is_numeric($em) ? $dd[0] : $em;
        } else {
          if (is_numeric($em)) $em = $dd;
          else $name = $dd;
        }
        $name ? $email->addTo($em, $name) : $email->addTo($em);
      }
    } else {
      $email->addTo($emails);
    }
    //$email->addContent("text/plain", "and easy to do anywhere, even with PHP");
    //die($html);
    $email->addContent("text/html", $html);
    $sendgrid = new \SendGrid($api);
    try {
      $response = $sendgrid->send($email);
      //print $response->statusCode() . "\n";
      //print_r($response->headers());
      //print $response->body() . "\n";
      $sender = true;
    } catch (Exception $e) {
      echo 'Caught exception: ' . $e->getMessage() . "\n";
      $sender = false;
    }
    return $sender;
  }
}
