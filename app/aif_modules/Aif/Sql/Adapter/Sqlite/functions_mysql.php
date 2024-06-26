<?php
if (isset($this)){

  /**
   * Cambia la sintaxis de expresiones que se declaran diferentes
   * @param unknown $sql
   * @return unknown
   */
  if (!function_exists('sqliteCreateFunctionsParser')){
    function sqliteCreateFunctionsParser($sql){
      $sql = preg_replace('/\s+/', ' ', $sql);
      // echo $sql."<hr>";
      if (preg_match('/ REGEXP[\(|\s]/', $sql)){
        if (preg_match('/\((.*?)\s+REGEXP/', $sql)) $sql = preg_replace(
          '/^(.*?)\((.*?)\s+REGEXP\((.*?)\)(.*?)$/', '$1(REGEXP($3,$2)$4', $sql);
      }
      return $sql;
    }
  }
  /**
   * Define funcion de CONCAT con la misma funcionalidad de mysql
   * @return string
   */
  if (!function_exists('sqliteConcat')){
    function sqliteConcat(){
      return func_num_args() ? implode('', func_get_args()) : '';
    }
  }
  $this->link->createFunction('CONCAT', 'sqliteConcat');

  /**
   *
   * @param unknown $str
   * @param unknown $delim
   * @param unknown $count
   * @return string
   */
  if (!function_exists('sqliteSubstringIndex')){
    function sqliteSubstringIndex($str, $delim, $count){
      return $count < 0 ? implode($delim,
        array_slice(explode($delim, $str), $count)) : implode($delim,
        array_slice(explode($delim, $str), 0, $count));
    }
  }
  $this->link->createFunction('SUBSTRING_INDEX', 'sqliteSubstringIndex');
  /**
   * Evalua una expresion
   * @param unknown $exp
   * @param unknown $str
   * @return number
   */
  if (!function_exists('sqliteRegExp')){
    function sqliteRegExp($exp, $str){
      return preg_match("/$exp/", $str);
    }
  }
  $this->link->createFunction('REGEXP', 'sqliteRegExp');
  /**
   * Sentencia de control IF de mysql
   */
  if (!function_exists('sqliteIf')){
    function sqliteIf($expr, $expr1, $expr2){
      return $expr ? $expr1 : $expr2;
    }
  }
  $this->link->createFunction('IF', 'sqliteIf');
}