<?php
/**
 * Undocumented class
 */
class UtilMenu
{
  public static $_menu = array();
  public static $_content = '_menu_content';
  public static $_version = '_menu_version';
  /**
   * Undocumented function
   *
   * @param array $menu
   * @param array $mm
   * @param integer $id
   * @param boolean $active
   * @return void
   */
  public static function menuConcat($menu, $mm = array(), $class=NULL, $title='')
  { if(!$mm || (is_array($mm) && !count($mm))) return $menu;
    if (isset($mm[0])) {
      foreach ($mm as $op) $menu = self::menuConcat($menu, $op, $class);
    } else {
      $m = array();
      $m[0] = isset($mm['title'])?$mm['title'] : $title; //titulo
      if(isset($mm['open'])) $m[1] = $mm['open'];
      elseif(!isset($mm['child'])) $m[1] = $class;
      if(isset($mm['is'])) $m[2] = $mm['is'];
      elseif(!isset($mm['open'])) $m[2]=$class;
      if(isset($mm['ico'])) $m[3]=$mm['ico'];          //Icono
      if(isset($mm['child']) && count($mm['child'])) {
        $m[4] = self::menuConcat(array(), $mm['child']);
      } elseif(!isset($mm['open'])) {
        return $menu;
      }
      if(isset($mm['order']) && intval($mm['order']) < count($menu)) {
        for($x=count($menu); $x>$mm['order']; $x--) $menu[$x] = $menu[$x-1];
        $menu[$mm['order']] = $m;
      } else {
        $menu[] = $m;
      }
    }
    return $menu;
  }
  /**
   * Undocumented function
   *
   * @param [type] $title
   * @param [type] $class
   * @param string $method
   * @param array|string $param
   * @return void
   */
  public static function itmMenu($title, $class, $method='index', $param = array())
  {
    return array(
      'title' => $title,
      'open' => "$class::$method".(count($param) ? '/'.explode($param,'/'):'')
    );
  }

  public static function itmMenuIs($title, $is,$class, $method='index', $param = array())
  {
    return array(
      'is' => $is,
      'title' => $title,
      'open' => "$class::$method".(count($param) ? '/'.explode($param,'/'):'')
    );
  }


  public static function isLoad(){
    $app = Aif::$application;
    return AifSession::isVar(self::$_version) 
      ? AifSession::get(self::$_version) !=  (isset($app::$lang)?$app::$lang:'').$app::$ver
      : true;
  }

  public static function set($menu = array(), $replace = TRUE) {
    if($replace) self::$_menu = array();
    self::$_menu[] = $menu;
  }
  /**
   * carga los datos del modulo para integrarlo a la interface
   *
   * @param array $_menu
   * @return array
   */
  public static function load()
  { 
    $menu = array();
    if(self::isLoad()) {
      $arg = func_get_args();
      $app = Aif::$application;
      if (method_exists($app, '__menu')) 
        $menu = self::menuConcat($menu, $app::__menu(), $app);
      foreach ($arg as $idx => $name) {
        $class = $app . $name;
        if (class_exists($class) &&  method_exists($class, '__menu')) {
          $menu = self::menuConcat($menu, $class::__menu(), $name);
        }
      }
      AifSession::set(self::$_content, $menu);
      AifSession::set(self::$_version, (isset($app::$lang)?$app::$lang:'').$app::$ver);
    } else {
      $menu = AifSession::get(self::$_content);
    }
    if(count(self::$_menu)) { 
      $menu = self::menuConcat($menu, self::$_menu);
    }
    $menu = self::refactory($menu);
    //print_r($menu); die();
    return $menu;
  }

  public static function refactory($mm)
  { $menu = array();
    foreach($mm as $m1) {
      $m = array( 'title' => $m1[0] );
      if(isset($m1[1]) && $m1[1]) {
        $link = '';
        $fn = '';
        $cs = $m1[1];
        if(strpos($m1[1],'::')) {
          list($cs,$fn) = explode('::',$m1[1]);
          $link = $cs.'/'.$fn.'/index.html'; 
        } else $link =$cs.'/index.html';
        $m['link'] = '/'.$link;
        if(!isset($m1[2])) {
          $m1[2] = $cs.(isset($fn)?'::'.$fn:'');
        }
      }
      if(isset($m1[2]) && $m1[2]){
        $cls = Aif::$class;
        $mtd = Aif::$method;
        $is = explode(',',$m1[2]);
        if(isset($m1[4]) && is_array($m1[4])){
          foreach($m1[4] as $m2) {
            if(isset($m2[1]) && $m2[1]) $is[] = $m2[1];
            if(isset($m2[2]) && $m2[2]) $is[] = $m2[1];
          }
        }
        $m['active'] = in_array($cls,$is) || in_array("$cls::$mtd",$is);
      }
      if(isset($m1[3]) && $m1[3]) $m['ico'] = $m1[3];
      if(isset($m1[4]) && $m1[4]) { 
       // reset($m1[4])
        $m['child'] = array();
       // foreach($m1[4] as $id => $m3) {
          $m['child'] = self::refactory($m1[4]);
        //}
      }
      $menu [] = $m;
    }
  
    return $menu;
    //return $menu;
  }
  
}
