<?php

/**
 * Clase para metodos globales del modelo
 */
class UtilModel
{
  /**
   * Undocumented function
   *
   * @param string $filter 0,0,0
   * @param array $itms  ejemplo: array('c.idClass','c.name')
   * @param array $type    ejemplo: array(0,1)
   * @param array $op    Operador $op = 'AND'
   * @return void
   */
  public static function getFilter(
    $filter = '',
    $itms = array(),
    $type = array(),
    $op = 'AND',
    $sub = FALSE
  ) {
    $_FILTER = array();
    if ($filter && is_array($itms) && count($itms)) {
      $rr = explode(',', utf8_decode(utf8_encode(urldecode($filter))));
      //print_r($rr);die();
      foreach ($itms as $idx => $itm) {
        if (isset($rr[$idx]) && $rr[$idx]) {
          $ty = isset($type[$idx]) && $type[$idx] ? $type[$idx] : 0;
          if (is_array($itm)) {
            $_oo = array();
            $_uu = array();
            foreach ($itm as $r) {
              $_uu[] = $rr[$idx];
              $ty && $_oo[] = $ty;
            }
            $_FILTER[] = "(" . self::getFilter(implode(",", $_uu), $itm, $_oo, 'OR', TRUE) . ")";
          } else {
            $_FILTER[] = ($ty ? "$itm LIKE '" . ($ty & 2 ? '%' : '') . UtilSafe::urlB64Decode($rr[$idx]) . ($ty & 3 ? '%' : '') . "'"
              : "$itm = " . $rr[$idx]);
          }
        }
      }
    }
    //return ($sub ? '' : " {$op} ").implode(" {$op} ", $_FILTER);
    return count($_FILTER) ? ($sub ? '' : " $op ") . implode(" $op ", $_FILTER) : " ";
  }


  
  /**
   *
   */
  public static function getOrderBy($order = '0,0')
  {
    $_ORDER = '';
    if ($order) {
      $i = 1;
      $rr = explode(',', $order);
      foreach ($rr as $x => $v) {
        if ($v === '' || $v === '-') continue;
        $v = $v == 1 ? 'ASC' : ($v == 2 ? 'DESC' : '');
        if ($v) $_ORDER .= ($_ORDER ? ',' : ' ORDER BY ') . "$i $v";
        $i++;
      }
    }
    return $_ORDER;
  }
}
