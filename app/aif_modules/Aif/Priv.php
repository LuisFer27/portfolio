<?php

/**
 * Clase que controla el acceso a las acciones del sistema
 * por medio de perfiles
 *
 * @version 1.0
 */
class AifPriv{
    /**
    *
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

  public static function getId(){
      $class = Aif::$className;
      return isset($class::$id)?$class::$id:Aif::error('No static identifier was found "'.Aif::$className.'::$id"');
  }
  public static function varSession($ty){
      $ty = isset(self::$cfg['session']) && isset(self::$cfg['session'][$ty]) 
            ? self::$cfg['session'][$ty] : $ty;
      $id = Aif::ecsc('aif/session','get',$ty,0);
      return intval($id);
  }

  public static function varForbidden($ty){
    return isset(self::$cfg['forbidden']) && isset(self::$cfg['forbidden'][$ty]) 
          ? self::$cfg['forbidden'][$ty] : NULL;
  }
  /**
   * [valid description]
   * @return [type] [description]
   */
  public static function valid(){
    if(self::permit('write') || self::permit('read')) return true;
    self::forbidden(self::getId() ,Aif::$class, 'Does not have access');
  }
  /**
   * 
   */
  public static function forbidden(){
      $class = self::varForbidden('class');
      if($class){
        $method = self::varForbidden('method');
        $method =$method ? $method :'forbidden';
        $call = self::varForbidden('call');
        if($class && $call){
            $static = self::varForbidden('static');
            if($static)
                forward_static_call_array(array($class, $method),func_get_args());
            else
                call_user_func_array (array($class,$method),func_get_args());
        }else
            Aif::set($class,$method,func_get_args());
    }else  
        Aif::error('Your role does not have access privileges for this section');
  }
  /**
   * Validar si tiene permisos de write, read 
   */
  public static function permit($ty){
    $id = self::getId();
    return $id && ($id & self::varSession($ty))? TRUE: FALSE;
  }


}
