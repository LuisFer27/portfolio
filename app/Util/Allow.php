<?php

/**
 * Manejo de permisos y privilegios de usuarios
 */
class UtilAllow {

  public static $_allow = array();
  public static $_content = '_allow_content';
  public static $_forbidden = null;
  public static $current_module= '';
  
  /**
   * Asigna la vista a utilizar en caso de acceso denegado 
   *
   * @param [type] $page
   * @return void
   */
  public static function setViewForbidden($page) {
    self::$_forbidden = $page;
  }

  /**
   * con algun modulo que tenga asignado.
   *
   * @return void
   */
  public static function some() {
    if(!self::any(func_get_args())) {
      self::forbidden();
    }
  }

  /**
   * Solo se permite los modulos pasado por parametros.
   *
   * @return void
   */
  public static function only() {
    if(!self::any(func_get_args())) {
      self::forbidden();
    }
  }
  /**
   * Mensaje de salida fulminante cuando no se tiene permisos
   *
   * @return void
   */
  public static function forbidden() {
    if(self::$_forbidden) {
      $code = '403';
      $error = 'forbidden';
      $name = self::$current_module;
      AifView::clean();
      AifView::set(self::$_forbidden);
      Aif::response(get_defined_vars());
      exit;
    } 
    header('HTTP/1.0 403 Forbidden');
    exit;
  }
  /**
   * Registra los permisos del usuario de la session
   *
   * @param [type] $allow
   * @return void
   */
  public static function set($allow) { 
    $app = Aif::$application;
    AifSession::set(self::$_content, $allow);
    return true;
  }
  /**
   * Recupera los permisos del usuario de la session
   *
   * @return void
   */
  public static function get(){
    return AifSession::get(self::$_content);
  }

  /**
   * Convierte los permisos del usuario a un formato de arreglos
   *
   * @return void
   */
  public static function getModules() {
    $mod = array();
    $rs = (array) self::get();
    foreach($rs as $name => $allow ) {
      $mod[$name] = explode(',', $allow);
    }
    return $mod;
  }
  /**
   * Verifica si tienes acceso a todos los modulos o secciones pasados por parametros.
   *
   * @return boolean
   */
  public static function has() {
    $arg = func_get_args();
    $arg = count($arg) && is_array($arg[0]) ? $arg[0] : $arg; 
    $mod = self::getModules();
    foreach ($arg as $cs) {
      if (strpos($cs,'::')) {
        list($cs, $md) = explode('::', $cs);
        self::$current_module = $cs;
        if(isset($mod[$cs])) {
          if(in_array($md, $mod[$cs]) === FALSE) 
            return FALSE; 
        } else return FALSE;
      } elseif(!isset($mod[$cs])) {
        self::$current_module = $cs;
        return FALSE;
      }
    }
    return TRUE;
  }
  /**
   * Verifica si tienes acceso alguno de los  modulos o secciones pasados por parametros.
   *
   * @return void
   */
  public static function any() {
    $arg = func_get_args();
    $arg = count($arg) && is_array($arg[0]) ? $arg[0] : $arg; 
    $mod = self::getModules();
    foreach ($arg as $cs) {
      self::$current_module = $cs;
      if (strpos($cs,'::')) {
        list($cs, $md) = explode('::', $cs);
        self::$current_module = $cs;
        if(isset($mod[$cs])) {
          if(in_array($md, $mod[$cs])) return TRUE; 
        }
      } elseif(isset($mod[$cs])) return TRUE;
    }
    return FALSE;
  }

  /**
   * Agregar al catalogo de permisos modulos y secciones
   *
   * @param array $allow
   * @param boolean $replace
   * @return void
   */
  public static function add($allow = array(), $replace = TRUE) {
    if($replace) self::$_allow = array();
    self::$_allow[] = $allow;
  }

  /**
   * Concatena registros de permisos para el catalogo 
   *
   * @param [type] $allow
   * @param array $mm
   * @param [type] $class
   * @param string $title
   * @return void
   */
  public static function allowConcat($allow, $mm = array(), $class=NULL, $title='')
  { if(!$mm || (is_array($mm) && !count($mm))) return $allow;
    if (isset($mm[0])) {
      foreach ($mm as $op) $allow = self::allowConcat($allow, $op, $class);
    } else {
      $m = $mm;
      $m['title'] = isset($mm['title']) ? $mm['title'] : ($class ? $class : 'No defined');
      $m['module'] = isset($mm['module']) ? $mm['module'] : ($class ? $class : 'None');
      $allow[] = $m;
    }
    return $allow;
  }
  /**
   * Recupera todos los catalogos de los modulos recibidos por parametros
   *
   * @return void
   */
  public static function load()
  { 
    $allow = array();
    $arg = func_get_args();
    $app = Aif::$application;
    if (method_exists($app, '__allow')) 
      $allow = self::allowConcat($allow, $app::__allow(), $app);
    foreach ($arg as $idx => $name) {
      $class = $app . $name;
      if (class_exists($class) &&  method_exists($class, '__allow')) {
        $allow = self::allowConcat($allow, $class::__allow(), $name);
      }
    }
    if(count(self::$_allow)) { 
      $allow = self::allowConcat($allow, self::$_allow, $app);
    }
    return $allow;
  }

}