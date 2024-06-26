<?php
/**
 * Manejo y manipulación de las Cookies
 *
 * Manejo y manipulación de las Cookies
 * @author $Author$
 * @copyright Copyright (c) 2008, {@link http://www.aquainteractive.com AquaInteractive}
 * @package Aif
 * @since 2013-04-15
 * @subpackage Lib
 * @version $Id$
 */

/**
 * Clase de manejo de Cookies
 *
 * Clase de manejo y manipulacion de Cookies
 * @package Aif
 * @subpackage Lib
 */
class AifCookie {
    /**
     * Establece una cookie para la aplicación
     * @param string $name
     * @param mixed $value
     * @param string $duration Duracion de la cookie
     * @param string $host URL para la cookie
     * @return boolean
     */
    public static function setApp($name, $value, $duration = '+8 hours',
            $host = NULL) {
        return self::set(Aif::$application . '[' . $name . ']', $value,
                $duration, $host);
    }
    /**
     * Establece una cookie normal
     * @param string $name
     * @param mixed $value
     * @param string $duration Duracion de la cookie
     * @param string $host URL para la cookie
     * @return boolean
     */
    public static function set($name, $value, $duration = '+8 hours',
            $host = NULL) {
        return setcookie($name,
                is_string($value) ? $value : json_encode($value),
                (is_string($duration) ? strtotime($duration) : $duration), '/', $host);
    }
    /**
     * Obtiene un valor de una cookie de aplicación
     * @param string $name
     * @return mixed Valor alamcenada e
     */
    public static function getApp($name) {
        return isset($_COOKIE[Aif::$application][$name]) ? AifLibConvert::toPhp(
                        $_COOKIE[Aif::$application][$name]) : FALSE;
    }
    /**
     * Obtiene el valor de una cookie
     * @param strin $name
     * @return mixed
     */
    public static function get($name) {
        return isset($_COOKIE[$name]) ? AifLibConvert::toPhp($_COOKIE[$name])
                : FALSE;
    }
    /**
     * Elimina una cookie de aplicación en el cliente
     * @param string $name
     * @return boolean
     */
    public static function removeApp($name = NULL) {
        return self::setApp($name, 'FALSE', '-1 day');
    }
    /**
     * Elimina una cookie en el cliente
     * @param string $name
     * @return boolean
     */
    public static function remove($name = NULL) {
        return self::set($name, 'FALSE', '-1 day');
    }
}
