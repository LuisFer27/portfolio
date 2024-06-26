<?php
/**
 * Manejo de fechas
 *
 * Manipulación y manejo de tiempo y fechas
 * @author $Author$
 * @copyright Copyright (c) 2008, {@link http://www.aquainteractive.com
 *            AquaInteractive}
 * @package Aif
 * @since 2013-04-15
 * @subpackage Lib
 * @version $Id$
 */

/**
 * lase de manejo de fechas
 *
 * Clase de manipulación y manejo de tiempo y fechas
 * @package Aif
 * @subpackage Lib
 */
class AifDate {
  /**
   * Idioma por default para la salida del tiempo y fechas
   * @var string
   */
  public static $lang = 'es';
  /**
   * Arreglo que contiene todos los meses
   * @var array
   */
  public static $mounths = array (
    'es' => array ('Mes invalido', 'enero', 'febrero', 'marzo', 'abril', 'mayo',
      'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre',
      'diciembre' ),
    'en' => array ('Invalid month', 'january', 'february', 'march', 'papril',
      'may', 'june', 'july', 'august', 'september', 'october', 'november',
      'december' ) );
  /**
   * Arreglo que contiene todos los meses abreiados
   * @var array
   */
  public static $monthsShortcuts = array (
    'es' => array ('Mes invalido', 'ene', 'feb', 'mar', 'abr', 'may', 'jun',
      'jul', 'ago', 'sept', 'oct', 'nov', 'dic' ),
    'en' => array ('Invalid month', 'jan', 'feb', 'mar', 'apr', 'may', 'jun',
      'jul', 'aug', 'sep', 'oct', 'nov', 'dec' ) );
  /**
   * Arreglo que contiene todos los días de la semana
   * @var array
   */
  public static $days = array (
    'es' => array ('domingo', 'lunes', 'martes', 'miércoles', 'jueves',
      'viernes', 'sábado', 'domingo', 'Día invalido' ),
    'en' => array ('sunday', 'monday', 'tuesday', 'wednesday', 'thursday',
      'friday', 'saturday', 'sunday', 'Invalid day' ) );
  /**
   * Arreglo que contiene todos los días abreviados de la semana
   * @var array
   */
  public static $daysShortcuts = array (
    'es' => array ('do', 'lu', 'ma', 'mi', 'ju', 'vi', 'sá', 'do',
      'Día invalido' ),
    'en' => array ('sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun',
      'Invalid day' ) );
  /**
   * Regresa el nombre del mes
   *
   * Recibe un timestamp o un entero de 1 al 12 y regresa el nombre del mes
   * y en minusculas
   * @param string $m Número que representa el día
   * @param string $av Devolver abreviatura
   * @param string $lag Solicitar el nombre en el idioma determinado (es o en)
   * @return string
   */
  public static function getMounth($m = NULL, $av = FALSE, $lag = NULL, $format=FALSE){
  	$format && $m = self::getFormat($m, "n");
    $m = $m > 12 ? ( int ) date('n', $m) : $m;
    $mnts = $av ? self::$monthsShortcuts : self::$mounths;
    $mnts = isset($mnts[$lag]) ? $mnts[$lag] : $mnts[self::$lang];
    return isset($mnts[$m]) ? $mnts[$m] : $mnts[0];
  }

  public static function getArray($date,$ft=0){
    $ar=null;
    if(preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $date)){
      $ar = explode('-', $date);
      $ar[]=0;$ar[]=0;$ar[]=0;
    }elseif (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{1,2}):(\d{1,2})$/', $date)){
      $ar = explode(',',str_replace(array ('-', ' ', ':' ), array (',', ',', ',' ), $date));
    }
    if($ft){
      $ar[0] -=2000;
      $ar[1] =self::getMounth(intval($ar[1]),true);
    }
    return $ar;
  }
  /**
   * Regresa el nombre del día
   *
   * Recibe un timestamp o número entero del 1 al 7 y regresa su equivalecia con
   * el nombre del día en minusculas
   * @param string $d Dia en numero de 1 a 7
   * @param string $av Devolver abreviatura
   * @param string $lag Solicitar el nombre en el idioma determinado (es o en)
   * @return string
   */
  public static function getDay($d = NULL, $av = FALSE, $lag = 'es', $format=FALSE){
  	$format && $d = self::getFormat($d, "N");
    $d = $d > 7 ? ( int ) date('N', $d) : $d;
    $days = $av ? self::$daysShortcuts : self::$days;
    $days = isset($days[$lag]) ? $days[$lag] : $days[self::$lang];
    return (isset($days[$d])) ? $days[$d] : $days[8];
  }
  /**
   * Regresa la diferencia en segundo entre dos fechas
   * @param string $start Fecha inicial
   * @param string $end Fecha final
   * @return integer Segundos
   */
  public static function dayDiff($start, $end, $skip=FALSE){
    $d1 = self::getTimestamp($start);
    $d2 = self::getTimestamp($end);
    if ($d1 && $d2){
      return $skip ? round(($d2 - $d1)) : round(($d2 - $d1) / (3600 * 24));
    }
    return FALSE;
  }
  /**
   * Transforma un texto de tiempo de MySQL (ISO 8601) a un tipo tiempo de PHP
   * @param string $data Texto en formato ISP 8601
   * @param integer $days D?as a ser agregados
   * @return time Regresa el valor de tiempo del string
   */
  public static function iso8601ToTime($data, $days = 0){
    $date = explode(' ', $data);
    $time = array (0, 0, 0 );
    if (count($date) == 2){
      $time = explode(':', $date[1]);
    }
    $date = explode('-', $date[0]);
    $return = mktime($time[0], $time[1], $time[2], $date[1], $date[2] + $days,
      intval($date[0]));
    return $return;
  }
  /**
   * Devuelve < 0 si $tm1 es menor que $tm2; > 0 si $tm1 es mayor que $mtm2 y 0
   * si son iguales.
   * @param timestamp $tm1
   * @param timestamp $tm2
   * @return number
   */
  public static function cmpTimestamp($tm1, $tm2){
    return ($tm1 > $tm2 ? 1 : ($tm1 < $tm2 ? -1 : 0));
  }
  /**
   * Compara dos
   * @param unknown $d1
   * @param unknown $d2
   * @return number
   */
  public static function cmpDate($d1, $d2){
    return self::cmpTimestamp(self::getTimestamp($d1), self::getTimestamp($d2));
  }
  /**
   * Suma fecha
   * @param unknown $d1
   * @param unknown $d1
   * @return Ambigous <boolean, string>
   */
  public static function sumDate($d1, $d2){
    $d1 = is_numeric($d1) ? $d1 : self::getTimestamp($d1);
    $d2 = is_numeric($d2) ? $d2 : self::getTimestamp($d2);
    return $d1 && $d2 ? date('Y-m-d H:i:s', $d1 + $d2) : FALSE;
  }
  /**
   *
   * @param unknown $date
   * @return number boolean
   */
  public static function getTimestamp($date){
    $date = preg_match('/(\d){1,2}\/(\d){1,2}\/(\d){4}/', $date) ? preg_replace(
      '/(\d+)\/(\d+)\/(\d+)/', '$3-$2-$1', $date) : $date;
    if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $date)){
      list ( $y, $m, $d ) = explode('-', $date);
      return mktime(0, 0, 0, $m, $d, $y);
    }elseif (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{1,2}):(\d{1,2})$/',
      $date)){
      list ( $y, $m, $d, $h, $i, $s ) = explode(',',
        str_replace(array ('-', ' ', ':' ), array (',', ',', ',' ), $date));
      return mktime($h, $i, $s, $m, $d, $y);
    }else{
      return FALSE;
    }
  }

  public static function getElapsed($dt,$ty=FALSE)
  { $ns = $ty?2:0;
    $mk = time() - self::getTimestamp($dt);
    if ($mk < 1)return '1 seg';
    $a = array( 365 * 24 * 60 * 60  =>  array('Año','Año','Año','Años'),
                 30 * 24 * 60 * 60  =>  array('Mes','Mes','Mes','Meses'),
                  7 * 24 * 60 * 60  =>  array('Sem','Sems','Semana','Semanas'),
                      24 * 60 * 60  =>  array('Día','Días','Día','Días'),
                           60 * 60  =>  array('Hr','Hrs','Hora','Horas'),
                                60  =>  array('Min','Mins','Minuto','Minutos'),
                                 1  =>  array('Seg','Segs','Segundo','Segundos'),
              );
    foreach ($a as $sc => $dd)
    {   $d = $mk / $sc;
        if ($d >= 1)
        { $r = floor($d);
          return $r.' '.$dd[$ns+($r>1?1:0)];
        }
    }
  }

  public static function getFormat($date,$format){
    if(preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $date)){
      list ( $y, $m, $d ) = explode('-', $date);
      return date($format,mktime(0, 0, 0, $m, $d, $y));
    }elseif (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{1,2}):(\d{1,2})$/', $date)){
      list ( $y, $m, $d, $h, $i, $s ) = explode(',',
      str_replace(array ('-', ' ', ':' ), array (',', ',', ',' ), $date));
      return date($format,mktime($h, $i, $s, $m, $d, $y));
    }
    return $date;
  }

  /**
   * Devuelve el formato para una fecha especifica
   * @param string $date
   * @param string $format
   * @return string
   */
  public static function getFormatDate ($date=NULL, $format=NULL){
  	$format = $format ? $format : 'Y-m-d H:i:s';
  	$date = $date ? $date : date($format);
  	return preg_match("/(0{2,}[- ])/i", $date) ? FALSE : date($format, strtotime($date));
  }

  /**
   * Función que aumenta en meses, dias, años, etc una fecha especifica
   * @param unknown $time
   * @param unknown $type
   * @param unknown $date
   * @param unknown $format
   * @return Ambigous <unknown, string>|string
   */
  public static function addTime($time, $type, $date, $format=NULL) {
  	$time = intval($time);
  	$type = strtolower($type);
  	$format = $format ? $format : 'Y-m-d H:i:s';
  	$date = $date ? $date : date($format);
  	list($year, $month, $day) = explode('-', date('Y-n-j', strtotime($date)));
  	list($hour, $minute, $second) = explode(':', date('H:i:s', strtotime($date)));

  	switch ($type) {
  		case 's':
  			$second += $time;
  			break;
  		case 'i':
  			$minute += $time;
  			break;
  		case 'h':
  			$hour += $time;
  			break;
  		case 'd':
  			$day += $time;
  			break;
  		case 'm':
  			$month += $time;
  			break;
  		case 'y':
  			$year += $time;
  			break;
  		default:
  			return $date;
  	}

  	$time = mktime($hour, $minute, $second, $month, $day, $year);
  	$date = date($format, $time);

  	return $date;
  }

  /**
   * Método que da un formato a una Fecha especifica
   * @param string $date   - Fecha a la que se le quiere dar un Formato
   * @param string $format - Formato de la cadena como se quiere devolver la
   * 						 fecha, ejemplos:
   *							"%D %d de %m del %Y" => "viernes 31 de 07 del 2015",
   * 							"%D %d de %M del %Y" => "viernes 31 de Julio del 2015"
   *							"%d de %M del %Y"    => "31 de Julio del 2015"
   * 							"%n %d de %N del %y" => "vi 31 de Jul del 15"
   * @param string $lang   - Idioma que se quiere usar : "es", "en"
   * @param Boolean $uc    - Si se quiere convertir la primer letra en Mayusculas
   * @return string
   */
  public static function getStringDate ($date=NULL, $format = "%D %d de %M del %Y", $lang='es', $uc=TRUE){
    $_f = preg_match("/(%[His])/i",$format) ? "Y-m-d H:i:s" : "Y-m-d";
    $date = $date ? self::getFormatDate($date, $_f) : date($_f);
    $f = $date;
    if($date && is_string($date) && $format){
      $formats = array(
        "%d" => self::getFormat($date, "d"), // Día
        "%D" => self::getDay($date, NULL, $lang, TRUE), //Nombre del día
        "%m" => self::getFormat($date, "m"), // Numero del mes
        "%M" => self::getMounth($date, NULL, $lang, TRUE), //Nombre del mes
        "%n" => self::getDay($date, TRUE, $lang, TRUE), // Abreviatura del nombre del día
        "%N" => self::getMounth($date, TRUE, $lang, TRUE), // Abreviatura del nombre del mes
        "%y" => self::getFormat($date, "y"), //Año de 2 digitos
        "%Y" => self::getFormat($date, "Y"), //Año de 2 digitos
        "%H" => self::getFormat($date, "H"), //Hora Formato de 24 horas
        "%h" => self::getFormat($date, "h"), //Hora Formato de 12 horas
        "%i" => self::getFormat($date, "i"), //minutos
        "%s" => self::getFormat($date, "s"), //segundos
        "%A" => self::getFormat($date, "A"), //A.M, P.M.

      );
      $f = str_replace(array_keys($formats), array_map(($uc ? 'ucfirst' : NULL), $formats), $format);
    }
    return $f;
  }


  public static function isDate($date){
    $t = explode('-', $date);
    return count($t)==3?checkdate($t[1], $t[2], $t[0]):false;
  }

}
