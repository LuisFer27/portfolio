<?php
/**
 * Procesa la solicitud web
 *
 * @author $Author$
 * @copyright Copyright (c) 2008, {@link http://www.aquainteractive.com
 *            AquaInteractive}
 * @package Aif
 * @since 2013-04-15
 * @subpackage Core
 * @version $Id$
 */

/**
 * Clase que procesa la solicitud web
 *
 * @package Aif
 * @subpackage Core
 */
class AifRequest {
  /**
   * Ruta del área de publicación relativa
   * @var string
   */
  public static $path = '';


  public static $headers= NULL;
  /**
   * Regresa una direccion electronica personalizado del sitio
   * @param string $url Cadena de criterios para construir el url
   * @param bool $h Cuando es falso omite el url del sitio
   * @return string Regresa el url deacuerdo a los criterios
   *         %a = Nombre de Aplicación + /
   *         %c = Nombre de Clase o Controlador + /
   *         %m = Nombre del Metodo o Accion + /
   */
  public static function getUrl($url = '%a/%c/%m', $h = true){
    return ($h ? Aif::$url : '') . str_replace('//', '/',
      str_replace(
        array (
            '%a',
            '%c',
            '%m'
        ),
        array (
            Aif::$multi ? Aif::$application : '',
            Aif::$class,
            Aif::$method
        ), '/' . $url));
  }
  /**
   * Obtiene el nombre de la pagina web sin argumentos GET
   * @return string
   */
  public static function getPage(){
    return preg_replace('/(.*?)\?(.*?)$/', '$1', Aif::$uri);
  }
  /**
   * Devuelve un arreglo de secciones
   * @param string $path
   * @return mixed
   */
  public static function getSections($path = '/'){
    $path = mb_strtolower($path);
    $url = mb_strtolower(Aif::$url) . $path;
    $file = str_replace($url, '', mb_strtolower(self::getPage()));
    return explode('/', preg_replace('/^(.*?)\.(.*?)$/', '$1', $file));
  }
  /**
   * Regresa la direccion electronica de la aplicación
   * @return string
   */
  public static function url($dir = ''){
    $dir = self::$path . $dir;
    return Aif::$url . (Aif::$multi ? '/' . Aif::$application : '') . $dir;
  }
  /**
   * Concatena una path al URL de la aplicacion
   * @param string $path
   */
  public static function setUrl($path){
    self::$path = $path;
  }
  /**
   * Obtiene la url de donde fue el llamado.
   * @return string
   */
  public static function fromUrl(){
    $url = self::referrer();
    return $url ? str_replace(self::url() . '/', '',
      Aif::$protocol . '://' . $url) : FALSE;
  }
  /**
   * De donde se esta llamando ($_SERVER['HTTP_REFERER'] o
   * $_SERVER['HTTP_REFERER'])
   * @return boolean string
   */
  public static function referrer($ty=FALSE){
    if (!isset($_SERVER['HTTP_FROM']) && !isset($_SERVER['HTTP_REFERER'])){
      return false;
    }
    if (isset($_SERVER['HTTP_REFERER'])){
      $referer = parse_url($_SERVER['HTTP_REFERER']);
      if (!isset($referer['scheme']) && isset($referer['path'])){
        $referer = parse_url('api://' . $_SERVER['HTTP_REFERER']);
      }
    }
    if ((is_null($referer['host']) || $referer['host'] == '') &&
         isset($_SERVER['HTTP_FROM'])){
      $referer = parse_url($_SERVER['HTTP_FROM']);
      if (!isset($referer['scheme'])){
        $referer = parse_url('api://' . $_SERVER['HTTP_FROM']);
      }
    }

    return $ty?$referer['host']:($referer['host'] .
           (isset($referer['port']) ? ':' . $referer['port'] : '') .
           (Aif::$multi ? '/' . Aif::$application : '') . $referer['path']);
  }
  /**
   *
   * @return string
   */
  public static function getIpReal(){
    $externalContent = file_get_contents('http://checkip.dyndns.com/');
    preg_match('/Current IP Address: ([\[\]:.[0-9a-fA-F]+)</', $externalContent, $m);
    if($m[1])$externalIp = $m[1];
    return file_get_contents('http://phihag.de/ip/');
  }
  /**
   * Regresa el ip del usuario actual
   * @return string El ip deducido del usuario.
   *
   */
  //ip interna: http://checkip.dyndns.com/
  public static function getIp(){
    $ips = array (
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'REMOTE_HOST',
        'REMOTE_ADDR'
    );
    foreach ($ips as $ip){
      if (isset($_SERVER[$ip])) return $_SERVER[$ip];
    }
    return NULL;
  }

  /**
   * Metodo de compatibilidad
   *
   * @param [type] $data
   * @return void
   */
  public static function format($data){
    return $data;
  }

  /**
   * Comprueba si el request es por medio de una llama AJAX
   * @return boolean devuelve true si es una peticion ajax
   */
  public static function isAjax() {
    return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
  }
  /**
   * Obtiene las cabeseras del llamado
   * {@see http://www.php.net/manual/en/function.apache-request-headers.php}
   * @return array
   */
  public static function getHeaders($head = NULL){
    $header = self::$headers ? self::$headers : apache_request_headers();
    return ($head && isset($header[$head])) ? $header[$head] : $header;
  }
  /**
   * Regresa los datos POST de la solicitud web
   * @return array
   */
  public static function getPost($var = NULL){
    $dd = $var ? (isset($_POST[$var]) ? $_POST[$var] : NULL) : $_POST;
    return Aif::ecsc('aif/safe','validPost',$dd,$dd);
  }
  /**
   * Alias de ::getPost, obtiene un valor o arreglo de valores POST
   * @param string $var
   * @return mixed
   */
  public static function post($var = NULL){
    return self::getPost($var);
  }
  /**
   * Regresa los archivos de la solicitud web
   * @return array
   */
  public static function getFiles($var = NULL){
    $dd = $var ? (isset($_FILES[$var]) ? $_FILES[$var] : NULL) : $_FILES;
    return Aif::ecsc('aif/safe','validFile',$dd,$dd);
  }
  /**
   * Alias de getFiles
   * @param string $var
   */
  public static function files($var = NULL){
    return self::getFiles($var);
  }
  /**
   * Regresa los datos GET de la solicitud web
   * @return array
   */
  public static function getGet($var = NULL){
    $dd = $var ? (isset($_GET[$var]) ? $_GET[$var] : NULL) : $_GET;
    return Aif::ecsc('aif/safe','validGet',$dd,$dd);
  }
  /**
   * Alias de ::getGet, obtiene un valor o arreglo de valores GET
   * @param string $var
   * @return mixed
   */
  public static function get($var = NULL){
    return self::getGet($var);
  }
  /**
   * Devuelve variables GET y POST
   */
  public static function getGP($var = NULL){
    $tmp = self::getGet($var);
    $tmp || ($tmp = self::getPost($var));
    return $tmp? $tmp: NULL;
  }
  /**
   * Devuelve variables POST y GET
   */
  public static function getPG($var = NULL){
    $tmp = self::getPost($var);
    $tmp || ($tmp = self::getGet($var));
    return $tmp? $tmp: NULL;
  }
}
