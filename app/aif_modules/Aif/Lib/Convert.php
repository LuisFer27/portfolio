<?php 
/**
 * Biblioteca de conversiones de datos
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
class AifLibConvert {
  /**
   *
   * @param string $data
   * @return boolean
   */
  public static function outCsv($data = NULL) {
    $result = FALSE;
    if (is_array ( $data ))
      $result = self::arrayToCsv ( $data );
    return $result ? print ($result)  : FALSE;
  }
  /**
   *
   * @param string $data
   * @return boolean
   */
  public static function outXml($data = NULL) {
    $result = FALSE;
    if (is_array ( $data ))
      $result = self::arrayToXml ( $data );
    return $result ? print ($result)  : FALSE;
  }

  /**
   *
   * @param string $data
   * @return boolean
   */
  public static function outJson($data = NULL, $force=TRUE ) {
    $force = $force ? JSON_FORCE_OBJECT : FALSE;
    $res = self::arrayToJson ( $data, $force );
    return $res ? print ($res)  : FALSE;
  }
  /**
   *
   * @param unknown $array
   * @param string $ty
   * @return string
   */
  public static function arrayToJson($array, $ty = JSON_FORCE_OBJECT) {
    return json_encode ( $array, $ty );
  }

  /**
   * Convierte un arreglo asociativo en una xml
   *
   * @param array $arr
   * @param string $encoding
   * @param string $ver
   * @return string
   */
  public static function arrayToXml($arr, $encoding = NULL, $ver = '1.0') {
    $encoding = $encoding ? $encoding : Aif::$charset;
    $tt = count ( $arr );
    return '<?xml version="' . $ver . '" encoding="' . $encoding . '" ?>' . ($tt == 1 ? self::arr2Xml ( $arr ) : '<xml>' . self::arr2Xml ( $arr ) . '</xml>');
  }

  /**
   * Convierte una cadena en xml
   *
   * @param Array $ar
   *        	Arreglo a convertir e xml
   * @return string boolean xml en caso de exito
   */
  private static function arr2Xml($ar) {
    if (is_array ( $ar )) {
      if (count ( $ar )) {
        $xml = '';
        foreach ( $ar as $k => $v ) {
          $p = '';
          $key = is_numeric ( $k ) ? 'row' : $k;
          if (is_array ( $v )) {
            foreach ( $v as $pk => $pv ) { // Propiedades
              if ($pk[0] == '@') {
                $p .= ' ' . str_replace ( '@', '', $pk ) . '="' . ($pv) . '"';
                unset ( $v [$pk] );
              }
            }
            $tm = self::arr2Xml ( $v );
          } else
            $tm = $v;
          $xml .= '<' . $key . $p . '>' . $tm . '</' . $key . '>';
        }
        return $xml;
      } else
        return '';
    } else
      return $ar;
  }
  /**
   *
   * @param unknown $arr
   */
  public static function arrayToCsv($arr) {
  }
  /**
   * Convierte un documento xml en un arreglo
   *
   * @param
   *        	file Nomre del archivo
   */
  public static function xmlToArray($file) {
    if (file_exists ( $file )) {
      $xml = @simplexml_load_file ( $file );
      if ($xml) {
        return self::xml2Array ( $xml );
      } else
         Aif::debugger('Errors','Inside Aif/lib/Convert','Error xml parser('.$file.')');
    } else
      Aif::debugger('Errors','Inside Aif/lib/Convert','The file does not exist ('.$file.')');
    return FALSE;
  }


public static function xmlToArrayByStr($str) {
      $xml = @simplexml_load_string ( $str );
      if ($xml) {
        return self::xml2Array ( $xml );
      } else 
        Aif::debugger('Errors','Inside Aif/lib/Convert','Error xml parser ('.$str.')');
   return FALSE;
  }

  /**
   * Convierte un objeto SimpleXMLElement a un array
   *
   * @param SimpleXMLElement $xml
   * @return string
   */
  private static function xml2Array($xml) {
    $i = 0;
    $arr = array ();
    if (is_object ( $xml ) && get_class ( $xml ) == 'SimpleXMLElement') {
      foreach ( $xml->attributes () as $key => $value ) {
        $value && ($atr ['@' . $key] = ( string ) $value);
      }
      foreach ( $xml->children () as $key => $value ) {
        if (isset ( $arr [$key] )) {
          if (! isset ( $arr [$key] [0] )) {
            $tmp = $arr [$key];
            $arr [$key] = array ();
            $arr [$key] [$i ++] = $tmp;
          }
          $arr [$key] [$i ++] = self::xml2Array ( $value );
        } else
          $arr [$key] = self::xml2Array ( $value );
      }
      if (isset ( $atr ) && ! count ( $arr ))
        $arr = array (
            '#' => ( string ) $xml
        );
    }
    return isset ( $atr ) ? array_merge ( $atr, $arr ) : (count ( $arr ) ? $arr : ( string ) $xml);
  }

  /**
   * Evalua un string para convertirlo en eun dato
   *
   * @param string $value
   * @return mixed
   */
  public static function evalString($value) {
    if (is_string ( $value )) {
      $value = urldecode ( $value );
      if ($value != "") {
        $allow = array (
            "false",
            "FALSE",
            "true",
            "TRUE",
            "null",
            "NULL"
        );
        if ($value [0] == '[' || $value [0] == '{' || in_array ( $value, $allow ) || preg_match ( '/^[0-9\\.]+$/i', $value )) {
          $value = json_decode ( $value, TRUE );
        }
      }
    }
    return $value;
  }

  /**
   * Interpreta un string para devolverlo como la variable
   *
   * @param string $data
   *        	El string a convertir
   * @param string $type
   *        	El tipo origen
   *        	(json|int|float|complex:array|array|time|string). Default: string
   * @return mixed El texto convertido
   */
  public static function toPhp($data, $type = 'string') {
    $type = strtolower ( $type );
    $return = $data;
    switch ($type) {
      case 'array' :
        $return = explode ( ',', $data );
        break;
      case 'complex:array' :
        $regexp = '/(?P<level>(?P<key>\w+):(?P<value>\w+)\,*)/i';
        preg_match_all ( $regexp, $data, $matches );
        $return = array_combine ( $matches ['key'], $matches ['value'] );
        break;
    }
    return self::evalString ( $return );
  }

  /**
   * Interpreta una variable para regrezarla la codificación indicada
   *
   * @param mixed $data
   *        	La variable a convertir
   * @param string $type
   *        	El tipo destino
   *        	(json|int|float|complex:array|array|time|string). Default: string
   * @return string El texto convertido
   */
  public static function toString($data, $type = 'string') {
    $type = strtolower ( $type );
    switch ($type) {
      case 'json' :
        $return = json_encode ( $data );
        break;
      case 'int' :
        $return = intval ( $data );
        break;
      case 'float' :
        $return = floatval ( $data );
        break;
      case 'complex:array' :
        foreach ( $data as $key => $value ) {
          $data [$key] = $key . ':' . $value;
        }
      case 'array' :
        $return = implode ( ',', $data );
        break;
      case 'time' :
        if (is_int ( $data ))
          $data = date ( 'Y-m-d H:i:s', intval ( $data ) );
      case 'string' :
      case 'html' :
      default :
        $return = $data;
    }
    if ($data == NULL) {
      $return = 'null';
    }
    return $return;
  }

  /**
   * Transforma un array asociativo en un string de XML
   *
   * @param variant $object
   *        	El array a convertir
   * @param string $name
   *        	Nombre del nodo
   * @return string El string con el XML
   */
  public static function toXML($object, $name = 'root', $asString = FALSE, &$remove = array()) {
    $backTrace = debug_backtrace ();
    $recursive = (count ( $backTrace ) > 2 && $backTrace [1] ['function'] == 'toXML' && $backTrace [1] ['class'] == 'Mekayotl_tools_utils_Conversion');
    if (is_string ( $name )) {
      $xml = new SimpleXMLElement ( '<' . $name . '/>' );
    } elseif ($name instanceof SimpleXMLElement) {
      $xml = $name;
    }
    if (is_array ( $object )) {
      foreach ( $object as $elementName => $vValue ) {
        if (substr_count ( $elementName, '@' )) {
          if (is_bool ( $vValue )) {
            $vValue = ($vValue) ? 'TRUE' : 'false';
          }
          if ($vValue) {
            $xml->addAttribute ( substr ( $elementName, 1 ), ( string ) $vValue );
          }
        } else {
          if (is_integer ( $elementName )) {
            $elementName = $xml->getName ();
            $remove [] = $elementName;
            $remove = array_unique ( $remove );
          }
          switch (gettype ( $vValue )) {
            case 'array' :
            case 'object' :
              $children = $xml->addChild ( $elementName );
              self::toXML ( $vValue, $children, FALSE, $remove );
              break;
            case 'boolean' :
              $vValue = ($vValue) ? 'TRUE' : 'false';
            case 'integer' :
            case 'double' :
            case 'string' :
            default :
              if (! is_null ( $vValue )) {
                $children = $xml->addChild ( $elementName, $vValue );
              }
          }
        }
      }
    }
    if (! $recursive) {
      if (! $asString) {
        return $xml;
      }
      $stringXML = $xml->asXML ();
      $arrayXML = explode ( "\n", $stringXML );
      $stringXML = $arrayXML [1];
      foreach ( $remove as $tag ) {
        $stringXML = str_replace ( array (
            '<' . $tag . '>',
            '</' . $tag . '>'
        ), '', $stringXML );
      }
      return $stringXML;
    }
  }
  /**
   * Busca los campos en un texto y los remplaza por datos de un array
   *
   * @param array $data
   *        	El elemento que tiene los valores.
   * @param string $text
   *        	El texto a ser remplazado.
   * @return string El texto con los datos colocados
   */
  public static function substitute(array $data, $text) {
    $search = array ();
    $replace = array ();
    foreach ( $data as $field => $value ) {
      $search [] = '{' . $field . '}';
      $replace [] = $value;
    }
    return str_replace ( $search, $replace, $text );
  }

  /**
   * Convierte una cantidad de bytes en formato legible
   * Ej.
   * "014 => "1KB"
   *
   * @param integer $bytes
   * @param number $decimals
   * @return string
   */
  function toSize($bytes, $decimals = 2) {
    $sz = 'BKMGTP';
    $ft = floor ( (strlen ( $bytes ) - 1) / 3 );
    return sprintf ( "%.{$decimals}f", $bytes / pow ( 1024, $ft ) ) . @$sz [$ft];
  }

  /**
   * Convierte una longitud de formato humano a bytes
   * Ej.
   * "1KB" => 1014
   *
   * @param string $str
   * @return integer
   */
  public static function toBytes($str) {
    $ar = array (
        'B' => 1,
        'KB' => 1024,
        'MB' => 1024 * 1024,
        'GB' => 1024 * 1024 * 1024,
        'TB' => 1024 * 1024 * 1024 * 1024,
        'PB' => 1024 * 1024 * 1024 * 1024 * 1024
    );
    $bytes = floatval ( $str );
    if (preg_match ( '/([KMGTP]?B)$/si', strtoupper ( $str ), $matches ) && ! empty ( $ar [$matches [1]] ))
      $bytes *= $ar [$matches [1]];
    return intval ( round ( $bytes, 2 ) );
  }

  /**
   * Procesa una linea CSV para cambiarla a un arreglo indexado.
   *
   * @param string $line
   *        	La linea a ser cambiada.
   * @return array Arreglo indexado.
   */
  public static function processCSVLine(&$line) {
    $line = trim ( $line );
    $line = str_getcsv ( $line );
    return $line;
  }

  /**
   * convierte un valor a un entero
   *
   * @param mixed $var
   *        	Valor
   * @return integer boolean
   */
  public static function intval($var) {
    if (is_int ( $var ))
      return $var;
    elseif (is_string ( $var )) {
      if (preg_match ( '/\./', $var )) {
        $ar = explode ( '.', $var );
        return intval ( array_shift ( $ar ) );
      }
    } elseif (is_array ( $var ))
      return self::intval ( $var [0] );
    return FALSE;
  }

  /**
   * Codifica caracteres especiales a html
   *
   * @param string $str
   * @return string
   */
  public static function toHtml($str, $tag = FALSE) {
    Aif::$charset == 'UTF-8' && mb_detect_encoding ( $str, 'UTF-8', TRUE ) != 'UTF-8' && ($str = utf8_encode ( $str ));
    return htmlentities ( $str, ENT_QUOTES|ENT_SUBSTITUTE, Aif::$charset );
  }

  /**
   * Convierte los carecteres HTML una cadena a un Texto simple
   *
   * @param string $str
   * @return string
   */
  public static function toText($str, $tag = FALSE) {
    Aif::$charset == 'UTF-8' && mb_detect_encoding ( $str, 'UTF-8', TRUE ) != 'UTF-8' && ($str = utf8_encode ( $str ));
    return trim ( html_entity_decode ( $str, ENT_QUOTES|ENT_SUBSTITUTE  , Aif::$charset ) );
  }

  /**
   * Convierte un texto a una clave
   *
   * @param string $name
   * @param string $prefix
   * @return string
   */
  public static function toKey($name, $prefix = '', $omit = '') {
    $iso = (Aif::$charset == 'ISO-8859-1');
    $utf8 = $iso ? mb_detect_encoding ( $name ) == 'UTF-8' : FALSE;
    $utf8 && ($name = utf8_decode ( $name ));
    $name = str_replace ( $iso ? utf8_decode ( ' año ' ) : ' año ', ' anio ', mb_strtolower ( ' ' . $name . ' ' ) );
    $name = preg_replace ( '/&([a-z])(uml|acute|grave|circ|tilde);/', '$1', self::toHtml ( $name ) );
    $name = preg_replace ( "/\s+/", '_', self::toText ( $name ) );
    $name = preg_replace ( "/[^a-z0-9_$omit]/", '', $name );
    return $prefix . $name;
  }
  /**
   * Convierte una cadena o cadenas propiedades o index arreglo o objeto en UTF8 decodificando y codificandolo
   *
   * @param mixed $str
   * @return string
   */
  public static function toUtf8($str, $decode = FALSE) {
    if (is_string ( $str )){
      if($decode && $str){
      	 mb_check_encoding ( $str, 'UTF-8' ) && $str = utf8_decode ( $str );
      }else{
      	 mb_check_encoding ( $str, 'UTF-8' ) || ($str = utf8_encode ( $str ));
      }
    }
    elseif (is_array ( $str ))
      foreach ( $str as $key => $data )
        $str [$key] = self::toUtf8 ( $data, $decode );
    elseif (is_object ( $str ))
      foreach ( $str as $key => $data )
        $str->$key = self::toUtf8 ( $data, $decode );
    return $str;
  }
  /**
   * Convierte una cadena o cadenas propiedades o index arreglo o objeto en ISO-8859-1 decodificando y codificandolo
   * @param unknown $str
   * @return Ambigous <string, mixed>
   */
  public static function toIso88591($str) {
    if (is_string ( $str ))
      mb_check_encoding ( $str, 'UTF-8' ) && ($str = utf8_decode ( $str ));
    elseif (is_array ( $str ))
    foreach ( $str as $key => $data )
      $str [$key] = self::toIso88591 ( $data );
    elseif (is_object ( $str ))
    foreach ( $str as $key => $data )
      $str->$key = self::toIso88591 ( $data );
    return $str;
  }
  /**
   *
   * @param unknown $str
   * @param string $s1
   * @param string $s2
   * @param string $s3
   * @param string $s4
   */
  public static function prString($str, $s1 = '|', $s2 = ',', $s3 = ':', $s4 = ';') {
    $rs = array ();
    $a1 = explode ( $s1, $str );
    foreach ( $a1 as $i1 => $k1 ) {
      $a2 = explode ( $s2, $k1 );
      $rs2 = array ();
      foreach ( $a2 as $i2 => $k2 ) {
        $a3 = explode ( $s3, $k2 );
        if (count ( $a3 ) == 2) {
          $s4 && strpos ( '-' . $a3 [1], $s4 ) && ($a3 [1] = explode ( $s4, $a3 [1] ));
          $rs2 [$a3 [0]] = $a3 [1];
        }
      }
      count ( $rs2 ) && ($rs [] = ( object ) $rs2);
    }
    return $rs;
  }

  /**
   * Convierte todos los elementos de un Array a Objeto Recursivamente
   * @param Array   $array - Array a convertir
   * @param Boolean $skip  - Variable que indica si se quieren excluir los array que no son asociativos **FALTA VALIDAR QUE SEA UTIL Y FUNCIONE
   * @return Object
   */
  public static function toObject($array, $skip=TRUE) {
  	$rs = $array;
  	if((is_array($array) || is_object($array))){
  		$is = $skip ? FALSE : TRUE;
  		if($array && count($array)){
  			$rs = array();
	  		foreach ($array as $i=>$j){
	  			$is = (!$is && preg_match("/^(\d+)$/", $i) ? FALSE : TRUE);
	  			$rs[$i] = (is_array($j) || is_object($j)) ? self::toObject($j, $skip) : $j;
	  		}
  		}
  		$is && $rs = (object) $rs;
  	}
  	return $rs;
  }
  public static function cleanSpaces($str='', $rpl=''){
  	return $str ? preg_replace('/(\s+)/', $rpl, trim($str)) : $str;
  }
}
