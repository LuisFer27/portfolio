<?php
/**
 * Micelanea y utilidades generales
 *
 * Micelanea y utilidades generales
 * @author $Author$
 * @copyright Copyright (c) 2008, {@link http://www.aquainteractive.com AquaInteractive}
 * @package Aif
 * @since 2013-04-15
 * @subpackage Lib
 * @version $Id$
 */

/**
 * Micelanea y utilidades generales
 *
 * Micelanea y utilidades generales
 * @package Aif
 * @subpackage Lib
 */
class AifLib {
  /**
   * Almacena el nombre del sistema operativo del servidor
   * @var string
   */
  public static $serverOs = NULL;

  /**
   * Llamar un metodo de forma estatica
   * @param $class Nombre de la clase
   * @param $method Nombre del metodo estatic
   * @param $param Arreglo de parametros
   * @return mixed El resultado del metodo statico
   */
  public static function stCall($class, $method, $param = array()){
    if (Aif::autoLoad($class)){
      if (function_exists('forward_static_call_array')){
        return forward_static_call_array(array ($class, $method
        ), is_array($param) ? $param : array ($param
        ));
      }else{ // Versiones inferiores php 5.3 - Falta proteger cadenas
        eval("return $class::$method('" . implode("',", $param) . "');");
      }
    }
    return NULL;
  }
  /**
   * LLamar metodos de una clase
   * @param string $method
   * @param array $param
   * @return mixed
   */
  public static function call($class, $method, $param = array()){
    if (Aif::autoLoad($class)){
      $cls = new $class();
      return call_user_func_array(array ($cls, $method
      ), $param);
    }
    return NULL;
  }
  /**
   * Acceder a moetodos de una clase estatica
   * @param string $class
   * @param string $var Nombre de la variable
   * @return mixed valor de la variable
   */
  public static function stProperty($class, $var, $val = NULL){
    if (Aif::autoLoad($class)){
      if (function_exists('forward_static_call_array')){
        $val && ($class::$$var = $val);
        return $class::$$var;
      }else{ // Versiones inferiores php 5.3 - Falta proteger cadenas
        eval(
          ($val ? (" $class::$var='" . addslashes($class::$var) . "';") : '') .
           "  return $class::$var;");
      }
    }
    return NULL;
  }
  /**
   * Crea un objeto para llamar metodos estaticos de forma normal "->"
   * @param string $class
   * @return AifLibClass
   */
  public static function stObj($class = NULL){
    $class = $class ? $class : Aif::$application;
    return NULL;//new AifLibClass($class, true);
  }
  /**
   * Crea una instancia indirecta de un objeto
   * @param string $class Nombre de la clase
   * @return mixed NULL
   */
  public static function obj($class = NULL){
    $class = $class ? $class : Aif::$application;
    if (Aif::autoLoad($class)){
      return new $class();
    }
    return NULL;
  }
  /**
   * Regresa el nombre del sistema operativo
   * @return string El nombre del Sistema operativo
   */
  public static function getServerOs(){
    if (self::$serverOs) return self::$serverOs;
    self::$serverOs = mb_strtolower(PHP_OS);
    return self::$serverOs;
  }
  /**
   * Comprueba el sistema operativo del servidor
   * @param string $os Nombre del sistema operativo a comprobar
   * @return boolean
   */
  public static function isServerOs($os){
    return ($os == self::getServerOs());
  }
  /**
   * Comprueba si el sistema operaivo es una version de windows
   * @return boolean
   */
  public static function isWin(){
    return (substr(self::getServerOs(), 0, 3) == 'win');
  }
  /**
   * Comprueba si el sistema operativo del servidor alguna version de linux o *nix
   * @return boolean
   */
  public static function isLinux(){
    return (PHP_SHLIB_SUFFIX == 'so' || self::isServerOs('linux'));
  }
  /**
   * Obtiene un arreglo apartir de una url
   * @param string $url
   * @return mixed
   */
  public static function urlArray($url){
    $ps = parse_url($url);
    $scheme = $fragment = $path = $user = $pass = $query = $port = '';
    if (isset($ps['scheme'])){
      $type = strtolower($ps['scheme']);
      //print_r($ps); die();
      switch ($type){
        case 'db' :
          extract($ps);
          return array (
              'server' => $host,
              'dbname' => basename($path),
              'user' => $user,
              'password' => $pass,
              'port' => $port,
              'driver' => $fragment != '' ? $fragment : 'mysql'
          );
        case 'ftp' :
        case 'soap' :
        case 'php' :
        case 'mail' :
        case 'gmail':
        case 'sendgrid':
          extract($ps);
          $name = isset($fragment) ? $fragment : '';
          strpos($name, '?') && (list($name,$port)=explode('?',$name));
          return array (
              'type' => $type,
              'server' => $host,
              'email' => basename($path),
              'user' => $user,
              'password' => $pass,
              'name' => $name,
              'port' => isset($port)?$port:25
          );
        case 'soap' :
          return $ps;
        default :
          extract($ps);
          isset($fragment) && ($port = $fragment);
          $type != 'sqlite' && ($path = basename($path));
          return array (
              'server' => $host,
              'dbname' => $path,
              'user' => $user,
              'password' => $pass,
              'port' => $port,
              'driver' => $scheme
          );
      }
    }
    return FALSE;
  }

  /**
   * Obtine el primero valor que coincida con la expresion
   * @param array $ar
   * @param string $ex espresion
   * @return mixed boolean
   */
  public static function findFirst($ar, $ex){
    foreach ($ar as $name){
      if (preg_match("/$ex/", mb_strtolower($name))) return $name;
    }
    return FALSE;
  }
  /**
   * Obtiene una subcadena con un postfijo de una cadena.
   * @param string $str
   * @param integer $leng Tamaño
   * @param string $suf
   * @param string $postfijo
   * @return string
   */
  public static function subStr($str, $leng, $suf = '', $_suf = ''){
    $start = 0;
    if (is_integer($suf)){
      $start = $leng;
      $leng = $suf;
      $suf = $_suf;
    }
    return strlen($str) > $leng ? (substr($str, $start, $leng) . $suf) : $str;
  }
  /**
   * Obtiene cualquier cadena antes de un punto
   * @param string $str
   * @return string
   */
  public static function beforeDot($str = ''){
    if (strpos($str, '.') !== FALSE){
      $ar = explode('.', $str);
      $str = array_shift($ar);
    }
    return $str;
  }
  /**
   * Construye un uid unico para aplicaciones o bien transforma un valor
   * a su uid correspondiente
   * Ejemplo: 101 = 38B3EFF8-BAF5-6627-478E-C76A704E9B52
   * @param string $id
   * @return string
   */
  public static function generateUid($id = NULL){
    $id = md5($id ? $id : uniqid(microtime()));
    $ar = str_split(strtoupper($id), 4);
    return "$ar[0]$ar[1]-$ar[2]-$ar[3]-$ar[4]-$ar[5]$ar[6]$ar[7]";
  }

  /**
   * Devuelve una subcadena del tamaño $n al primer caracter de separacion de la cadena
   * @param string $s Cadenda
   * @param integer$n Tamaño de subcadena
   * @param string $fx postfijo de la cadena
   * @return string
   */
  public static function subWrapStr($s, $n=1,$fx='') {
    $a = explode('~', wordwrap($s, $n, '~'));
    return $a[0].(isset($a[1])?$fx:'');
  }

 /**
  * Require comandos de linea de comandos iconv y dos2unix
  * @param [type] $dir
  * @param [type] $src
  * @param string $ext
  * @param boolean $show
  * @return void
  */
  public static function iconvDirUtf8($dir,$src=NULL,$ext='php|phtml|css|js',$show=TRUE){
    if (!file_exists($dir)) return TRUE;
    if (!is_dir($dir) || is_link($dir)){
      $cmd = strtoupper(substr(PHP_OS, 0, 3))=='LIN'?'file -bi':'file -I';
      $charset = preg_replace('/^(.*?)charset\=(.*?)$/','$2', exec("$cmd $dir "));
      if($charset != 'us-ascii' && $charset != 'binary' && $charset!='utf-8'
          && preg_match('/\.('.$ext.'$)/', $dir)){
        echo "Convert $charset > utf-8 - $dir\n";
        return $src ? exec("iconv -f $charset -t utf-8 $dir > $src")
                    : exec("dos2unix $dir");
      }
      echo "--- $dir\n";
      return $src?copy($dir,$src):TRUE;

    }
    $src && (file_exists($src) || mkdir($src,0775,true));
    $ar = scandir($dir);
    foreach ($ar as $item){
      if ($item == '.' || $item == '..') continue;
      self::iconvDirUtf8("$dir/$item",$src?"$src/$item":NULL,$ext,$show);
    }
  }




}
