<?php
/**
 * Clase manejador de base de datos para servidor MYSQL
 *
 * @author    $Author$
 * @copyright    Copyright (c) 2008, {@link http://www.aquainteractive.com Aqua
 * Interactive}
 * @package    Aif
 * @since    $Date$
 * @subpackage    core
 * @version    $Id$
 */

class AifSqlAdapterPgsql extends AifSqlAbstract {
    /**
     * Inicializa el manejador de base de datos
     * @param array $cfg Arreglo con los parametros de configuracion
     */
    public function __construct($cfg = array()) {
        parent::__construct($cfg);
    }
    protected function connect(){
        $this->link = $this->persistent ? pg_pconnect("host={$this->server}
                        user={$this->user} password={$this->password} dbname={$this->dbname}")
                : @pg_connect("host={$this->server}
                                user={$this->user} password={$this->password} dbname={$this->dbname}");
        if (!$this->link) {
            $this->error = pg_last_error() . ": " . pg_result_error();
            $this->errors[] = $this->error;
            return false;
        }
        Aif::$charset == 'UTF-8' && $this->link && self::query("SET NAMES 'utf8'");
        return True;
    }
    public function close(){
       pg_close($this->link);
       return true;
     }
     /**
      * Selecciona una base de datos
      * @param string $name Nombre de la base de datos
      * @return bool Regresa verdadero si se ejecuta con exito
      */
     protected function useDb($dbname) {
       /*$pg_q=pg_query($this->link,"USE $dbname;");
       if (pg_dbname($this->link)!=$dbname) {
           $this->error = pg_last_error() . ": " . pg_result_error();
           $this->errors[] = $this->error;
           return false;
       }*/
         return null;
     }
     /**
      * Ejecuta un query
      * @param string $sql Cadena con consulta SQL
      * @return bool Regresa verdadero si se ejecuta con exito
      */
     protected function doQuery($sql = "") {
         $this->sql = $sql;
         $result = pg_query($this->link,$this->sql);
         if ($result === FALSE) {
             $this->error = pg_last_error() . ": " . pg_result_error();
             $this->errors[] = $this->error;
             return FALSE;
         }
         return $result;
     }
     /**
      * Obtiene un arreglo del resultado de una consulta
      * @param resource $result Resultado de una consulta
      * @return array Regresa un arreglo con los resultados
      */
     public function fetchRow($result) {
         return pg_fetch_row($result);
     }
     /**
      * Obtiene un arreglo asociativo del resultado de una consulta
      * @param resource $result Resultado de una consulta
      * @return array Regresa un arreglo asociativo con los resultados
      */
     public function fetchAssoc($result) {
         return pg_fetch_assoc($result);
     }
     /**
      * Obtiene un objecto del resultado de una consulta
      * @param resource $result Resultado de una consulta
      * @return object Regresa un objecto con los resultados
      */
     public function fetch($result) {
         return pg_fetch_object($result);
     }
     /**
      *
      */
     public function getColumKey($table=NULL){
         $table = $table = $table ? $table : $this->table;
         $result = self::getFields(null,$table);
         if ($result) {
             return $result->key_;
         }
         return FALSE;
     }
     /**
      * Obtiene el ultimo id agregado
      * @param mysqli_stmt|resource $resultset El objeto a ser analizado
      * @return integer El identificador del registro agregado
      */

     public function getInsertId($id = NULL, $table = NULL) {
         return null;
     }

     /**
      * Obtiene la información de la consulta
      * @param mysqli_stmt|resource $resultset El objeto a ser analizado
      * @return string El resultado de la consulta, {@link
      * http://mx2.php.net/manual/es/function.mysql-info.php MySQL} o {@link
      * http://mx2.php.net/manual/es/mysqli.info.php MySQLi}
      */

     private function getInfo($resultset=NULL) {
         return null;
     }

     /**
      * Obtiene la cantidad de registros afectados
      * @param mysqli_stmt|resource $resultset El objeto a ser analizado
      * @return integer El resultado de la consulta, {@link
      * http://mx2.php.net/manual/es/function.mysql-affected-rows.php MySQL} o
      * {@link http://mx2.php.net/manual/es/mysqli.affected-rows.php MySQLi}
      */

     private function getAfectedRows($resultset=NULL) {
         return null;
     }
     /**
      * Obtiene la cantidad de registros afectados
      * @param mysqli_stmt|resource $resultset El objeto a ser analizado
      * @param mixed $return El valor a regresar si no contiene errores
      * el $resultset
      * @return mixed Una descripción completa del error, el valor de return
      * si existe o $resultset
      */

     private function getError($resultset=NULL, $return=NULL) {
         $error = mysql_errno($this->_link);
         if ($error == 0) {
             return $return ? $return : $resultset;
         }
         return null;
     }

     /**
      * Cuenta los registros devueltos por una consulta
      * @access public
      * @param resource $resultset El resource del cual se quiere el conteo
      * @return integer La cantidad de registros
      */

     public function doCount($resultset) {
         return null;
     }
     /**
      * (non-PHPdoc)
      * @see AifSqlAbstract::getFilds()
      */
     public function getFields($reg, $table = NULL) {
         $this->table = $table = $table ? $table : $this->table;
         $table = $table ? $table : $reg;
         $result = $this->query("SELECT DISTINCT
              a.attnum as num,
              a.attname as name,
              format_type(a.atttypid, a.atttypmod) as typ,
              a.attnotnull as notnull,
              com.description as comment,
              coalesce(i.indisprimary,false) as primary_key,
              def.adsrc as default
          FROM pg_attribute a
          JOIN pg_class pgc ON pgc.oid = a.attrelid
          LEFT JOIN pg_index i ON
              (pgc.oid = i.indrelid AND i.indkey[0] = a.attnum)
          LEFT JOIN pg_description com on
              (pgc.oid = com.objoid AND a.attnum = com.objsubid)
          LEFT JOIN pg_attrdef def ON
              (a.attrelid = def.adrelid AND a.attnum = def.adnum)
          WHERE a.attnum > 0 AND pgc.oid = a.attrelid
          AND pg_table_is_visible(pgc.oid)
          AND NOT a.attisdropped
          AND pgc.relname = '$table'  -- Your table name here
          ORDER BY a.attnum;");
         if (is_resource($result)) {
             $return = array();
             $isReg = is_array($reg);
             while (($o = $this->fetch($result))) {
                 if ($isReg) {
                     if (isset($reg[$o->name]))
                         $return[$o->name] = $reg[$o->name];
                 } else
                    $return[$o->name] = $o->primary_key == 't' ? $o->name : '';
                    $return["key_"] = $o->primary_key == 't' ? $o->name : null;
                    //$return[$o->Field] = $o->Key != 'PRI' ? $o->Default : '';
             }
             return $isReg ? $return : (object) $return;
         }
         return FALSE;
     }
}
