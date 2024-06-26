<?php
/**
 * Manejo y acceso a la base de datos
 *
 * @author $Author$
 * @copyright Copyright (c) 2008, {@link http://www.aquainteractive.com
 *            AquaInteractive}
 * @package Aif
 * @since 2013-04-15
 * @subpackage Sql
 * @version $Id$
 */

/**
 * Clase para el manejo y acceso a las bases de datos
 *
 * @package Aif
 * @subpackage Sql
 */
class AifDb {
  /**
   * Arreglo de objeto para multi-conexiones de base de datos
   */
  public static $db = array ();

  public static $displayErrors=FALSE;

  /**
   *
   * @var unknown
   */
  public static $onlySQL = FALSE;

  /**
   * M?todo que realizar? funci?n de sincronizac?on para los m?todos:
   * update,delete,replace
   * @obsolete Ya no se usuara para la sincronizacion, en su lugar usar setReplicate
   */
  public static $sync = FALSE;


  /**
   * Replicacion de metodos que se pueden configurar para log o bien
   * para replicar en difernetes base de datos
   * @var boolean
   */
  public static $replicate = FALSE;
  /**
   * [$callReplicate description]
   * @var null
   */
  private static $callReplicate = NULL;
  /**
   * [$dbReplicate description]
   * @var null
   */
  private static $dbReplicate = NULL;
  /**
   * Meto privado que se ejecuta cuando cambie le contenido de la base de datos
   * @return [type] [description]
   */
  private static function onReplicate(){
     if(self::$replicate || self::$dbReplicate){
        $args = func_get_args();
        if(self::$dbReplicate){
          $db = self::$dbReplicate;
          is_array($db) || ($db = array($db));
          foreach ($db as $idx => $sec) {
            if(isset(Aif::$cfg->$sec)){ // Existe ejecuta la replicacion
              call_user_func_array (
                  array(self::connect($sec), $args[0] ),
                  array_slice($args, 1)
              );
            }
          }
        }
        if(self::$replicate){
          $call = self::$callReplicate;
          $isStatic = FALSE;
          if(is_array($call)){//Es una clase
            $isStatic = $call[2]; unset($call[2]); // Metodo estatico
            if(!$isStatic){
              $call[0] = new $call[0]();
              if(!is_object($call[0]))return FALSE;
            }
            if(!$call[1]){//No esta definido el metodo
              $call[1] = array_shift($args);//define como el primer parametro como metodo
            }
          }
          return $isStatic ? forward_static_call_array($call, $args)
                            : call_user_func_array ($call, $args);
        }
    }
    return FALSE;
  }
  /**
   * Define una clase y metodo la que va recibir las llamadas a la base de datos
   * de cambios de los datos, tambien puede se puede definir uno o varios
   * de indentificadores de base de datos para hacer la replicacion de cambios
   * @param string $class  Nombre de la clase
   * @param string $method Nombre del metodo
   */
  public static function setReplicate($class,$method=NULL,$static=FALSE){
    self::$replicate = FALSE;
    if($class){
      if (Aif::autoLoad ($class ) && class_exists($class)){
         self::$callReplicate = array ($class, $method, $static);
         self::$replicate = TRUE;
      }elseif(function_exists($class)){
        self::$callReplicate = $class;
        self::$replicate = TRUE;
      }else{
        self::$dbReplicate = $class;
      }
    }
    return self::$replicate;
  }
  /**
   * Método utilizado para encender la replicación en la bitácora
   */
  public static function downReplicate(){
    self::$replicate=False;
  }
/**
 * Método utilizado para apagar la repicación en la bitácora
 * @return [type] [description]
 */
  public static function upReplicate(){
    self::$replicate=True;
  }
  /**
   * Carga el manejador de base de datos
   * @param string $name Nombre de la seccion de la cfg que contienen
   *        los parametros de base de datos
   * @return object intacia del manejador de base de datos
   */
  public static function connect($name = 'DataBase'){
    $name || ($name= 'DataBase');
    if (!isset(self::$db[$name]) || !self::$db[$name])
      self::$db[$name] = Aif::ecsc('aif/sql','load',$name);
    return self::$db[$name];
  }

  public static function close(){
    return self::connect()->close();
  }

  public static function undefined($name){
    if(isset(self::$db[$name])){
      unset(self::$db[$name]);
    }
  }

  public static function activeUtf8($ty = TRUE){
    return self::connect()->isUtf8 = $ty;
  }
  /**
   * Develve una matriz de objetos de una consulta
   * @param string|resource $sql Consulta en lenguaje sql o resultado de una
   *        consulta
   * @return array Resultado de la consulta
   */
  public static function toObject($sql){
    return self::connect()->toObject($sql);
  }
  /**
   * Obtiene un objecto de un registro donde los campos son las propiedades
   * @param string_type $sql
   * @return ooject
   */
  public static function firstObject($sql){
    return self::connect()->firstObject($sql);
  }
  /**
   * Develve un arreglo tipo catalogo $id=>$name de la consulta
   * @param string|resource $sql Consulta en lenguaje sql o resultado de una
   *        consulta
   * @return array Resultado de la consulta
   */
  public static function toCatArray($sql){
    return self::connect()->toCatArray($sql);
  }
  /**
   * Develve un arreglo tipo catalogo $id=>$name de la consulta
   * @param string|resource $sql Consulta en lenguaje sql o resultado de una
   *        consulta
   * @return array Resultado de la consulta
   */
  public static function toArray($sql){
    return self::connect()->toArray($sql);
  }
  /**
   * Convierte un valor en numero entero
   * @param mixed $var Valor a comvertir
   * @return integer
   */
  public static function intVal($var){
    return intval($var);
  }
  /**
   * Convierte un valor en numero real
   * @param mixed $var Valor a comvertir
   * @return number
   */
  public static function floatVal($var){
    return floatval($var);
  }
  /**
   * Convierte un valor a cadena protegiendo las comillas
   * @param mixed $var Valor a comvertir
   * @return string
   */
  public static function stringVal($var){
    return addslashes(( string ) $var);
  }
  /**
   * Limpia una cadena de comandos basicos SQL
   * @param string $var Valor a comvertir
   * @return string
   */
  public static function cleanSql($var){
    $var = Aif::ecsc('aif/safe','escapeString',$var,$var);
    return preg_replace('/\s(AND|OR|XOR|UNION|JOIN|INNER)\s/i', ' ', $var);
  }

  public static function dieErrors($rs,$ty){
    if(self::$displayErrors){
      $err = AifDb::connect()->error;
      $sql = AifDb::connect()->getSql();
      switch($ty){
        //case 'I': $rs || die('Error:'.$err."\n Sql:(".$sql.")"); break;
        case 'U':break;
        case 'D':break;
        case 'S':break;
        case 'R':break;
      }
    }
  }
  /**
   * Alias del metod cleanSql
   * @param unknown $var
   * @return unknown
   */
  public static function clean($var){
    return self::cleanSql($var);
    // return self::clean($var);
    // return self::connect()->clean($var);
  }
  /**
   * Ejecuta un consulta SQL
   * @param string $sql Consulta SQL
   * @return boolean si es diferente de FALSE, el query se ejcuto correctamente
   */
  public static function query($sql){
    return self::connect()->query($sql);
  }
  /**
   * Devuelve un objeto recordset de una consulta
   * @param string $sql Cadena sql
   * @return AifSqlRecord $var Valor a comvertir
   */
  public static function recordSet($sql){
    return self::connect()->recordSet($sql);
  }
  /**
   * Inserta un registro en base de datos
   * @param array $reg Arreglo asociativo del registro
   * @param string $table Nombre de la tabla
   * @return Regresa el Id de la clave insertada
   */
  public static function insert($reg, $table){
    $r_=self::connect()->insert($reg, $table);
    $r_ && self::onReplicate('insert',$reg, $table);
    (self::$sync && $r_) && (eval(self::$sync."('I','$table',$r_,'".json_encode($reg)."');"));
    self::dieErrors($r_,'I');
    return $r_;
  }
  /**
   * Actualiza un registro en base de datos
   * @param array $reg Arreglo asociativo del registro
   * @param string $table Nombre de tabla
   * @param string $filter Condicion a cumplir para actualizar
   * @return boolean si es diferente de FALSE se actualizo correctamente
   */
  public static function update($reg, $table, $filter){
    $tmp = (self::$sync ? json_encode(self::getSingleQuery($table, $filter)) : NULL);
    $r_=self::connect()->update($reg, $table, $filter);
	  $r_ && self::onReplicate('update',$reg, $table, $filter);
    if((self::$sync && $r_)){
  		$filter = str_replace(array("'"), '"', $filter);
  		$reg = str_replace(array("'"), "\\\\\\'", json_encode($reg));
  		$tmp = $tmp ? str_replace(array("'"), "\\\\\\'", $tmp) : NULL;
  		$fun_ = preg_replace("/(\s+)/i"," ","('U','$table','$filter','".$reg."','$tmp');");
  		/*
  		//$filter = addslashes($filter);
  		print_r("<H1>INTENTELO NUEVAMENTE, ESTAMOS VERIFICANDO UN ERROR NOTIFICADO POR EL MONITOR DE WARNING<H1><BR><BR>");
  		print_r($fun_."<br><br>\n\n");*/
  		(eval(self::$sync . $fun_));
    }
    self::dieErrors($r_,'U');
    return $r_;
  }
  /**
   * Inserta o actualiza un registro
   * @param array $reg
   * @param string $table nombre de la tabla
   * @param string $filter
   */
  public static function save($reg, $table, $filter){
	  $r_ = self::connect()->save($reg, $table, $filter);
    $r_ && self::onReplicate('save',$reg, $table, $filter);
    self::dieErrors($r_,'S');
    return $r_;
  }
  /**
   * Replaza un registro
   * @param array $reg
   * @param string $table
   * @param boolean
   */
  public static function replace($reg, $table){
    $r_=self::connect()->replace($reg, $table);
	  $r_ && self::onReplicate('replace',$reg, $table);
    (self::$sync && $r_) && (eval(self::$sync."('R','$table',$r_,'".json_encode($reg)."');"));
    self::dieErrors($r_,'R');
    return $r_;
  }
  /**
   * Eliminacion fisica o logico de un registro en base de datos
   * @param string $table Nombre de tabla
   * @param string $filter Condicion a cumplir para actualizar
   * @param array|string $reg valores de borrado logico
   * @return boolean si es diferente de FALSE se realizó correctamente
   */
  public static function delete($table, $filter, $set = NULL){
    $tmp = (self::$sync ? json_encode(self::getSingleQuery($table, $filter)) : NULL);
    $r_=self::connect()->delete($table, $filter, $set);
	  $r_ && self::onReplicate('delete',$table, $filter,$set);
    if((self::$sync && $r_)){
      $filter = str_replace(array("'"), '"', $filter);
  	  $set = is_string($set) ? str_replace(array("'"), '"', $set) : $set;
      $tmp = $tmp ? str_replace(array("'"), "\\\\\\'", $tmp) : NULL;
      (eval(self::$sync."('D','$table','$filter','$set', '$tmp');"));
    }
    self::dieErrors($r_,'D');
    return $r_;
  }
  /**
   * Obtiene un objeto con los campos como propiedades haceindo un
   * match con el arreglo
   * @param array|string $reg Nombre de la tablas o arreglo de ementos para
   *        hacer un match con los campos del registro
   * @param string $table (opcional) Nombre de la table
   * @return object
   */
  public static function getFields($reg, $table = NULL){
    return self::connect()->getFields($reg, $table);
  }
  /**
   * Obtiene un objecto con los campos como propiedades
   * @param unknown_type $table
   * @return object
   */
  public static function getVo($table = NULL){
    return self::connect()->getVo($table);
  }
  /**
   * Obtiene el valor maximo de un campo
   * @param string $colum Nombre del campo
   * @param string $table (opcional) Nombre de la tabla
   * @return mixed
   */
  public static function getMax($colum, $table = NULL, $filter = NULL){
    return self::connect()->getMax($colum, $table, $filter);
  }
  /**
   * Obtiene el valor minimo de un campo
   * @param string $colum Nombre del campo
   * @param string $table (opcional) Nombre de la tabla
   * @return mixed
   */
  public static function getMin($colum, $table = NULL, $filter = NULL){
    return self::connect()->getMin($colum, $table, $filter);
  }
  /**
   * Cuenta el numero de registro de una tabla o resultado sql
   * @param string|resource $table (opcional) Nombre de la tabla o
   *        resultado de un query
   * @param string $filter (opcional) Condicion a realizar
   * @return integer
   */
  public static function count($table = NULL, $filter = NULL){
    return self::connect()->count($table, $filter);
  }
  /**
   * Obtien el numero de registros del ultimo resultado sql
   * @return integer
   */
  public static function lastCount(){
    return self::connect()->lastCount();
  }
  /**
   * Obtine el ultimo resultado sql un tipo de datos resource
   * @return resource
   */
  public static function lastResource(){
    return self::connect()->lastResource();
  }
  /**
   * Instacia sql para construccion de consultas
   * @return AifSql
   */
  public static function sql(){
    return new AifSql();
  }
  /**
   * Obtiene un objecto siguiente de un resultado sql
   * @param resource $result
   * @return object
   */
  public static function fetch($result){
    return self::connect()->fetch($result);
  }
  /**
   * Regresa la última consulta realizada
   * @return string
   */
  public static function getSql(){
    return self::connect()->getSql();
  }
  /**
   * Regresa el ultimo valor del identificador clave de la tabla
   */
  public static function getLastId($id = NULL, $table = NULL){
    return self::connect()->getLastId($id, $table);
  }
  /**
   *
   */
  public static function createSqlFunctions($type){
    return self::connect()->createSqlFunctions($type);
  }

  /**
   * Metodo que activa la opcion que devuelve solamente la consulta
   * sin ejecutarla desde el m?todo abstracto de self::conect()->query();
   * @param boolean $onlySQL - TRUE  : Activa y devuelve solamente la consulta
   * 						   FALSE : Desactiva y ejecuta la consulta
   * @return string
   */
  public static function setOnlySQL($onlySQL=FALSE){
  	return self::connect()->onlySQL = $onlySQL;
  }

  /**
   * [getSingleQuery description]
   * @param  [type] $table  [description]
   * @param  [type] $filter [description]
   * @return [type]         [description]
   */
  public static function getSingleQuery($table, $filter=NULL, $fields='*'){
    return self::toArray("SELECT $fields FROM $table ".($filter ? "WHERE $filter " : ""));
  }

  public static function getError(){
    return self::connect()->getErrors();
  }
}
