<?php
/**
 * Undocumented class
 */
class UtilHtml
{
  /**
   * Carga la plantilla PHTML a usar para renderizar la 
   * vista del controlador que la manda a llamar
   *
   * @return void
   */
  static public function view()
  {
    $rs = func_get_args();
    foreach ($rs as $idx => $param) {
      AifView::set($param);
    }
  }
  /**
   * Carga la plantilla PHTML a usar para renderizar la 
   * vista del controlador que la manda a llamar
   * 
   * @return void
   */
  static public function template()
  {
    $rs = func_get_args();
    foreach ($rs as $idx => $param) {
      AifView::set($param);
    }
  }

  static public function set() {
    $css = array();
    $js = array();
    $rs = func_get_args();
    foreach($rs as $file) {
      if(strpos($file,'.css')) $css[] = $file;
      else if(strpos($file,'.js')) $js[] = $file;
      else if(strpos($file,'.phtml')) AifView::set($file);
    }
    if(count($css))self::src($css);
    if(count($js))self::src($js, 1);
  }
/**
 * Undocumented function
 *
 * @param [type] $rs
 * @param integer $ty
 * @param integer $if
 * @return void
 */
  static public function src($rs, $ty = 0, $if = 0)
  { $ver = isset(Aif::$application::$ver)?Aif::$application::$ver:Aif::$version;
    foreach ($rs as $idx => $param) {
      Aif::csc(
        'aif/html',
        $ty ? 'setScript' : 'setCss',
        $if ? array($param . '?ver=' . $ver, $if)
          : array($param . '?ver=' . $ver)
      );
    }
  }
  /**
   * Carga las hojas de estilos para usar en la vista que el controlador 
   * esta invocando para mostrar el resultado esperado 
   *
   * @return void
   */
  static public function css()
  {
    self::src(func_get_args());
  }
  /**
   * Carga las hojas de estilos condicionadas con alguna version del navegador
   * que se establce como primer argumento, las cueales se usaran para la vista 
   * que el controlador esta invocando para mostrar el resultado esperado 
   *
   * @return void
   */
  static public function cssIf()
  {
    $rs = func_get_args();
    $if = array_shift($rs);
    self::src($rs, 0, $if);
  }
  /**
   * Carga los archivos script para usarse la vista que el controlador 
   * esta invocando para mostrar el resultado esperado 
   *
   * @return void
   */
  static public function script()
  {
    self::src(func_get_args(), 1);
  }
  /**
   * Carga los archivos script condicuionados a una version del navegador 
   * que se establece como primer argumento para usuarse en la vista que el controlador 
   * esta invocando para mostrar el resultado esperado 
   *
   * @return void
   */
  static public function scriptIf()
  {
    $rs = func_get_args();
    $if = array_shift($rs);
    self::src($rs, 1, $if);
  }
}
