<?php
/**
 * Clase principal de Aqua Interactive Framework (Aif)
 *
 * Agrupa los metC3dos necesarios para ejecutar las aplicaciones asi como
 * preparar los elementos necesarios a disponibilidad de los programadores para
 * que puedan cubrirse sus requerimientos mas bbB0sicos.
 * @author $Author$
 * @copyright Copyright (c) 2008, {@link http://www.aquainteractive.com AquaInteractive}
 * @package Aif
 * @since 2013-04-15
 * @subpackage Core
 * @version 2.0.0
 */

/**
 * Clase principal de Aif
 *
 * @package Aif
 * @subpackage Core
 */
class Aif {
  /**
   * @var object Objeto de configuración de la aplicación en curso que se construye a partir
   * de una cadena de estructura json y Aif.json.
   */
  public static $cfg = NULL;
  /**
   * @var string Cadena original URI devuelta por el servidor web
   */
  public static $uri = NULL;
  /**
   * @var boolean Se activa para el soporte de multiple aplicaciones desde una misma
   * instalacion
   */
  public static $multi = TRUE;
  /**
   * @var string Nombre del protocolo web
   */
  public static $protocol = 'http';
  /**
   * @var string Nombre del dominio web
   */
  public static $domain = '';
  /**
   * @var string Nombre del sitio web (protocolo + domain)
   */
  public static $site = '';
  /**
   * @var string Directorio web
   */
  public static $web = '';
  /**
   * @var string Nombre base de la url de la aplicaciC3n
   */
  public static $url = '';
  /**
   * @var string Nombre de la aplicaciC3n en aplicaciC3n
   */
  public static $application = '';
  /**
   * @var string Nombre virtual de la clase en aplicaciC3n (solicitada por el cliente web)
   */
  public static $class = '';
  /**
   * @var string Nombre real de la clase en aplicaciC3n
   */
  public static $className = '';
  /**
   * @var string Nombre del metC3do en aplicaciC3n. (por default es "index")
   */
  public static $method = 'index';
  /**
   * @var array Lista de parametros del metC3do en aplicaciC3n
   */
  public static $param = array ();
  /**
   * @var string Formato de salida, procesa la salida del metC3do en aplicaciC3n
   */
  public static $render = 'html';
  /**
   * @var integer Nivel de depuraciC3n de aplicaciones, Se puede combinar por ejemplo:
   * 3 = 1 y 2, 6 = 2 y 4, 15 = 1,2,4,8
   * 0 = Deshabilta todos los mensajes (Modo producciC3n)
   * 1 = Habilita la salida de errores y warnings de PHP
   * 2 = Habilita la salida de informacion de mensajes: AifDebug
   * 4 = Habilita la salida de las propiedades de Aif
   */
  public static $debugger = 0;
  /**
   * @var array Definicione de debugger
   */
  public static $debugDef = array();
  /**
   * @var boolean Se active cuando se utiliza el tipo de ejecucion query
   */
  public static $isQry = FALSE;
  /**
   * @var string Version de Aif
   */
  public static $version = '2.0.0';
/**
 * @var string Tipo de codificacion que se debe utilizar para los proyectos
 */
  public static $charset = 'UTF-8';
/**
 * @var string Toma el nombre del servidor
 */
  public static $webServer = "";
/**
 * @var array Listado de dependencia de clases
 */
public static $cls = array();
/**
 * @var array Listado de cambios de clases
 */
public static $chg = array();

public static $_nfnd=NULL;
/**
 * Carga un archivo de codigo
 * @param [type] $file
 * @param string $class
 * @return void
 */
  private static function loadPhp($file, $class = '') {
    include_once $file;
    self::debugger('class',$class,$file);
    return TRUE;
  }
   /**
   * Recalcula lel formato de salida del metodo
   */
  private static function searchRender($name){
    if(strpos ( $name, '?' ) !== FALSE)
    $name = preg_replace('/^(.*?)\?(.*?)$/','$1',$name);
    if (strpos ( $name, '.' ) !== FALSE) {
      $ar = explode ( '.', $name );
      $name = array_shift ( $ar );
      self::$render = self::request(mb_strtolower ( array_pop ( $ar ) ) );
      return $name;
    }
    return FALSE;
  }
  private static function methodRender() {
    $name = self::searchRender(self::$method);
    if ($name !== FALSE) self::$method = $name;
    else{ 
      $x = count(self::$param);
      while($x--) 
        if(($name =self::searchRender(self::$param[$x]))){
          self::$param[$x] = $name;
           break;
        }
    }
    self::$method = self::request (self::$method );
  }
  /**
   * Realiza la autocarga las clases para su utilzacibb	%n en la aplicaciC3n
   * @param string $className Nombre de la clase
   */
  public static function autoLoad($class) {
    $name = preg_replace ( '/([a-z0-9])([A-Z])/', "$1/$2", $class );
    $file = $name . '.php';
    isset(self::$cfg->alias) && ($file = str_replace(self::$application, self::$cfg->alias, $file));
    $path = defined ( 'AIF_APP_' ) ? AIF_APP_ : AIF_APP;
    if (file_exists ( $path . '/' . $file )) {
      return self::loadPhp ( $path . '/' . $file, $class );
    } elseif (defined ( 'AIF_APP_' ) && file_exists ( AIF_APP_ . '/' . $file )) {
      return self::loadPhp ( AIF_APP_ . '/' . $file, $class );
    }elseif(self::exists($name)){
      return self::loadPhp ( $path . '/aif_modules/' . $file, $class );
    }
    self::debugger('error',$class,'Not found');
    return FALSE;
  }
  /**
   * Envia cadena y termina la ejecucion del programa
   */
  public static function error(){
    if(self::exists('aif/error')) return self::csc('aif/error','send',func_get_args());
    die(implode(' ',func_get_args()));
  }
  /**
   * Metodo para enviar valores por el usuario
   * @param [type] $level
   * @param [type] $title
   * @param [type] $val
   */
  public static function debugger($sec,$title,$val,$debug=0){
    if(!self::$debugger)return FALSE;
    if(self::exists('aif/debugger'))return self::csc('aif/debugger','set',func_get_args());
    isset(self::$debugDef[$sec]) || (self::$debugDef[$sec]=array());
    self::$debugDef[$sec][$title]=$val;
  }
  /**
   * ImplementaciC3n que envia inicio y terminacion de la cache
   * @param [type] $ty
   */
  public static function cache($ty){
    if(self::exists('aif/cache'))return self::csc('aif/cache','run',$ty);
  }
  /**
   * Aplica un formato para los parametros principales de entra Aif
   * @param [type] $data
   * @return void
   */
  public static function request($data){
    if(self::exists('aif/request'))return self::csc('aif/request','format',func_get_args());
    return $data;
  }
  /**
   * Eniva respuesta y salida de los resultados
   * @param [type] $result
   */
  public static function response($result){
    if(defined('AIF_COMMAND'))die();
    self::cache('start');
    $rs = self::ecsc('aif/view','send',func_get_args(),FALSE);
    $rs = $rs?$rs:self::ecsc('aif/html','send',func_get_args(),FALSE);
    $rs = $rs?$rs:self::ecsc('aif/response','send',func_get_args(),FALSE);
    if(!$rs){
      if ($result && (is_string ($result) || is_numeric ($result)))
        echo $result;
      else
        echo isset(self::$cfg->exec)?'':'return: '.print_r($result,TRUE);
    }
    self::cache('stop');
  }
  /**
   * Salida del debugger
   */
  public static function debuggerOut(){
    if(!self::$debugger)return FALSE;
    if(self::exists('aif/debugger'))return self::csc('aif/debugger','out');
    echo '<pre>'.str_pad('',40,'-')."\n".'<h2>DEBUGGER AIF</h2>'.str_pad('',40,'-');
    if(self::$debugger & 2) {
        echo "\n<B>INFORMATION</B>\n";
        print_r(self::$debugDef);
    }
    if(self::$debugger & 4) {
        echo str_pad('',40,'-')."\n<B>CONSTANT</B>\n";
        print_r(array (
              'AIF_PATH' => AIF_PATH,
              'AIF_WEB' => AIF_WEB,
              'AIF_APP' => AIF_APP
      ));
        echo str_pad('',40,'-')."\n<B>ATTRIBUTES</B>\n";
        print_r(@get_class_vars("Aif"));
    }
    echo '</pre>';
    return '';
  }
  /**
   * Undocumented function
   */
  public static function csc($class,$method,$param=array()){
    $class = strtolower($class);
    isset(self::$chg[$class]) && $class = self::$chg[$class];
    $r = explode('/',$class); $class =''; foreach($r as $c)$class.=ucfirst($c);
   // echo "M:".$class."\n";
    return self::callStatic($class, $method,is_array($param)?$param:array($param));
  }
/**
 * Undocumented function  
 */
  public static function callStatic($class,$method,$param=array()){
    return forward_static_call_array(array($class, $method),$param);
  }
  /**
   * Undocumented function
   */
  public static function call($class,$method,$param=array()){
    return call_user_func_array (array($class,$method),$param);
  }
/**
 * Valida si existe un paquete
 * @param [type] $clss
 * @return void
 */
  public static function exists($class,$debug=FALSE){
    $class = strtolower($class);
    isset(self::$chg[$class]) && $class = self::$chg[$class];
    if(isset(self::$cls[$class]))return TRUE;
    $debug && self::debugger('required',$class,'aif install '.$class);
    return FALSE;
  }

  public static function ecsc($class,$method,$param=array(),$rs=''){
    return self::exists($class)?self::csc($class,$method,$param):$rs;
  }
  /**
   * Inicia el proceso de solicitud del servidor para resolver variables
   * principales del Aif
   */
  public static function initialize() {
    $acm = '';
    $sv = isset ( $_SERVER ) ? $_SERVER : array ();__('./');
    if(isset ($sv['HTTPS']) && $sv['HTTPS']=='on' )self::$protocol = 'https';
    elseif(isset ($sv['HTTP_CLUSTER_HTTPS']) && $sv['HTTP_CLUSTER_HTTPS']=='on' )self::$protocol = 'https';
    elseif (isset ( $sv ['SERVER_PROTOCOL'] ) && stripos ( $sv ['SERVER_PROTOCOL'], 'https' ) !== FALSE)
      self::$protocol = 'https';
    if(isset($sv['SERVER_SOFTWARE'])){
      self::$webServer = strtolower(strstr($sv['SERVER_SOFTWARE'],'/',true));
      if(self::$webServer=='nginx')unset($sv ['PHP_SELF']);
    }
    self::$domain = isset ( $sv ["HTTP_HOST"] ) ? $sv ["HTTP_HOST"] : 'exec';
    self::$site = $uri = trim ( self::$protocol . '://' . self::$domain );
    self::$web = isset ( $sv ['PHP_SELF'] ) ? $sv ['PHP_SELF'] : (isset ( $sv ['SCRIPT_NAME'] ) ? $sv ['SCRIPT_NAME'] : '');
    self::$web = dirname ( self::$web );
    $qrys = array ('','',
        'REDIRECT_QUERY_STRING',
        'QUERY_STRING'
    );
    $ur = array (
        'REQUEST_URI',
        'HTTP_X_REWRITE_URL',
        'REDIRECT_URL'
    );
    $ur [] = 'ORIG_PATH_INFO';
    foreach ( $ur as $id => $ul ) {
      if (isset ( $sv [$ul] )) {
        strpos ( strtolower ( $sv ['SERVER_SOFTWARE'] ), 'mongoose' ) !== FALSE && ($qrys [$id] = 'QUERY_STRING');
        $acm = $sv [$ul];
        $uri .= $acm;
        if ($qrys [$id] && isset ( $sv [$qrys [$id]] ))
          $uri .= '?' . $sv [$qrys [$id]];
        break;
      }
    }
    self::$uri = $uri;
    if (strpos ( $uri, '?aif=' ) !== FALSE) {
      self::$isQry = TRUE;
      self::$site .= (strlen ( self::$web ) > 1 ? self::$web : '');
      list ( $ur, $acm ) = explode ( ':::', preg_replace ( '/^(.*?)\?aif(=\/|=)(.*?)$/', '$1:::$3', $uri ) );
      self::$url = $ur . '?aif=';
    }
    if (isset ( self::$cfg->name )) {
      self::$multi = FALSE;
      $app = self::$cfg->name;
    } else {
      $app = preg_replace ( '/^[\/]+(.*?)\/(.*?)$/', '$1', '/' . $acm . '/' );
      self::$cfg->name = $app;
    }
    if (preg_match ( '/' . $app . '/', $acm ) || ! self::$multi) {
      if (self::$isQry)
        $acm = preg_replace ( '/^' . $app . '\//', '', $acm );
      else {
        if (self::$multi) {
          $ar = explode ( '/' . $app . '/', $uri );
          if (isset ( $ar [1] )) {
            self::$site = array_shift ( $ar );
            $acm = implode ( '/' . $app . '/', $ar );
          }
        }
        self::$url = self::$site;
        if (self::$web) {
          if (strlen ( self::$web ) > 1) {
            self::$url = self::$site = (self::$site . self::$web);
          } else
            $acm = preg_replace ( '/^\//', '', $acm );
        }
      }
      $app = self::request($app );
      self::$application = self::$className = self::$class = $app;
      $acm = self::$isQry ? $acm : str_replace ( self::$web . '/', '', $acm );
      $ar = explode ( '/', $acm );
      if (($acm = array_shift ( $ar ))) {
        self::$class = self::request ($acm );
        if (($acm = array_shift ( $ar ))) {
          self::$method = $acm;
          self::$param = self::request (array_values ( $ar ) );
        }
      }
      self::methodRender ();
      if (isset ( self::$cfg->ucase ) && self::$cfg->ucase)
        self::$class = ucfirst ( self::$class );
    } else self::error('Not defined application (' . $app . ')');__('../');
  }
/**
 * Establece las variables locales a espaC1ol y zona horaria
 */
  public static function setLocale($g='mx',$z=''){
    $g || ($g='m');
    $z || ($z='America/Mexico_City');
    if(self::exists('aif/locale')) return self::csc('aif/locale','sec',func_get_args());
    $l = array('es'=>'es','en'=>'en','mx'=>'es');
    date_default_timezone_set ($z);
    isset($l[$g]) && setlocale(LC_ALL,array($l[$g],$l[$g],$l[$g].'-'.strtoupper($g)));
  }
  /**
   * Carga y devuelve la configuraciC3n Aif.json y la que se envia por parametro del run({config})
   * @param [type] $cfg
   * @return object coleccion de datos de configuracion
   */
  public static function loadConfig($cfg=null){
    $cfg = json_decode($cfg ? $cfg : '{}');
    $name = isset($cfg->name)?$cfg->name:'Aif';
    $js = AIF_APP.'/'.$name.'.json';
    $pk = file_exists($js)?(array)json_decode(file_get_contents($js)):array();
    $cfg = (object)array_merge($pk,(array)$cfg);
    if(isset($cfg->debugger)){
      $cfg->debugger && error_reporting ( E_ALL | E_STRICT );
      ini_set ( 'display_errors', $cfg->debugger ? '1' : '0' );
      self::$debugger = $cfg->debugger;
    }
    isset($cfg->version) && (self::$version = $cfg->version);
    isset($cfg->charset) && (self::$charset = $cfg->charset);
    if(isset($cfg->class))foreach($cfg->class as $key=>$val)self::$cls[$key]=$val;
    if(isset($cfg->changes))foreach($cfg->changes as $key=>$val)self::$chg[$key]=$val;
    self::setLocale(isset($cfg->locale)?$cfg->locale:'',isset($cfg->timezone)?$cfg->timezone:'');
    return $cfg; 
  }
  /**
   * Ejecuta la aplicaciC3n
   * @param string $config (opcional) Cadena de configuraciC3n en formato ini
   */
  public static function run($config = '{}') {
    if(defined('AIF_COMMAND_ARGV') && !defined('AIF_COMMAND'))return self::runCommand(AIF_COMMAND_ARGV,$config);
    self::$cfg = self::loadConfig($config);
    self::initialize ();
    if(defined('AIF_COMMAND')) Aif::runCommand();
    $rs = NULL;
    if (self::$application && self::autoLoad ( self::$application )) {
      $cls = new self::$application ();
      if (self::$application != self::$class) {
        self::$className = self::$application . self::$class;
        if (! self::autoLoad ( self::$className ))
          self::error (self::$className,'Not defined');
        $cls = new self::$className ();
      }
      if (! method_exists ( $cls, self::$method ) && ! method_exists ( $cls, '__call' ))
        if(self::$_nfnd)$cls = self::runNotfound();
        else self::error (self::$className.'::'.self::$method , 'Not defined ');
      $rs = self::call($cls,self::$method,self::$param);
      self::response($rs);
    } else
      self::error ('Application','Not defined');
      self::debuggerOut($rs);
  }
  /**
   * Utiliza los metodos de la aplicacion si existen a nivel de clases
   */
  public static function methodInApplication() {
    $methods = get_class_methods ( self::$application );
    if (in_array ( self::$class, $methods ) || strpos ( self::$class, '.' ) !== FALSE || (! class_exists ( self::$application . self::$class ) && (self::$application . self::$class != self::$application . self::$application) && in_array ( '__call', $methods ))) {
      self::$param = array_merge(array(self::$method),self::$param);
      self::$method = self::$class;
      self::$className = self::$class = self::$application;
      self::methodRender ();
    }
  }
  /**
   * Undocumented function
   */
  public static function notFound($class,$method=NULL){
    self::$_nfnd=array($method?$class:self::$application,$method?$method:$class);
  }
  /**
   * Undocumented function
   */
  public static function runNotfound(){
      self::$className=(self::$application==self::$_nfnd[0]?'':self::$application).self::$_nfnd[0];
      self::$method=self::$_nfnd[1];
      if (! self::autoLoad(self::$className ))self::error (self::$className,'Not defined');
      $cls = new self::$className();
      if (!method_exists($cls,self::$method))self::error(self::$className.'::'.self::$method ,'Not defined');
      return $cls;
  }
  /**
   * Verifica si el mC)todo actual de la aplicaciC3n es uno de la lista de metodos
   * @param string $method [...] Lista de los metC3dos a comprobar
   * @return boolean
   */
  public static function inAppMethod() {
    $ar = func_get_args ();
    return $ar ? self::inClass ( self::$application ) && self::inMethod ( $ar ) : FALSE;
  }
  /**
   * Verifica si clase actual es una de la lista de clases
   * @param a string $class [...] Lista de clases a comprobar
   * @return boolean
   */
  public static function inClass() {
    $ar = func_get_args ();
    return $ar ? in_array ( self::$class, is_array ( $ar [0] ) ? $ar [0] : $ar ) : FALSE;
  }
  /**
   * Verifica si el metodo actual es una de la lista de metodos
   * @param  string $method [...] Lista de metodos a comprobar
   * @return boolean
   */
  public static function inMethod() {
    $ar = func_get_args ();
    return $ar ? in_array ( self::$method, is_array ( $ar [0] ) ? $ar [0] : $ar ) : FALSE;
  }
  /**
   * Habilita como parametros las variables recibidas por metodo GET
   * Ej. ?param1=valor1&param2=valor2 ==> metodo(valo1, valor2)
   */
  public static function parametersByGet() {
    if (isset ( $_GET ) && count ( $_GET )) {
      if (self::$isQry)unset ( $_GET ['aif'] );
      self::$param = array_merge ( self::$param, array_values ( $_GET ) );
    }
  }
  /**
   * Habilita como parametros las variables recibidas por metodo POST
   * Ej. [param1]=valor1, [param2]=valor2 ==> metodo(valo1, valor2)
   */
  public static function parametersByPost() {
    if (isset ( $_POST ) && count ( $_POST )) {
      self::$param = array_merge ( self::$param, array_values ( $_POST ) );
    }
  }
  /**
   * Undocumented function
   *
   * @param [type] $name
   * @return void
   */
  public static function getElementConfig($name){
    return isset(Aif::$cfg->{$name}) ? Aif::$cfg->{$name} : NULL;
  }
  /**
   * Undocumented function
   *
   * @param [type] $class
   * @param string $method
   * @return void
   */
  public static function set($class,$method='index',$param=NULL){
    self::$class=$class;
    self::$method=$method;
    if($param) self::$param = is_array($param)?$param:array($param);
  }
  /**
   * Ejecuta desde la liena de comandos una aplicacion
   * @return [type] [description]
   */
  public static function runCommand($argv=NULL,$cfg=NULL) {
    if($argv && $cfg){
      define('AIF_COMMAND', 1);
      isset($argv[1]) && define('AIF_COMMAND_CLASS', $argv[1]);
      isset($argv[2]) && define('AIF_COMMAND_METHOD', $argv[2]);
      isset($argv[3]) && define('AIF_COMMAND_PARRAM', implode('|',array_slice($argv,3)));
      self::run($cfg);
    }else{
      defined('AIF_COMMAND_CLASS') && (self::$class = AIF_COMMAND_CLASS);
      defined('AIF_COMMAND_METHOD') && (self::$method = AIF_COMMAND_METHOD);
      defined('AIF_COMMAND_PARRAM') && (self::$param = explode('|',AIF_COMMAND_PARRAM));
    }
  }
  /**
   * Aif como aplicaciC3n
   */
  public function index() {
    return 'Aif '.self::$version." is installed and ready.";
  }
}


if(isset($argv[0])) define('AIF_COMMAND_ARGV', $argv);