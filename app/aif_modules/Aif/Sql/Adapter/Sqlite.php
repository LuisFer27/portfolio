<?php
/**
 * Clase Adaptador de base de datos de Sqlite3 para php5.3 >
 * @author $Author$
 * @copyright Copyright (c) 2008, {@link http://www.aquainteractive.com Aqua Interactive}
 * @package Aif
 * @since $Date$
 * @subpackage core
 * @version $Id$
 */
class AifSqlAdapterSqlite extends AifSqlAbstract {
  public $path = NULL;
  /**
   * Utilizando el cargado de funciones
   * @var unknown
   */
  public $isFunc = FALSE;
  /**
   * Inicializa el manejador de base de datos
   * @param array $cfg Arreglo con los parametros de configuracion
   */
  public function __construct($cfg = array()){
    parent::__construct($cfg);

  }

 public function close(){
    $this->link->close();
    return true;
  }

  // SELECT id, min(date) as min_date, max(date) as max_date, type, partMsg,
  // level, page, COUNT(partMsg) AS msgCount
  // FROM (SELECT id, date, type, SUBSTRING_INDEX(message, ':', 1) AS partMsg,
  // level, page FROM alarms a WHERE acknowledged='0') p
  // GROUP BY partMsg

  // SELECT id, min(date) as min_date, max(date) as max_date, type, partMsg,
  // level, page, COUNT(partMsg) AS msgCount FROM (SELECT id, date, type,
  // substr(message, ':', 1) AS partMsg, level, page FROM alarms a
  // WHERE acknowledged='0') p GROUP BY partMsg

  /**
   * Realiza una conexion a la base de datos
   * @return bool Regresa verdadero si se establece una conexion
   */
  protected function connect(){
    if ($this->link) return TRUE;
    $const = Aif::config('SQLITE3_CONST') || NULL;
    $const || ($const=SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
    $this->path || ($this->path = preg_replace('/^\//', '', $this->dbname));
    if (file_exists($this->path) || file_exists(dirname($this->path))){
      $this->link = new SQLite3($this->path,$const , $this->password);
      if ($this->link) return TRUE;
      $this->error = 'Error de conexion';
      $this->errors[] = $this->error;
    }
    return FALSE;
  }

  /**
   * Selecciona una base de datos nueva
   * @param string $name Nombre de la base de datos
   * @return bool Regresa verdadero si se ejecuta con exito
   */
  protected function useDb($dbname){
    if (file_exists($dbname)){
      $this->link = (!$this->password) ? $this->link = new SQLite3($this->path) : new SQLite3(
          $this->path, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE,
          $this->password);
      if (!$this->link){
        $this->error = 'Error de conexion';
        $this->errors[] = $this->error;
        return FALSE;
      }
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Ejecuta un query
   * @param string $sql Cadena con consulta SQL
   * @return bool Regresa verdadero si se ejecuta con exito
   */
  protected function doQuery($sql = ""){
    $this->isFunc && ($sql = sqliteCreateFunctionsParser($sql));
    $this->sql = $sql;
    $result = FALSE;
    if ($this->link){
      $result = $this->link->query($this->sql);// or die($this->sql);
      if ($result === FALSE){
        $this->error = $this->link->lastErrorCode() . ": " .
             $this->link->lastErrorMsg();
        $this->errors[] = $this->error;
        return FALSE;
      }
    }
    return $result;
  }

  /**
   * Obtiene un arreglo del resultado de una consulta
   * @param resource $result Resultado de una consulta
   * @return array Regresa un arreglo con los resultados
   */
  public function fetchRow($result){
    return $result ? $result->fetchArray() : NULL;
  }

  /**
   * Obtiene un arreglo asociativo del resultado de una consulta
   * @param resource $result Resultado de una consulta
   * @return array Regresa un arreglo asociativo con los resultados
   */
  public function fetchAssoc($result){
    return $result ? $result->fetchArray(SQLITE3_ASSOC) : NULL;
  }

  /**
   * Obtiene un objecto del resultado de una consulta
   * @param resource $result Resultado de una consulta
   * @return object Regresa un objeto con los resultados
   */
  public function fetch($result){
    $row = $this->fetchAssoc($result);
    return $row == FALSE ? NULL : ( object ) $row;
  }

  /**
   * Obtiene la primera columna tipo key
   * @param $table string (table) Nombre de la tabla
   */
  public function getColumKey($table = NULL){
    $table = $table ? $table : $this->table;
    if ($table){
      $result = $this->query("PRAGMA table_info ($table)");
      if ($result){
        while (($o = $this->fetch($result)))
          if ($o->pk) return $o->name;
      }
    }
    return NULL;
  }

  /**
   * Obtiene el ultimo id agregado
   * @param mysqli_stmt|resource $resultset El objeto a ser analizado
   * @return integer El identificador del registro agregado
   */
  public function getInsertId($id = NULL, $table = NULL){
    // $id = $id === TRUE ? NULL : $id;
    $_id = $this->link->lastInsertRowID();
    return $_id ? $_id : $this->getLastId($id, $table);
  }

  /**
   * Obtiene la información de la consulta
   */
  private function getInfo($result){
    return FALSE;
  }

  /**
   * Obtiene la cantidad de registros afectados
   */
  private function getAfectedRows($result){
    return FALSE;
  }

  /**
   * Obtiene el ultimo error
   */
  private function getError($resultset, $return){
    return FALSE;
  }

  /**
   * Cuenta los registros devueltos por una consulta
   * @access public
   * @param resource $resultset El resource del cual se quiere el conteo
   * @return integer La cantidad de registros
   */
  public function doCount($result){
    return is_object($result) ? $result->numColumns() : FALSE;
  }
  /**
   * Obtiene los campos de un tabla
   */
  public function getFields($reg, $table = NULL){
    $this->table = $table = $table ? $table : $this->table;
    $table = $table ? $table : $reg;
    $result = $this->query('PRAGMA table_info(' . $table . ')');
    if ($result){
      $return = array ();
      $isReg = is_array($reg);
      while (($o = $this->fetch($result))){
        if ($isReg){
          if (isset($reg[$o->name])) $return[$o->name] = $reg[$o->name];
        }else
          $return[$o->name] = $o->pk ? $o->dflt_value : '';
      }
      return $isReg ? $return : ( object ) $return;
    }
    return FALSE;
  }

  /**
   * Crea funciones para compatibilidad con otros motores de base de datos
   * @param unknown $type
   */
  public function _createSqlFunctions($type){
    $this->isFunc = TRUE;
    switch ($type){
      case 'mysql' :
        require (defined('AIF_APP_') ? AIF_APP_ : AIF_APP) .
                 "/Aif/Sql/Adapter/Sqlite/functions_mysql.php";
        break;
    }
  }

  // Mysql//IF(lkey >= $key, lkey + 2 , lkey )
  // CASE WHEN lkey >= $key THEN lkey + 2 ELSE lkey END
}
