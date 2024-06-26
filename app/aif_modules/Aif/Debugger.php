<?php
/**
 * Errores y warnings del Aif
 *
 * Gestion y manejo errores y warnings del Aif
 * @author $Author$
 * @copyright Copyright (c) 2008, {@link http://www.aquainteractive.com
 *            AquaInteractive}
 * @package Aif
 * @since 2013-04-15
 * @subpackage Core
 * @version 1.0.0
 */
/**
 * Clase de errores y debug del Aif
 *
 * Clase de GestiC3n y manejo errores y debug del Aif
 * @package Aif
 * @subpackage Core
 */
class AifDebugger {
  /**
   * Undocumented variable
   *
   * @var string
   */
  public static $version = '1.0.0';

  public static $debug = array ();
  /**
   * Interrumple la ejecucion y despliega un mensaje de error
   * @param string $text mensaje de error
   */
  public static function error($text){
    echo $text . '<br /> ';
    echo self::out(true);
    exit();
  }
  /**
   * Registra mensaje en el debug en formato variable valor
   * @param String $tag Nombre del tag
   * @param Strig $val Valor
   */
  public static function set($sec, $tag=NULL, $val = NULL){
      if($val===NULL){
        $val = $tag;
        $tag='';
      }
      $sec = strtoupper($sec);
      $ty = (is_array($val) || is_object($val));
      isset(self::$debug[$sec]) || (self::$debug[$sec]=array());
      self::$debug[$sec][$tag] = $ty?self::getData($val): $val;
  }
  /**
   * Registra mensaje en el debug
   * @param string $text mensaje
   */
  public static function getData($ar = NULL){
    $rs ='<div class="Aif-array">';
     foreach($ar as $idx=>$val){
        $rs.='<div><strong>'.$idx.'</strong>: ';
        if(is_array($val) || is_object($val)) $rs.=self::getData($val);
        else $rs.=$val;
        $rs.='</div>';
     }
    $rs.='</div>';
    return $rs;
  }
  /**
   * Imprime la salida del debug
   * @param boolean $rtn Forza a regresar una cadena sin imprimirla en pantalla
   * @return string
   */
  public static function out($rtn = FALSE,$debug=NULL){
    $debug || ($debug = Aif::$debugger);
    //$debug = $debug.'';
    !($debug & 2) && self::$debug = array ();
    if ($debug & 4){
      $ar = get_class_vars('Aif');
      $rs = array();
      foreach($ar as $name=>$data){
        if(in_array($name,array('pack','cfg','debugDef'))) continue;
        $ar[$name]=$data;
      }
      self::set('AIF ATTRIBUTES (Aif::${attr})',$ar);
      self::set('CONSTANTS','',array (
              'AIF_PATH' => AIF_PATH,
              'AIF_WEB' => AIF_WEB,
              'AIF_APP' => AIF_APP
      ));
    }
    // Configuracion
    $debug & 8 && self::set(Aif::$application.'.json', Aif::$cfg);
    // Variables
    if($debug & 16){
       //self::set('Variables retornadas',AifView::$__vars);
       self::set('POST',$_POST);
       self::set('GET',$_GET);
       self::set('FILE',$_FILES);
       self::set('COOKIES',$_COOKIE);
       self::set('SESSION',isset($_SESSION)?$_SESSION:'');
    }

    if ($debug & 64){
      self::set('BROWSER',$_SERVER['HTTP_USER_AGENT']);
      self::set('SERVER',$_SERVER);
      //self::set('Information HEADER',$http_response_header);

      self::set('PHP',
        array (
            //
            'Version' => PHP_VERSION,
            'Server API (SAPI)' => php_sapi_name(),
            'Archivo ini' => php_ini_loaded_file(),
            'Modules' => implode(', ', get_loaded_extensions()),
            'Extension dinamic' => ini_get('extension'),
            'Path de extensiones' => PHP_EXTENSION_DIR,

            // Opciones de lenguaje B6
            'implicit_flush' => ini_get('implicit_flush'),
            'output_handler' => ini_get('output_handler'),

            'short_open_tag' => ini_get('short_open_tag'),
            'precision' => ini_get('precision'),
            'serialize_precision' => ini_get('serialize_precision'),
            'y2k_compliance' => ini_get('y2k_compliance'),
            'allow_call_time_pass_referenc' => ini_get(
              'allow_call_time_pass_referenc'),  // Obsoleta 5.4
            'disable_functions' => ini_get('disable_functions'),  // seguridad
            'disable_classes' => ini_get('disable_classes'),  // seguididad
            'exit_on_timeout' => ini_get('exit_on_timeout'),
            'expose_php' => ini_get('expose_php'),
            // LC-mite de recursos B6
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'max_input_time' => ini_get('max_input_time'),

            // Ajuste del rendimiento B6
            'realpath_cache_size' => ini_get('realpath_cache_size'),
            'realpath_cache_ttl' => ini_get('realpath_cache_ttl'),
            // Manejo de datos B6
            'track_vars' => ini_get('track_vars'),  // $_ENV, $_GET, $_POST, $_COOKIE, y $_SERVER
            'arg_separator.output' => ini_get('arg_separator.output'),
            'arg_separator.input' => ini_get('arg_separator.input'),
            'variables_order' => ini_get('variables_order'),
            'auto_globals_jit' => ini_get('auto_globals_jit'),  // activada resultarC! en una mejora del rendimiento
            'register_globals' => ini_get('register_globals'),  // PHP <= 4.2.3. Eliminada en PHP 5.4.0.
            'enable_post_data_reading' => ini_get('enable_post_data_reading'),  // Al deshabilitar esta opciC3n hace que $_POST y $_FILES no sean rellenados
            'post_max_size' => ini_get('post_max_size'),
            'upload_tmp_dir' => ini_get('upload_tmp_dir'),
            'gpc_order' => ini_get('gpc_order'),
            'auto_prepend_file' => ini_get('auto_prepend_file'),
            'auto_append_file' => ini_get('auto_append_file'),
            'default_mimetype' => ini_get('default_mimetype'),
            'default_charset' => ini_get('default_charset'),
            // Rutas y directorios B6
            'include_path' => ini_get('include_path'),
            'extension_dir' => ini_get('extension_dir'),
            'fastcgi.logging' => ini_get('fastcgi.logging'),
            // Subida de ficheros B6
            'file_uploads' => ini_get('file_uploads'),  // permitir o no la subida de ficheros mediante HTTP
            'upload_tmp_dir' => ini_get('upload_tmp_dir'),
            'max_input_nesting_level' => ini_get('max_input_nesting_level'),
            'max_input_vars' => ini_get('max_input_vars'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'max_file_uploads' => ini_get('max_file_uploads'),
            // ConfiguraciC3n de SQL en general B6
            'sql.safe_mode' => ini_get('sql.safe_mode'),
            // session
            'session.save_path' => ini_get('session.save_path'),
            //
            'date.timezone' => ini_get('date.timezone'),
            //
            'default_socket_timeout' => ini_get('default_socket_timeout'),
            //
            'display_errors' => ini_get('display_errors'),
            'display_startup_errors' => ini_get('display_startup_errors'),
            'error_prepend_string' => ini_get('error_prepend_string'),
            'error_reporting' => ini_get('error_reporting'),
            //
            'htscanner.config_file' => ini_get('htscanner.config_file'),
            // Mail
            'sendmail_from' => ini_get('sendmail_from'),
            'sendmail_path' => ini_get('sendmail_path'),
            'SMTP' => ini_get('SMTP'),
            'smtp_port' => ini_get('smtp_port'),
            //
            'pcre.backtrack_limit' => ini_get('pcre.backtrack_limit'),
            'pcre.recursion_limit' => ini_get('pcre.recursion_limit')
        )
        );
    }

    if ($debug & 30){
      $result = '';
      $result .= '<br><div class="Aif-debug">';
      foreach (self::$debug as $sec => $rs){
        $result .= '<div class="Aif-secc"> <h2>'.$sec.'</h2>' ;
       foreach ($rs as $tag => $text){
          $result .= '<div>' .($tag?'<span>'.$tag.'</span>: ':''). $text . "</div>\n";
        }
        $result .= "</div>\n";
      }
      $result .= '</div><br> <br>';
      if ($rtn) return $result;


      echo '<style> #idAifDebugger{ font-family: Verdana, Geneva, sans-serif; font-size: 10px; z-index:9999991; position: fixed; left: 0px; bottom: 0px; border-top: 3px solid #F55; width:100%; height:20%;  min-height:150px; background-color: #E0F8E0;}  #idAifDebugger.contract{ width: 0%; left:-800px; }  #idAifDebugger.expanded{ height: 100%; } #idAifDebugger section{ display:block; padding: 5px; padding-top:15px; color:#008; width: 100%; height: 100%; bottom: 0px; left: 0px; overflow:auto;} #idAifDebugger header {position: absolute; left:50px;  top: -15px;  display: inline-block;  width: auto;  height: 20px;  border: 2px solid #F44;  font-size: 14px; padding:2px 20px;  background-color: #F55;  color: #FFF; z-index:9999992;} #idAifDebugger.expanded header{ top:0px; } #idAifDebugger header span{ text-align: center; position:absolute; display: inline-block;  left:-40px; top:0px; width: 26px; height: 26px; font-size: 18px; font-weight: bold; cursor: pointer; background-color: #F55; color:#FFF;  } #idAifDebugger header span#idAifDebuggerMax{ left:auto; right:-40px; } #idAifDebugger.contract header span#idAifDebuggerLeft{ left:750px; opacity: 0.3; filter: alpha(opacity=30); }  #idAifDebugger header span#idAifDebuggerLeft:after{ font-size: 12px; content: "\25C0"; } #idAifDebugger header span#idAifDebuggerMax:after{ content: "\25b2"; }  #idAifDebugger.contract header span#idAifDebuggerLeft:after{font-size: 12px; content: "\25b6"; } #idAifDebugger.expanded header span#idAifDebuggerMax:after{ content: "\25bc"; } .Aif-secc{border: 1px solid #888; padding:5px; margin-bottom:5px;} .Aif-secc h2{ color:#933; } .Aif-secc span{  font-weight: bold; } .Aif-array{ padding:5px; padding-left:20px;}</style>';
      echo '<div id="idAifDebugger" class="contract"><header><span id="idAifDebuggerLeft"></span>Debugger Aif ('.Aif::$debugger.')<span id="idAifDebuggerMax"></span></header><section>'.$result.'</section></div>';
      echo '<script type="text/javascript">'.
      'if(window.jQuery){'.
      ' $("#idAifDebuggerMax").click(function(){$("#idAifDebugger").toggleClass("expanded");});'.
      ' $("#idAifDebuggerLeft").click(function(){$("#idAifDebugger").toggleClass("contract");});'.
      '}else{ '.
      '  function hasClass(ele, clsName) {'.
      '    var el = ele.className;'.
      '    el = el.split(" ");'.
      '    if(el.indexOf(clsName) > -1){'.
      '        var cIndex = el.indexOf(clsName);'.
      '        el.splice(cIndex, 1);'.
      '        ele.className = " ";'.
      '        el.forEach(function(item, index){'.
      '          ele.className += " " + item;'.
      '        })'.
      '    }'.
      '    else {'.
      '        el.push(clsName);'.
      '        ele.className = " ";'.
      '        el.forEach(function(item, index){'.
      '          ele.className += " " + item;'.
      '        })'.
      '    }'.
      ' }'.
      " var el = document.getElementById('idAifDebugger'); ".
      " var b1 = document.getElementById('idAifDebuggerMax');".
      " var b2 = document.getElementById('idAifDebuggerLeft');".
      " b1.addEventListener('click',function(){ hasClass(el,'expanded'); });".
      " b2.addEventListener('click',function(){ hasClass(el,'contract'); });".
      '}'.
      '</script>';
    }
  }
}
