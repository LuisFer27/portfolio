<?php
/**
 * Clase para manejo de consultas SQL y cargador de dapatador
 *
 * Clase que permite cargar el adaptador de base de datos
 * @author $Author$
 * @copyright Copyright (c) 2008, {@link http://www.aquainteractive.com Aqua
 * Interactive}
 * @package Aif
 * @since 2013-04-15
 * @subpackage Sql
 * @version $Id$
 */

/**
 * Clase para manejo de consultas SQL y cargador de dapatar
 *
 * @package Aif
 * @subpackage Sql
 */

class AifSql {
    /**
     * Consulta sql
     * @var string
     */
    public $sql = '';
    /**
     * Cargar el adaptador de base de datos
     * @param string $name (Opcional)nombre de la seccion de configuración con los parametros
     * de base de datos, pordefault: "DataBase"
     * @return object Objecto controlador de base de atos
     */
    public static function load($name = 'DataBase') {
        $cfg = Aif::$cfg->$name;
        !is_array($cfg) && $cfg != '' && ($cfg = Aif::ecsc('aif/lib','urlArray',$cfg));
        if (isset($cfg['driver'])) {
            $driver = ucfirst(mb_strtolower($cfg['driver']));
            if($cfg['driver']=='nosql')return new AifNosql($cfg);
            if (Aif::autoLoad('AifSqlAdapter' . $driver)) {
                $cc = 'AifSqlAdapter' . $driver;
                return new $cc($cfg);
            } else
                Aif::ecsc('aif/debugger','error',"Adapter not found \"$driver\"");
        } else
        Aif::ecsc('aif/debugger','error','Database driver not found');
    }
    /**
     * Devulve la cadena sql
     * @return string
     */
    public function __toString() {
        return $this->sql;
    }
    /**
     * Concatena la sentencia SELECT dentro de la propiedad $sql
     * @param string $sql
     * @return AifSql
     */
    public function select($sql) {
        $this->sql = 'SELECT ' . $sql;
        return $this;
    }
    /**
     * Concatena la sentencia FROM dentro de la propiedad $sql
     * @param string $sql
     * @return AifSql
     */
    public function from($sql) {
        $this->sql .= ' FROM ' . $sql;
        return $this;
    }
    /**
     * Concatena la sentencia WHERE dentro de la propiedad $sql
     * @param string $sql
     * @return AifSql
     */
    public function where($sql = '') {
        if ($sql)
            $this->sql .= ' WHERE ' . $sql;
        return $this;
    }
    /**
     * Concatena la sentencia LEFT JOIN dentro de la propiedad $sql
     * @param string $table
     * @param string $filter
     * @return AifSql
     */
    public function leftJoin($table, $filter) {
        $this->sql .= ' LEFT JOIN ' . $table . ' ON ' . $filter;
        return $this;
    }
    /**
     * Concatena la sentencia INNER JOIN dentro de la propiedad $sql
     * @param string $table
     * * @param string $filter
     * @return AifSql
     */
    public function innerJoin($table, $filter) {
        $this->sql .= ' INNER JOIN ' . $table . ' ON ' . $filter;
        return $this;
    }
    /**
     * Concatena la sentencia GROUP BY dentro de la propiedad $sql
     * @param string $sql
     * @return AifSql
     */
    public function groupBy($sql) {
        $this->sql .= ' GROUP BY ' . $sql;
        return $this;
    }
    /**
     * Concatena la sentencia ORDERB BY dentro de la propiedad $sql
     * @param string $sql
     * @return AifSql
     */
    public function orderBy($sql) {
        $this->sql .= ' ORDER BY ' . $sql;
        return $this;
    }
    /**
     * Concatena la sentencia HAVING dentro de la propiedad $sql
     * @param string $filter
     * @return AifSql
     */
    public function having($filter) {
        $this->sql .= ' HAVING ' . $filter;
        return $this;
    }
    /**
     * Concatena la sentencia LIMIT dentro de la propiedad $sql
     * @param string $ini
     * * @param string $fin
     * @return AifSql
     */
    public function limit($ini, $fin = NULL) {
        $this->sql .= ' LIMIT ' . $ini . ' ON ' . $fin;
        return $this;
    }
    /**
     * Devuelve la cadena sql formada por los metos
     * @return string
     */
    public function getSql() {
        return $this->sql;
    }

}
