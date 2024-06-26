<?php
/**
 * Clase para manejo de base de datos
 *
 * @author
 *
 *
 *
 *
 *
 * @copyright Copyright (c) 2008, {@link http://www.aquainteractive.com Aqua
 *            Interactive}
 * @package Aif
 * @since 2011-03-01
 * @subpackage Sql
 * @version $Id$
 */

/**
 * Clase para manejo de acceso a base de datos
 *
 * @package Aif
 * @subpackage Sql
 */
abstract class AifSqlAbstract {
  /**
   * Url de conexion
   * @var string
   */
  protected $url = "mysql://root:@localhost/test#p";
  /**
   * Manejador de base de datos
   * @var string
   */
  protected $driver = '';
  /**
   * Host del servidor
   * @var string
   */
  protected $server = 'localhost';
  /**
   * Nombre del usuario
   * @var string
   */
  protected $user = 'root';
  /**
   * Contraseña del usuario
   * @var string
   */
  protected $password = '';
  /**
   * Nombre de la base de datos
   * @var string
   */
  protected $dbname = 'test';
  /**
   * Puerto de conexion
   * @var integer
   */
  protected $port = 3356;
  /**
   * Determina si la conexion es persistente
   * @var boolean
   */
  protected $persistent = false;
  /**
   * Arreglo de errores
   * @var array
   */
  public $errors = array ();
  /**
   * Ultimo error
   * @var string
   */
  public $error = '';
  /**
   * Ultima cadena sql utilizada
   * @var string
   */
  public $sql = '';
  /**
   * Identificador de conexion de base de datos
   * @var mixed
   */
  public $link = NULL;
  /**
   * Ultimo resultado obtenido de un query
   * @var resource
   */
  protected $result = NULL;
  /**
   *
   * @var unknown
   */
  public $onlySQL = FALSE;
  /**
   * Ultima tabla utilizada
   * @var string
   */
  public $table = NULL;
  public $isUtf8 = false;

  /**
   * Recibe las propiedades de conexion en un arreglo
   * @param array $cfg
   */
  public function __construct($cfg = array()){
    foreach ($cfg as $key => $value)
      $this->$key = $value;
    $this->connect();
  }
  /**
   * Evalúa si esta activa una session para realizar la consulta proporcionada.
   * @param string $SQL Contiene la consulta sql
   * @return mixed El valor retornado por this::doQuery()
   */
  public function query($sql){

  	if($this->onlySQL) return $this->sql = $sql;
    $this->result = $this->doQuery($sql);
    $ty = $this->result !== FALSE ? 'TRUE' : 'FALSE';
    $error = $this->result === FALSE ? " ERROR({$this->error})" : '';
    Aif::ecsc('aif/debugger','set',Array("$ty/Query",'('.$this->sql. ") $error"),2);
    return $this->result;
  }

  /**
   * [getError description]
   * @param  [type] $sql [description]
   * @return [type]      [description]
   */
  public function getErrors(){
    return $this->errors;
  }
  /**
   * Obtine un areglo de objectos de una seleccion de registros
   * @param string $sql
   * @param string $ordBy
   * @param string $page
   * @return array
   */
  public function toObject($sql, $ordBy = NULL, $page = NULL){
    $sql = $sql ? $sql : $this->sql;
    if (is_resource($sql)){
    }else{
      if (($rs = $this->query($sql)) !== FALSE){
        $row = array ();
        while ($r = $this->fetch($rs))
          $row[] = $r;
        return $row;
      }
    }
    return FALSE;
  }
  /**
   * Obtine un objecto de un registro
   * @param string $sql
   * @param string $ordBy
   * @param string $page
   */
  public function firstObject($sql, $ordBy = NULL, $page = NULL){
    $return = $this->toObject($sql, $ordBy, $page);
    return isset($return[0]) ? $return[0] : NULL;
  }
  /**
   * Obtiene un catalgo
   * @param string $sql
   * @param string $ordBy
   * @param string $page
   * @return multitype:NULL |boolean
   */
  public function toCatArray($sql, $ordBy = NULL, $page = NULL){
    $sql = $sql ? $sql : $this->sql;
    if (is_resource($sql)){
    }else{
      $row = array ();
      if (($rs = $this->query($sql))){
        while ($r = $this->fetch($rs))
          $row[$r->id] = $r->name;
      }
      return $row;
    }
    return false;
  }
  /**
   *
   * @param string $sql
   * @param string $ordBy
   * @param string $page
   * @return multitype:unknown |boolean
   */
  public function toArray($sql = NULL, $ordBy = NULL, $page = NULL){
    $sql = $sql ? $sql : $this->sql;
    if (is_resource($sql)){
    }else{
      $row = array ();
      if (($rs = $this->query($sql))){
        while ($r = $this->fetchAssoc($rs))
          $row[] = $r;
      }
      return $row;
    }
    return false;
  }

  /**
   * Inserta un registro solo una vez
   * @param unknown $reg
   * @param unknown $table
   * @param string $id
   * @return boolean
   */
  public function insertOnce($reg, $table = NULL, $id = NULL){
    $this->table = $table = $table ? $table : $this->table;
    $keys = '(`' . implode('`,`', array_keys($reg)) . '`)';
    $vals = "('" . implode("','", $this->clean($reg)) . "')";
    $md5 = md5($keys . $vals);
    $this->sql = "INSERT INTO $table $keys VALUES $vals";
    if (Aif::ecsc('aif/session','get','__once_insert__') != $md5){
      $res = $this->query($this->sql);
      if ($res === FALSE) return FALSE;
      Aif::ecsc('aif/session','set',Array('__once_insert__', $md5));
      return $this->getInsertId($id);
    }
    Aif::ecsc('aif/debugger','set','Refresh::(' . $this->sql . ')');
    return TRUE;
  }
  /**
   * Isertar registros de arreglos asociativos de multiple dimenciones
   * @param unknown $reg
   * @param string $table
   * @param string $id
   * @return boolean
   */
  public function insertMultiple($reg, $table = NULL, $id = NULL){
    $this->table = $table = $table ? $table : $this->table;
    if (isset($reg[0])){
      $keys = '(`' . implode('`,`', array_keys($reg[0])) . '`)';
      foreach ($reg as $idx => $rr)
        $reg[$idx] = "('" . implode("','", $this->clean($rr)) . "')";
      $vals = implode(',', $reg);
      $this->sql = "INSERT INTO $table $keys VALUES $vals";
      $res = $this->query($this->sql);
      if ($res === FALSE) return FALSE;
      Aif::ecsc('aif/debugger','set','Multiple insert::(' . $this->sql . ')');
      return TRUE;
    }
    return FALSE;
  }
  /**
   *
   * @param unknown $reg
   * @param unknown $table
   * @param string $id
   * @param string $once
   * @return boolean
   */
  public function insert($reg, $table = NULL, $id = NULL, $once = TRUE){
    if (isset($reg[0])) return $this->insertMultiple($reg, $table, $id);
    if ($id === TRUE || $once) return $this->insertOnce($reg, $table, $id);
    $this->table = $table = $table ? $table : $this->table;
    $keys = '(`' . implode('`,`', array_keys($reg)) . '`)';
    $vals = "('" . implode("','", $this->clean($reg)) . "')";
    $res = $this->query("INSERT INTO $table $keys VALUES $vals");
    return $res === FALSE ? FALSE : $this->getInsertId($id);
  }


  public function replaceMultiple($reg, $table = NULL, $id = NULL){
    $this->table = $table = $table ? $table : $this->table;
    if (isset($reg[0])){
      $keys = '(`' . implode('`,`', array_keys($reg[0])) . '`)';
      foreach ($reg as $idx => $rr)
        $reg[$idx] = "('" . implode("','", $this->clean($rr)) . "')";
      $vals = implode(',', $reg);
      $this->sql = "REPLACE INTO $table $keys VALUES $vals";
      $res = $this->query($this->sql);
      if ($res === FALSE) return FALSE;
      Aif::ecsc('aif/debugger','set','Multiple replace::(' . $this->sql . ')');
      return TRUE;
    }
    return FALSE;
  }
  /**
   *
   * @param unknown $reg
   * @param unknown $table
   * @return mixed
   */
  public function replace($reg, $table = NULL,$id=NULL){
    if (isset($reg[0])) return $this->replaceMultiple($reg, $table, $id);
    $this->table = $table = $table ? $table : $this->table;
    $keys = '(`' . implode('`,`', array_keys($reg)) . '`)';
    $vals = "('" . implode("','", $this->clean($reg)) . "')";
    return $this->query("REPLACE INTO $table $keys VALUES $vals");
  }

  /**
   * Solo ejecuta un update
   * @param unknown $table
   * @param unknown $set
   * @param unknown $filter
   * @return mixed
   */
  public function onlyUpdate($table, $set, $filter){
    $this->table = $table;
    return $this->query("UPDATE $table SET $set WHERE $filter");
  }
  /**
   *
   * @param unknown $reg
   * @param unknown $table
   * @param unknown $filter
   * @param string $id
   * @return boolean
   */
  public function update($reg, $table, $filter, $id = NULL){
    $set = '';
    if (is_array($reg)){
      foreach ($reg as $k => $s)
        $set .= ($set ? ',' : '') . $k . "='" . $this->clean($s) . "'";
    }else{
      $set = $table;
      $table = $reg;
    }
    $this->table = $table;
    return $this->query("UPDATE $table SET $set WHERE $filter");
  }
  /**
   *
   * @param unknown $reg
   * @param unknown $table
   * @param unknown $filter
   * @return Ambigous <boolean, mixed>|boolean
   */
  public function save($reg, $table, $filter=NULL){
    $this->table = $table;
    if($filter){
    	$rs = $this->query("SELECT COUNT(*) As isRow FROM $table WHERE $filter");
   // if (is_resource($rs)){
    	$rd = $this->fetch($rs);
    }
    return ($filter && is_object($rd) && $rd->isRow) ? $this->update($reg, $table,
    	$filter) : $this->insert($reg, $table);
   // }
    return FALSE;
  }
  /**
   *
   * @param unknown $table
   * @param unknown $filter
   * @param string $set
   * @return Ambigous <boolean, unknown>
   */
  public function delete($table, $filter, $set = NULL){
    $this->table = $table;
    if ($set)     // Borrado lgico
    return $this->query("UPDATE $table SET $set WHERE $filter");
    else return $this->query("DELETE FROM $table WHERE $filter");
  }

  /**
   * Protege y quita speacios en blanco al inicio y final de la cadena o
   * valores del arreglo
   * @param array|string $data Cadena o arreglo
   * @return array string o arreglo
   */
  public function clean($data){
    if (is_array($data)) return array_map(array ($this, 'clean'
    ), $data);
    if(is_object($data)){
      print_r($data);
      die();
    }
    $data = trim($data);
    if (!intval($data)){
      // $data = addslashes($data);
      // $data = mysql_real_escape_string($data);
      // $data = htmlentities($data, NULL, "ISO-8859-1");
      $data = str_replace(array ('<', '>',"'"
      ), array ('&lt;', '&gt;','&apos;'
      ), $data);
      // echo $data;
    }
    return $data;
  }

  /**
   *
   * @param unknown $table
   */
  public function getVo($table = NULL){
    $this->table = $table = $table ? $table : $this->table;
    return $this->getFields($table);
  }
  /**
   *
   * @param unknown $colum
   * @param string $table
   * @return boolean
   */
  public function getMax($colum, $table = NULL, $filter=NULL){
    $this->table = $table = $table ? $table : $this->table;
    $rs = $this->query('SELECT MAX(' . $colum . ') As rs FROM ' . $table .
      		($filter ? ' WHERE ' . $filter : ''));
    $rd = $this->fetch($rs);
    return $rd ? $rd->rs : FALSE;
  }
  /**
   *
   * @param unknown $colum
   * @param string $table
   * @return boolean
   */
  public function getMin($colum, $table = NULL, $filter=NULL){
    $this->table = $table = $table ? $table : $this->table;
    $rs = $this->query('SELECT MIN(' . $colum . ') As rs FROM ' . $table .
      		($filter ? ' WHERE ' . $filter : ''));
    $rd = $this->fetch($rs);
    return $rd ? $rd->rs : FALSE;
  }

  /**
   * Regresa la última consulta realizada
   */
  public function getSql(){
    return $this->sql;
  }

  /**
   *
   * @param string $table
   * @param string $filter
   * @return boolean
   */
  public function count($table = NULL, $filter = NULL){
    if (is_resource($table) || is_object($table)) return $this->doCount($table);
    $this->table = $table = $table ? $table : $this->table;
    $rs = $this->query(
      'SELECT COUNT(*) As rs FROM ' . $table .
       ($filter ? ' WHERE ' . $filter : ''));
    $rd = $this->fetch($rs);
    return $rd ? $rd->rs : FALSE;
  }

  /**
   *
   * @param string $id
   * @param string $table
   * @return boolean
   */
  public function getLastId($id = NULL, $table = NULL){
    $table = $table ? $table : $this->table;
    $this->table = $table;
    if ($table){
      $id || ($id = $this->getColumKey($table));
      return $this->getMax($id, $table);
    }
    return FALSE;
  }
  /**
   *
   * @return boolean
   */
  public function lastCount(){
    return $this->count($this->lastResource());
  }
  /**
   *
   * @return resource
   */
  public function lastResource(){
    return $this->result;
  }

  /**
   */
  public function createSqlFunctions($type){
    return $this->driver == 'sqlite' ? $this->_createSqlFunctions($type) : FALSE;
  }
  /**
   */
  abstract protected function connect();
  /**
   *
   * @param unknown_type $name
   */
  abstract protected function useDb($name);
  /**
   *
   * @param unknown_type $sql
   */
  abstract protected function doQuery($sql);
  /**
   *
   * @param unknown_type $result
   */
  abstract public function fetch($result);
  /**
   *
   * @param unknown_type $result
   */
  abstract public function fetchAssoc($result);
  /**
   *
   * @param unknown_type $result
   */
  abstract public function fetchRow($result);
  /**
   */
  abstract public function getInsertId($id = NULL, $table = NULL);
  /**
   *
   * @param unknown $reg
   * @param string $table
   */
  abstract public function getFields($reg, $table = NULL);
  /**
   *
   * @param unknown $result
   */
  abstract public function doCount($result);
  /**
   *
   * @param unknown $table
   */
  abstract function getColumKey($table = NULL);

  /**
   *
   * @param unknown $table
   */
  abstract public function close();
}
