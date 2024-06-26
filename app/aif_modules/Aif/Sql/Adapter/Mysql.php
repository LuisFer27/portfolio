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

class AifSqlAdapterMysql extends AifSqlAbstract {
    /**
     * Inicializa el manejador de base de datos
     * @param array $cfg Arreglo con los parametros de configuracion
     */
    public function __construct($cfg = array()) {
        parent::__construct($cfg);
    }
    /**
     * Realiza una conexion a la base de datos
     * @return bool Regresa verdadero si se establece una conexion
     */
    protected function connect(){
        $this->link = $this->persistent ? mysql_pconnect($this->server,
                        $this->user, $this->password)
                : @mysql_connect($this->server, $this->user, $this->password);
        if (!$this->link) {
            $this->error = mysql_errno() . ": " . mysql_error();
            $this->errors[] = $this->error;
            return false;
        }
        Aif::$charset == 'UTF-8' && $this->link && self::query("SET NAMES 'utf8'");
        return $this->useDb($this->dbname);
    }

     public function close(){
        mysql_close($this->link);
        return true;
      }

    /**
     * Selecciona una base de datos
     * @param string $name Nombre de la base de datos
     * @return bool Regresa verdadero si se ejecuta con exito
     */
    protected function useDb($dbname) {
        if (!mysql_select_db($dbname, $this->link)) {
            $this->error = mysql_errno() . ": " . mysql_error();
            $this->errors[] = $this->error;
            return false;
        }
        return true;
    }

    /**
     * Ejecuta un query
     * @param string $sql Cadena con consulta SQL
     * @return bool Regresa verdadero si se ejecuta con exito
     */
    protected function doQuery($sql = "") {
        $this->sql = $sql;
        $result = mysql_query($this->sql, $this->link);
        if ($result === FALSE) {
            $this->error = mysql_errno() . ": " . mysql_error();
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
        return mysql_fetch_row($result);
    }

    /**
     * Obtiene un arreglo asociativo del resultado de una consulta
     * @param resource $result Resultado de una consulta
     * @return array Regresa un arreglo asociativo con los resultados
     */
    public function fetchAssoc($result) {
        return mysql_fetch_assoc($result);
    }

    /**
     * Obtiene un objecto del resultado de una consulta
     * @param resource $result Resultado de una consulta
     * @return object Regresa un objecto con los resultados
     */
    public function fetch($result) {
        return mysql_fetch_object($result);
    }

    /**
     *
     */
    public function getColumKey($table=NULL){
        $table = $table = $table ? $table : $this->table;
        $result = $this->query('SHOW COLUMNS FROM ' . $table);
        if ($result) {
            while (($o = $this->fetch($result))) {
                if($o->Key == 'PRI')return $o->Field;
            }
        }
        return FALSE;
    }


    /**
     * Obtiene el ultimo id agregado
     * @param mysqli_stmt|resource $resultset El objeto a ser analizado
     * @return integer El identificador del registro agregado
     */

    public function getInsertId($id = NULL, $table = NULL) {
        return mysql_insert_id($this->link);
    }

    /**
     * Obtiene la información de la consulta
     * @param mysqli_stmt|resource $resultset El objeto a ser analizado
     * @return string El resultado de la consulta, {@link
     * http://mx2.php.net/manual/es/function.mysql-info.php MySQL} o {@link
     * http://mx2.php.net/manual/es/mysqli.info.php MySQLi}
     */

    private function getInfo($resultset=NULL) {
        return mysql_info($this->_link);
    }

    /**
     * Obtiene la cantidad de registros afectados
     * @param mysqli_stmt|resource $resultset El objeto a ser analizado
     * @return integer El resultado de la consulta, {@link
     * http://mx2.php.net/manual/es/function.mysql-affected-rows.php MySQL} o
     * {@link http://mx2.php.net/manual/es/mysqli.affected-rows.php MySQLi}
     */

    private function getAfectedRows($resultset=NULL) {
        return mysql_affected_rows($this->_link);
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
        return mysql_errno($this->_link) . ': ' . mysql_error($this->_link);
    }

    /**
     * Cuenta los registros devueltos por una consulta
     * @access public
     * @param resource $resultset El resource del cual se quiere el conteo
     * @return integer La cantidad de registros
     */

    public function doCount($resultset) {
        return is_resource($resultset) ? mysql_num_rows($resultset) : FALSE;
    }
    /**
     * (non-PHPdoc)
     * @see AifSqlAbstract::getFilds()
     */
    public function getFields($reg, $table = NULL) {
        $this->table = $table = $table ? $table : $this->table;
        $table = $table ? $table : $reg;
        $result = $this->query('SHOW COLUMNS FROM ' . $table);
        if (is_resource($result)) {
            $return = array();
            $isReg = is_array($reg);
            while (($o = $this->fetch($result))) {
                if ($isReg) {
                    if (isset($reg[$o->Field]))
                        $return[$o->Field] = $reg[$o->Field];
                } else
                    $return[$o->Field] = $o->Key != 'PRI' ? $o->Default : '';
            }
            return $isReg ? $return : (object) $return;
        }
        return FALSE;
    }

}
