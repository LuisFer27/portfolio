<?php

class UtilEmail
{
  public static $production = 0;

  public static $cfg = NULL;

  /**
   * Si esta activada envia un correo electronico a lista de $testMails
   *
   * @var integer
   */
  public static $test = 0;
  /** cuentas principales de prueba */
  /*public static $testMails = array('mygnet@gmail.com','luisfernando.anemona@gmail.com');*/
  /** cuentas individual*/
public static $testMails = array('luisfernando.anemona@gmail.com');

  /**
   * Enviá un correo electrónico
   *
   * @param array $emails Arreglo de correos electrónico a lo que se tienen que enviar
   * @param string $subject Asunto del correo electrónico
   * @param [type] $template  Contenido de correo o bien plantilla
   * @param [type] $vars Arreglo de variables dentro de la plantilla
   * @param [type] $files Arreglo de archivos que se adjuntaran
   * @return bool regresa true o false
   */
  public static function send($emails = array(), $subject = '', $template = NULL, $vars = NULL, $files = NULL)
  {
    return UtilSendGrid::send($emails, $subject, $template, $vars, $files);

    Aif::$cfg->mail = self::$cfg ? self::$cfg : (isset(Aif::$cfg->mail) ? Aif::$cfg->mail : null);
    $app = Aif::$application;
    $app = isset(Aif::$application::$ver) ? Aif::$application : Aif::$application . 'Vars';

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
    $sender = TRUE;
    if ($app::$production) {
      $sender = AifMailer::send($emails, $subject, $html, $files);
      $subject = "{$subject} | " . (implode(",", $emails)) . " | PROD";
    } else {
      $subject = "{$subject} | DEV";
    }

    if (self::$test) {
      // código para realizar pruebas en html el apartado de email
      /*echo "<h3 style='text-align:center'>".$subject."</h3>";
      die($html);
      die("<h3 style='text-align:center'>".$subject."</h3>".$html);*/

      AifMailer::send(self::$testMails, $subject, $html, $files);
    }
    return $sender;
  }
}
