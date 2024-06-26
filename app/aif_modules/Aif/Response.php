<?php
/**
 * Procesa la respuesta web
 *
 * @author $author$
 * @copyright Copyright (c) 2008, {@link http://www.aquainteractive.com AquaInteractive}
 * @package Aif
 * @since 2013-04-15
 * @subpackage Core
 * @version 1.0.0
 */

/**
 * Clase que se utiliza para preparar respuesta web
 *
 * @package Aif
 * @subpackage Core
 */

class AifResponse {
    /**
     * Variable que servira para indicar si se quiere forzar la salida de un
     * objeto en JSON o no necesariamente y tome en cuenta también la salida
     * como tipo ARRAY
     * @var [boolean] TRUE|FALSE
     */
    public static $__force = TRUE;
    /**
     * Modo seguro
     * @var boolean
     */
    public static $safe = TRUE;
    /**
     * Dias pordefault para establecer la cache de un recurso
     * @var integer
     */
    public static $cache = 15;
    /**
     * Version del protocolo HTTP
     * @var string
     */
    public static $httpVer = 'HTTP/1.0';
    /**
     * Formatos de salida permitida para el método en ejecución
     * @var array
     */
    private static $__renders = array('html' => true);
    /**
     * Cabeceras de salidas Web
     * @var array
     */
    private static $__headers = array();
    /**
     * Permisos para que los metodos sean ejecutados desde clases externas
     * @var array
     */
    private static $__method = array();
    /**
     * Tipo de recursos que pueden ser enviados al web
     * @var string
     */
    private static $__src = 'html|js|css|txt|ini|mp3|swf|flv|svg|gif|jpg|png|svg|ico';
    /**
     * Tipos de contenidos web
     * @var Array Tipos de archivo
     */

    /**
     * Obtiene el protocolo http y version para respuesta
     * @return string
     */
    public static function getProtocol() {
        if (isset($_SERVER['SERVER_PROTOCOL'])
                && $_SERVER['SERVER_PROTOCOL'] != '') {
            self::$httpVer = $_SERVER['SERVER_PROTOCOL'];
        }
        return self::$httpVer;
    }
    /**
     * Registra formato de salida para renderizar el método en ejecución
     * @param string $ext,.. Cadena del formato de salida para el render
     */
    public static function set() {
        self::$__renders = func_num_args() ? array_flip(func_get_args())
                : array();
        if (self::$safe && Aif::$render != 'html' && !self::isAllow()) {
            Aif::csc('aif/debugger','error',
                    'It interrupts the execution of the method, "'
                            . Aif::$render . '" format is not supported.');
        }
    }
    /**
     * Permite activar y desactivar la cahe de la pagina web
     * @param boolean $cache
     */
    public static function setCache($cache = TRUE) {
        $cache = is_bool($cache) && $cache ? (60 * 60 * 24 * self::$cache)
                : $cache;
        $expires = intval($cache);
        if ($expires) {
            $strDate = gmdate('D, d M Y H:i:s', time() + $expires);
            header("Pragma: public");
            header("Cache-Control: maxage=" . $expires);
            header('Expires: ' . $strDate . ' GMT');
        } else {
            header("Cache-Control: no-cache, must-revalidate");
            header("Expires: Mon, 30 Jan 1978 05:00:00 GMT");
        }
        return TRUE;
    }
    /**
     * Asigna na cabecera para ser lanzada de respesuta web
     * @param string $header
     */
    public static function setHeader($header) {
        header($header);
        return TRUE;
    }
    /**
     * Registra en la cabecera el tipo de respuesta web
     * @param string $type
     * @param string $ex Lista separada por comas de tipo de contenidos excluidos
     * @return boolean
     */
    public static function setContentType($type, $excl = NULL) {
        if ($excl && preg_match('/' . preg_quote($excl, '/') . '/', $type))
            return FALSE;
         return self::setHeader('Content-type: ' . Aif::ecsc('aif/mime','getByExt',$type,'text/html'));
    }
    /**
     *
     * @param unknown_type $ar
     * @param unknown_type $path
     * @return Ambigous <boolean, string>
     */
    private static function _getFile($ar, $path = '') {
        $file='';
        if(is_array($ar)){
            $file = array_shift($ar);
            is_array($file) && ($file = '/' . implode('/', $file));
            if (count($ar)) {
                Aif::$render = preg_replace(
                        '/^(.*?)\.(' . implode('|', $ar) . ')$/', '$2', $file);
                self::set(Aif::$render);
            }
            $file = $path . $file;
        }
        return file_exists($file) ? file_get_contents($file) : FALSE;
    }
    /**
     * Regresa el contenido de un archivo
     * @param string $file
     */
    public static function getFile() {
        return self::_getFile(func_get_args());
    }
    /**
     * Regresa el contenido de un archivo de recurso /src
     * @param string $file
     */
    public static function getFileSrc() {
        return self::_getFile(func_get_args(), AIF_APP . '/Aif/Src');
    }
    /**
     * Regresa el contenido de un archivo del directorio de publicacion
     * @param string $file
     */
    public static function getFileWeb() {
        return self::_getFile(func_get_args(), AIF_WEB);
    }
    /**
     * Regresa el contenido de un archivo del path de las vistas
     * @param string $file
     */
    public static function getFileView() {
        return self::_getFile(func_get_args(), Aif::ecsc('aif/view','getFileView',Array(),NULL));
    }
    /**
     * Devuelve el nombre y tipo de documento de un archivo
     * @param string $file
     * @param string $name (opcional)
     * @param string $type (opcional)
     * @return array Arreglo (Type,Name)
     */
    public static function getNameType($file, $name = NULL, $type = NULL) {
       (!$name || !$type) && ($pp = pathinfo($file));
        $name = $name ? $name : $pp['basename'];
        $type || ($type =Aif::ecsc('aif/mime','getByExt',$pp['extension'],'text/html'));
        return array($type, $name);
    }
    /**
     * Envia como respuesta de salida un archivo
     * @param string $file Nombre absoluta del archivo web
     */
    public static function sendFile($file, $name = NULL, $type = NULL,
            $size = NULL) {
        if (is_readable($file)) {
            $ar = self::getNameType($file, $name, $type);
            $name ? (list($type, $name) = $ar) : (list($type) = $ar);
            //die('('.$type.')');
            //ob_clean();
            $type && header("Content-Type: " . $type);
            $name && header("Content-Disposition: attachment; filename=$name");
            header("Content-Length: " . ($size ? $size : filesize($file)));
            flush();
            readfile($file);
            exit;
        }
        Aif::ecsc('aif/debugger','set',array('sendFile', 'Not found: ' . $file));
        return FALSE;
    }
    /**
     * Envia como respuesta una descarga web de un archivo
     * @param string $file Nombre absoluta del archivo web
     */
    public static function download($file, $name = NULL, $type = NULL,
            $size = NULL) {
        if (is_readable($file)) {
        	$ar = self::getNameType($file, $name, $type);
        	$type = $ar[0];
        	$name = $ar[1];
            //ob_clean();
            $type && header("Content-Type: " . $type);
            !$type && header('Content-Description: File Transfer');
            header("Content-Disposition: attachment; filename=$name");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . ($size ? $size : filesize($file)));
            flush();
            readfile($file);
            exit;
        }
        Aif::ecsc('aif/debugger','set',array('download', 'Not found: ' . $file));
        return FALSE;
    }
    /**
     * Envia un codigo HTTP en la respuesta web
     * @param string $code
     */
    public static function status($code) {
        $ver = self::getProtocol();
        $ar = array(200 => 'Ok', 201 => 'Created', 202 => 'Accepted',
                204 => 'No Content', 300 => 'Moved Permanently',
                301 => 'Moved Temporarily', 302 => 'See Other',
                304 => 'Not Modified', 400 => 'Bad Request',
                401 => 'Unauthorized', 403 => 'Forbidden', 404 => 'Not Found',
                500 => 'Internal Server Error', 501 => 'Not Implemented',
                502 => 'Bad Gateway', 503 => 'Service Unavailable');
        return isset($ar[$code]) && header("$ver $code " . $ar[$code]);
    }
    /**
     * Envia un respuesta de página o documento web no encontrado en el servidor
     * @param string $file (opcional)
     * @param string $text (opcional)
     */
    public static function notFound($file = 'The requested URL',
            $text = ' / was not found on this server') {
        self::status(404);
        die(
                '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">'
                        . '<html><head><title>404 Not Found</title></head><body>'
                        . '<h1>Not Found</h1>' . '<p>' . $file . ' ' . $text
                        . '</p>'
                        . '<hr><address>Aqua Interactive Framework (Aif '
                        . Aif::$version . ')</address>' . '</body></html>');
    }
    /**
     * Envia como respuesta un rediccionamiento web
     * @param string $url Direccion electronica
     * @param string $onBase Considera direccion electronica completa
     */
    public static function redirect($url = NULL, $onBase = TRUE) {
        $url = $url ? preg_replace('/^\//', '', $url) 
                    : $url;
        $url = $url ? ($onBase ? Aif::ecsc('aif/request','getUrl','%a/') : '') . $url
                    : Aif::$uri;
        header('location: ' . $url);
        exit;
    }
    /**
     * Comprueba si se permite el tipo de contendo como salida para el método
     * @param string $ext Formato de salida para el render
     * @return boolean Devuelve verdadero si existe la salida
     */
    public static function isContentType($ext) {
        return isset(self::$__renders[mb_strtolower($ext)]) ? TRUE : FALSE;
    }
    /**
     * Comprueba si tiene permitido la salida
     * @return boolean
     */
    public static function isAllow() {
        //die(self::isContentType(Aif::$render)?'':'');
        return self::isContentType(Aif::$render)
                || (self::$__method != NULL
                        && self::allowed(Aif::$render,
                                array(Aif::$className, Aif::$method)));
    }
    /**
     * Permitir ejecutar metodos desde clases externas. por ejemplo Amfphp.
     *  @param string $Type Tipo de contenido Ej, "amf"
     *  @param string $...(Opcional) Nombre de los metodos
     */
    public static function setAllow() {
        $ar = func_get_args();
        $tt = count($ar);
        if ($tt) {
            if ($tt == 1) {
                self::$__method = NULL;
                // $ar[] = Aif::$method;
            } else {
                $type = strtolower(array_shift($ar));
                $type = $type . ':' . Aif::$application . ':' . Aif::$className;
                if (isset(self::$__method[$type]))
                    self::$__method[$type] = array_merge(
                            self::$__method[$type], array_flip($ar));
                else
                    self::$__method[$type] = array_flip($ar);
            }
        }
    }
    /**
     * Comprueba si tiene permisos de ejecucion el metodo de una clase para salida web
     * @param string $Type Tipo de contenido Ej, "amf"
     * @param array $ar [0]Nombre de clase, [1]Nombre del metodo
     * @return boolean
     */
    public static function allowed($type, $ar) {
        if (count($ar) > 1) {
            Aif::$className = $ar[0];
            Aif::$method = $ar[1];
            if (Aif::$className != Aif::$application)
                Aif::$class = str_replace(Aif::$application, '',
                        Aif::$className);
            return isset(
                    self::$__method[$type . ':' . Aif::$application . ':'
                            . Aif::$className][Aif::$method]) ? TRUE
                    : (self::$__method == NULL ? TRUE : FALSE);
        }
        return FALSE;
    }
    /**
     * Envia salida web para el despliege en relacion al tipo de contenido solicitado
     * @param mixid $result Resultado de método en ejecución
     */
    public static function send($result = NULL) {
        if (self::isAllow()) {
            self::setContentType(Aif::$render, 'amf');
            switch (Aif::$render) {
            case 'amf':
                if(Aif::exists('aif/provider/amfphp',true)) 
                    return Aif::csc('aif/provider/amfphp','index',$result) && die();
                else return $result;
            case 'csv':
               if(Aif::exists('aif/lib/convert',true)) 
                    return  Aif::csc('aif/lib/convert','outCsv',$result) && die();
                else return $result;
            case 'json':
                if(Aif::exists('aif/lib/convert',true)) 
                    return  Aif::csc('aif/lib/convert','outJson',Array($result, self::$__force)) && die();
                else return $result;
            case 'rss':
                if(Aif::exists('aif/lib/convert',true)) 
                    return  Aif::csc('aif/lib/convert','outRss',$result) && die();
                else return $result;
            case 'soap':
                if(Aif::exists('aif/lib/soap',true)) 
                    return  Aif::csc('aif/lib/soap','soap',$result) && die();
                else return $result;
            case 'wsdl':
                if(Aif::exists('aif/lib/soap',true)) 
                    return  Aif::csc('aif/lib/soap','wsdl',$result) && die();
                else return $result;
            case 'xml':
                if(Aif::exists('aif/lib/convert',true)) 
                Aif::csc('aif/lib/convert','outXml',$result) && die();
                else return $result;
            case 'xmlrpc':
                if(Aif::exists('aif/lib/xmlrcp',true)) 
                    return  Aif::csc('aif/lib/xmlrcp','service',$result) && die();
                else return $result;
            case 'zip':
                if(Aif::exists('aif/lib/zip',true)) 
                    return  Aif::csc('aif/lib/zip','outData',$result) && die();
                else return $result;
            case 'manifest':
            case 'appcache':
                if(Aif::exists('aif/cache',true)) 
                    return  Aif::csc('aif/cache','appCacheOut',$result) && die();
                else return $result;
            case 'html': 
                return FALSE;
            default:
                if(Aif::$charset == 'UTF-8')
                    header('Content-Type: text/html; charset='.Aif::$charset);
                if (preg_match('/(' . self::$__src . ')/', Aif::$render))
                    die($result);
                Aif::ecsc('aif/debugger','error',Aif::$render . ', unsupported format.');
            }
        } 
        Aif::ecsc('aif/debugger','error','Content is not enabled this method. (' . Aif::$render. ')');
    }
    /**
     * Este metodo se usa en las clases que no requieren atender ninguna respuetsa
     * via WEB, por seguridad, solo llamadas de metodos estaticos o bien
     * la instancia de objetos desde llamadas de comandos
     * @param  string $msg mensaje de salida, en caso de no estar permitido
     * @return boolean
     */
    public static function none($msg =''){
        $isServer = isset ( $_SERVER ) ? true: false;
        if($isServer && isset($_SERVER['argv']) && isset($_SERVER['PHP_SELF'])
            && $_SERVER['argv'][0]==$_SERVER['PHP_SELF']){
            return true;
        }
        Aif::ecsc('aif/debugger','error',
                ($msg   ?   $msg
                        :(  Aif::$debugger>0
                                ?   'Only internal calls to static methods, '.
                                    'or by command console can be made an instance of this class'
                                :   ''
                         )
                ));
    }

}
