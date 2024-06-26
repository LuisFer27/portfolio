<?php
/**
 * Manejo y control de idiomas
 *
 * Manejo y control de idiomas
 * @author $Author$
 * @copyright Copyright (c) 2008, {@link http://www.aquainteractive.com AquaInteractive}
 * @package Aif
 * @since 2013-04-15
 * @subpackage Lib
 * @version $Id$
 */

/**
 * Manejo y control de idiomas
 *
 * Clase de manejo y control de idiomas
 * @package Aif
 * @subpackage Lib
 */
class AifLang {
    /**
     * Constante para formato de archivo de idiomas INI
     * @var integer
     */
    const INI = 1;
    /**
     * Constante para formato de de archivo idiomas XML
     * @var integer
     */
    const XML = 2;
    /**
     * Constante para formato de archivo idiomas en arreglo de PHP
     * @var integer
     */
    const PHP_ARRAY = 3;
    /**
     * Codigo md5 de la etiqueta
     * @var integer
     */
    const CODE_MD5 = 1;
    /**
     * Codigo clave de la etiqueta
     * @var integer
     */
    const CODE_TEXT = 2;
    /**
     *  Codigo tipo etiqueta
     * @var integer
     */
    const CODE_TAG = 3;

    public static $header = "<?php \n/**\n * Archivo de idioma, generado el #DATE# \n */\n\n";

    /**
     * Nombre del arhivo de idiomas
     * @var string
     */
    public static $file;
    /**
     * Tipo de codigo a utilizar como clave para las etiquetas de idioma
     * @var integer
     */
    public static $useKeyCode = NULL;
    /**
     * Determina si contruye de forma automatica un archivo de idiomas
     * @var boolean
     */
    public static $isWrite = FALSE;
    /**
     * Tipo de formato a utilizar para manejos de idiomas
     * @var integer
     */
    public static $type = self::PHP_ARRAY;
    /**
     * Arreglo donde se cargan las etiqutas del sistema
     * @var array
     */
    public static $lang = NULL;

    /**
     * Carga un archivo de idiomas existente
     * @param string $file
     * @param string $type
     * @return boolean
     */
    public static function load($file, $type = NULL) {
        self::$useKeyCode
                || (self::$useKeyCode = (self::CODE_TEXT | self::CODE_MD5));
        if (Aif::ecsc('aif/lib/file','isfile',$file,0))
            self::$file = $file;
        if (is_readable($file)) {
            $type && (self::$type = $type);
            switch (self::$type) {
            case self::PHP_ARRAY:
                include_once self::$file;
                if (isset($lang) && is_array($lang)) {
                    self::$lang = $lang;
                    return TRUE;
                }
                break;
            case self::INI:
                break;
            case self::XML:
                break;
            }

        }
        return FALSE;
    }
    /**
     * Construye una clave apartir de un texto
     * @param string $text
     * @return string
     */
    public static function toKey($text) {
        switch (self::$useKeyCode) {
        case self::CODE_TAG:
            return $text;
        case self::CODE_MD5:
            return md5(preg_replace("/[^a-z0-9]/", '', mb_strtolower($text)));
        case (self::CODE_MD5 | self::CODE_TEXT):
            return md5(Aif::ecsc('aif/lib/convert','toKey',$text,$text));
        case self::CODE_TEXT:
            return Aif::ecsc('aif/lib/convert','toKey',$text,'');
        }
        return $text;
    }
    /**
     * Agrega una etiqueta fisicamente en el archivo
     * @param unknown $text
     * @param string $toKey
     */
    public static function set($text, $toKey = NULL) {
        if (self::$file) {
            $toKey || ($toKey = self::toKey($text));
            switch (self::$type) {
            case self::PHP_ARRAY:
                self::$lang[$toKey] = $text;
                self::$useKeyCode || ($toKey = str_replace("'", "\'", $toKey));
                $text = str_replace("'", "\'", $text);
                $text = '$lang' . "['$toKey']='" . $text . "';";
                if (!file_exists(self::$file)) Aif::ecsc('aif/lib/file','write',
                        array(self::$file, str_replace('#DATE#', date('d/m/Y H:i:s'),self::$header)));
                Aif::ecsc('aif/lib/file','append',array(self::$file, $text));
                break;
            case self::INI:
            //$text = '$lang' . "['$toKey']='" . addslashes($text) . "';";
                break;

            case self::XML:
                break;
            }

        }
    }

    /**
     * Devuelve una etiqueta en un dioma
     * @return string
     */
    public static function get($val = NULL, $idTxtMd5 = NULL) {
        $num = func_num_args();
        if ($num && !isset($val[1])) {
            $ar = func_get_args();
            if (is_array($ar[0])) {
                $num = count($ar[0]);
                $ar = array_shift($ar);
            }
            $text = $ar[0];
            if (self::$lang && $text) {
                $toKey = self::toKey($text);
                if (isset(self::$lang[$toKey])) {
                    $text = self::$lang[$toKey];
                } elseif (self::$isWrite)
                    self::set($text, $toKey);
            } elseif (self::$isWrite && $text)
                self::set($text);

            if ($num > 1) {
                foreach ($ar as $idx => $arg) {
                    $bsq[] = '%' . $idx;
                    $rmp[] = $arg;
                }
                $text = str_replace($bsq, $rmp, $text);
            }
            return $text;
        } else if (isset($val[1])) {
            $text = $val[1];
            if (self::$lang && $val[1]) {
                if (isset(self::$lang[$val[1]]))
                    $text = self::$lang[$val[1]];
            }
            return $text;
        }
    }

    public static function out($val = NULL, $idTxtMd5 = NULL) {
        echo self::get($val,$idTxtMd5);
    }
}
