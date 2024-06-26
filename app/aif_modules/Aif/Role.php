<?php

/**
 * Clase que controla el acceso a las acciones del sistema
 * por medio de perfiles
 *
 * @version 1.0
 */
class AifRol{

    /**
    * Define los permisos de acceso a los métodos por rols, asi como definir
    * los roles que pueden escribir, se pueden definir para toda la clase o bien para cada método
    * @var array
    */
    public static $op = array();
    /**
    * [$allow description]
    * @var array
    */
    public static $allow = array();//
    /**
     */
    public static $cfg=array();


    /** 
    * Carga la configuracion
    */
    public static function config($cfg=array()){
        if(is_array($cfg)){
            self::$cfg = $cfg;
        }elseif(is_string($cfg)){
            self::$cfg = json_decode($cfg,TRUE);
        }
        if(!self::$cfg) Aif::error('AifPriv::config','Error in the configuration format');
    }

  /**
    * Asigna y define los privilegios a los Roles del sistema
    *  
    *  Use:
    *  AifPriv::allowed('admins')
    *
   */
  public static function set(){
    $arg = func_get_args();
    $not = array(); //no tiene permismos
    $rols =array(); 
    $arg = array_combine($arg, $arg);
    if(isset(self::$allow[Aif::$class][Aif::$method])){
       $arg = array_replace(self::$allow[Aif::$class][Aif::$method] , $arg);
    }
    foreach ($arg as $i => $r) {
      $c = substr($arg[$i], 0,1);
      if(preg_match('/^\W$/',$c)){ //Cualquier caracter no letra, numero, guion bajo
        $r = substr($r, 1);
        if($c=='~' || $c=='!'){ $not[]=$r; continue; }
        self::setOp($c,$r);
      }
      $rols[] = strtolower($r);
    }
    if(isset(self::$cfg['rols'])){
        foreach (self::$cfg['rols'] as $group => $usr) {
            if(in_array($group, $rols)){
                foreach ($usr as $i => $r){
                    in_array($r, $rols) ||  (!in_array($r, $not) &&  $rols[]=$r);
                }
            }
        }
    }
    self::setAllow($rols);
    in_array('none', $rols) && self::clean();
    self::setDeny($not);
    self::valid();
  }
/**
 * [setOp description]
 * @param [type] $c   [description]
 * @param string $rol [description]
 */
  public static function setOp($c,$rol=''){
     if(preg_match('/^\W$/',$c)){ //Cualquier caracter no letra, numero, guion bajo
        if(isset(self::$cfg['rols']) && isset(self::$cfg['rols'][$rol])){
          foreach (self::$cfg['rols'][$rol] as $idx => $r) {
            self::$op[Aif::$class][Aif::$method][$c][$r] = $r;
          }
        }else {
          $rol && (self::$op[Aif::$class][Aif::$method][$c][$rol] = $rol);
        }
      }
  }
/**
 * [allow description]
 * @param  [type] $rols   [description]
 * @param  string $class  [description]
 * @param  string $method [description]
 * @return [type]         [description]
 */
  public static function setAllow($rols, $class='',$method=''){
    $class || ($class = Aif::$class);
    $method || ($method = Aif::$method);
    if(is_array($rols)){
      self::$allow[$class][$method] = array_combine($rols, $rols);
    }else{
      self::$allow[$class][$method][$rols]=$rols;
    }
  }

/**
 * [clean description]
 * @return [type] [description]
 */
  public static function clean(){
      self::$allow = array();
      self::$op =array();
  }

  /**
   * [valid description]
   * @return [type] [description]
   */
  public static function valid(){
    $rol = isset(self::$cfg['session']) && isset(self::$cfg['session']['rol']) 
            ? self::$cfg['session']['rol'] : 'rol';
    $rol = AifSession::get($rol);
    if(!isset(self::$allow[Aif::$class][Aif::$method][$rol]))
        self::forbidden(403,$rol,Aif::$class,Aif::$method);
  }

/**
 * [setDeny description]
 */
  public static function setDeny(){
    $arg = func_get_args();
    foreach ($arg as $i => $rol) {
      if(is_array($rol)){
        foreach ($rol as $idx => $r) self::setDeny($r);
      }else{
        if(isset(self::$allow[Aif::$class][Aif::$method][$rol]))
            unset(self::$allow[Aif::$class][Aif::$method][$rol]);
      }
    }
  }
  
  public static function forbidden($err=0){
      if(isset(self::$cfg['forbidden']) && isset(self::$cfg['forbidden']['class']) ){
        $class = self::$cfg['forbidden']['class'];
        $method =isset(self::$cfg['forbidden']['method'])?self::$cfg['forbidden']['method']:'index';
        $call = isset(self::$cfg['forbidden']['call'])?self::$cfg['forbidden']['call']:FALSE;
        if($class && $method && $call){
            $static = isset(self::$cfg['forbidden']['call'])?self::$cfg['forbidden']['call']:FALSE;
            $param = is_array($err)?$err:array($err);
            if($static)
                forward_static_call_array(array($class, $method),$param);
            else
                call_user_func_array (array($class,$method),$param);
        }else
            Aif::set($class,$method,$param);
    }else  
        Aif::error('Your role does not have access privileges for this section');
  }
  
/**
  * [op description]
  * @param  [type] $c   [description]
  * @param  [type] $rol [description]
  * @return [type]      [description]
  */
 public static function op($c,$rol=NULL){
    $id = isset(self::$cfg['session']) && isset(self::$cfg['session']['rol']) 
            ? self::$cfg['session']['rol'] : 'rol';
    $rol || ( $rol = AifSession::get($id));
    if(preg_match('/^\W$/',$c)){ //Cualquier caracter no letra, numero, guion bajo
      return  isset(self::$op[Aif::$class][Aif::$method][$c][$rol])? TRUE : FALSE;
    }
  }

  /**
  *
  *  Use:
  *  if(AifPriv::allowed('write')){
  *   //success permits
  * }
  */
  public static function allow(){
        $rols = func_get_args();
        $id = isset(self::$cfg['session']) && isset(self::$cfg['session']['rol']) 
                ? self::$cfg['session']['rol'] : 'rol';
        $rol = AifSession::get($id);
        return in_array($rol,$rols)? TRUE : FALSE;
  }

  public static function permit(){
    $pers = func_get_args();
    //$per = array(); 
    //foreach ($arg as $i => $r)$per[] = strtolower($r);
    if(isset(self::$cfg['privileges'])){
        $id = isset(self::$cfg['session']) && isset(self::$cfg['session']['privilege']) 
        ? self::$cfg['session']['privilege'] : 'privilege';
        $current_bit = AifSession::get($id);
        if($current_bit){
            foreach (self::$cfg['privileges'] as $bit => $permits) {
                $bit = intval($bit);
                if(is_array($permits)){
                    foreach ($permits as $i => $perm){
                        if(in_array($perm, $pers) && $current_bit & $bit) 
                            return TRUE;
                    }
                }elseif(in_array($permits, $pers) && $current_bit & $bit)
                    return TRUE;
            }
        }
    }
     return FALSE;
}


}
